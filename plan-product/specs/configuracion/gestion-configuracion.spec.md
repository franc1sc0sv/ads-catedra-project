# Gestión de Configuración Global

## What does this part of the system do?

Permite al administrador ajustar los parámetros operativos del sistema sin modificar código ni reiniciar el servidor. Los valores viven en `CatConfiguracion`, se leen en tiempo de ejecución y tienen efecto en el siguiente request que los consulte.

## Who uses it?

Solo el administrador.

## Claves del MVP

| Clave | Tipo | Default | Módulo que la lee |
|---|---|---|---|
| `dias_alerta_vencimiento` | INTEGER | 30 | Alertas de stock — ventana de próximos a vencer (días) |
| `umbral_aviso_stock_bajo` | INTEGER | 0 | Alertas de stock — margen adicional sobre `nStockMinimo` del producto; 0 = usar solo el mínimo configurado en el catálogo |

Estas dos claves deben existir en el seed inicial. Si falta una clave al arrancar la lectura, el módulo usa el default hardcodeado como fallback.

## How does it work?

El admin entra a la sección de configuración y ve una tabla con todas las claves donde `bEditable = true`. Cada fila muestra la clave (`cClave`), la descripción (`cDescripcion`), el tipo de dato (`eTipoDato`) y el valor actual (`cValor`). Al hacer clic en editar aparece un campo apropiado al tipo: `INTEGER` muestra un input numérico, `BOOLEAN` un toggle, `STRING` un input de texto, `DECIMAL` un input decimal. Al guardar, el valor se persiste como string en `cValor` y `fActualizado` se regenera. No hay reinicio ni caché que invalidar: el próximo request que lea esa clave obtiene el valor nuevo.

Las claves con `bEditable = false` son visibles pero no editables desde la UI; su valor solo cambia vía migración o seeder.

## Out of scope

Los cambios en `CatConfiguracion` no generan entradas en `AuditoriaAcceso` en el MVP.

## Skills relevantes

- `/laravel-specialist` — para el CRUD con cast dinámico según `eTipoDato` y la validación por tipo al guardar.
