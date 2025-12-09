<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique();
            $table->foreignId('department_id')->nullable()->constrained('hrm_departments')->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained('hrm_branches')->onDelete('set null');
            $table->string('employment_type'); // full_time, part_time, contract, internship
            $table->integer('experience_required')->nullable(); // in years
            $table->decimal('salary_range_min', 15, 2)->nullable();
            $table->decimal('salary_range_max', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('benefits')->nullable();
            $table->integer('vacancies')->default(1);
            $table->date('posted_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->string('status')->default('draft'); // draft, active, closed, on_hold
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_published']);
            $table->index('department_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_jobs');
    }
};
