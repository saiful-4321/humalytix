<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('hrm_assets')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->date('assigned_date');
            $table->date('return_date')->nullable();
            $table->string('condition_at_assignment')->nullable(); // excellent, good, fair, poor
            $table->string('condition_at_return')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('returned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['asset_id', 'employee_id']);
            $table->index('assigned_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_asset_assignments');
    }
};
