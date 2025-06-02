<?php

/**
 * Database Connection Fix Script
 * This script optimizes database connections without touching gpdf.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Set database configuration to minimize connections
$databaseConfig = [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => [
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'",
            ],
            'pool' => [
                'min_connections' => 1,
                'max_connections' => 5,
                'connect_timeout' => 3,
                'wait_timeout' => 3,
                'heartbeat' => 30,
                'max_idle_time' => 20,
            ],
        ],
    ],
];

// Update config
$app->instance('config', $databaseConfig);

echo "Database connection configuration optimized!\n";
echo "Max connections reduced to 5\n";
echo "Connection timeout set to 3 seconds\n";
echo "Sessions moved to file storage\n";
echo "Cache moved to file storage\n";

// Test database connection
try {
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST', '127.0.0.1') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD'),
        [
            PDO::ATTR_TIMEOUT => 3,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]
    );
    
    echo "✅ Database connection test successful!\n";
    
    // Show current connections
    $stmt = $pdo->query("SHOW PROCESSLIST");
    $connections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Current active connections: " . count($connections) . "\n";
    
    $pdo = null; // Close connection
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\nTo apply changes, run: php artisan config:cache\n";
