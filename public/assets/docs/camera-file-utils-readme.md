# Camera and File Utils Components

## Overview
This package provides reusable camera and file attachment functionality that can be implemented across different parts of the application. It includes JavaScript utilities, CSS styles, PHP helpers, and HTML components.

## Components

### 1. JavaScript Files

#### camera-utils.js
- **Purpose**: Main utility class for camera and file operations
- **Features**:
  - Camera access and photo capture
  - File validation and preview
  - Base64 image processing
  - Form data preparation
  - Error handling and user feedback

#### file-preview-component.js
- **Purpose**: HTML component generator for file upload cards
- **Features**:
  - Generates standardized file upload UI
  - Configurable colors, icons, and text
  - Support for existing files in edit mode
  - Responsive design support

### 2. CSS Styles

#### camera-file-utils.css
- **Purpose**: Styling for camera and file components
- **Features**:
  - Responsive design
  - Dark mode support
  - Animation effects
  - Accessibility improvements
  - Loading states

### 3. PHP Components

#### File_upload_helper.php
- **Purpose**: Backend file handling and validation
- **Features**:
  - Camera image processing (base64 to file)
  - Regular file upload handling
  - File validation and security checks
  - Thumbnail generation
  - Batch upload support
  - File cleanup utilities

#### camera_modal.php
- **Purpose**: Reusable camera modal component
- **Features**:
  - Bootstrap modal structure
  - Camera controls (capture, switch, retake)
  - Responsive layout

## Implementation Guide

### Basic Setup

1. **Include Required Files in Your Layout**:
```php
<!-- CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/camera-file-utils.css'); ?>">

<!-- JavaScript -->
<script src="<?= base_url('assets/js/camera-utils.js'); ?>"></script>
<script src="<?= base_url('assets/js/file-preview-component.js'); ?>"></script>
```

2. **Include Camera Modal**:
```php
<?php $this->load->view('components/camera_modal'); ?>
```

3. **Load PHP Helper**:
```php
$this->load->helper('file_upload');
```

### Using the File Preview Component

```javascript
// Initialize the component
const filePreview = new FilePreviewComponent({
    baseUrl: '<?= base_url(); ?>',
    uploadPath: 'uploads/',
    colors: {
        primary: 'primary',
        success: 'success',
        warning: 'warning',
        info: 'info'
    }
});

// Generate file upload cards
const fileCardsHtml = filePreview.generateFileCardsSection(
    'ADJUNTAR ARCHIVOS',
    [
        {
            fieldName: 'fotografia_carpeta',
            title: 'Fotografía de Carpeta',
            color: 'primary',
            icon: 'ri-folder-line',
            isRequired: true,
            isEdit: false,
            tabIndex: 5
        },
        {
            fieldName: 'fotografia_orden',
            title: 'Fotografía de Orden',
            color: 'success',
            icon: 'ri-file-text-line',
            isRequired: true,
            isEdit: false,
            tabIndex: 6
        }
    ],
    'Puede tomar fotografías con la cámara o adjuntar archivos (imágenes o PDFs). Máximo 5MB por archivo.'
);

// Insert into DOM
document.getElementById('file-upload-container').innerHTML = fileCardsHtml;
```

### Using the Camera Utils

```javascript
// Initialize camera utilities
const cameraUtils = new CameraFileUtils({
    maxFileSize: 5 * 1024 * 1024, // 5MB
    allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'],
    quality: 0.8,
    toastr: true // Enable toastr notifications
});

// Form submission with file validation
$('#myForm').on('submit', function(e) {
    e.preventDefault();
    
    const requiredFiles = ['fotografia_carpeta', 'fotografia_orden'];
    
    // Validate files
    if (!cameraUtils.validateFiles(requiredFiles, false)) {
        return;
    }
    
    // Create FormData
    const formData = new FormData(this);
    
    // Process camera images
    cameraUtils.processCameraImagesForForm(formData, requiredFiles);
    
    // Submit form
    $.ajax({
        type: 'POST',
        url: '<?= base_url("your/endpoint"); ?>',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            // Handle success
        }
    });
});
```

### Backend File Processing

```php
// In your controller
public function upload_files() {
    // Load helper
    $this->load->helper('file_upload');
    
    // Configure upload settings
    $config = array(
        'upload_path' => './uploads/mandamientos_judiciales/' . $mandamiento_id . '/',
        'allowed_types' => 'jpg|jpeg|png|gif|pdf',
        'max_size' => 5120, // 5MB
        'encrypt_name' => TRUE
    );
    
    $file_helper = new File_upload_helper($config);
    
    // Handle multiple files
    $fields = ['fotografia_carpeta', 'fotografia_orden', 'fotografia_oficio'];
    $upload_results = array();
    
    foreach ($fields as $field) {
        // Validate file
        $validation = $file_helper->validate_file($field);
        if (!$validation['success']) {
            // Handle validation errors
            continue;
        }
        
        // Upload file
        $result = $file_helper->handle_upload($field, $config['upload_path']);
        if ($result['success']) {
            $upload_results[$field] = $result['file_name'];
        }
    }
    
    // Save to database
    $data = array(
        'mandamiento_id' => $mandamiento_id,
        'fotografia_carpeta' => $upload_results['fotografia_carpeta'] ?? null,
        'fotografia_orden' => $upload_results['fotografia_orden'] ?? null,
        'fotografia_oficio' => $upload_results['fotografia_oficio'] ?? null
    );
    
    $this->db->insert('mandamiento_datos', $data);
}

// Delete file endpoint
public function delete_file() {
    $field_name = $this->input->post('field_name');
    $filename = $this->input->post('filename');
    $mandamiento_id = $this->input->post('mandamiento_id');
    
    $file_path = './uploads/mandamientos_judiciales/' . $mandamiento_id . '/' . $filename;
    
    $this->load->helper('file_upload');
    $file_helper = new File_upload_helper();
    
    $result = $file_helper->delete_file($file_path);
    
    if ($result['success']) {
        // Update database
        $this->db->where('mandamiento_id', $mandamiento_id);
        $this->db->update('mandamiento_datos', array($field_name => null));
    }
    
    echo json_encode($result);
}
```

## Configuration Options

### CameraFileUtils Options
```javascript
{
    maxFileSize: 5 * 1024 * 1024, // Maximum file size in bytes
    allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'],
    quality: 0.8, // JPEG quality for camera images (0.0 - 1.0)
    toastr: true // Enable/disable toastr notifications
}
```

### FilePreviewComponent Options
```javascript
{
    baseUrl: '', // Base URL for file paths
    uploadPath: 'uploads/', // Upload directory path
    colors: { // Bootstrap color classes
        primary: 'primary',
        success: 'success',
        warning: 'warning',
        info: 'info'
    },
    icons: { // Icon classes (Remix Icons)
        folder: 'ri-folder-line',
        document: 'ri-file-text-line',
        // ... other icons
    },
    texts: { // Customizable text labels
        takePhoto: 'Tomar Foto',
        attachFile: 'Adjuntar Archivo',
        // ... other texts
    }
}
```

### File_upload_helper Options
```php
array(
    'upload_path' => './uploads/',
    'allowed_types' => 'jpg|jpeg|png|gif|pdf',
    'max_size' => 5120, // KB
    'encrypt_name' => TRUE,
    'remove_spaces' => TRUE,
    'create_thumb' => FALSE,
    'thumb_width' => 300,
    'thumb_height' => 300
)
```

## Features

### Camera Functionality
- Access front and rear cameras
- Switch between cameras
- Capture photos with quality control
- Real-time preview
- Error handling for permission issues

### File Management
- Drag and drop support (when implemented)
- File type validation
- Size validation
- Preview generation for images and PDFs
- Thumbnail creation
- Batch upload support

### Security
- MIME type validation
- File extension validation
- Size limits
- Secure file naming
- Path traversal protection

### User Experience
- Responsive design
- Loading states
- Progress indicators
- Error messaging
- Accessibility support
- Dark mode compatibility

## Browser Compatibility
- Modern browsers with MediaDevices API support
- Fallback to file upload only for unsupported browsers
- Progressive enhancement approach

## Dependencies
- Bootstrap 5.x (for modals and styling)
- jQuery (for DOM manipulation and AJAX)
- Toastr (optional, for notifications)
- Remix Icons (for icons)
- CodeIgniter 3.x (backend)

## Future Enhancements
- Drag and drop file upload
- Multiple file selection
- Image editing capabilities
- Cloud storage integration
- WebRTC improvements
- PWA camera access
