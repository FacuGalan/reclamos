# Playbook de deploy — Reclamos

Pasos para actualizar el servidor de producción con lo último de `main`.

> Reemplazar `/ruta/al/proyecto` por la ruta real del proyecto en el servidor
> y `www-data` por el usuario del servidor web si es otro.

---

## 0. Antes de empezar

- Confirmar que `main` en GitHub tiene los cambios a deployar.
- Revisar si el deploy trae **migraciones** (carpeta `database/migrations`) o
  cambios en **dependencias** (`composer.lock`, `package-lock.json`).
- Si hay migraciones: hacer **backup de la base** (paso 1).

## 1. Backup de la base de datos

```bash
mysqldump -u USUARIO -p NOMBRE_BD > ~/backups/reclamos_$(date +%Y%m%d_%H%M).sql
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
git pull origin main
```

Si `git status` muestra archivos modificados en el servidor, **no pisarlos a
ciegas**: revisar qué son (ver `SECURITY_REMEDIATION.md` por archivos
sospechosos) antes de continuar.

## 4. Dependencias PHP

Solo si cambió `composer.lock` (en la duda, correrlo igual, es idempotente):

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

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

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
  Alcanza con los pasos 3 y 7 (y reiniciar PHP-FPM si hay OPcache sin
  revalidación: `sudo systemctl reload php*-fpm`).
- **Verificar:** logueado en el panel, abrir `/nuevo-reclamo` en el mismo
  navegador → el motivo de gestión personalizada **no** aparece.
