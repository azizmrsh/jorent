<?php

// Temporarily disable gpdf to avoid dependency errors
return [
    'temp_dir' => sys_get_temp_dir(),
    'font_dir' => storage_path('fonts'),
    'font_cache' => storage_path('fonts/cache'),
    'enabled' => false,
    'default_font' => 'Arial',
    'default_paper_size' => 'A4',
    'default_paper_orientation' => 'portrait',
    'dpi' => 96,
    'enable_php' => false,
    'is_remote_enabled' => false,
    'is_javascript_enabled' => false,

    // GpdfSet options
    // Set this to `true` if you want numbers to appear in Hindi format (e.g., ١,٢,٣,٤,٥).
    // Set to `false` to display numbers in standard format (e.g., 1, 2, 3, 4, 5).
    'show_numbers_as_hindi' => false,

    // Set Max number of chars you can fit in one line, default is 50
    'max_chars_per_line' => 100,

    // Font height ratio setting.
    'font_height_ratio' => defined('GpdfDefault::FONT_HEIGHT_RATIO') ? GpdfDefault::FONT_HEIGHT_RATIO : 1.0,

    // Enable or disable font subsetting.
    'is_font_sub_setting_enabled' => defined('GpdfDefault::IS_FONT_SUB_SETTING_ENABLED') ? GpdfDefault::IS_FONT_SUB_SETTING_ENABLED : false,

    // Chroot directory for security purposes.
    'chroot' => realpath(dirname(__DIR__)),

    'storage_path' => defined('GpdfDefault::STORAGE_PATH') ? GpdfDefault::STORAGE_PATH : null,

    'aws_bucket' => '',
    'aws_region' => '',
    'aws_key' => '',
    'aws_secret' => '',

    // Enable or disable entity conversion.
    'convert_entities' => defined('GpdfDefault::CONVERT_ENTITIES') ? GpdfDefault::CONVERT_ENTITIES : false,

    // Allowed protocols for remote resources.
    'allowed_protocols' => defined('GpdfDefault::ALLOWED_PROTOCOLS') ? GpdfDefault::ALLOWED_PROTOCOLS : [],

    // Enable or disable artifact path validation.
    'artifact_path_validation' => defined('GpdfDefault::ARTIFACT_PATH_VALIDATION') ? GpdfDefault::ARTIFACT_PATH_VALIDATION : false,

    // Path to the log output file.
    'log_output_file' => defined('GpdfDefault::LOG_OUTPUT_FILE') ? GpdfDefault::LOG_OUTPUT_FILE : null,

    // Default media type for the generated PDFs.
    'default_media_type' => defined('GpdfDefault::DEFAULT_MEDIA_TYPE') ? GpdfDefault::DEFAULT_MEDIA_TYPE : 'application/pdf',

    // Default paper size for the generated PDFs.
    'default_paper_size' => defined('GpdfDefault::DEFAULT_PAPER_SIZE') ? GpdfDefault::DEFAULT_PAPER_SIZE : 'A4',

    // Default paper orientation for the generated PDFs.
    'default_paper_orientation' => defined('GpdfDefault::DEFAULT_PAPER_ORIENTATION') ? GpdfDefault::DEFAULT_PAPER_ORIENTATION : 'portrait',

    // DPI setting for the generated PDFs.
    'dpi' => defined('GpdfDefault::DPI') ? GpdfDefault::DPI : 96,

    // Enable or disable PHP execution in the PDFs.
    'enable_php' => defined('GpdfDefault::IS_PHP_ENABLED') ? GpdfDefault::IS_PHP_ENABLED : false,

    // Alias for ENABLE_PHP.
    'is_php_enabled' => defined('GpdfDefault::IS_PHP_ENABLED') ? GpdfDefault::IS_PHP_ENABLED : false,

    // Enable or disable remote resource fetching.
    'is_remote_enabled' => defined('GpdfDefault::IS_REMOTE_ENABLED') ? GpdfDefault::IS_REMOTE_ENABLED : false,

    // List of allowed remote hosts.
    'allowed_remote_hosts' => defined('GpdfDefault::ALLOWED_REMOTE_HOSTS') ? GpdfDefault::ALLOWED_REMOTE_HOSTS : [],

    // Enable or disable JavaScript execution in the PDFs.
    'is_javascript_enabled' => true,

    // Enable or disable HTML5 parser in the PDFs.
    'is_html5_parser_enabled' => defined('GpdfDefault::IS_HTML5_PARSER_ENABLED') ? GpdfDefault::IS_HTML5_PARSER_ENABLED : false,

    // Enable or disable PNG debugging.
    'debug_png' => defined('GpdfDefault::DEBUG_PNG') ? GpdfDefault::DEBUG_PNG : false,

    // Enable or disable keeping temporary files.
    'debug_keep_temp' => defined('GpdfDefault::DEBUG_KEEP_TEMP') ? GpdfDefault::DEBUG_KEEP_TEMP : false,

    // Enable or disable CSS debugging.
    'debug_css' => defined('GpdfDefault::DEBUG_CSS') ? GpdfDefault::DEBUG_CSS : false,

    // Enable or disable layout debugging.
    'debug_layout' => defined('GpdfDefault::DEBUG_LAYOUT') ? GpdfDefault::DEBUG_LAYOUT : false,

    // Enable or disable layout lines debugging.
    'debug_layout_lines' => defined('GpdfDefault::DEBUG_LAYOUT_LINES') ? GpdfDefault::DEBUG_LAYOUT_LINES : false,

    // Enable or disable layout blocks debugging.
    'debug_layout_blocks' => defined('GpdfDefault::DEBUG_LAYOUT_BLOCKS') ? GpdfDefault::DEBUG_LAYOUT_BLOCKS : false,

    // Enable or disable layout inline debugging.
    'debug_layout_inline' => defined('GpdfDefault::DEBUG_LAYOUT_INLINE') ? GpdfDefault::DEBUG_LAYOUT_INLINE : false,

    // Enable or disable layout padding box debugging.
    'debug_layout_padding_box' => defined('GpdfDefault::DEBUG_LAYOUT_PADDING_BOX') ? GpdfDefault::DEBUG_LAYOUT_PADDING_BOX : false,

    // Backend used for generating PDFs.
    'pdf_backend' => defined('GpdfDefault::PDF_BACKEND') ? GpdfDefault::PDF_BACKEND : null,

    // License key for the PDF library.
    'pdf_lib_license' => defined('GpdfDefault::PDF_LIB_LICENSE') ? GpdfDefault::PDF_LIB_LICENSE : null,

    // HTTP context options for fetching remote resources.
    'http_context' => defined('GpdfDefault::HTTP_CONTEXT') ? GpdfDefault::HTTP_CONTEXT : null,
];