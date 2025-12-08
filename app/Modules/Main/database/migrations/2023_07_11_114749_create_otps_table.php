<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ["SMS", "EMAIL"])->nullable()->index();
            $table->string('to', 128)->index();
            $table->string('otp', 8)->index();
            $table->dateTime('expired_time')->index();
            $table->tinyInteger('status')->default(1);
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
            $table->string('ip_address', 20)->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('otps');
    }
};
