# Testing Upload Functionality - Step by Step

## 🔧 How to Test the Upload Feature

### 1. Open Browser Developer Tools
- Press `F12` or right-click → "Inspect"
- Go to the **Network** tab
- Check "Preserve log" option

### 2. Test File Upload
1. Navigate to the search page
2. Find a mandamiento record
3. Click the "Cambiar Estado" button (blue refresh icon)
4. Select "CUMPLIMENTADO" from the dropdown
5. The photo upload fields should appear
6. Try uploading a file:
   - Click "Subir Archivo" button
   - Select an image or PDF file
   - A preview should appear
7. Fill in required fields (fecha, no. oficio)
8. Click "Guardar"
9. Check Network tab for the POST request

### 3. Test Camera Capture
1. Follow steps 1-5 above
2. Try camera capture:
   - Click "Cámara" button
   - Allow camera permissions
   - Take a photo
   - Click "Use Photo"
   - A preview should appear
3. Fill in required fields
4. Click "Guardar"
5. Check Network tab for the POST request

### 4. What to Look For in Network Tab

#### Successful Request Should Show:
- **Request Method**: POST
- **URL**: `/mandamientosjudiciales/cambiar_estado_mandamiento`
- **Status**: 200 OK
- **Response**: `{"success": true, "message": "Estado del mandamiento actualizado exitosamente"}`

#### Form Data Should Include:
- `mandamiento_id`: The ID number
- `estado_mandamiento`: "CUMPLIMENTADO"
- `fecha_cumplimentacion`: Date value
- `no_oficio_cumplimentacion`: Text value
- **For File Upload**: `fotografia_oficio` or `fotografia_imputado` with file data
- **For Camera**: `camera_modal_fotografia_oficio` or `camera_modal_fotografia_imputado` with base64 data

### 5. Common Issues to Check

#### ❌ No Upload Data in Form
- Check if hidden inputs exist in modal HTML
- Check if JavaScript is setting the camera data correctly
- Verify file input is not empty

#### ❌ Server Error Response
- Check PHP error logs
- Look for specific error message in response
- Verify upload directory permissions

#### ❌ Database Not Updated
- Check if response shows success but data doesn't persist
- Verify model update method is working
- Check database connection

### 6. Browser Console Errors
- Check Console tab for JavaScript errors
- Look for camera permission errors
- Verify all scripts are loaded correctly

### 7. Server Logs
- Check `logs/` directory for PHP errors
- Look for the debug messages we added:
  - "Processing fotografia_oficio file"
  - "Upload result for oficio"
  - "Processing camera data"

## 🐛 Quick Fixes to Try

1. **Clear browser cache** - Force refresh with Ctrl+F5
2. **Check camera permissions** - Make sure browser allows camera access
3. **Try different file formats** - Test with JPG, PNG, PDF
4. **Check file size** - Make sure file is under 5MB
5. **Test in different browser** - Rule out browser-specific issues

## 📋 Expected File Locations
- Uploaded files should be saved in: `uploads/mandamientos_judiciales/{mandamiento_id}/`
- Database fields updated: `fotografia_oficio`, `fotografia_imputado`
- Files stored as: `{prefix}_{timestamp}.{ext}` (e.g., `oficio_123456789.jpg`)

## 🚨 If Still Not Working
1. Check for any JavaScript errors in browser console
2. Verify all required CSS/JS files are loaded
3. Test with a simple file upload first (not camera)
4. Check PHP error logs for detailed error messages
5. Ensure database table has the correct columns
