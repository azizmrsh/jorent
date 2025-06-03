<?php

/**
 * Quick Database Connection Cleanup
 * Run this script when you hit connection limits
 */

echo "🔧 Quick Database Cleanup Starting...\n";

try {
    // Load environment variables
    if (file_exists(__DIR__ . '/.env')) {
        $env = file_get_contents(__DIR__ . '/.env');
        $lines = explode("\n", $env);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
                [$key, $value] = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
    
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $dbname = $_ENV['DB_DATABASE'] ?? '';
    $username = $_ENV['DB_USERNAME'] ?? '';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    if (!$dbname || !$username) {
        echo "❌ Database credentials not found in .env\n";
        exit(1);
    }
    
    // Quick connection to clean up
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname}",
        $username,
        $password,
        [
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]
    );
    
    echo "✅ Connected to database\n";
    
    // 1. Clean old sessions (older than 2 hours)
    $cleanedSessions = $pdo->exec("DELETE FROM sessions WHERE last_activity < " . (time() - 7200));
    echo "✅ Cleaned {$cleanedSessions} old sessions\n";
    
    // 2. Show current process list
    $stmt = $pdo->query("SHOW PROCESSLIST");
    $processes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "📊 Current connections: " . count($processes) . "\n";
    
    // 3. Kill sleeping connections older than 30 seconds
    $killedConnections = 0;
    foreach ($processes as $process) {
        if ($process['Command'] === 'Sleep' && $process['Time'] > 30 && $process['User'] === $username) {
            try {
                $pdo->exec("KILL " . $process['Id']);
                $killedConnections++;
            } catch (Exception $e) {
                // Ignore kill errors
            }
        }
    }
    echo "✅ Killed {$killedConnections} sleeping connections\n";
    
    // 4. Show status
    $stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        echo "📊 Active threads: " . $result['Value'] . "\n";
    }
    
    $pdo = null; // Close connection
    
    echo "\n🎉 Database cleanup completed!\n";
    echo "💡 Your app should now have fewer active connections.\n";
    
} catch (Exception $e) {
    echo "❌ Cleanup failed: " . $e->getMessage() . "\n";
    echo "\n⏰ If you're still hitting limits, wait 1 hour for the connection limit to reset.\n";
    exit(1);
}
