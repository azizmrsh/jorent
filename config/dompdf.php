<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DomPDF Configuration with Arabic Support
    |--------------------------------------------------------------------------
    */
    
    'show_warnings' => false,
    'public_path' => null,
    'convert_entities' => true,
    
    'options' => [
        // Font directories - pointing to our Arabic fonts
        'font_dir' => public_path('fonts/'),
        'font_cache' => storage_path('fonts/'),
        'temp_dir' => sys_get_temp_dir(),
        
        // Security settings
        'chroot' => realpath(base_path()),
        'allowed_protocols' => [
            'data://' => ['rules' => []],
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],
        
        // Arabic and Unicode support
        'enable_font_subsetting' => true,
        'pdf_backend' => 'CPDF',
        'default_media_type' => 'print',
        'default_paper_size' => 'a4',
        'default_paper_orientation' => 'portrait',
        'default_font' => 'serif',
        'dpi' => 96,
        
        // Security - keep these disabled
        'enable_php' => false,
        'enable_javascript' => false,
        'enable_remote' => false,
        'allowed_remote_hosts' => null,
        
        // Typography improvements for Arabic
        'font_height_ratio' => 1.2,
        'enable_html5_parser' => true,
        
        // Additional Arabic-specific configurations
        'unicode_enabled' => true,
        'isUnicode' => true,
        'autodetect_language' => true,
        'substitutions' => true,
    ],
];