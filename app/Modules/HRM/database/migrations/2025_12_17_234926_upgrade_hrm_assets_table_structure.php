<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hrm_assets', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_assets', 'asset_category_id')) {
                $table->foreignId('asset_category_id')->nullable()->after('id')->constrained('hrm_asset_categories')->onDelete('set null');
            }
            if (Schema::hasColumn('hrm_assets', 'type')) {
                // We'll keep 'type' temporarily or drop it if data migration isn't needed yet.
                // For a clean implementation, let's drop it if we assume new structure.
                // But generally safer to make it nullable first.
                $table->dropColumn('type');
            }
            
            // Additional tracking
            $table->string('location')->nullable()->after('condition');
            $table->decimal('salvage_value', 12, 2)->default(0)->after('purchase_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_assets', function (Blueprint $table) {
            $table->dropForeign(['asset_category_id']);
            $table->dropColumn('asset_category_id');
            $table->string('type')->nullable(); // Restore
            $table->dropColumn('location');
            $table->dropColumn('salvage_value');
        });
    }
};
