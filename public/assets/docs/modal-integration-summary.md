# Integración de Carga de Archivos en Modal de Cambio de Estado

## Resumen de Cambios Completados

Se ha completado exitosamente la migración de los campos de carga de archivos `fotografia_oficio` y `fotografia_imputado` desde el formulario de creación de mandamientos hacia el modal de "Cambiar Estado del Mandamiento" en la vista de búsqueda.

### Archivos Modificados

#### 1. Vista de Búsqueda (`mandamientosjudiciales_busqueda_view.php`)

**Cambios realizados:**
- ✅ Agregado CSS del sistema de cámara y archivos
- ✅ Incluido el componente modal de cámara
- ✅ Agregados scripts JS de camera-utils y file-preview-component
- ✅ Agregados campos de upload de fotografías en la sección `modal_cumplimentado_photos`
- ✅ Configurado JavaScript para mostrar/ocultar campos según estado seleccionado
- ✅ Inicialización de componentes de cámara y preview para el modal
- ✅ Carga automática de archivos existentes al abrir el modal
- ✅ Función de limpieza de archivos al resetear el modal

**Características implementadas:**
- Los campos de fotografías solo aparecen cuando se selecciona "CUMPLIMENTADO"
- Soporte completo para cámara y subida de archivos
- Previsualización de archivos existentes
- Validación de tipos y tamaños de archivo
- Integración con sistema de toastr para notificaciones

#### 2. Vista de Creación (`mandamientosjudiciales_crear_datos_view.php`)

**Cambios realizados:**
- ✅ Removidos campos `fotografia_oficio` y `fotografia_imputado` del formulario
- ✅ Actualizada lista de archivos requeridos en validación
- ✅ Actualizado título de sección a "ADJUNTAR ARCHIVOS INICIALES"
- ✅ Agregada nota explicativa sobre cuándo se suben los otros archivos

#### 3. Controlador (`Mandamientosjudiciales.php`)

**Cambios realizados:**
- ✅ Actualizado método `cambiar_estado_mandamiento` para manejar uploads de archivos
- ✅ Agregado procesamiento de `fotografia_oficio` y `fotografia_imputado` para estado CUMPLIMENTADO
- ✅ Actualizada lista de archivos requeridos en creación (removidos oficio e imputado)
- ✅ Mantenido soporte para eliminación de archivos en todos los campos

#### 4. Modelo (`Mandamientosjudiciales_model.php`)

**Cambios realizados:**
- ✅ Actualizado método `update_datos` para preservar fotografías de cumplimentación
- ✅ Agregado comentario explicativo sobre preservación de archivos

### Flujo de Trabajo Actualizado

#### Creación de Mandamiento:
1. Se crean solo con `fotografia_carpeta` y `fotografia_orden`
2. Los campos `fotografia_oficio` y `fotografia_imputado` se omiten
3. El sistema indica que estos archivos se subirán al cumplimentar

#### Cumplimentación de Mandamiento:
1. Usuario abre modal de "Cambiar Estado"
2. Selecciona "CUMPLIMENTADO" en el dropdown
3. Aparecen automáticamente los campos de fotografías
4. Usuario puede usar cámara o subir archivos
5. Sistema valida y guarda los archivos junto con los datos de cumplimentación

### Características Técnicas

**Compatibilidad con Sistema Existente:**
- ✅ Utiliza las mismas clases y componentes modularizados
- ✅ Mantiene validación de archivos existente
- ✅ Preserva funcionalidad de eliminación de archivos
- ✅ Compatible con sistema de permisos y upload helper

**Validaciones Implementadas:**
- ✅ Tipos de archivo permitidos: imágenes (JPEG, PNG, GIF) y PDFs
- ✅ Tamaño máximo: 5MB por archivo
- ✅ Validación de archivos requeridos según estado
- ✅ Manejo de errores con notificaciones al usuario

**Funcionalidad de Cámara:**
- ✅ Captura directa desde la cámara del dispositivo
- ✅ Alternancia entre cámara frontal y trasera
- ✅ Compresión automática de imágenes
- ✅ Preview antes de confirmar la foto

### Beneficios Logrados

1. **Flujo Lógico:** Los archivos de cumplimentación se cargan solo cuando realmente se cumplimenta
2. **Modularidad:** Reutilización completa de componentes existentes
3. **UX Mejorada:** Interface más intuitiva y contextual
4. **Mantenibilidad:** Código organizado y reutilizable
5. **Consistencia:** Misma funcionalidad de cámara en toda la aplicación

### Testing Recomendado

Para verificar que todo funciona correctamente:

1. **Crear Mandamiento:** Verificar que solo se requieren carpeta y orden
2. **Cambiar Estado a Cumplimentado:** 
   - Verificar que aparecen campos de fotografías
   - Probar subida desde archivo
   - Probar captura desde cámara
   - Verificar preview de archivos
3. **Editar Mandamiento Cumplimentado:**
   - Verificar que se cargan archivos existentes
   - Probar eliminación de archivos
   - Probar reemplazo de archivos

### Notas Técnicas

- Las fotografías se almacenan en la tabla `mj_datos` 
- El upload helper maneja la validación y almacenamiento
- Los archivos se guardan en `uploads/mandamientos/`
- El sistema preserva archivos existentes al cambiar estados
- Compatible con el sistema de global camera instance

## Estado: ✅ COMPLETADO

La integración ha sido completada exitosamente y está lista para pruebas en el entorno de desarrollo.
