<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_employee_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('hrm_letter_templates')->onDelete('set null');
            $table->string('letter_number')->unique();
            $table->string('subject');
            $table->text('content'); // Final generated content
            $table->date('issued_date')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('draft'); // draft, issued, acknowledged
            $table->timestamp('acknowledged_at')->nullable();
            $table->string('file_path')->nullable(); // PDF file path
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'status']);
            $table->index('letter_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_employee_letters');
    }
};
