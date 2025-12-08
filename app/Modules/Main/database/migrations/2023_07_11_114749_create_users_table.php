<?php

use App\Modules\Main\Enums\ActiveInactiveEnum;
use App\Modules\Main\Enums\PaymentStatusEnum;
use App\Modules\Main\Enums\UserStatusEnum;
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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 128)->index();
            $table->string('email', 128)->unique()->index();
            $table->string('password')->nullable();
            $table->string('mobile', 20)->unique()->index();
            $table->string('nid', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('api_token', 100)->nullable();
            $table->enum('status', UserStatusEnum::getValues())->default(UserStatusEnum::Pending)->index();
            $table->enum('payment_status', PaymentStatusEnum::getValues())->default(PaymentStatusEnum::Pending)->index();
            $table->boolean('bo_status')->default(ActiveInactiveEnum::Inactive)->index();
            $table->string('bo_cdbl_approval_file', 128)->nullable();
            $table->rememberToken();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
