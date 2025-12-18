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
        Schema::create('hrm_policies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('version')->default('1.0');
            $table->date('effective_date')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hrm_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('type')->default('Employment'); // Employment, NDA, etc.
            $table->enum('status', ['draft', 'sent', 'signed', 'expired', 'terminated'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hrm_signatures', function (Blueprint $table) {
            $table->id();
            $table->morphs('signable'); // signable_type, signable_id
            $table->foreignId('user_id')->constrained('users'); // Who signed
            $table->text('signature_image')->nullable(); // Base64 or path
            $table->string('ip_address')->nullable();
            $table->timestamp('signed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_signatures');
        Schema::dropIfExists('hrm_contracts');
        Schema::dropIfExists('hrm_policies');
    }
};
