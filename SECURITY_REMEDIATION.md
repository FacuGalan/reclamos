# Remediación post-incidente — Runbook para el servidor

> **Para Claude Code corriendo en el servidor de producción:** este archivo es el
> handoff de una sesión previa en el entorno de desarrollo. Contiene el paso a
> paso para terminar de mitigar un incidente de seguridad en el que el servidor
> fue comprometido mediante un upload de archivos sin validar (RCE vía Livewire).
>
> Los fixes de código ya vienen en este commit. Lo que queda es **limpieza del
> servidor + hardening de infraestructura + rotación de credenciales**.
>
> **Antes de ejecutar cualquier paso destructivo, confirmá con el usuario.**

---

## Contexto del incidente

- **Vulnerabilidad raíz:** `app/Livewire/AltaReclamo.php` tenía una propiedad
  `public $imagen` cuya regla de validación (`nullable|image|max:5120`) estaba
  declarada pero nunca se ejecutaba antes de `$this->imagen->store('reclamos', 'public')`.
- **Exposición:** 4 rutas públicas (`/nuevo-reclamo`, `reclamos/crear-interno-publico`,
  `reclamos/crear-interno-publico/formulario`, y un cuarto camino) renderizaban
  el componente sin auth.
- **Impacto observado:** el atacante subió shells PHP a `storage/app/public/reclamos/`
  (accesibles como `https://<host>/storage/reclamos/<hash>.php` porque
  `public/storage` es symlink). También tocó `public/index.php`, `public/.htaccess`
  y dejó un `php.ini` override. Múltiples familias de shells porque el box quedó
  indexado por scanners automatizados.

## Qué vino arreglado en este commit

- Validación de `$imagen` endurecida (`mimes` + `mimetypes` + `dimensions`) con
  `updatedImagen()` que valida en el momento del upload y descarta el temporal
  si falla.
- `storeAs()` con nombre aleatorio (`bin2hex(random_bytes(16))`) y extensión
  derivada del MIME real, no del nombre que manda el cliente.
- `validateStep2()` ahora incluye `imagen` (antes se saltaba).
- Rate limit `throttle:30,1` en todas las rutas públicas.
- `/orden/imprimir` pasó a `auth + verified`.
- `public/.htaccess` endurecido: deny de dotfiles/`.env`/logs, deny de cualquier
  `.php` que no sea `index.php`, headers de seguridad (`X-Content-Type-Options`,
  `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`).
- Nuevo `storage/app/public/.htaccess` que apaga `php_flag engine off` y deniega
  extensiones ejecutables — **defensa en profundidad contra futuras vulnerabilidades**.
- Queries geoespaciales (`ST_Contains` con `POINT(...)`) pasadas a bindings puros.

---

## PASO 0 — Backup antes de tocar nada

```bash
# Crear snapshot del filesystem y la DB ANTES de cualquier cambio.
# Ajustar rutas/credenciales según el servidor.
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR=/root/backups_reclamos
mkdir -p "$BACKUP_DIR"

# 1. Tar del proyecto completo (para análisis forense posterior)
tar -czf "$BACKUP_DIR/proyecto_$TIMESTAMP.tar.gz" \
    --exclude="node_modules" \
    --exclude="vendor" \
    /ruta/al/proyecto

# 2. Dump de la base
mysqldump -u <user> -p <database> > "$BACKUP_DIR/db_$TIMESTAMP.sql"

# 3. Copia del vhost de Apache
cp /etc/apache2/sites-enabled/*reclamos* "$BACKUP_DIR/" 2>/dev/null || true
```

**No avanzar sin que los tres backups existan y sean legibles.**

---

## PASO 1 — Pull del código arreglado

```bash
cd /ruta/al/proyecto

# Ver si hay cambios locales (probablemente sí — archivos modificados por el atacante)
git status

# NO hacer git checkout . todavía — primero hay que saber qué tocó el atacante.
# Guardar evidencia de los cambios maliciosos:
git diff > /root/backups_reclamos/git_diff_before_cleanup_$(date +%Y%m%d_%H%M%S).txt

# Ahora sí: traer los fixes
git fetch origin
git log HEAD..origin/main --oneline   # revisar qué viene

# Si los cambios locales son SOLO los tampering del atacante (public/index.php,
# public/.htaccess, etc.) y no hay trabajo legítimo sin commitear:
git checkout -- public/index.php public/.htaccess
git pull origin main

# Si hay trabajo legítimo sin commitear, confirmarlo con el usuario antes de
# descartar nada. NO usar git reset --hard a menos que el usuario autorice.
```

---

## PASO 2 — Buscar y eliminar archivos maliciosos

> **Crítico.** El atacante dejó múltiples shells. Si quedan, el parche de código
> no alcanza — vuelven a entrar.

### 2.1 — Archivos PHP que no deberían existir

```bash
cd /ruta/al/proyecto

# En public/ SOLO debería existir index.php
find public -type f -name "*.php" ! -name "index.php"
# Si aparece algo, es una shell. Mostrarle al usuario antes de borrar.

# En storage/app/public/ NO debería haber ejecutables
find storage/app/public -type f \
    \( -name "*.php" -o -name "*.phtml" -o -name "*.phar" \
       -o -name "*.pl" -o -name "*.py" -o -name "*.jsp" \
       -o -name "*.sh" -o -name "*.cgi" \)

# Archivos .htaccess que no reconozcamos (debería existir solo uno, el nuestro)
find storage -name ".htaccess" -exec ls -la {} \;
find public -name ".htaccess" -exec ls -la {} \;
```

### 2.2 — Buscar firmas de shells conocidas

```bash
# Patrones típicos de backdoors en PHP
grep -rlE "eval\(base64_decode|eval\(gzinflate|assert\(\\\$_|preg_replace.*\/e|system\(\\\$_|passthru\(\\\$_|shell_exec\(\\\$_" \
    --include="*.php" /ruta/al/proyecto 2>/dev/null

# Archivos PHP modificados después de la última fecha de deploy legítimo.
# Ajustar la fecha al último deploy previo al incidente.
find /ruta/al/proyecto -name "*.php" -newermt "2025-10-01" \
    -not -path "*/vendor/*" -not -path "*/node_modules/*" -not -path "*/.git/*"
```

### 2.3 — `php.ini` u overrides sueltos

```bash
# Overrides que el atacante pudo dejar para reactivar funciones peligrosas
find /ruta/al/proyecto -name "php.ini" -o -name ".user.ini" 2>/dev/null
# Deberían ser cero. Si aparecen, revisar y borrar.
```

**Antes de borrar cualquier cosa:** listar todo, confirmar con el usuario que
son archivos maliciosos (no uploads legítimos de vecinos, no ajustes propios).
Recién después: `rm <archivo>`.

---

## PASO 3 — Verificar Apache (hardening de infra)

### 3.1 — DocumentRoot debe apuntar a `public/`

```bash
grep -E "DocumentRoot|<Directory" /etc/apache2/sites-enabled/*reclamos*
```

**Debe ser `/ruta/al/proyecto/public`** — no la raíz del proyecto. Si apunta a
la raíz, cualquier archivo queda servible y es gravísimo.

### 3.2 — Bloquear ejecución de PHP en uploads desde el vhost

Los `.htaccess` que vienen en el commit son la primera línea, pero la config
del vhost es más fuerte porque no depende de `AllowOverride`. Agregar:

```apache
<Directory /ruta/al/proyecto/storage/app/public>
    php_admin_flag engine off
    Options -ExecCGI -Indexes
    <FilesMatch "\.(php|phtml|phar|pl|py|jsp|asp|sh|cgi)$">
        Require all denied
    </FilesMatch>
</Directory>

# Y asegurar que los .htaccess nuestros sean respetados
<Directory /ruta/al/proyecto/public>
    AllowOverride All
</Directory>
```

Después:
```bash
apache2ctl configtest && systemctl reload apache2
```

### 3.3 — Confirmar `AllowOverride` para que los `.htaccess` del repo surtan efecto

```bash
grep -A 5 "<Directory.*proyecto" /etc/apache2/apache2.conf /etc/apache2/sites-enabled/*
```

Si el `Directory` del docroot tiene `AllowOverride None`, los `.htaccess` del
repo se ignoran. Cambiar a `AllowOverride All` (o al menos
`AllowOverride FileInfo AuthConfig Limit Options=ExecCGI,Indexes`).

---

## PASO 4 — Rotar TODAS las credenciales

> El atacante tuvo lectura del filesystem. Asumir que **todo** lo que estaba en
> disco está comprometido.

```bash
cd /ruta/al/proyecto

# 1. Generar nueva APP_KEY (invalida todas las sesiones/cookies existentes)
php artisan key:generate

# 2. Editar .env y cambiar manualmente:
#    - DB_PASSWORD (y actualizar en MySQL: ALTER USER '<user>'@'<host>' IDENTIFIED BY '<nueva>';)
#    - MAIL_PASSWORD (regenerar en el proveedor de mail)
#    - Cualquier API key (reCAPTCHA, servicios externos, WhatsApp)
nano .env

# 3. Si había passwords de usuarios conocidos del staff, forzar cambio en el próximo login
# (requiere discusión con el usuario — depende de la política del municipio)
```

---

## PASO 5 — Auditar la base de datos

El atacante tuvo RCE → pudo escribir en la DB. Buscar:

```sql
-- 5.1 Reclamos con descripciones sospechosas (XSS stored)
SELECT id, descripcion, created_at FROM reclamos
WHERE descripcion REGEXP '<script|onerror=|onload=|javascript:|<iframe|<embed'
ORDER BY created_at DESC;

-- 5.2 Usuarios creados que no reconozcas
SELECT id, name, email, dni, created_at, last_login_at
FROM users
ORDER BY created_at DESC LIMIT 50;

-- 5.3 Usuarios con emails raros o permisos elevados
SELECT u.id, u.name, u.email, r.name as rol
FROM users u
LEFT JOIN role_user ru ON ru.user_id = u.id
LEFT JOIN roles r ON r.id = ru.role_id
ORDER BY u.created_at DESC;

-- 5.4 Personas con emails que parecen inyectados
SELECT id, dni, nombre, apellido, email, telefono, created_at
FROM personas
WHERE email REGEXP '<|>|javascript:|http'
   OR nombre REGEXP '<|>|script'
   OR apellido REGEXP '<|>|script';
```

**Mostrar los resultados al usuario — no borrar nada sin aprobación**.

---

## PASO 6 — Re-compilar y cachear

```bash
cd /ruta/al/proyecto

# Limpiar caches contaminadas
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reinstalar dependencias desde composer.lock (por si vendor/ fue tocado)
rm -rf vendor
composer install --no-dev --optimize-autoloader

# Regenerar assets frontend
rm -rf node_modules public/build
npm ci
npm run build

# Recachear para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permisos correctos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Recrear symlink de storage (por si fue tocado)
php artisan storage:link
```

---

## PASO 7 — Verificación post-remediación

### 7.1 — Test de la vulnerabilidad original

Intentar reproducir el ataque:

```bash
# Crear un "shell" de prueba (PHP renombrado a .jpg)
echo '<?php echo "PWNED"; ?>' > /tmp/fake.jpg

# El upload debe fallar en la validación. Probar desde el formulario público
# (/nuevo-reclamo). Debe rechazar el archivo.
# Verificar en storage/app/public/reclamos/ que NO aparece el archivo.
ls -la storage/app/public/reclamos/
```

### 7.2 — Test de acceso directo

```bash
# Estos tres deben dar 403/404, no ejecutar PHP ni exponer data:
curl -I https://<host>/.env
curl -I https://<host>/composer.json
curl -I https://<host>/storage/reclamos/test.php

# El flujo legítimo debe seguir funcionando:
curl -I https://<host>/nuevo-reclamo
curl -I https://<host>/       # home
```

### 7.3 — Headers de seguridad

```bash
curl -I https://<host>/ | grep -iE "X-Content-Type|X-Frame|Referrer-Policy"
# Deben aparecer los tres.
```

### 7.4 — Logs

```bash
# Buscar intentos recientes de explotar el path viejo
tail -500 /var/log/apache2/access.log | grep -iE "storage/reclamos.*\.php|shell|cmd\.php|\.phtml"
# Idealmente: cero hits nuevos después del fix.
```

---

## Decisiones pendientes (requieren input del usuario)

Estos puntos NO se aplicaron automáticamente porque cambian UX o requieren
credenciales externas. Cuando el usuario esté, preguntarle:

### A. Captcha en formularios públicos
Opciones: Cloudflare Turnstile (recomendada, gratis, sin tracking), hCaptcha,
reCAPTCHA v3. Requiere crear keys en el proveedor, agregarlas al `.env` y
modificar los componentes `AltaReclamo` y `AltaReporte`.

### B. Fuga de PII por enumeración de DNI
En `AltaReclamo::buscarPersonaPorDni()` y `AltaReporte::buscarPersonaPorDni()`,
cualquier visitante puede tipear DNIs y obtener nombre/apellido/teléfono/email.
El throttle mitiga pero no resuelve. Fix propuesto: en contexto `'publico'`, no
autocompletar teléfono ni email (solo nombre/apellido).

### C. Firmar las URLs del flujo externo (`crear-interno-publico`)
La ruta acepta `?user=DNI&nombre=X&apellido=Y` sin validar que los tres
coincidan. Debería usar `URL::signedRoute()` con HMAC. Esto requiere
coordinación con quien genera los links (sistema de WhatsApp del municipio).

---

## Orden sugerido de ejecución

1. **Paso 0** (backup) — obligatorio, sin skip.
2. **Paso 1** (pull) — trae los fixes.
3. **Paso 2** (limpieza de shells) — el más crítico.
4. **Paso 3** (Apache) — sin esto los `.htaccess` pueden no aplicar.
5. **Paso 6** (caches/permisos) — para que el código nuevo surta efecto.
6. **Paso 4** (rotar credenciales) — puede hacerse en paralelo al 5.
7. **Paso 5** (auditar DB) — requiere inspección manual.
8. **Paso 7** (verificación) — confirmar que el fix efectivamente bloquea.
9. **Decisiones pendientes (A/B/C)** — recién acá, con el sistema ya seguro.

Tras el paso 7 exitoso, avisar al usuario y esperar decisiones sobre A/B/C
antes de continuar.
