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
        Schema::create('user_session', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('ip_address');
            $table->foreignId('user_id')->index();
            $table->enum('is_pretend',['Yes','No'])->default('No');
            $table->enum('guard',['Web','Api'])->default('Web');
            $table->foreignId('pretend_user_id')->nullable();
            $table->text('user_agent');
            $table->dateTime('logged_in_at');
            $table->dateTime('logged_out_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_session');
    }
};
