# Release 2026-10-27 — Agenda de contactos para reclamos internos

Guía para el Claude Code del servidor (prueba u oficial). Complementa los pasos
generales de `docs/deploy-playbook.md`; acá está **lo específico de este
release**: qué cambia, qué puede fallar y cómo resolverlo.

## Qué cambia (en criollo)

1. **Agenda** (menú lateral → *Agenda*): ABM de contactos externos
   (electricistas, plomeros, etc.) con nombre, teléfono y ocupación. Solo la ve
   quien tiene acceso a reclamos y está viendo **internos** (`users.ver_privada = 1`).
2. **Responsable (Agenda)** en reclamos internos: es informativo, no cambia el
   circuito del reclamo ni genera movimientos.
   - En el alta (`reclamos/create/interno`, solo panel logueado) es opcional.
   - En el reclamo ya creado se guarda apenas se elige el contacto.
   - Se ve en el listado (debajo del motivo) y hay filtro en "Búsqueda avanzada".
   - No aparece en la web pública ni en el formulario interno público
     (`reclamos/crear-interno-publico`).
3. **Fix: edificio** — al editar un reclamo interno, el edificio elegido no se
   guardaba. Ahora sí.
4. **Fix: botón Internos/Públicos** — antes no actualizaba la vista si se había
   llegado a Reclamos desde el menú. Ahora recarga la página desde el servidor
   y limpia filtros del modo anterior (barrio, edificio, motivo, responsable).
5. **Orden de impresión** — el historial ya no muestra el nombre del operador.

## Pasos de deploy que aplican

| Paso del playbook | ¿Aplica? | Por qué |
|---|---|---|
| 1. Backup BD | **Sí** | hay migración |
| 4. Composer | No (idempotente, se puede correr igual) | sin cambios en `composer.lock` |
| 5. `npm ci && npm run build` | **Sí** | clases Tailwind nuevas en vistas |
| 6. `php artisan migrate --force` | **Sí** | 1 migración nueva |
| 7. Cachés | **Sí** | rutas nuevas (`/agenda`) → `route:cache` |
| `.env` | No | sin variables nuevas |

## La migración

`database/migrations/2026_10_27_120000_create_contactos_agenda_table.php`

- Crea `contactos_agenda` (`id`, `nombre`, `telefono` null, `ocupacion` null,
  timestamps, `deleted_at`).
- Agrega `reclamos.contacto_agenda_id` (nullable, con índice), después de
  `responsable_id`. **No tiene foreign key a propósito**: agregar una FK en
  `reclamos` hace que MySQL revalide las FKs existentes, y en desarrollo falló
  por reclamos con `domicilio_id` huérfano (`SQLSTATE[23000] ... 1452 ...
  reclamos_domicilio_id_foreign`). No "arreglar" esto agregando la FK.
- Inserta 6 contactos iniciales: `(Sario) Torres, Diego`, `Bernárdez, Daniel`,
  `Defelippe, Julio`, `Cortessi, Juan Pablo`, `Tarulla, Carlos Gabriel`, `Otros`
  (sin teléfono ni ocupación; los completa el usuario desde el ABM).

### Si la migración falla

MySQL no hace rollback de DDL: puede quedar **a medias**. Dejar el sitio en
mantenimiento y revisar el estado antes de reintentar:

```bash
php artisan migrate:status | grep contactos_agenda      # ¿quedó Pending?
php artisan tinker --execute="echo json_encode([
  'tabla' => Schema::hasTable('contactos_agenda'),
  'columna' => Schema::hasColumn('reclamos','contacto_agenda_id'),
]);"
```

- Si sigue **Pending** pero la tabla o la columna ya existen, limpiarlas y
  volver a correr `php artisan migrate --force`:
  ```bash
  php artisan tinker --execute="
    if (Schema::hasColumn('reclamos','contacto_agenda_id')) {
      Schema::table('reclamos', function(\$t){ \$t->dropIndex(['contacto_agenda_id']); \$t->dropColumn('contacto_agenda_id'); });
    }
    Schema::dropIfExists('contactos_agenda');"
  ```
  (si el `dropIndex` falla porque el índice no llegó a crearse, sacarlo y
  dejar solo el `dropColumn`).
- Si el error es otro, mostrárselo completo a Facundo y preguntar antes de
  tocar datos. Ante la duda, restaurar el backup del paso 1.

### Ojo: no duplicar contactos

Los 6 contactos se cargan **dentro** de la migración. Si se hace rollback y se
vuelve a migrar, se borran y se recrean (la tabla se dropea), así que no se
duplican. **No** insertarlos a mano ni con un seeder.

## Verificación post-deploy (decirle al usuario qué probar)

Con un usuario que vea reclamos internos:

- [ ] Menú *Agenda* → están los 6 contactos. Crear uno, editarlo y eliminarlo.
- [ ] *Nuevo Reclamo* interno → en el paso de edificio aparece "Responsable
      (Agenda)". Elegir uno y crear el reclamo → en el listado aparece el
      nombre debajo del motivo.
- [ ] Abrir un reclamo interno → cambiar el responsable → aviso "Responsable
      de la agenda asignado". Cambiar el edificio y *Guardar* → al reabrir,
      quedó el edificio nuevo.
- [ ] Búsqueda avanzada → filtro "Responsable (Agenda)" funciona.
- [ ] Usuario con acceso a internos **y** públicos: entrar a Reclamos desde el
      menú lateral y tocar el botón de abajo a la derecha → la lista, los
      filtros y el menú cambian al toque.
- [ ] Reclamo → *Imprimir Orden* → el historial muestra fecha y texto, sin
      nombre de operador.
- [ ] Web pública `/nuevo-reclamo`: sigue andando y **no** muestra nada de la
      agenda.
- [ ] `tail -n 50 storage/logs/laravel.log` sin errores nuevos.

## Problemas posibles y qué hacer

| Síntoma | Causa probable | Solución |
|---|---|---|
| `/agenda` da 404 | caché de rutas vieja | `php artisan route:clear && php artisan route:cache` |
| `/agenda` da 403 | el usuario está viendo públicos (`ver_privada = 0`) | es lo esperado; cambiar a Internos con el botón |
| No aparece *Agenda* en el menú | usuario sin `lReclamosAbm` o viendo públicos | idem; revisar rol del usuario |
| El responsable se ve sin estilos / íconos gigantes | no se recompilaron assets | paso 5 (`npm ci && npm run build`) |
| Error `Unknown column 'contacto_agenda_id'` | migración no corrió | paso 6 |
| Error `Class "App\Models\ContactoAgenda" not found` | autoload/OPcache viejo | `composer dump-autoload -o` y `sudo systemctl reload php*-fpm` |
| El botón Internos/Públicos no recarga | JS/vista cacheada vieja | `php artisan view:clear`, recargar con Ctrl+F5 |

## Rollback de este release

```bash
php artisan down
php artisan migrate:rollback --step=1 --force   # borra contactos_agenda y la columna (se pierden las asignaciones)
git checkout <commit_anterior>
npm ci && npm run build
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```
