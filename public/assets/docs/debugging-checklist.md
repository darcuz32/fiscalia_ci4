# Debugging Checklist for Modal Upload Issues

## Recent Changes Made:
1. ✅ Added hidden inputs for camera data to modal form:
   - `<input type="hidden" id="camera_modal_fotografia_oficio" name="camera_modal_fotografia_oficio">`
   - `<input type="hidden" id="camera_modal_fotografia_imputado" name="camera_modal_fotografia_imputado">`

2. ✅ Fixed controller to properly set camera data in $_POST for helper:
   - Manual assignment before calling helper for camera uploads
   - Proper field name mapping for helper

## Key Components Check:
- **Hidden Inputs**: ✅ Present in modal form
- **Camera Utils JS**: ✅ Sets `camera_${fieldName}` hidden input
- **File Upload Helper**: ✅ Looks for `camera_${fieldName}` in $_POST
- **Controller Logic**: ✅ Handles both file and camera uploads
- **Modal JS**: ✅ Uses FormData for submission
- **Database Update**: ✅ Uses proper model method

## Testing Steps:
1. Open browser dev tools and go to Network tab
2. Open a mandamiento modal and change state to "CUMPLIMENTADO"
3. Upload a file or take a photo
4. Submit the form
5. Check:
   - FormData contains the file/camera data
   - Server response shows success/error
   - Database is updated with filename
   - File exists in uploads directory

## Expected Behavior:
- **File Upload**: FormData should contain file in `fotografia_oficio` or `fotografia_imputado`
- **Camera Upload**: FormData should contain base64 data in `camera_modal_fotografia_oficio` or `camera_modal_fotografia_imputado`
- **Server Response**: JSON with `success: true` and `message`
- **DB Update**: `fotografia_oficio` and/or `fotografia_imputado` fields updated with filename
- **File Storage**: Files saved in `uploads/mandamientos_judiciales/{mandamiento_id}/`

## Common Issues:
1. **Missing Hidden Inputs**: ✅ Fixed - Now present in modal
2. **Field Name Mismatch**: ✅ Fixed - Controller now maps correctly
3. **Helper Not Finding Camera Data**: ✅ Fixed - Manual $_POST assignment
4. **Modal Reset**: Check if hidden inputs are cleared on modal close
5. **JavaScript Errors**: Check browser console for errors

## Next Steps:
1. Test with actual file upload
2. Test with camera capture
3. Verify database updates
4. Check file system for uploaded files
5. Remove debug logging after confirmation
