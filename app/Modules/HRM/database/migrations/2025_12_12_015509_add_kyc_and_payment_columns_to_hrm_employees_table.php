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
        Schema::table('hrm_employees', function (Blueprint $table) {
            // Payment Info
            $table->enum('payment_method', ['bank', 'cash', 'cheque', 'mfs'])->default('bank')->after('bank_branch');
            $table->enum('mfs_provider', ['bkash', 'nagad', 'rocket', 'upay'])->nullable()->after('payment_method');
            $table->string('mfs_account_number', 50)->nullable()->after('mfs_provider');
            
            // Extended KYC / Personal Info
            $table->string('father_name')->nullable()->after('nationality');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('spouse_name')->nullable()->after('mother_name');
            $table->string('personal_email')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_employees', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'mfs_provider',
                'mfs_account_number',
                'father_name',
                'mother_name',
                'spouse_name',
                'personal_email',
            ]);
        });
    }
};
