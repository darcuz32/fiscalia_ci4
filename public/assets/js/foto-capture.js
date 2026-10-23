/**
 * FotoCapture — reusable photo upload + camera capture widget.
 *
 * Pairs with:
 *   - application/views/components/foto_capture_field.php (dual-button widget)
 *   - application/views/components/foto_capture_modal.php  (Bootstrap modal markup)
 *
 * Discovers triggers by selector `.foto-capture-trigger[data-foto-input]`. The data
 * attribute carries the id of the <input type="file"> that the captured photo should
 * land in. There is exactly one modal per page; the class owns its full lifecycle
 * (stream start/stop, capture, retake, switch camera, use photo).
 *
 * Backend contract: the captured JPEG is converted to a File and assigned to the
 * targeted input via DataTransfer.files, then a bubbling `change` event is dispatched
 * so existing preview handlers (e.g. the delegated `change` on .foto-input) keep
 * working untouched. Form submission keeps using $_FILES[<prefix>][fotografia] —
 * no controller changes required.
 *
 * See assets/docs/foto-capture-readme.md for usage and extension.
 *
 * ponytail: targeted inputs may not exist yet on DOMContentLoaded (cloned rows),
 * so triggers are bound once on document and delegated by selector.
 */
(function ($) {
    'use strict';

    const MODAL_ID         = 'fotoCaptureModal';
    const VIDEO_ID         = 'fotoCaptureVideo';
    const CANVAS_ID        = 'fotoCaptureCanvas';
    const PREVIEW_ID       = 'fotoCapturePreview';
    const STATUS_ID        = 'fotoCaptureStatus';
    const BTN_CAPTURE      = '#fotoCaptureCaptureBtn';
    const BTN_SWITCH       = '#fotoCaptureSwitchBtn';
    const BTN_USE          = '#fotoCaptureUseBtn';
    const BTN_RETAKE       = '#fotoCaptureRetakeBtn';
    const PREVIEW_WRAP_CLS = 'foto-capture-preview-wrap';
    const VIDEO_WRAP_CLS   = 'foto-capture-video-wrap';

    class FotoCapture {
        constructor(opts) {
            this.maxBytes     = (opts && opts.maxBytes)     || 2 * 1024 * 1024;
            this.quality      = (opts && opts.quality)      || 0.85;
            this.allowedTypes = (opts && opts.allowedTypes) || ['image/jpeg', 'image/png'];
            this.facingMode   = 'environment'; // 'user' = front, 'environment' = back
            this.stream       = null;
            this.targetInput  = null;
            this.bsModal      = null;
            this._switching   = false;
            this.bindModal();
        }

        // ponytail: bootstrap.Modal only resolves when the modal DOM is present.
        // The view is responsible for including foto_capture_modal.php.
        bindModal() {
            const el = document.getElementById(MODAL_ID);
            if (!el || typeof bootstrap === 'undefined') return;
            // A modal inside another modal inherits its parent's stacking context,
            // so no z-index can reliably bring it forward. Keep this singleton at
            // document.body before Bootstrap creates its backdrop.
            if (el.parentElement !== document.body) document.body.appendChild(el);
            this.bsModal = bootstrap.Modal.getOrCreateInstance(el);
            el.addEventListener('hidden.bs.modal', () => this.stopStream());
            document.getElementById(BTN_CAPTURE.replace('#', ''))
                ?.addEventListener('click', () => this.capture());
            document.getElementById(BTN_SWITCH.replace('#', ''))
                ?.addEventListener('click', () => this.switchCamera());
            document.getElementById(BTN_USE.replace('#', ''))
                ?.addEventListener('click', () => this.usePhoto());
            document.getElementById(BTN_RETAKE.replace('#', ''))
                ?.addEventListener('click', () => this.retake());
        }

        // ponytail: entry point — invoked by delegated click on any .foto-capture-trigger.
        open(triggerEl) {
            const inputId = $(triggerEl).attr('data-foto-input');
            const input   = inputId ? document.getElementById(inputId) : null;
            if (!input) {
                this.toast('No se encontró el campo de imagen destino.', 'error');
                return;
            }
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                this.toast('Su navegador no soporta cámara. Use Subir para elegir un archivo.', 'Navegador no compatible', 'error');
                return;
            }
            this.targetInput = input;
            this.showStatus('');
            this.resetModalView();
            if (!this.bsModal) {
                this.toast('Falta el modal de cámara en esta página.', 'Error', 'error');
                return;
            }
            // Bootstrap gives sibling modals the same z-index. When this widget is
            // launched from a form modal, lift both the camera and its newest backdrop.
            const modalEl = document.getElementById(MODAL_ID);
            modalEl.addEventListener('show.bs.modal', () => {
                requestAnimationFrame(() => {
                    modalEl.style.zIndex = '1080';
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    const backdrop = backdrops[backdrops.length - 1];
                    if (backdrop) backdrop.style.zIndex = '1070';
                });
            }, { once: true });
            modalEl.addEventListener('hidden.bs.modal', () => {
                modalEl.style.zIndex = '';
                const backdrops = document.querySelectorAll('.modal-backdrop');
                const backdrop = backdrops[backdrops.length - 1];
                if (backdrop) backdrop.style.zIndex = '';
            }, { once: true });
            this.bsModal.show();
            // ponytail: getUserMedia sólo después de que el modal esté visible —
            // algunos navegadores rechazan el permiso si el gesto no se asocia a UI.
            document.getElementById(MODAL_ID).addEventListener('shown.bs.modal', () => {
                this.startStream();
            }, { once: true });
        }

        resetModalView() {
            document.querySelector('.' + VIDEO_WRAP_CLS)?.classList.remove('d-none');
            document.querySelector('.' + PREVIEW_WRAP_CLS)?.classList.add('d-none');
            const cap = document.querySelector(BTN_CAPTURE);
            const sw  = document.querySelector(BTN_SWITCH);
            if (cap) cap.disabled = false;
            if (sw)  sw.disabled  = false;
        }

        async startStream() {
            try {
                await this.stopStream();
                const constraints = {
                    video: {
                        facingMode: this.facingMode,
                        width:  { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false
                };
                this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                const video = document.getElementById(VIDEO_ID);
                if (!video) return;
                video.srcObject = this.stream;
                await video.play().catch(() => {});
            } catch (err) {
                this.handleCameraError(err);
            }
        }

        async stopStream() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
        }

        handleCameraError(err) {
            let msg = 'No se pudo acceder a la cámara. ';
            if (err && err.name === 'NotAllowedError') {
                msg += 'Permita el acceso a la cámara en su navegador.';
            } else if (err && err.name === 'NotFoundError') {
                msg += 'No se encontró ninguna cámara en el dispositivo.';
            } else if (err && err.name === 'NotReadableError') {
                msg += 'La cámara está siendo utilizada por otra aplicación.';
            } else {
                msg += 'Verifique los permisos e intente nuevamente.';
            }
            this.toast(msg, 'Cámara', 'error');
            this.showStatus(msg);
            if (this.bsModal) this.bsModal.hide();
        }

        capture() {
            const video  = document.getElementById(VIDEO_ID);
            const canvas = document.getElementById(CANVAS_ID);
            if (!video || !canvas) return;
            if (!video.videoWidth || !video.videoHeight) {
                this.toast('Espere a que la cámara esté lista.', 'Cámara', 'warning');
                return;
            }
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const dataUrl = canvas.toDataURL('image/jpeg', this.quality);
            const preview = document.getElementById(PREVIEW_ID);
            if (preview) preview.src = dataUrl;
            document.querySelector('.' + VIDEO_WRAP_CLS)?.classList.add('d-none');
            document.querySelector('.' + PREVIEW_WRAP_CLS)?.classList.remove('d-none');
            const cap = document.querySelector(BTN_CAPTURE);
            const sw  = document.querySelector(BTN_SWITCH);
            if (cap) cap.disabled = true;
            if (sw)  sw.disabled  = true;
        }

        retake() {
            this.resetModalView();
            this.startStream();
        }

        async switchCamera() {
            if (this._switching) return;
            this._switching = true;
            const previous = this.facingMode;
            this.facingMode = this.facingMode === 'user' ? 'environment' : 'user';
            try {
                await this.startStream();
                this.toast('Cámara cambiada a ' + (this.facingMode === 'user' ? 'frontal' : 'trasera') + '.', 'Cámara', 'info');
            } catch (err) {
                // ponytail: si el dispositivo no tiene la otra cámara, dejamos la anterior.
                this.facingMode = previous;
                try { await this.startStream(); } catch (_) { /* ya estábamos en la única cámara */ }
                this.toast('No se encontró otra cámara disponible.', 'Cámara', 'warning');
            } finally {
                this._switching = false;
            }
        }

        usePhoto() {
            const canvas = document.getElementById(CANVAS_ID);
            if (!canvas || !this.targetInput) return;
            const dataUrl = canvas.toDataURL('image/jpeg', this.quality);
            // ponytail: dataURL → blob → File. DataTransfer.items.add() es la única vía
            // soportada para asignar files a un <input type=file> desde JS moderno.
            const blob = this.dataUrlToBlob(dataUrl);
            if (blob.size > this.maxBytes) {
                this.toast('La foto capturada (' + this.formatBytes(blob.size) + ') excede el máximo permitido (' + this.formatBytes(this.maxBytes) + ').', 'Imagen demasiado grande', 'error');
                return;
            }
            if (!this.allowedTypes.includes('image/jpeg')) {
                this.toast('El componente no permite JPEG.', 'Tipo no permitido', 'error');
                return;
            }
            const fileName = 'camera_capture_' + Date.now() + '.jpg';
            const file = new File([blob], fileName, { type: 'image/jpeg', lastModified: Date.now() });
            const dt = new DataTransfer();
            dt.items.add(file);
            try {
                this.targetInput.files = dt.files;
            } catch (e) {
                // ponytail: algunos navegadores viejos no aceptan asignación; fallback: no-op.
                this.toast('Su navegador no permite asignar la captura al campo. Use Subir.', 'Navegador', 'error');
                return;
            }
            // ponytail: disparamos change con bubbles:true para que el handler delegado
            // existente (.foto-input change → preview) se ejecute igual que con un file picker.
            this.targetInput.dispatchEvent(new Event('change', { bubbles: true }));
            if (this.bsModal) this.bsModal.hide();
            this.toast('Foto capturada correctamente.', 'Listo', 'success');
        }

        dataUrlToBlob(dataUrl) {
            const [meta, b64] = dataUrl.split(',');
            const mime = (meta.match(/:(.*?);/) || [, 'image/jpeg'])[1];
            const bin  = atob(b64);
            const len  = bin.length;
            const arr  = new Uint8Array(len);
            for (let i = 0; i < len; i++) arr[i] = bin.charCodeAt(i);
            return new Blob([arr], { type: mime });
        }

        showStatus(msg) {
            const el = document.getElementById(STATUS_ID);
            if (el) el.textContent = msg || '';
        }

        toast(message, title, type) {
            if (typeof toastr !== 'undefined') {
                toastr[type || 'info'](message, title || '');
            } else if (window.console) {
                console[(type === 'error') ? 'error' : 'log'](title + ': ' + message);
            }
        }

        formatBytes(b) {
            if (b < 1024) return b + ' B';
            if (b < 1024 * 1024) return (b / 1024).toFixed(1) + ' KB';
            return (b / (1024 * 1024)).toFixed(2) + ' MB';
        }
    }

    // ponytail: global singleton por simplicidad — sólo un modal en la página, no
    // se justifica una instancia por botón. Si en el futuro se necesita más de un
    // modal simultáneo, cambiar a registro por-id.
    window.FotoCaptureInstance = null;
    $(function () {
        if (!window.FotoCaptureInstance) {
            window.FotoCaptureInstance = new FotoCapture();
        }
        // ponytail: delegación a nivel document para soportar filas clonadas (Detenidos).
        $(document).on('click', '.foto-capture-trigger', function (ev) {
            ev.preventDefault();
            window.FotoCaptureInstance.open(this);
        });
    });
})(jQuery);
