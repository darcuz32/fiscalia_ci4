/**
 * Camera and File Utils
 * Reusable camera and file attachment functionality
 * Author: Novarum.app
 * Version: 1.0.0
 */

class CameraFileUtils {
    constructor(options = {}) {
        this.currentStream = null;
        this.currentFieldName = '';
        this.facingMode = 'environment'; // 'user' for front, 'environment' for back
        this.maxFileSize = options.maxFileSize || 5 * 1024 * 1024; // 5MB default
        this.allowedTypes = options.allowedTypes || ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'];
        this.quality = options.quality || 0.8;
        this.toastrEnabled = options.toastr !== false; // Enable toastr by default
        
        this.init();
    }

    /**
     * Initialize event listeners
     */
    init() {
        // Bind modal events
        $('#cameraModal').on('hidden.bs.modal', () => {
            this.stopCamera();
        });

        // Camera control buttons
        $('#captureBtn').on('click', () => this.capturePhoto());
        $('#switchCameraBtn').on('click', () => this.switchCamera());
        $('#usePhotoBtn').on('click', () => this.usePhoto());
        $('#retakeBtn').on('click', () => this.retakePhoto());
    }

    /**
     * Show toast message
     */
    showToast(message, title = '', type = 'info') {
        if (this.toastrEnabled && typeof toastr !== 'undefined') {
            toastr[type](message, title);
        } else {
            console.log(`${type.toUpperCase()}: ${title} - ${message}`);
        }
    }

    /**
     * Open camera for taking photo
     */
    openCamera(fieldName) {
        this.currentFieldName = fieldName;
        
        // Check browser support
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            this.showToast('Su navegador no soporta acceso a cámara. Use la opción de adjuntar archivo.', 'Navegador no compatible', 'error');
            return;
        }
        
        // Reset modal state
        $('#capturedImageContainer').addClass('d-none');
        $('#cameraVideo').removeClass('d-none');
        $('#captureBtn, #switchCameraBtn').prop('disabled', false);
        
        // Open modal
        const modal = new bootstrap.Modal(document.getElementById('cameraModal'));
        modal.show();
        
        // Initialize camera when modal is fully shown
        $('#cameraModal').on('shown.bs.modal', () => {
            this.startCamera();
        });
    }

    /**
     * Initialize camera
     */
    async startCamera() {
        try {
            // Stop current stream if exists
            if (this.currentStream) {
                this.currentStream.getTracks().forEach(track => track.stop());
            }
            
            const constraints = {
                video: {
                    facingMode: this.facingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };
            
            this.currentStream = await navigator.mediaDevices.getUserMedia(constraints);
            const video = document.getElementById('cameraVideo');
            video.srcObject = this.currentStream;
            
            // Wait for video to be ready
            video.onloadedmetadata = function() {
                video.play();
            };
            
        } catch (error) {
            console.error('Error accessing camera:', error);
            let errorMessage = 'No se pudo acceder a la cámara. ';
            
            if (error.name === 'NotAllowedError') {
                errorMessage += 'Permita el acceso a la cámara en su navegador.';
            } else if (error.name === 'NotFoundError') {
                errorMessage += 'No se encontró ninguna cámara en el dispositivo.';
            } else if (error.name === 'NotReadableError') {
                errorMessage += 'La cámara está siendo utilizada por otra aplicación.';
            } else {
                errorMessage += 'Verifique los permisos e intente nuevamente.';
            }
            
            this.showToast(errorMessage, 'Error de Cámara', 'error');
            const modal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
            if (modal) {
                modal.hide();
            }
        }
    }

    /**
     * Capture photo from camera
     */
    capturePhoto() {
        const video = document.getElementById('cameraVideo');
        const canvas = document.getElementById('cameraCanvas');
        const context = canvas.getContext('2d');
        const capturedImage = document.getElementById('capturedImage');
        
        // Check if video is ready
        if (video.readyState !== video.HAVE_ENOUGH_DATA) {
            this.showToast('Espere a que la cámara esté lista antes de capturar.', 'Cámara no lista', 'warning');
            return;
        }
        
        // Configure canvas with video dimensions
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Draw current video frame to canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert to image and show
        const imageDataUrl = canvas.toDataURL('image/jpeg', this.quality);
        capturedImage.src = imageDataUrl;
        
        // Show preview and hide video
        $('#cameraVideo').addClass('d-none');
        $('#capturedImageContainer').removeClass('d-none');
        $('#captureBtn, #switchCameraBtn').prop('disabled', true);
    }

    /**
     * Use captured photo
     */
    usePhoto() {
        const canvas = document.getElementById('cameraCanvas');
        const imageDataUrl = canvas.toDataURL('image/jpeg', this.quality);
        
        // Save image data to hidden input
        const cameraInput = document.getElementById(`camera_${this.currentFieldName}`);
        if (cameraInput) {
            cameraInput.value = imageDataUrl;
        }
        
        // Create temporary file for preview
        fetch(imageDataUrl)
            .then(res => res.blob())
            .then(blob => {
                const file = new File([blob], `camera_${this.currentFieldName}_${Date.now()}.jpg`, {
                    type: 'image/jpeg'
                });
                
                // Update preview only (don't assign to file input)
                this.updatePreview(this.currentFieldName, file, imageDataUrl);
                
                // Close modal and stop camera
                this.stopCamera();
                const modal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
                modal.hide();
                
                this.showToast('Fotografía capturada exitosamente', 'Éxito', 'success');
            });
    }

    /**
     * Retake photo
     */
    retakePhoto() {
        $('#capturedImageContainer').addClass('d-none');
        $('#cameraVideo').removeClass('d-none');
        $('#captureBtn, #switchCameraBtn').prop('disabled', false);
    }

    /**
     * Switch camera (front/back)
     */
    async switchCamera() {
        try {
            // Change camera mode
            this.facingMode = this.facingMode === 'user' ? 'environment' : 'user';
            
            // Try to initialize with new camera
            await this.startCamera();
            
            this.showToast(`Cambiado a cámara ${this.facingMode === 'user' ? 'frontal' : 'trasera'}`, 'Cámara', 'info');
            
        } catch (error) {
            // If it fails, go back to previous mode
            this.facingMode = this.facingMode === 'user' ? 'environment' : 'user';
            
            console.warn('Could not switch camera:', error);
            this.showToast('No se encontró otra cámara disponible.', 'Cambio de cámara', 'warning');
            
            // Try to restore previous camera
            try {
                await this.startCamera();
            } catch (fallbackError) {
                console.error('Error restoring previous camera:', fallbackError);
                this.showToast('Error al acceder a la cámara.', 'Error', 'error');
            }
        }
    }

    /**
     * Stop camera
     */
    stopCamera() {
        if (this.currentStream) {
            this.currentStream.getTracks().forEach(track => track.stop());
            this.currentStream = null;
        }
    }

    /**
     * Handle file selection
     */
    handleFileSelect(input, fieldName) {
        const file = input.files[0];
        if (!file) return;
        
        // Validate size
        if (file.size > this.maxFileSize) {
            this.showToast(`El archivo excede el tamaño máximo de ${this.formatFileSize(this.maxFileSize)}`, 'Archivo muy grande', 'error');
            input.value = '';
            return;
        }
        
        // Validate type
        if (!this.allowedTypes.includes(file.type)) {
            this.showToast('Tipo de archivo no permitido. Use JPG, PNG, GIF o PDF', 'Tipo no válido', 'error');
            input.value = '';
            return;
        }
        
        // Create preview
        if (file.type === 'application/pdf') {
            this.updatePreview(fieldName, file, null);
        } else {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.updatePreview(fieldName, file, e.target.result);
            };
            reader.readAsDataURL(file);
        }
        
        this.showToast(`Archivo "${file.name}" seleccionado exitosamente`, 'Archivo Cargado', 'success');
    }

    /**
     * Update file preview
     */
    updatePreview(fieldName, file, dataUrl) {
        const previewContainer = document.getElementById(`preview_${fieldName}`);
        if (!previewContainer) return;
        
        let previewHTML = '';
        
        if (file.type === 'application/pdf') {
            previewHTML = `
                <div class="preview-pdf position-relative">
                    <div class="file-actions">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeFile('${fieldName}')" title="Eliminar archivo">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <i class="ri-file-pdf-line text-danger" style="font-size: 3rem;"></i>
                    <h6 class="mt-2 mb-1">${file.name}</h6>
                    <small class="text-muted">${this.formatFileSize(file.size)}</small>
                    <div class="mt-2">
                        <small class="badge bg-danger">PDF</small>
                    </div>
                </div>
            `;
        } else {
            previewHTML = `
                <div class="position-relative">
                    <div class="file-actions">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeFile('${fieldName}')" title="Eliminar archivo">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <img src="${dataUrl}" class="preview-image" alt="Preview" style="max-width: 100%; height: auto; border-radius: 4px;">
                    <div class="mt-2">
                        <small class="text-muted">${file.name} (${this.formatFileSize(file.size)})</small>
                    </div>
                </div>
            `;
        }
        
        previewContainer.innerHTML = previewHTML;
    }

    /**
     * Remove selected file
     */
    removeFile(fieldName) {
        const fileInput = document.getElementById(`file_${fieldName}`);
        const cameraInput = document.getElementById(`camera_${fieldName}`);
        const previewContainer = document.getElementById(`preview_${fieldName}`);
        
        // Clear inputs
        if (fileInput) fileInput.value = '';
        if (cameraInput) cameraInput.value = '';
        
        // Restore default preview
        if (previewContainer) {
            previewContainer.innerHTML = `
                <div class="text-center text-muted p-3 border border-dashed rounded">
                    <i class="ri-image-add-line" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2">Vista previa del archivo</p>
                </div>
            `;
        }
        
        this.showToast('Archivo eliminado', 'Información', 'info');
    }

    /**
     * Format file size
     */
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /**
     * Process camera images for form submission
     */
    processCameraImagesForForm(formData, cameraFields) {
        cameraFields.forEach(fieldName => {
            const cameraInput = document.getElementById(`camera_${fieldName}`);
            const fileInput = document.getElementById(`file_${fieldName}`);
            
            // If there's camera data but no file, convert base64 to blob
            if (cameraInput && cameraInput.value && (!fileInput.files || fileInput.files.length === 0)) {
                const dataUrl = cameraInput.value;
                
                // Convert dataURL to blob
                const arr = dataUrl.split(',');
                const mime = arr[0].match(/:(.*?);/)[1];
                const bstr = atob(arr[1]);
                let n = bstr.length;
                const u8arr = new Uint8Array(n);
                
                while(n--){
                    u8arr[n] = bstr.charCodeAt(n);
                }
                
                const blob = new Blob([u8arr], {type: mime});
                const fileName = `camera_${fieldName}_${Date.now()}.jpg`;
                
                // Add to FormData
                formData.append(fieldName, blob, fileName);
                
                console.log(`Adding camera image for ${fieldName}:`, fileName, 'Size:', blob.size, 'bytes');
            } else if (fileInput.files && fileInput.files.length > 0) {
                console.log(`Regular file for ${fieldName}:`, fileInput.files[0].name);
            }
        });
    }

    /**
     * Validate files for form submission
     */
    validateFiles(requiredFiles, isEditMode = false) {
        if (isEditMode) {
            // In edit mode, validate files only if new ones are selected
            const fileFields = requiredFiles;
            let filesValid = true;
            
            fileFields.forEach(fieldName => {
                const fileInput = document.getElementById(`file_${fieldName}`);
                if (fileInput.files && fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    
                    if (file.size > this.maxFileSize) {
                        this.showToast(`El archivo ${file.name} excede el tamaño máximo de ${this.formatFileSize(this.maxFileSize)}`, 'Archivo muy grande', 'error');
                        filesValid = false;
                    }
                    
                    if (!this.allowedTypes.includes(file.type)) {
                        this.showToast(`El archivo ${file.name} no es un tipo válido (JPG, PNG, GIF, PDF)`, 'Tipo de archivo inválido', 'error');
                        filesValid = false;
                    }
                }
            });
            
            return filesValid;
        } else {
            // In creation mode, validate all required files
            let filesValid = true;
            
            requiredFiles.forEach(fieldName => {
                const fileInput = document.getElementById(`file_${fieldName}`);
                const cameraInput = document.getElementById(`camera_${fieldName}`);
                
                // Check if there's file or camera data
                const hasFile = fileInput.files && fileInput.files.length > 0;
                const hasCameraData = cameraInput && cameraInput.value && cameraInput.value.length > 0;
                
                if (!hasFile && !hasCameraData) {
                    this.showToast(`Por favor adjunte un archivo o tome una foto para: ${fieldName.replace('fotografia_', '').replace('_', ' ')}`, 'Archivo requerido', 'error');
                    filesValid = false;
                } else if (hasFile) {
                    // Validate file if exists
                    const file = fileInput.files[0];
                    
                    if (file.size > this.maxFileSize) {
                        this.showToast(`El archivo ${file.name} excede el tamaño máximo de ${this.formatFileSize(this.maxFileSize)}`, 'Archivo muy grande', 'error');
                        filesValid = false;
                    }
                    
                    if (!this.allowedTypes.includes(file.type)) {
                        this.showToast(`El archivo ${file.name} no es un tipo válido (JPG, PNG, GIF, PDF)`, 'Tipo de archivo inválido', 'error');
                        filesValid = false;
                    }
                }
                // Camera images are always valid JPEG, no additional validation needed
            });
            
            return filesValid;
        }
    }

    /**
     * Delete existing file (for edit mode)
     */
    deleteExistingFile(fieldName, filename, mandamientoId, deleteUrl) {
        // Show confirmation
        if (!confirm('¿Está seguro de que desea eliminar este archivo? Esta acción no se puede deshacer.')) {
            return;
        }
        
        // Make AJAX request to delete file
        $.ajax({
            type: "POST",
            url: deleteUrl,
            data: {
                mandamiento_id: mandamientoId,
                field_name: fieldName,
                filename: filename
            },
            dataType: 'json',
            beforeSend: () => {
                // Show loading on button
                $(`button[onclick*="deleteExistingFile('${fieldName}'"]`).prop('disabled', true)
                    .html('<i class="ri-loader-4-line"></i>');
            },
            success: (response) => {
                if (response.success) {
                    this.showToast('Archivo eliminado exitosamente', 'Éxito', 'success');
                    
                    // Update UI
                    // Hide current file info
                    $(`#preview_${fieldName}`).closest('.card-body')
                        .find('.existing-file-info, .mt-2.p-2.bg-light.rounded').hide();
                    
                    // Restore default preview
                    $(`#preview_${fieldName}`).html(`
                        <div class="text-center text-muted p-3 border border-dashed rounded">
                            <i class="ri-image-add-line" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Vista previa del archivo</p>
                        </div>
                    `);
                    
                } else {
                    this.showToast(response.message || 'Error al eliminar el archivo', 'Error', 'error');
                }
            },
            error: (xhr, status, error) => {
                console.error("Error deleting file:", error);
                this.showToast('Error al eliminar el archivo. Por favor, intente nuevamente.', 'Error', 'error');
            },
            complete: () => {
                // Restore button
                $(`button[onclick*="deleteExistingFile('${fieldName}'"]`).prop('disabled', false)
                    .html('<i class="ri-delete-bin-line"></i>');
            }
        });
    }
}

// Global functions for backward compatibility
window.cameraUtilsInstance = null;

// Initialize global instance when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (!window.cameraUtilsInstance) {
        window.cameraUtilsInstance = new CameraFileUtils();
    }
});

function openCamera(fieldName) {
    if (!window.cameraUtilsInstance) {
        window.cameraUtilsInstance = new CameraFileUtils();
    }
    window.cameraUtilsInstance.openCamera(fieldName);
}

function handleFileSelect(input, fieldName) {
    if (!window.cameraUtilsInstance) {
        window.cameraUtilsInstance = new CameraFileUtils();
    }
    window.cameraUtilsInstance.handleFileSelect(input, fieldName);
}

function removeFile(fieldName) {
    if (!window.cameraUtilsInstance) {
        window.cameraUtilsInstance = new CameraFileUtils();
    }
    window.cameraUtilsInstance.removeFile(fieldName);
}

function deleteExistingFile(fieldName, filename, mandamientoId, deleteUrl) {
    if (!window.cameraUtilsInstance) {
        window.cameraUtilsInstance = new CameraFileUtils();
    }
    window.cameraUtilsInstance.deleteExistingFile(fieldName, filename, mandamientoId, deleteUrl);
}
