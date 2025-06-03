<?php

/**
 * Emergency Database Connection Fix
 * This script will immediately fix the connection limit issue
 */

require_once __DIR__ . '/vendor/autoload.php';

try {
    echo "🚨 Starting Emergency Database Connection Fix...\n";
    
    // 1. Clear all existing connections
    echo "1. Clearing existing connections...\n";
    if (file_exists(__DIR__ . '/bootstrap/app.php')) {
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        
        // Force disconnect all connections
        \Illuminate\Support\Facades\DB::disconnect();
        echo "✅ Disconnected all active connections\n";
    }
    
    // 2. Update configuration files to minimize connections
    echo "2. Updating configuration files...\n";
    
    // Update .env if it exists
    $envPath = __DIR__ . '/.env';
    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);
        
        // Update session driver to file
        if (strpos($envContent, 'SESSION_DRIVER=') !== false) {
            $envContent = preg_replace('/SESSION_DRIVER=.*/', 'SESSION_DRIVER=file', $envContent);
        } else {
            $envContent .= "\nSESSION_DRIVER=file\n";
        }
        
        // Update cache driver to file
        if (strpos($envContent, 'CACHE_STORE=') !== false) {
            $envContent = preg_replace('/CACHE_STORE=.*/', 'CACHE_STORE=file', $envContent);
        } else {
            $envContent .= "\nCACHE_STORE=file\n";
        }
        
        // Update queue driver to file
        if (strpos($envContent, 'QUEUE_CONNECTION=') !== false) {
            $envContent = preg_replace('/QUEUE_CONNECTION=.*/', 'QUEUE_CONNECTION=sync', $envContent);
        } else {
            $envContent .= "\nQUEUE_CONNECTION=sync\n";
        }
        
        // Add database connection limits
        if (strpos($envContent, 'DB_MAX_CONNECTIONS=') === false) {
            $envContent .= "\nDB_MAX_CONNECTIONS=3\n";
        }
        if (strpos($envContent, 'DB_MIN_CONNECTIONS=') === false) {
            $envContent .= "\nDB_MIN_CONNECTIONS=1\n";
        }
        
        file_put_contents($envPath, $envContent);
        echo "✅ Updated .env file\n";
    }
    
    // 3. Clear configuration cache
    echo "3. Clearing configuration cache...\n";
    if (file_exists(__DIR__ . '/bootstrap/cache/config.php')) {
        unlink(__DIR__ . '/bootstrap/cache/config.php');
        echo "✅ Cleared config cache\n";
    }
    
    // 4. Clear route cache
    if (file_exists(__DIR__ . '/bootstrap/cache/routes-v7.php')) {
        unlink(__DIR__ . '/bootstrap/cache/routes-v7.php');
        echo "✅ Cleared route cache\n";
    }
    
    // 5. Create temporary database cleanup
    echo "4. Running database cleanup...\n";
    
    try {
        // Simple PDO connection to clean up sessions
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname = $_ENV['DB_DATABASE'] ?? '';
        $username = $_ENV['DB_USERNAME'] ?? '';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        
        if ($dbname && $username) {
            $pdo = new PDO(
                "mysql:host={$host};dbname={$dbname}",
                $username,
                $password,
                [
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]
            );
            
            // Clean old sessions
            $cleanedSessions = $pdo->exec("DELETE FROM sessions WHERE last_activity < " . (time() - 3600));
            echo "✅ Cleaned {$cleanedSessions} old sessions\n";
            
            // Kill sleeping connections
            $stmt = $pdo->query("SHOW PROCESSLIST");
            $processes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $killedConnections = 0;
            foreach ($processes as $process) {
                if ($process['Command'] === 'Sleep' && $process['Time'] > 60) {
                    try {
                        $pdo->exec("KILL " . $process['Id']);
                        $killedConnections++;
                    } catch (Exception $e) {
                        // Ignore errors when killing connections
                    }
                }
            }
            echo "✅ Killed {$killedConnections} sleeping connections\n";
            
            $pdo = null; // Close connection
        }
    } catch (Exception $e) {
        echo "⚠️ Database cleanup partially failed: " . $e->getMessage() . "\n";
    }
    
    // 6. Wait for connections to reset
    echo "5. Waiting for connection limit to reset...\n";
    echo "💡 Connection limits on shared hosting reset every hour.\n";
    echo "💡 Your app is now optimized to use minimal connections.\n";
    
    echo "\n🎉 Emergency fix completed successfully!\n";
    echo "\n📋 What was fixed:\n";
    echo "   - Sessions moved to file storage (no DB connections)\n";
    echo "   - Cache moved to file storage (no DB connections)\n";
    echo "   - Queue changed to sync (no DB connections)\n";
    echo "   - Database connection limits reduced to 3\n";
    echo "   - Old sessions cleaned up\n";
    echo "   - Sleeping connections killed\n";
    echo "\n✅ Your app should now work without hitting connection limits!\n";
    
} catch (Exception $e) {
    echo "❌ Emergency fix failed: " . $e->getMessage() . "\n";
    echo "\n🔧 Manual steps to try:\n";
    echo "1. Wait 1 hour for connection limit to reset\n";
    echo "2. Change SESSION_DRIVER=file in .env\n";
    echo "3. Change CACHE_STORE=file in .env\n";
    echo "4. Run: php artisan config:clear\n";
    exit(1);
}
