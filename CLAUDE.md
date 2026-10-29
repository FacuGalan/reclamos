# CLAUDE.md — Sistema de Reclamos

Contexto para Claude Code. Leelo antes de tocar código.

## Stack

- Laravel 12 + Livewire 3 (Volt + Flux free), Tailwind 4 vía Vite, MySQL (XAMPP en dev).
- **PHP 8.4** (lo exige el `composer.lock`; producción corre 8.4). Entorno de
  dev: Windows + XAMPP, proyecto en `C:\xampp\htdocs\reclamos`. El PHP del
  XAMPP es 8.2 y lo usan otros proyectos: para Reclamos usar `C:\php84\php.exe`
  (`C:\php84\php.exe artisan ...`, `C:\php84\php.exe C:\composer\composer.phar ...`)
  y no reemplazar el del XAMPP.
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
php artisan serve           # en todos: C:\php84\php.exe en vez de php (ver Stack)
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

## Con quién estás trabajando

Mirá `gh api user --jq .login` (o `git config user.name`):

- **FacuGalan (Facundo)**: dueño del proyecto, técnico. Maneja Git, GitHub y
  los deploys. Con él se puede hablar de ramas, PRs, migraciones y servidores.
- **Cualquier otra persona (ej. Flor)**: **no es técnica en Git/GitHub ni en
  deploys.** Con ella:
  - Hablar solo de lo funcional: qué cambió, qué ver en pantalla, qué probar.
  - **Nunca** mencionar ramas, commits, PRs, merges, conflictos, CI, servidores,
    entornos ni deploys, ni pedirle decisiones sobre eso. Todo lo de Git lo
    resuelve Claude solo, en silencio.
  - No frenar ni trabar el trabajo por temas de Git/GitHub/deploy. Si algo de
    eso no se puede resolver solo (ej. conflicto ambiguo con cambios de
    Facundo, error de permisos en GitHub), decirle en una línea simple
    "esto lo tiene que ver Facundo" y seguir con lo demás.

## Entornos (contexto interno; no explicárselo a Flor)

1. **Local**: PC de cada dev, XAMPP + base local propia. Acá se desarrolla.
2. **Prueba** y 3. **Producción**: servidores. Los deploya **Facundo** a partir
   de lo que está en `main` en GitHub (con el skill `/actualizar` en cada
   server). La sesión local **nunca** deploya ni ofrece deployar.

## Flujo de trabajo (para todos)

Nunca commitear ni pushear directo a `main`: todo entra por PR, que la propia
sesión crea y mergea. No hay CI ni revisión: no esperar checks ni aprobaciones.

1. **Al empezar una tarea**, en silencio:
   - `git status`. Si estás en `main` limpio: `git pull origin main` y crear
     rama `feat/<descripcion-corta>` o `fix/<...>`. Si ya hay una rama de
     trabajo en curso con cambios sin subir, seguir en ella.
   - Si el pull trajo cambios en `composer.lock` / `package-lock.json` /
     migraciones: `composer install`, `npm install`, `php artisan migrate`.
2. **Desarrollar en local** aplicando los cambios a medida que se piden.
   Pedirle a la persona que pruebe en el navegador (web pública **y** panel
   si toca `AltaReclamo`). Commitear en la rama a medida que se avanza, con
   mensajes en español, imperativo y descriptivo. Antes de `git add -A`,
   revisar `git status`: no commitear `.env`, dumps, zips ni borrados (`D`)
   que no se hicieron a propósito.
3. **No subir nada a GitHub hasta que la persona lo pida** ("subilo",
   "mergealo", "subí los cambios", "ya está, mandalo"). Ahí, sin pedir
   confirmación:
   ```bash
   git fetch origin && git merge origin/main      # traer lo de Facundo; resolver conflictos
   git push -u origin <rama>
   gh pr create --base main --title "<titulo>" --body "<qué cambia, cómo probarlo, notas para el deploy>"
   gh pr merge --squash --delete-branch
   git checkout main && git pull origin main
   ```
   - Pushear **todos** los commits antes de mergear.
   - Si el merge con `origin/main` trajo cambios, probar rápido que lo
     tocado siga andando antes de subir.
   - A Flor confirmarle solo "listo, los cambios quedaron subidos".
4. **Notas para el deploy** (siempre, en silencio): si el cambio trae
   migración, clases Tailwind nuevas / JS / CSS, variables de `.env` nuevas o
   pasos manuales, agregarlo en `docs/deploy-playbook.md` → "Notas por
   release" dentro del mismo PR, y también en el cuerpo del PR. Así Facundo
   sabe qué hacer al deployar.

Además:
- Nunca `git push --force`, `git reset --hard`, ni borrar ramas o trabajo
  ajeno.
- Si `gh` no está logueado o no hay permisos sobre el repo: no insistir; con
  Flor, "esto lo tiene que ver Facundo".

## Deploy (solo Facundo / Claude en los servidores)

- Seguir `docs/deploy-playbook.md` (lo usa el skill `/actualizar`).
- Orden: `main` → servidor de prueba → validar → servidor oficial con el commit
  validado en prueba.
