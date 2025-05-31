<?php

return [
    'default_format' => 'xlsx',
    'time_format' => 'M_d_Y-H_i',
    'default_page_orientation' => 'portrait',
    'disable_additional_columns' => false,
    'disable_filter_columns' => false,
    'disable_file_name' => false,
    'disable_file_name_prefix' => false,
    'disable_preview' => false,
    'csv_delimiter' => ',',
    'preview_records' => 100,
    'chunk_size' => 1000,
    'snappy_pdf' => [
        'enabled' => false,
        'binary' => '',
        'timeout' => false,
        'options' => [],
    ],
];
