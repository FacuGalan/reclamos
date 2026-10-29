# Playbook de deploy — Reclamos

Pasos para actualizar un servidor con lo último de `main`. En el server lo
ejecuta Claude Code con el skill `/actualizar` (`.claude/skills/actualizar`).

> Reemplazar `/ruta/al/proyecto` por la ruta real del proyecto en el servidor
> y `www-data` por el usuario del servidor web si es otro.

## Entornos

| Entorno | Dónde | Qué corre | Quién lo actualiza |
|---|---|---|---|
| Local | PC de cada dev (XAMPP) | su rama de trabajo | la sesión de Claude local |
| **Prueba** | servidor de prueba | último `main` | Claude del server de prueba (`/actualizar`) |
| **Producción** | servidor oficial | un commit de `main` **ya validado en prueba** | Claude del server oficial (`/actualizar`) |

- Todo cambio entra a `main` por PR (lo crea y mergea la sesión local).
- Orden fijo: **merge → deploy a prueba → validar → deploy a producción**.
  Nunca deployar a producción algo que no pasó por prueba.
- Para producción se deploya el **commit exacto** que se validó en prueba
  (`git rev-parse HEAD` en prueba), no "lo último de main": si entró otro PR
  después, ese todavía no fue validado.
- Cada server tiene su propio `.env` y su propia base: nunca copiar `.env` ni
  bases entre prueba y producción.
- **Hoy no existe server de prueba.** Mientras sea así, se deploya
  `origin/main` a producción solo con OK explícito de Facundo, nombrando el
  hash a deployar.

### Datos del server de producción (atencionciudadana)

- PHP 8.4 como **mod_php** de Apache (no hay php-fpm), con
  `opcache.validate_timestamps=1`.
- `storage` y `bootstrap/cache` son `root:webdev` con ACL `u:www-data:rwx`
  (ver paso 8; **no** hacer `chown`).
- Node 18.19.1: alcanza para Vite 6. Si algún día se sube a Vite 7, hace falta
  Node 20.19+ en el server.
- En el `.env` las líneas `DB_*` tienen un espacio adelante: no leerlas con
  `grep '^DB_'`, usar `php artisan tinker` (ver paso 1).

---

## 0. Antes de empezar

- Confirmar que `main` en GitHub tiene los cambios a deployar.
- Producción: tener el hash del commit validado en prueba.
- Revisar si el deploy trae **migraciones** (carpeta `database/migrations`) o
  cambios en **dependencias** (`composer.lock`, `package-lock.json`).
- Si hay migraciones: hacer **backup de la base** (paso 1).

## 1. Backup de la base de datos

`mysqldump -u root` no anda en el server (auth_socket). Leer las
credenciales de la config de Laravel:

```bash
cd /ruta/al/proyecto
php artisan tinker --execute='echo config("database.connections.mysql.username"), " ", config("database.connections.mysql.database");'
PW=$(php artisan tinker --execute='echo config("database.connections.mysql.password");')
mkdir -p ~/backups
MYSQL_PWD="$PW" mysqldump -u <usuario> --single-transaction --routines --triggers --no-tablespaces <bd>   | gzip > ~/backups/reclamos_$(date +%Y%m%d_%H%M).sql.gz
unset PW
```

## 2. Modo mantenimiento

```bash
cd /ruta/al/proyecto
php artisan down --retry=60
```

## 3. Bajar el código

```bash
git fetch origin
git status              # no debería haber cambios locales en el servidor
git log --oneline HEAD..origin/main   # qué baja

# Prueba:
git pull origin main
# Producción (commit validado en prueba; debe estar en origin/main):
git merge --ff-only <hash_validado_en_prueba>
```

Verificar después que el server quedó en el commit esperado
(`git rev-parse HEAD`). Un `200` en el navegador **no** prueba que el deploy
haya corrido.

Si `git status` muestra archivos modificados en el servidor, **no pisarlos a
ciegas**: revisar qué son (ver `SECURITY_REMEDIATION.md` por archivos
sospechosos) antes de continuar.

## 4. Dependencias PHP

Solo si cambió `composer.lock` (`git diff --name-only <commit_anterior> HEAD -- composer.lock`):

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

## 5. Assets (CSS/JS)

`public/build` **no está en git**, así que hay que compilar en el servidor
cada vez que cambian vistas Blade o JS/CSS (Tailwind genera solo las clases
que se usan en las vistas):

```bash
npm ci
npm run build
```

> Si el servidor no tiene Node: correr `npm run build` en local y subir la
> carpeta `public/build` completa por SFTP.

## 6. Migraciones

```bash
php artisan migrate --force
```

## 7. Limpiar y regenerar cachés

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 8. Permisos

En producción **no** hacer `chown -R www-data`: `storage` y `bootstrap/cache`
son `root:webdev` con ACL para `www-data`. Solo verificar:

```bash
getfacl storage bootstrap/cache | grep www-data
```

Si el release crea una carpeta de uploads nueva, darle ACL (también la
default, para lo que se cree adentro):

```bash
setfacl -R -m u:www-data:rwX <carpeta>
setfacl -R -d -m u:www-data:rwX <carpeta>
```

## OPcache

Normalmente no hace falta hacer nada (`validate_timestamps=1`). Si después
del deploy quedara corriendo código viejo: `systemctl reload apache2`
(**pedir OK antes**, afecta a todo el server).

## 9. Salir de mantenimiento

```bash
php artisan up
```

## 10. Verificación post-deploy

- [ ] La web pública carga y permite iniciar un reclamo (captcha OK).
- [ ] Login al panel funciona.
- [ ] Revisar `storage/logs/laravel.log` por errores nuevos:
      `tail -n 50 storage/logs/laravel.log`
- [ ] Verificaciones puntuales del release (ver sección abajo).
- [ ] **No tocar `public/.htaccess`**: la versión de git (endurecida) es la
      que corre en producción desde 2026-10-29 y está probada con Livewire.
      Cualquier cambio ahí va por PR.

## Rollback

```bash
php artisan down
git log --oneline -5                 # identificar el commit anterior
git checkout <commit_anterior>       # o git reset --hard <commit_anterior>
php artisan migrate:rollback --step=1 --force   # solo si el release trajo migración
composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run build
php artisan optimize:clear
php artisan up
```

Si la migración falló a mitad de camino, restaurar el backup del paso 1.

---

## Notas por release

Cada release con algo especial tiene acá un resumen. Si el release es grande,
además hay una guía propia en `docs/releases/AAAA-MM-DD-<tema>.md` con
contexto, problemas conocidos y cómo resolverlos: el Claude del servidor la lee
antes de deployar.

### 2026-09-25 — Motivos de "gestión personalizada"

- **Migración:** `2026_09_25_120000_add_gestion_personalizada_to_categorias_table`
  (agrega `categorias.gestion_personalizada`, default `false`; no afecta motivos
  existentes). Correr el paso 6.
- **Assets:** hay clases Tailwind nuevas (badge en ABM de motivos) → correr el
  paso 5.
- **Verificar:**
  - [ ] En *Motivos*, editar un motivo público → aparece el tilde
        "Gestión personalizada". Marcarlo y guardar → se ve el badge en el listado.
  - [ ] En `/nuevo-reclamo` ese motivo **no** aparece (tampoco si el navegador
        tiene sesión iniciada en el panel).
  - [ ] Desde el panel logueado → *Nuevo Reclamo*, el motivo **sí** aparece y se
        puede dar de alta.

### 2026-09-25 (fix) — Gestión personalizada visible en la web pública con sesión iniciada

- Solo código PHP (`app/Livewire/AltaReclamo.php`): sin migración ni assets.
  Alcanza con los pasos 3 y 7 (OPcache: ver la sección OPcache).
- **Verificar:** logueado en el panel, abrir `/nuevo-reclamo` en el mismo
  navegador → el motivo de gestión personalizada **no** aparece.

### 2026-10-27 — Agenda de contactos, responsable en reclamos internos, fix toggle

> Guía completa (qué puede fallar y cómo resolverlo):
> `docs/releases/2026-10-27-agenda-contactos.md`. **Leerla antes de deployar.**

- **Migración:** `2026_10_27_120000_create_contactos_agenda_table` (crea
  `contactos_agenda` con soft delete, agrega `reclamos.contacto_agenda_id`
  nullable con índice —sin FK a propósito, por datos viejos inconsistentes— y
  **precarga 6 contactos**: Torres, Bernárdez, Defelippe, Cortessi, Tarulla,
  Otros). Correr el paso 6.
- **Assets:** clases Tailwind nuevas (listado de reclamos) → correr el paso 5.
- **Verificar:**
  - [ ] Usuario con acceso a internos → menú *Agenda* con los 6 contactos;
        crear/editar/eliminar.
  - [ ] Reclamo interno → campo "Responsable (Agenda)" en el alta (panel) y
        dentro del reclamo (se guarda solo); aparece en el listado (columna
        Categoría) y en el filtro de búsqueda avanzada.
  - [ ] Editar un reclamo interno y cambiar el edificio → queda guardado
        (antes no se guardaba).
  - [ ] Usuario con acceso a ambos: el botón Internos/Públicos recarga el
        listado y el menú al instante (también entrando a Reclamos desde el menú).
  - [ ] *Imprimir Orden*: el historial ya no muestra el nombre del operador.

### 2026-10-29 — composer.lock alineado con producción y correcciones al playbook

- **composer.lock alineado con producción** (Livewire 3.8.11, Laravel
  12.70.1 y ~50 dependencias del `composer update` hecho en el server el
  2026-10-22). En el server `composer install` no cambia nada.
- **Requiere PHP ≥ 8.4.1** (Symfony 8.1 vía Carbon 3.15). Producción ya
  tiene 8.4; las PCs de desarrollo con XAMPP en PHP 8.2 tienen que pasar a
  8.4 para poder hacer `composer install`.
- Playbook y skill `/actualizar` corregidos según el deploy del 2026-10-29
  (backup con credenciales vía artisan, permisos con ACL, OPcache con
  mod_php, sin server de prueba, Node, `public/.htaccess`).
- Sin migraciones ni assets.
