<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_employee_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('hrm_skills')->onDelete('cascade');
            $table->string('proficiency_level')->default('beginner'); // beginner, intermediate, advanced, expert
            $table->integer('years_of_experience')->nullable();
            $table->date('last_used_date')->nullable();
            $table->string('certification_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['employee_id', 'skill_id']);
            $table->index('proficiency_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_employee_skills');
    }
};
