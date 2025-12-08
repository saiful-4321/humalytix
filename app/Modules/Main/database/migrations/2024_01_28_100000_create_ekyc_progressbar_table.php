<?php

use App\Modules\Main\Enums\ActiveInactiveEnum;
use App\Modules\Main\Enums\EkycProgressbarStatusEnum;
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
        Schema::create('ekyc_progressbar', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->boolean('basic_registration')->default(ActiveInactiveEnum::Active)->comment('Step 1: Basic Registation');
            $table->boolean('otp_verification')->default(ActiveInactiveEnum::Inactive)->comment('Step 2: OTP Verification');
            $table->boolean('bo_account_holder')->default(ActiveInactiveEnum::Inactive)->comment('Step 3: Account Holder Information');
            $table->boolean('bo_bank_information')->default(ActiveInactiveEnum::Inactive)->comment('Step 4: Bank Account Information');
            $table->boolean('bo_authorize_information')->default(ActiveInactiveEnum::Inactive)->comment('Step 5: Authorize Information');
            $table->boolean('bo_nominee_information')->default(ActiveInactiveEnum::Inactive)->comment('Step 6: Nominee Information');
            $table->boolean('bo_documents')->default(ActiveInactiveEnum::Inactive)->comment('Step 7: Upload Documents');
            $table->enum('status', EkycProgressbarStatusEnum::getValues())->default(EkycProgressbarStatusEnum::Pending); 
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ekyc_progressbar');
    }
};
