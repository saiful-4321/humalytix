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
        // 1. Fiscal Years
        Schema::create('finance_fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "2024-2025"
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['open', 'closed', 'locked'])->default('open');
            $table->boolean('is_current')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Account Types
        Schema::create('finance_account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Asset, Liability, Equity, Income, Expense
            $table->string('code_prefix')->unique(); // 1, 2, 3, 4, 5
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->timestamps();
        });

        // 3. Chart of Accounts
        Schema::create('finance_chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 1001, 1002
            $table->string('name'); // Cash, Bank, Sales
            $table->foreignId('type_id')->constrained('finance_account_types')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('finance_chart_of_accounts')->onDelete('set null');
            $table->boolean('is_group')->default(false); // True if it's a parent account
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->boolean('is_system')->default(false); // Prevent deletion
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Journals
        Schema::create('finance_journals', function (Blueprint $table) {
            $table->id();
            $table->string('journal_number')->unique(); // JRN-2024-00001
            $table->date('date');
            $table->string('reference')->nullable(); // Invoice #, PO #
            $table->string('reference_type')->nullable(); // App\Models\Invoice
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('fiscal_year_id')->constrained('finance_fiscal_years');
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Journal Entries
        Schema::create('finance_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('finance_journals')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('finance_chart_of_accounts')->onDelete('cascade');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->text('description')->nullable(); // Line item description
            $table->timestamps();
        });

        // 6. Payroll Mappings
        Schema::create('finance_payroll_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('component_name'); // Basic Salary, House Rent, Tax
            $table->string('component_slug')->unique(); // basic_salary, tax
            $table->foreignId('debit_account_id')->nullable()->constrained('finance_chart_of_accounts'); // Expense Account
            $table->foreignId('credit_account_id')->nullable()->constrained('finance_chart_of_accounts'); // Liability/Cash Account
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_payroll_mappings');
        Schema::dropIfExists('finance_journal_entries');
        Schema::dropIfExists('finance_journals');
        Schema::dropIfExists('finance_chart_of_accounts');
        Schema::dropIfExists('finance_account_types');
        Schema::dropIfExists('finance_fiscal_years');
    }
};
