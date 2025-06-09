<?php

// Test file to verify the language switch configuration
echo "Testing Language Switch Configuration\n";
echo "=====================================\n\n";

// Test 1: Check if the package is installed
try {
    $reflection = new ReflectionClass('BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch');
    echo "✓ FilamentLanguageSwitch package is installed\n";
    echo "  Package location: " . dirname($reflection->getFileName()) . "\n\n";
} catch (Exception $e) {
    echo "✗ FilamentLanguageSwitch package not found: " . $e->getMessage() . "\n\n";
}

// Test 2: Check if language directories exist
$langDir = __DIR__ . '/lang';
$enDir = $langDir . '/en';
$arDir = $langDir . '/ar';

echo "Language directories:\n";
echo is_dir($enDir) ? "✓ English language directory exists\n" : "✗ English language directory missing\n";
echo is_dir($arDir) ? "✓ Arabic language directory exists\n" : "✗ Arabic language directory missing\n";

// Test 3: Check if language files exist
$arValidation = $arDir . '/validation.php';
$arGeneral = $arDir . '/general.php';
$enGeneral = $enDir . '/general.php';

echo "\nLanguage files:\n";
echo file_exists($arValidation) ? "✓ Arabic validation file exists\n" : "✗ Arabic validation file missing\n";
echo file_exists($arGeneral) ? "✓ Arabic general file exists\n" : "✗ Arabic general file missing\n";
echo file_exists($enGeneral) ? "✓ English general file exists\n" : "✗ English general file missing\n";

// Test 4: Check current app locale settings
echo "\nCurrent Laravel configuration:\n";
echo "Default locale: " . config('app.locale') . "\n";
echo "Fallback locale: " . config('app.fallback_locale') . "\n";

echo "\n=====================================\n";
echo "Configuration test completed!\n";
echo "If all tests pass, the language switch should work.\n";
