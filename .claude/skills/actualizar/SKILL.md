---
name: actualizar
description: Deploy de Reclamos en un servidor (prueba u oficial/producción). Usar cuando digan "actualizá", "actualizar el servidor", "hacé el deploy", "bajá lo último". Solo corre EN un servidor, nunca en la PC de desarrollo.
---

# Actualizar servidor (Reclamos)

Seguir `docs/deploy-playbook.md` (pasos 0–10). Este skill agrega los chequeos
que no se pueden saltear.

## 1. Pre-chequeos (si algo falla: FRENAR y avisar)

1. Confirmar que esto es un servidor (Linux, proyecto bajo el webroot, `.env`
   con `APP_ENV` distinto de `local`). En una PC de desarrollo: no tocar nada,
   avisar que el deploy se corre en el server.
2. Identificar el entorno: **prueba** o **producción**. Si `APP_ENV`/`APP_URL`
   no lo dejan claro, preguntar al usuario. Decirlo explícitamente antes de seguir.
3. `git status --porcelain` limpio y rama `main`. Con cambios locales, no
   pisarlos: listarlos y preguntar (pueden ser archivos maliciosos, ver
   `SECURITY_REMEDIATION.md`).
4. `git fetch origin` y mostrar `git log --oneline HEAD..origin/main`.
   Leer en `docs/deploy-playbook.md` → "Notas por release" lo que corresponda
   a esos commits (migraciones, assets, variables de `.env` nuevas).
   Si esos commits agregan o tocan archivos en `docs/releases/`
   (`git diff --name-only HEAD origin/main -- docs/releases`), leerlos
   completos con `git show origin/main:<archivo>` **antes** de bajar el
   código: traen problemas conocidos y cómo resolverlos. Ante cualquier
   duda durante el deploy, buscar primero ahí.

## 2. Qué se deploya

- **Prueba:** `git pull origin main` (último main).
- **Producción:** pedir el hash del commit validado en prueba (sale de
  `git rev-parse HEAD` en el server de prueba). Verificar que esté en
  `origin/main` y hacer `git merge --ff-only <hash>`. Si no hay hash
  validado, NO deployar.

## 3. Resto del deploy

Pasos 1, 2 y 4–9 del playbook: backup de la base si hay migraciones,
`php artisan down`, composer, `npm ci && npm run build` si cambiaron
vistas/JS/CSS, `php artisan migrate --force`, cachés, permisos y `php artisan up`.
Si una migración falla: dejar el sitio en mantenimiento, mostrar el error
completo y preguntar (restaurar backup o corregir).

## 4. Post-chequeo OBLIGATORIO

- `git rev-parse HEAD` == commit esperado. Si no coincide, el deploy NO
  corrió: avisar antes de diagnosticar otra cosa.
- Revisar `tail -n 50 storage/logs/laravel.log`.
- Recorrer las verificaciones del release en el playbook y decirle al
  usuario qué probar en el navegador.
- En **prueba**: terminar informando el hash deployado, que es el que se usa
  después para producción.
