<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifying ENUM in existing table is tricky in standard Laravel/Doctrine.
        // Using raw SQL for MySQL is safest/easiest path here.
        DB::statement("ALTER TABLE finance_journals MODIFY COLUMN status ENUM('draft', 'posted', 'void', 'dishonoured') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE finance_journals MODIFY COLUMN status ENUM('draft', 'posted', 'void') DEFAULT 'draft'");
    }
};
