<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('hrm_candidates')->onDelete('cascade');
            $table->string('interview_type'); // phone, video, in_person, technical, hr, final
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->integer('duration')->default(60); // in minutes
            $table->json('interviewer_ids')->nullable(); // Array of user IDs
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled, rescheduled, no_show
            $table->text('feedback')->nullable();
            $table->integer('rating')->nullable(); // 1-5
            $table->string('recommendation')->nullable(); // hire, maybe, reject
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['candidate_id', 'status']);
            $table->index('scheduled_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_interviews');
    }
};
