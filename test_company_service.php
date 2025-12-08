<?php

use App\Modules\Settings\Services\CompanyService;

$service = new CompanyService();

echo "Testing CompanyService...\n";

// Test 1: Get all settings (should be default from config as DB is empty)
$settings = $service->get();
echo "Default Company Name: " . $settings['company_name'] . "\n";

if ($settings['company_name'] === 'Sylnovia') {
    echo "PASS: Default config value loaded.\n";
} else {
    echo "FAIL: Default config value NOT loaded.\n";
}

// Test 2: Get specific key
$email = $service->get('email');
echo "Default Email: " . $email . "\n";

if ($email === 'info@sylnovia.com') {
    echo "PASS: Specific key loaded.\n";
} else {
    echo "FAIL: Specific key NOT loaded.\n";
}
