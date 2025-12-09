<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hrm_interviews', function (Blueprint $table) {
            $table->dropColumn(['scheduled_date', 'scheduled_time', 'interviewer_ids']);
            $table->dateTime('scheduled_at')->after('interview_type')->nullable();
            $table->foreignId('job_id')->nullable()->after('candidate_id')->constrained('hrm_jobs')->onDelete('cascade');
        });

        Schema::create('hrm_interview_interviewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained('hrm_interviews')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_interview_interviewers');
        Schema::table('hrm_interviews', function (Blueprint $table) {
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->json('interviewer_ids')->nullable();
            $table->dropColumn('scheduled_at');
            $table->dropForeign(['job_id']);
            $table->dropColumn('job_id');
        });
    }
};
