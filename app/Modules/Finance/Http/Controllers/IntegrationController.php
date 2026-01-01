<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\PayrollMapping;
use App\Modules\Finance\Models\ChartOfAccount;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function index()
    {
        $mappings = PayrollMapping::all();
        $accounts = ChartOfAccount::where('is_group', 0)->orderBy('code')->get();

        // If no mappings exist, seed them in memory for display or create default
        if ($mappings->isEmpty()) {
            $defaults = ['Basic Salary', 'House Rent', 'Medical Allowance', 'Conveyance', 'Tax Deduction', 'Provident Fund'];
            foreach ($defaults as $name) {
                PayrollMapping::firstOrCreate(
                    ['component_slug' => \Illuminate\Support\Str::slug($name)],
                    ['component_name' => $name]
                );
            }
            $mappings = PayrollMapping::all();
        }

        return view('Finance::pages.settings.mapping', compact('mappings', 'accounts'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'mappings' => 'required|array',
            'mappings.*.id' => 'required|exists:finance_payroll_mappings,id',
            'mappings.*.debit_account_id' => 'nullable|exists:finance_chart_of_accounts,id',
            'mappings.*.credit_account_id' => 'nullable|exists:finance_chart_of_accounts,id',
        ]);

        foreach ($data['mappings'] as $mappingData) {
            PayrollMapping::where('id', $mappingData['id'])->update([
                'debit_account_id' => $mappingData['debit_account_id'],
                'credit_account_id' => $mappingData['credit_account_id'],
            ]);
        }

        return back()->with('success', 'Payroll mappings updated successfully');
    }
}
