# CLAUDE.md — Sistema de Reclamos

Contexto para Claude Code. Leelo antes de tocar código.

## Stack

- Laravel 12 + Livewire 3 (Volt + Flux free), Tailwind 4 vía Vite, MySQL (XAMPP en dev).
- PHP ^8.2. Entorno de dev: Windows + XAMPP, proyecto en `C:\xampp\htdocs\reclamos`.
- Todo el código, comentarios, UI y mensajes de commit van **en español**.

## Setup local (primera vez)

```bash
composer install
npm install
cp .env.example .env        # ajustar DB_DATABASE / DB_USERNAME / DB_PASSWORD
php artisan key:generate
php artisan migrate         # la BD debe existir (crearla en phpMyAdmin)
php artisan storage:link
npm run build               # o `npm run dev` mientras se trabaja
php artisan serve
```

- `RECAPTCHA_ENABLED=false` en dev (sin keys el verificador hace bypass).
- `MAIL_MAILER=log` en dev: los mails quedan en `storage/logs/laravel.log`.
- El `DatabaseSeeder` solo crea datos de ejemplo (secretarías, áreas, usuarios
  fake). Para datos reales (motivos, estados, barrios, usuarios con roles) pedir
  un dump de la BD a Facundo; no inventar datos.
- **Nunca** commitear `.env`, dumps de BD ni `reclamos.zip`.

## Arquitectura rápida

- Componentes Livewire en `app/Livewire`, vistas en `resources/views/livewire`.
  Los ABM siguen el patrón `AbmXxx.php` + `abm-xxx.blade.php`; copiar la
  estructura de uno existente para uno nuevo.
- `AltaReclamo` es el wizard de alta de reclamos y se usa en **dos contextos**:
  - Web pública (`/nuevo-reclamo`, `reclamos/crear-interno-publico/...`), sin auth.
  - Panel (`reclamos/create`, `reclamos/create/interno`), con auth; `isPrivateArea`
    / `contexto` lo distinguen.
- Modelo `Categoria` = "Motivos" en la UI. Flags relevantes: `privada` (reclamo
  interno), `urgente`, `gestion_personalizada`.
- Reportes de seguridad: `AltaReporte` / `AbmReportes` (dominio aparte de reclamos).
- Estadísticas: `Estadisticas` + trait `ManejadorEstadisticas`.

## Reglas y trampas conocidas (aprendidas a los golpes)

1. **Seguridad de uploads.** El servidor fue comprometido por un upload sin
   validar (ver `SECURITY_REMEDIATION.md`). Todo archivo subido debe:
   validarse con `mimes` + `mimetypes` (+ `dimensions` si es imagen) **antes**
   de guardarse, validar también en `updatedXxx()`, guardarse con `storeAs()`
   y nombre aleatorio (`bin2hex(random_bytes(16))`) con extensión derivada del
   MIME real. Nunca confiar en el nombre del cliente.
2. **Rutas públicas** van dentro del grupo `throttle:30,1` en `routes/web.php`.
   Cualquier cosa que muestre datos de reclamos/personas va con `auth` + `verified`.
3. **Público vs. logueado:** para decidir si algo se muestra al ciudadano, usar
   el contexto de la ruta (`isPrivateArea` / `contexto === 'privado'`), **no**
   `Auth::check()` solo — un empleado logueado puede abrir la web pública en el
   mismo navegador. Ej.: los motivos con `gestion_personalizada = true` nunca
   aparecen en la web pública, aunque haya sesión; además se re-valida en el submit.
4. **reCAPTCHA v3** (`components/recaptcha.blade.php`, `App\Services\RecaptchaVerifier`):
   - Se exige cuando el usuario es invitado (`Auth::guest()`).
   - El `<script>` de Google se inyecta con `@assets` en el componente Livewire
     **padre** (`@assets` no funciona en sub-componentes Blade).
   - El widget usa `x-data` inline (los morph de Livewire no ejecutan `<script>`
     inline) y se monta **una sola vez al inicio del wizard**, fuera de los
     `@if($step == X)`, con `mode="widget"`; el texto legal va con `mode="disclaimer"`.
5. **Tailwind 4 solo genera clases que aparecen en las vistas.** Si se agregan
   clases nuevas hay que recompilar (`npm run build`); `public/build` no está en git.
6. Usuarios con soft delete: tenerlo en cuenta en consultas y relaciones.
7. Cambios de esquema siempre vía migración nueva (nunca editar una migración ya
   deployada), con default que no rompa datos existentes.

## Flujo de trabajo

- Antes de empezar: `git pull origin main`, y si cambiaron `composer.lock` /
  `package-lock.json` / migraciones: `composer install`, `npm install`,
  `php artisan migrate`.
- Commits chicos, mensaje en español en imperativo y descriptivo
  (ej.: "Fix captcha: discriminar por usuario logueado, no por isPrivateArea").
- Se trabaja sobre `main`. Hacer `git pull` antes de `git push` y no usar
  `git push --force` ni `git reset --hard` sin confirmarlo con la persona.
- Probar el cambio en el navegador (web pública **y** panel si toca `AltaReclamo`)
  antes de commitear.

## Deploy

- Seguir `docs/deploy-playbook.md`. Cada release que necesite algo especial
  (migración, assets, verificaciones) se documenta en la sección
  "Notas por release" de ese archivo, **en el mismo commit**.
- El deploy en el servidor lo hace un Claude Code corriendo allá a partir del
  playbook; por eso las notas tienen que ser autocontenidas.
