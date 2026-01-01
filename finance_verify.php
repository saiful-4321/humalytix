<?php

use App\Modules\Finance\Services\DepreciationService;
use App\Modules\HRM\Models\Asset;
use App\Modules\HRM\Models\AssetCategory;
use App\Modules\Finance\Models\FiscalYear;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$category = AssetCategory::first();
if (!$category) {
    echo "Error: No Asset Category found.\n";
    return;
}
echo "Using Category: {$category->name} (Depr: {$category->depreciation_rate}%, Life: {$category->useful_life_years})\n";

if (!$category->depreciationAccount || !$category->accumulatedAccount) {
    echo "Error: Category accounts not mapped.\n";
    return;
}

$asset = Asset::firstOrCreate(
    ['code' => 'TEST-ASSET-001'],
    [
        'name' => 'Test Laptop',
        'asset_category_id' => $category->id,
        'purchase_date' => now()->subMonths(6),
        'purchase_cost' => 50000,
        'status' => 'assigned'
    ]
);

// 2. Run Depreciation
$service = new DepreciationService();
try {
    $journal = $service->runDepreciation(now());
    echo "Depreciation Run Success!\n";
    echo "Journal Created: {$journal->journal_number} (Type: {$journal->type})\n";
    foreach ($journal->entries as $entry) {
        echo " - {$entry->account->name}: " . ($entry->debit > 0 ? "Dr {$entry->debit}" : "Cr {$entry->credit}") . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
