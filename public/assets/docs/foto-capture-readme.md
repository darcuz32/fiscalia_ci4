# FotoCapture — widget reutilizable de foto (subir + cámara)

Componente JS/CSS/PHP para capturar la **fotografía** de una persona, objeto o expediente con dos botones:

- **Subir** — abre el selector de archivos nativo (igual que el flujo tradicional).
- **Tomar** — abre un modal con la cámara del dispositivo, permite cambiar entre cámaras, previsualizar la captura y volver a tomarla.

La foto capturada se inyecta en el `<input type="file">` existente, de modo que el submit del formulario y el backend (`$_FILES[...]`) siguen funcionando sin cambios.

## Archivos que lo componen

| Archivo | Rol |
|---|---|
| `assets/js/foto-capture.js` | Clase `FotoCapture`. Abre el modal, maneja `getUserMedia`, captura con `<canvas>`, inyecta el File al input destino vía `DataTransfer`. |
| `assets/css/foto-capture.css` | Estilos del modal y del doble botón `.foto-wrap--dual`. |
| `application/views/components/foto_capture_modal.php` | Markup del modal Bootstrap (incluir una vez por página). |
| `application/views/components/foto_capture_field.php` | Helper PHP que emite el widget con la fila del formulario. |

## Cómo incluirlo en una vista nueva

1. **CSS** en `<head>`:

   ```php
   <link rel="stylesheet" href="<?= base_url('assets/css/foto-capture.css'); ?>">
   ```

2. **JS** antes de `</body>` (después de `<?= $this->js ?>`):

   ```php
   <script src="<?= base_url('assets/js/foto-capture.js'); ?>"></script>
   ```

3. **Modal** (una sola vez por página, junto al JS):

   ```php
   <?php $this->load->view('components/foto_capture_modal'); ?>
   ```

4. **Widget** dentro del formulario:

   ```php
   <?php $this->load->view('components/foto_capture_field', [
       'prefix'   => 'detenidos[0]',
       'foto_url' => !empty($persona['fotografia']) ? base_url($persona['fotografia']) : '',
   ]); ?>
   ```

## API del partial `foto_capture_field`

| Variable | Tipo | Default | Notas |
|---|---|---|---|
| `$prefix` | string | `'detenidos[0]'` | Se usa para el `name` del input y para generar IDs únicos por fila. |
| `$foto_url` | string | `''` | URL absoluta de la foto existente (modo edición). Vacía = "Subir". |
| `$input_name` | string | `$prefix . '[fotografia]'` | Override del `name` del input si lo necesitas distinto. |
| `$extra_class` | string | `''` | Clases extra para `.foto-wrap` (para layouts especiales). |

## Contrato con el backend

El submit del form sigue intacto. La foto capturada se asigna al `<input type="file">` con `name="<prefix>[fotografia]"` y se dispara un evento `change`, de modo que el handler delegado existente (si lo tienes) también actualiza la preview.

Servidor (`Detenciones::_maybeUploadPersonPhoto`, `BienesAsegurados::_maybeUploadFoto`): sin cambios. Aceptan `jpg|jpeg|png` ≤ 2 MB, que es lo mismo que captura el componente.

## Limitaciones

- **HTTPS o localhost** — `getUserMedia` exige un origen seguro en la mayoría de los navegadores. En `http://localhost` funciona; en IP/red local sin TLS, no.
- **Tipos permitidos** — sólo JPEG/PNG. Si necesitas otro formato, pasa `allowedTypes` al instanciar `FotoCapture`.
- **Tamaño** — por defecto 2 MB. Ajustable vía `new FotoCapture({ maxBytes: 5 * 1024 * 1024 })`.
- **Múltiples filas** — soportado: cada invocación del partial genera IDs únicos (`foto_input_<prefix>_<uniqid>`), y el JS los vincula por `data-foto-input`.

## Extender

Si necesitas cambiar el comportamiento por defecto (calidad, tamaño, tipos), edita el `constructor` de `FotoCapture` en `assets/js/foto-capture.js` o instancia tu propia versión pasando opciones. Ejemplo:

```js
window.FotoCaptureInstance = new FotoCapture({
    maxBytes: 5 * 1024 * 1024,
    quality:  0.7,
    allowedTypes: ['image/jpeg']
});
```

(Esto debe ir antes del click en cualquier `.foto-capture-trigger`, idealmente en el `<script>` del `<head>` o al final del body.)

## Dónde se usa hoy

- `application/views/detenidos/_detenido_fields.php` — fotografía del detenido en `/detenciones/crear` y `/detenciones/editar/{id}`.
- `application/views/bienes_asegurados_crear_view.php` — fotografía del objeto asegurado en `/bienesasegurados/crear` y `/bienesasegurados/editar/{id}`.

(Si encuentras otro lugar con el mismo patrón "label azul + file transparente", reemplázalo por `foto_capture_field` siguiendo los pasos de arriba.)