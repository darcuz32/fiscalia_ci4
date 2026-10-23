/**
 * File Preview Component
 * Reusable file preview and management component
 * Author: Novarum.app
 * Version: 1.0.0
 */

class FilePreviewComponent {
    constructor(options = {}) {
        this.baseUrl = options.baseUrl || '';
        this.uploadPath = options.uploadPath || 'uploads/';
        this.colors = options.colors || {
            primary: 'primary',
            success: 'success', 
            warning: 'warning',
            info: 'info'
        };
        this.icons = options.icons || {
            folder: 'ri-folder-line',
            document: 'ri-file-text-line',
            file: 'ri-file-copy-line',
            user: 'ri-user-line',
            camera: 'ri-camera-line',
            attachment: 'ri-attachment-line',
            eye: 'ri-eye-line',
            delete: 'ri-delete-bin-line',
            close: 'ri-close-line',
            imageAdd: 'ri-image-add-line',
            filePdf: 'ri-file-pdf-line'
        };
        this.texts = options.texts || {
            takePhoto: 'Tomar Foto',
            attachFile: 'Adjuntar Archivo',
            formats: 'Formatos: JPG, PNG, GIF, PDF. Máximo 5MB',
            currentFile: 'Archivo actual:',
            deleteFile: 'Eliminar archivo actual',
            view: 'Ver',
            delete: 'Eliminar',
            preview: 'Vista previa del archivo',
            required: '<span class="text-danger">*</span>'
        };
    }

    /**
     * Generate file upload card component
     */
    generateFileCard(config) {
        const {
            fieldName,
            title,
            color = 'primary',
            icon = this.icons.folder,
            isRequired = false,
            isEdit = false,
            existingFile = null,
            mandamientoId = null,
            tabIndex = 1
        } = config;

        const requiredMark = isRequired && !isEdit ? this.texts.required : '';
        const colorClass = `bg-${color}`;
        const textColorClass = color === 'warning' ? 'text-white' : color === 'primary' || color === 'success' || color === 'info' ? 'text-white' : '';

        return `
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header ${colorClass}">
                        <h6 class="mb-0 ${textColorClass}">
                            <i class="${icon} me-2"></i>
                            ${title} ${requiredMark}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Action buttons -->
                                <div class="btn-group w-100 mb-3" role="group">
                                    <button type="button" class="btn btn-outline-${color}" onclick="openCamera('${fieldName}')" tabindex="${tabIndex}">
                                        <i class="${this.icons.camera} me-1"></i>${this.texts.takePhoto}
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('file_${fieldName}').click()">
                                        <i class="${this.icons.attachment} me-1"></i>${this.texts.attachFile}
                                    </button>
                                </div>
                                
                                <!-- Hidden file input -->
                                <input type="file" class="d-none" id="file_${fieldName}" name="${fieldName}" 
                                       accept="image/*,application/pdf" onchange="handleFileSelect(this, '${fieldName}')">
                                
                                <!-- Hidden camera input -->
                                <input type="hidden" id="camera_${fieldName}" name="camera_${fieldName}">
                                
                                <small class="form-text text-muted">${this.texts.formats}</small>
                                
                                ${this.generateExistingFileInfo(fieldName, existingFile, isEdit)}
                            </div>
                            <div class="col-md-6">
                                <!-- Preview -->
                                <div class="preview-container" id="preview_${fieldName}">
                                    ${this.generatePreviewContent(fieldName, existingFile, isEdit, mandamientoId)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
    }

    /**
     * Generate existing file info section
     */
    generateExistingFileInfo(fieldName, existingFile, isEdit) {
        if (!isEdit || !existingFile) {
            return '';
        }

        return `
        <div class="mt-2 p-2 bg-light rounded">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-info">
                    <i class="ri-file-line me-1"></i>
                    ${this.texts.currentFile} ${existingFile}
                </small>
                <button type="button" class="btn btn-sm btn-outline-danger"
                        onclick="deleteExistingFile('${fieldName}', '${existingFile}')"
                        title="${this.texts.deleteFile}">
                    <i class="${this.icons.delete}"></i>
                </button>
            </div>
        </div>
        `;
    }

    /**
     * Generate preview content
     */
    generatePreviewContent(fieldName, existingFile, isEdit, mandamientoId) {
        if (!isEdit || !existingFile) {
            return this.generateDefaultPreview();
        }

        const filePath = `${this.baseUrl}${this.uploadPath}mandamientos_judiciales/${mandamientoId}/${existingFile}`;
        const extension = existingFile.split('.').pop().toLowerCase();
        const isPdf = extension === 'pdf';

        if (isPdf) {
            return `
            <div class="current-file-preview">
                <div class="preview-pdf position-relative">
                    <div class="file-actions">
                        <button type="button" class="btn btn-sm btn-primary me-1" 
                                onclick="window.open('${filePath}', '_blank')" title="${this.texts.view}">
                            <i class="${this.icons.eye}"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" 
                                onclick="deleteExistingFile('${fieldName}', '${existingFile}')">
                            <i class="${this.icons.close}"></i>
                        </button>
                    </div>
                    <i class="${this.icons.filePdf} text-danger" style="font-size: 3rem;"></i>
                    <h6 class="mt-2">${existingFile}</h6>
                    <div class="mt-2">
                        <small class="badge bg-danger">PDF</small>
                    </div>
                </div>
            </div>
            `;
        } else {
            return `
            <div class="current-file-preview">
                <div class="position-relative">
                    <div class="file-actions">
                        <button type="button" class="btn btn-sm btn-primary me-1" 
                                onclick="window.open('${filePath}', '_blank')" title="${this.texts.view}">
                            <i class="${this.icons.eye}"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" 
                                onclick="deleteExistingFile('${fieldName}', '${existingFile}')">
                            <i class="${this.icons.close}"></i>
                        </button>
                    </div>
                    <img src="${filePath}" class="preview-image" alt="${existingFile}">
                    <div class="mt-2">
                        <small class="text-muted">${existingFile}</small>
                    </div>
                </div>
            </div>
            `;
        }
    }

    /**
     * Generate default preview placeholder
     */
    generateDefaultPreview() {
        return `
        <div class="text-center text-muted p-3 border border-dashed rounded">
            <i class="${this.icons.imageAdd}" style="font-size: 2rem;"></i>
            <p class="mb-0 mt-2">${this.texts.preview}</p>
        </div>
        `;
    }

    /**
     * Generate multiple file cards
     */
    generateMultipleFileCards(configs) {
        return configs.map(config => this.generateFileCard(config)).join('');
    }

    /**
     * Generate file cards section with title
     */
    generateFileCardsSection(title, configs, subtitle = null) {
        const subtitleHtml = subtitle ? `<p class="text-muted small">${subtitle}</p>` : '';
        
        return `
        <div class="row">
            <div class="col-md-12">
                <h6 class="mb-3 text-muted">${title}</h6>
                ${subtitleHtml}
            </div>
        </div>
        
        ${this.generateMultipleFileCards(configs)}
        `;
    }
}
