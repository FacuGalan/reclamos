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

## Entornos

1. **Local**: PC de cada dev (Facundo, Flor), XAMPP + base local propia.
2. **Prueba**: servidor de prueba, corre el último `main`.
3. **Producción**: servidor oficial, corre un commit de `main` ya validado en prueba.

Orden fijo: **PR mergeado → deploy a prueba → validar → deploy a producción.**
Los deploys los corre Claude Code **en cada servidor** con `/actualizar`; la
sesión local no tiene acceso a los servers y no ofrece deployar desde acá.

## Flujo de trabajo: todo cambio por PR (OBLIGATORIO, para todos)

Nunca commitear ni pushear directo a `main`. No hay CI: el PR es para dejar
registro de cada cambio y no pisarse entre devs. **La propia sesión crea el PR
y lo mergea sola**, sin esperar aprobación ni checks.

1. **Al empezar una tarea** (antes de tocar código):
   - `git status` y rama actual. Si hay trabajo sin commitear o una rama en
     curso, preguntar antes de moverse.
   - `gh pr list --state open`: si hay PRs abiertos (propios o del otro dev),
     avisar y preguntar si se mergean primero o se sigue igual.
   - `git checkout main && git pull origin main`; si cambiaron
     `composer.lock` / `package-lock.json` / migraciones: `composer install`,
     `npm install`, `php artisan migrate`.
   - Rama nueva: `feat/<descripcion-corta>`, `fix/<...>` o `docs/<...>`.
2. **Implementar y probar** en el navegador (web pública **y** panel si toca
   `AltaReclamo`). Pedirle a la persona que lo pruebe en vivo cuando sea UI.
3. **Commit**: mensajes en español, imperativo y descriptivo
   (ej.: "Fix captcha: discriminar por usuario logueado, no por isPrivateArea").
   Antes de `git add -A`, revisar `git status`: no commitear `.env`, dumps,
   zips ni borrados (`D`) que no se hicieron a propósito.
4. **Si el cambio necesita algo en el deploy** (migración, `npm run build`,
   variable de `.env` nueva, pasos manuales): agregar la nota en
   `docs/deploy-playbook.md` → "Notas por release", **en el mismo PR**.
5. **PR + merge**, todo seguido y sin pedir confirmación:
   ```bash
   git push -u origin <rama>
   gh pr create --base main --title "<titulo>" --body "<qué cambia, cómo probarlo, notas de deploy>"
   gh pr merge --squash --delete-branch
   git checkout main && git pull origin main
   ```
   Pushear **todos** los commits antes de mergear (lo que se pushee después
   queda huérfano).
6. **Cerrar** con un resumen: número de PR, qué probar, y si hay deploy
   pendiente "queda para correr `/actualizar` en el server de prueba" (con las
   migraciones / variables de `.env` que trae).

Además:
- Nunca `git push --force`, `git reset --hard` ni borrar ramas ajenas sin
  confirmarlo con la persona.
- Si el merge da conflicto: `git merge origin/main` en la rama, resolver,
  probar de nuevo y recién ahí mergear. Si el conflicto es con trabajo del
  otro dev y no es obvio, preguntar.

## Deploy

- Seguir `docs/deploy-playbook.md` (lo usa el skill `/actualizar` en los servers).
- Las notas por release tienen que ser autocontenidas: las lee un Claude en el
  servidor que no vio la sesión de desarrollo.
