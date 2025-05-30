<?php

/**
 * Configuration file for the Gpdf package.
 * Simplified configuration to avoid enum issues
 */
return [
    'temp_dir' => sys_get_temp_dir(),
    'font_dir' => realpath(__DIR__ . '/../public/vendor/gpdf/fonts/'),
    'font_cache' => realpath(__DIR__ . '/../public/vendor/gpdf/fonts/'),
    'default_font' => 'NotoNaskhArabic',
    'enable_arabic_numbers' => true,
    'enable_remote' => true,
    'enable_javascript' => false,
    'enable_html5_parser' => true,
    'enable_font_subsetting' => false,
    'pdf_backend' => 'CPDF',
    'default_media_type' => 'screen',
    'default_paper_size' => 'a4',
    'default_orientation' => 'portrait',
    'log_output_file' => null,
    'enable_php' => false,
    'chroot' => realpath(base_path()),
    'admin_username' => 'user',
    'admin_password' => 'password',
    'dpi' => 96,
    'default_paper_orientation' => 'portrait',
    'is_rtl' => true,
    'max_chars_per_line' => 100,
    'font_height_ratio' => 1.1,
    'show_numbers_as_hindi' => false,
];
