<?php

namespace App\Modules\Finance\database\seeders;

use Illuminate\Database\Seeder;
use App\Modules\Finance\Models\AccountType;
use App\Modules\Finance\Models\ChartOfAccount;
use App\Modules\Finance\Models\FiscalYear;
use App\Modules\Finance\Models\Journal;
use App\Modules\Finance\Models\JournalEntry;
use App\Modules\Finance\Models\PayrollMapping;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FinanceSeeder extends Seeder
{
    private $journalCounter = 1;

    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        JournalEntry::truncate();
        Journal::truncate();
        PayrollMapping::truncate();
        ChartOfAccount::truncate();
        FiscalYear::truncate();
        AccountType::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->seedFiscalYears();
        $this->seedAccountTypes();
        $this->seedChartOfAccounts();
        $this->seedPayrollMappings();
        $this->seedDummyJournals();
        // $this->seedLiabilityTransactions(); // Integrated into main loop
    }

    private function seedFiscalYears()
    {
        // 2023-2027
        FiscalYear::create(['name' => 'FY 2023-24', 'start_date' => '2023-07-01', 'end_date' => '2024-06-30', 'is_current' => false, 'status' => 'closed']);
        FiscalYear::create(['name' => 'FY 2024-25', 'start_date' => '2024-07-01', 'end_date' => '2025-06-30', 'is_current' => false, 'status' => 'closed']);
        FiscalYear::create(['name' => 'FY 2025-26', 'start_date' => '2025-07-01', 'end_date' => '2026-06-30', 'is_current' => true, 'status' => 'open']);
        FiscalYear::create(['name' => 'FY 2026-27', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_current' => false, 'status' => 'open']);
    }

    private function seedAccountTypes()
    {
        AccountType::create(['name' => 'Asset', 'code_prefix' => '1', 'normal_balance' => 'debit']);
        AccountType::create(['name' => 'Liability', 'code_prefix' => '2', 'normal_balance' => 'credit']);
        AccountType::create(['name' => 'Equity', 'code_prefix' => '3', 'normal_balance' => 'credit']);
        AccountType::create(['name' => 'Revenue', 'code_prefix' => '4', 'normal_balance' => 'credit']);
        AccountType::create(['name' => 'Expense', 'code_prefix' => '5', 'normal_balance' => 'debit']);
    }

    private function seedChartOfAccounts()
    {
        $assetId = AccountType::where('code_prefix', '1')->first()->id;
        $liabId = AccountType::where('code_prefix', '2')->first()->id;
        $equityId = AccountType::where('code_prefix', '3')->first()->id;
        $revId = AccountType::where('code_prefix', '4')->first()->id;
        $expId = AccountType::where('code_prefix', '5')->first()->id;

        // Assets
        $curAsset = ChartOfAccount::create(['code' => '1000', 'name' => 'Current Assets', 'type_id' => $assetId, 'is_group' => true]);
        $cash = ChartOfAccount::create(['code' => '1010', 'name' => 'Cash in Hand', 'type_id' => $assetId, 'parent_id' => $curAsset->id, 'is_group' => true]);
        ChartOfAccount::create(['code' => '1011', 'name' => 'Main Cash', 'type_id' => $assetId, 'parent_id' => $cash->id, 'is_group' => false, 'opening_balance' => 50000]);
        ChartOfAccount::create(['code' => '1012', 'name' => 'Petty Cash', 'type_id' => $assetId, 'parent_id' => $cash->id, 'is_group' => false, 'opening_balance' => 5000]);
        
        $bank = ChartOfAccount::create(['code' => '1020', 'name' => 'Bank Accounts', 'type_id' => $assetId, 'parent_id' => $curAsset->id, 'is_group' => true]);
        ChartOfAccount::create(['code' => '1021', 'name' => 'City Bank', 'type_id' => $assetId, 'parent_id' => $bank->id, 'is_group' => false, 'opening_balance' => 1000000]);
        ChartOfAccount::create(['code' => '1022', 'name' => 'HSBC Bank', 'type_id' => $assetId, 'parent_id' => $bank->id, 'is_group' => false, 'opening_balance' => 500000]);

        // Liabilities
        $curLiab = ChartOfAccount::create(['code' => '2000', 'name' => 'Current Liabilities', 'type_id' => $liabId, 'is_group' => true]);
        ChartOfAccount::create(['code' => '2010', 'name' => 'Accounts Payable', 'type_id' => $liabId, 'parent_id' => $curLiab->id, 'is_group' => false]);
        ChartOfAccount::create(['code' => '2020', 'name' => 'Payroll Payable', 'type_id' => $liabId, 'parent_id' => $curLiab->id, 'is_group' => false]);

        // Equity
        $equityCapital = ChartOfAccount::create(['code' => '3000', 'name' => 'Share Capital', 'type_id' => $equityId, 'is_group' => false, 'opening_balance' => 2000000]);
        
        // Seed initial capital journal to ensure it shows in reports
        $this->createJournal(FiscalYear::first()->start_date, FiscalYear::first(), 'OPEN', "Initial Capital", [
            ['account_id' => $equityCapital->id, 'debit' => 0, 'credit' => 2000000],
            ['account_id' => ChartOfAccount::where('code', '1021')->first()->id, 'debit' => 2000000, 'credit' => 0],
        ]);

        // Revenue
        $opRev = ChartOfAccount::create(['code' => '4000', 'name' => 'Operating Revenue', 'type_id' => $revId, 'is_group' => true]);
        ChartOfAccount::create(['code' => '4010', 'name' => 'Service Sales', 'type_id' => $revId, 'parent_id' => $opRev->id, 'is_group' => false]);

        // Expenses
        $opExp = ChartOfAccount::create(['code' => '5000', 'name' => 'Operating Expenses', 'type_id' => $expId, 'is_group' => true]);
        $admin = ChartOfAccount::create(['code' => '5010', 'name' => 'Administrative Expenses', 'type_id' => $expId, 'parent_id' => $opExp->id, 'is_group' => true]);
        ChartOfAccount::create(['code' => '5011', 'name' => 'Office Rent', 'type_id' => $expId, 'parent_id' => $admin->id, 'is_group' => false]);
        ChartOfAccount::create(['code' => '5012', 'name' => 'Utility Bills', 'type_id' => $expId, 'parent_id' => $admin->id, 'is_group' => false]);
    }

    private function seedPayrollMappings()
    {
        $rent = ChartOfAccount::where('code', '5011')->first();
        if ($rent) {
            PayrollMapping::create([
                'component_name' => 'Office Rent', 
                'component_slug' => 'office_rent',
                'debit_account_id' => $rent->id,
                'credit_account_id' => ChartOfAccount::where('code', '2010')->first()->id ?? null
            ]);
        }
    }

    // ... (Keep existing methods until seedDummyJournals)

    private function seedDummyJournals()
    {
        $faker = Faker::create();
        $fiscalYears = FiscalYear::all();
        $banks = ChartOfAccount::where('code', 'like', '102%')->where('is_group', false)->get();
        $revs = ChartOfAccount::where('code', 'like', '4%')->where('is_group', false)->get();
        $exps = ChartOfAccount::where('code', 'like', '5%')->where('is_group', false)->get();
        $cashs = ChartOfAccount::where('code', 'like', '101%')->where('is_group', false)->get();

        foreach ($fiscalYears as $fy) {
            // Generate ~750 entries per year => Total ~3000
            for ($i = 0; $i < 750; $i++) {
                $date = $faker->dateTimeBetween($fy->start_date, $fy->end_date);
                $bank = $banks->random();
                
                $rand = $faker->numberBetween(1, 100);
                
                // 40% Payment
                if ($rand <= 40) {
                    $exp = $exps->random();
                    $amt = $faker->randomFloat(2, 500, 15000);
                    $this->createJournal($date, $fy, 'PAY', "Payment for $exp->name", [
                        ['account_id' => $exp->id, 'debit' => $amt, 'credit' => 0],
                        ['account_id' => $bank->id, 'debit' => 0, 'credit' => $amt],
                    ]);
                } 
                // 30% Receipt
                elseif ($rand <= 70) {
                    $rev = $revs->random();
                    $amt = $faker->randomFloat(2, 1000, 50000);
                    $this->createJournal($date, $fy, 'REC', "Received from $rev->name", [
                        ['account_id' => $bank->id, 'debit' => $amt, 'credit' => 0],
                        ['account_id' => $rev->id, 'debit' => 0, 'credit' => $amt],
                    ]);
                }
                // 15% Bills (Liabilities)
                elseif ($rand <= 85) {
                     $exp = $exps->random();
                     $ap = ChartOfAccount::where('code', '2010')->first();
                     $amt = $faker->randomFloat(2, 2000, 20000);
                     if ($ap) {
                         $this->createJournal($date, $fy, 'BILL', "Bill due for $exp->name", [
                            ['account_id' => $exp->id, 'debit' => $amt, 'credit' => 0],
                            ['account_id' => $ap->id, 'debit' => 0, 'credit' => $amt],
                        ]);
                     }
                }
                // 10% Contra (Bank Transfer / Cash withdrawal)
                elseif ($rand <= 95) {
                    $cash = $cashs->random();
                    $amt = $faker->randomFloat(2, 5000, 30000);
                    // Bank to Cash
                    $this->createJournal($date, $fy, 'CONTRA', "Cash Withdrawal", [
                        ['account_id' => $cash->id, 'debit' => $amt, 'credit' => 0],
                        ['account_id' => $bank->id, 'debit' => 0, 'credit' => $amt],
                    ]);
                }
                // 5% Dishonoured / Cancelled
                else {
                     $rev = $revs->random();
                     $amt = $faker->randomFloat(2, 5000, 10000);
                     // Create a dishonoured receipt
                     $this->createJournal($date, $fy, 'REC', "Dishonoured Cheque from $rev->name", [
                        ['account_id' => $bank->id, 'debit' => $amt, 'credit' => 0],
                        ['account_id' => $rev->id, 'debit' => 0, 'credit' => $amt],
                    ], 'dishonoured');
                }
            }
        }
    }

    private function seedLiabilityTransactions()
    {
        $faker = Faker::create();
        $fy = FiscalYear::first();
        $ap = ChartOfAccount::where('code', '2010')->first();
        $exps = ChartOfAccount::where('code', 'like', '5%')->where('is_group', false)->get();
        $bank = ChartOfAccount::where('code', '1021')->first();

        if (!$ap || $exps->isEmpty()) return;

        for ($i = 0; $i < 10; $i++) {
            $exp = $exps->random();
            $amt = $faker->randomFloat(2, 5000, 25000);
            $date = $faker->dateTimeBetween($fy->start_date, $fy->end_date);
            
            // Bill (Liability Creation)
            $this->createJournal($date, $fy, 'BILL', "Bill for $exp->name", [
                ['account_id' => $exp->id, 'debit' => $amt, 'credit' => 0],
                ['account_id' => $ap->id, 'debit' => 0, 'credit' => $amt],
            ]);

            // 50% chance to Pay it off
            if ($faker->boolean()) {
                $this->createJournal($date, $fy, 'PAY', "Payment of Bill", [
                    ['account_id' => $ap->id, 'debit' => $amt, 'credit' => 0],
                    ['account_id' => $bank->id, 'debit' => 0, 'credit' => $amt],
                ]);
            }
        }
    }

    private function createJournal($date, $fy, $type, $desc, $entries, $status = 'posted')
    {
        $num = $type . '-' . $date->format('Ynd') . '-' . str_pad($this->journalCounter++, 4, '0', STR_PAD_LEFT);
        $j = Journal::create([
            'journal_number' => $num, 'date' => $date, 'reference' => $num, 'description' => $desc,
            'fiscal_year_id' => $fy->id, 'status' => $status, 'type' => (strtolower($type) == 'pay' || strtolower($type) == 'bill') ? 'payment' : (strtolower($type) == 'rec' ? 'receipt' : 'journal'),
            'created_by' => 1, 'posted_by' => 1, 'posted_at' => now(),
        ]);
        foreach ($entries as $e) {
            $j->entries()->create($e);
            $acc = ChartOfAccount::find($e['account_id']);
            if ($acc) {
                if ($acc->type->normal_balance == 'debit') {
                    $acc->current_balance += ($e['debit'] - $e['credit']);
                } else {
                    $acc->current_balance += ($e['credit'] - $e['debit']);
                }
                $acc->save();
            }
        }
    }
}
