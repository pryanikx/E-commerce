<?php

return [
    'directory' => env('EXPORT_DIRECTORY', 'app/exports'),
    'file_prefix' => env('EXPORT_FILE_PREFIX', 'catalog_export_'),
    'file_extension' => env('EXPORT_FILE_EXTENSION', '.csv'),
    'directory_permissions' => env('EXPORT_DIRECTORY_PERMISSIONS', 0755),
]; 