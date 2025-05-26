&lt;?php

echo "Testing database connection...\n";

try {
    // Test basic database query
    $pdo = new PDO('mysql:host=localhost;dbname=jorent', 'root', '');
    echo "✅ Database connection successful\n";
    
    // Check properties table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM properties");
    $result = $stmt->fetch();
    echo "✅ Properties count: " . $result['count'] . "\n";
    
    // Check addresses table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM addresses");
    $result = $stmt->fetch();
    echo "✅ Addresses count: " . $result['count'] . "\n";
    
    // Test join query (simulating what export does)
    $stmt = $pdo->query("
        SELECT p.name, a.city 
        FROM properties p 
        LEFT JOIN addresses a ON p.id = a.property_id 
        LIMIT 3
    ");
    $results = $stmt->fetchAll();
    
    echo "\n✅ Sample data with addresses:\n";
    foreach ($results as $row) {
        echo "- Property: " . $row['name'] . " | City: " . ($row['city'] ?? 'No city') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nTest complete.\n";
