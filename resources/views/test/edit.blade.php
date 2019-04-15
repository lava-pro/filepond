<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>Test | Edit</title>
    <!-- Get FilePond and FilePond image preview plugin styles from a CDN -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        /* FilePond will automatically fill up all available horizontal space, it's best to limit it in some way */
        form {
            max-width:24em;
        }
    </style>
</head>
<body>
    <form action="/filepond/update" method="post" enctype="multipart/form-data">
        <input type="file" name="filepond[]" multiple>
        <button type="submit">&nbsp; SUBMIT &nbsp;</button>
    </form>
    <!-- Babel polyfill, contains Promise -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.6.15/browser-polyfill.min.js"></script>
    <!-- Get FilePond polyfills from the CDN -->
    <script src="https://unpkg.com/filepond-polyfill/dist/filepond-polyfill.js"></script>
    <!-- Get FilePond JavaScript and its plugins from the CDN -->
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <!-- FilePond init script -->
    <script>

    // Register plugins
    FilePond.registerPlugin(
        FilePondPluginFileValidateSize,
        FilePondPluginImageExifOrientation,
        FilePondPluginImagePreview,
        FilePondPluginImageCrop,
        FilePondPluginImageResize,
        FilePondPluginImageTransform
    );
    // Set default FilePond options
    FilePond.setOptions({
        // Maximum allowed file size
        maxFileSize: '5MB',
        // Maximum files count
        maxFiles: 10,
        // Crop the image to a 1:1 ratio
        imageCropAspectRatio: '1.5:1',
        // Resize the image
        imageResizeTargetWidth: 240,
        // The name to use in front of the file name.
        imageTransformVariantsDefaultName: 'thumb_',
        // Should the transform plugin output the original file.
        imageTransformVariantsIncludeOriginal: true,
        // The name to use in front of the original file name.
        imageTransformVariantsOriginalName: '',
        // upload to this server end point
        server: {
            process: '/filepond/upload',
            revert:  '/filepond/revert',
            restore: '/filepond/restore/',
            load:    '/filepond/load/',
            fetch:   '/filepond/fetch/'
        }
    });
    // Turn a file input into a file pond
    const files = {!! $files !!};
    var pond = FilePond.create(document.querySelector('input[type="file"]'), {
        files: files
    });

    </script>
</body>
</html>