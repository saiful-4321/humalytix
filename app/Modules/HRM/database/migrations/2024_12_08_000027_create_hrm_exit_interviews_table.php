<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_exit_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resignation_id')->constrained('hrm_resignations')->onDelete('cascade');
            $table->foreignId('conducted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('conducted_date')->nullable();
            $table->text('reason_for_leaving')->nullable();
            $table->text('feedback')->nullable();
            $table->boolean('would_recommend')->nullable();
            $table->boolean('would_rejoin')->nullable();
            $table->text('suggestions')->nullable();
            $table->integer('rating')->nullable(); // 1-5
            $table->json('questionnaire_responses')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('resignation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_exit_interviews');
    }
};
