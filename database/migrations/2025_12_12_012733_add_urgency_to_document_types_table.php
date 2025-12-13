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
        Schema::table('hrm_document_types', function (Blueprint $table) {
            $table->enum('urgency', ['required', 'nice_to_have', 'not_required'])->default('not_required')->after('is_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_document_types', function (Blueprint $table) {
            $table->dropColumn('urgency');
        });
    }
};
