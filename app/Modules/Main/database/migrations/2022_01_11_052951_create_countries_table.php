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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('iso2')->nullable();
            $table->string('iso3')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->foreignId('created_by')->nullable()->comment('0 for system');
            $table->foreignId('updated_by')->nullable()->comment('0 for system');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
};
