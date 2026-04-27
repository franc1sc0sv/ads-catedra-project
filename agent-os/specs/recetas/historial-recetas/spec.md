# Historial de Recetas

## Resumen

Archivo completo de todas las recetas registradas en el sistema, sin importar su estado (pendiente, validada, rechazada, surtida, anulada). Sirve como registro de auditoría para inspecciones sanitarias, disputas con pacientes o médicos y revisión de trabajo previo. El acceso está limitado al farmacéutico (revisión de su propio trabajo) y al administrador (auditoría general).

A diferencia de la cola activa de validación, esta vista es estrictamente de lectura: no permite cambiar estado, agregar observaciones ni editar campos. Es una superficie consultable construida sobre los datos que ya generan los demás flujos de receta.

## Comportamiento

### Tabla principal

Lista paginada del lado del servidor con las siguientes columnas:

- Número de receta
- Paciente
- Médico (nombre y especialidad)
- Fecha de emisión
- Estado actual
- Farmacéutico que validó (puede estar vacío si la receta nunca fue tomada)
- Fecha de validación / decisión

El orden por defecto es por fecha de emisión descendente. La paginación se resuelve enteramente en el backend; nunca se carga el dataset completo al cliente.

### Filtros combinables

Todos los filtros se aplican vía query string y se pueden combinar libremente:

- Estado de la receta (todos los valores del enum, incluyendo anulada)
- Médico emisor
- Paciente
- Farmacéutico validador
- Rango de fechas (desde / hasta) sobre la fecha de emisión

Los filtros vacíos se ignoran. La paginación se reinicia al primer página cuando cambia cualquier filtro.

### Búsqueda exacta

Campo dedicado para buscar por número de receta. La coincidencia es exacta (no parcial, no fuzzy) — la idea es que el operador llegue al ticket directamente cuando ya conoce el número, típicamente desde un papel físico o referencia externa.

### Detalle de receta

Al abrir una receta del listado se muestra una vista de solo lectura con:

- Datos completos de la receta (paciente, médico, medicamentos, dosis, indicaciones)
- Estado y trazabilidad (quién validó, cuándo)
- Observación del farmacéutico (campo `cObservacion`) — evidencia clave para auditoría cuando hubo rechazo o ajuste
- Venta vinculada cuando existe, con su número y enlace al detalle de venta

No hay acciones desde esta vista. Cualquier corrección debe pasar por los flujos correspondientes (validación, anulación) y queda fuera de alcance.

## Roles permitidos

- `pharmacist` — revisa su propio historial y el del equipo
- `administrator` — auditoría completa del archivo
