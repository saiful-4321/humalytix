<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_clearance_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resignation_id')->constrained('hrm_resignations')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('hrm_departments')->onDelete('cascade');
            $table->foreignId('cleared_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('cleared_at')->nullable();
            $table->string('status')->default('pending'); // pending, cleared, issues
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['resignation_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_clearance_checklists');
    }
};
