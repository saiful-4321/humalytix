<?php

use App\Modules\Main\Enums\DocumentTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() : void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('type', 64)->default(DocumentTypeEnum::System)->index();
            $table->string('title', 100)->nullable();
            $table->string('path')->nullable();
            $table->string('extension', 10)->nullable()->comment("jpg,png,pdf,zip");
            $table->string('size', 20)->nullable()->comment("file size");
            $table->string('ref_table', 100)->nullable();
            $table->string('ref_id', 100)->nullable();
            $table->boolean('status')->default(true)->comment('0=Inactive, 1=Active');
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
    public function down() : void
    {
        Schema::dropIfExists('documents');
    }
};
