# Industry-Level Payroll Management System

## Complete Documentation

---

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [System Architecture](#system-architecture)
4. [Database Schema](#database-schema)
5. [Installation & Setup](#installation--setup)
6. [Payroll Processing Journey](#payroll-processing-journey)
7. [Module Documentation](#module-documentation)
8. [API Reference](#api-reference)
9. [User Guide](#user-guide)
10. [Developer Guide](#developer-guide)

---

## Overview

This is a comprehensive, industry-level Payroll Management System built with Laravel, designed to handle all aspects of employee compensation, from salary structures to final settlements.

### Key Highlights

- ✅ **Complete Salary Management** - Components, structures, grades
- ✅ **Automated Tax Calculation** - Progressive tax slabs by income/gender
- ✅ **Loan & Advance Management** - Full lifecycle with auto-deduction
- ✅ **Overtime Calculation** - Weekend, holiday, night shift multipliers
- ✅ **Bonus & Commission** - Multiple types with approval workflows
- ✅ **PDF Salary Slips** - Professional, downloadable payslips
- ✅ **Multi-level Approvals** - Workflow-based approval chains
- ✅ **Gratuity Calculation** - End-of-service benefits
- ✅ **Full & Final Settlement** - Complete exit processing

---

## Features

### 1. Salary Structure Management

#### Salary Components
- **Earnings**: Basic, HRA, Transport, Medical, etc.
- **Deductions**: PF, Insurance, Professional Tax, etc.
- **Calculation Types**: Fixed amount or percentage-based
- **Taxable Status**: Mark components as taxable/non-taxable

#### Salary Structures (Grades)
- Create multiple templates (Grade A, B, C, Manager Level, etc.)
- Bundle components with custom values
- Override default component values per structure
- Assign structures to employees

#### Tax Configuration
- Progressive tax slabs
- Gender-specific rules (Male/Female/Other/All)
- Income range-based rates
- Fixed deductions + percentage-based

### 2. Loan Management

#### Loan Types
- Personal Loan
- Emergency Loan
- Education Loan
- Housing Loan
- Vehicle Loan

#### Features
- Interest calculation (Flat/Reducing balance)
- Auto-generate installment schedule
- Maximum amount and tenure limits
- Approval workflow
- Auto-deduction from salary

### 3. Salary Advances

- Request and approval workflow
- Configurable repayment period (1-12 months)
- Auto-deduction from monthly salary
- Track outstanding balances

### 4. Overtime (OT) Management

#### OT Policies
- Regular OT multiplier (e.g., 1.5x)
- Weekend multiplier (e.g., 2.0x)
- Holiday multiplier (e.g., 2.5x)
- Night shift multiplier (e.g., 1.25x)
- Configurable night shift hours
- Min/Max OT hours limits

#### OT Calculation
- Auto-calculate based on hourly rate
- Apply appropriate multipliers
- Approval workflow
- Auto-add to monthly payroll

### 5. Bonus & Commission

#### Bonus Types
- Festival Bonus (Eid, etc.)
- Performance Bonus
- Joining Bonus
- Year-end Bonus
- Custom bonuses

#### Calculation Types
- Fixed amount
- Percentage of basic/gross
- Performance-based

### 6. Payroll Processing

#### Automated Calculation
1. Fetch employee's salary structure
2. Calculate basic + allowances
3. Add approved overtime
4. Add approved bonuses
5. Calculate tax (auto-apply slabs)
6. Deduct loans/advances
7. Deduct other deductions
8. Generate net salary

#### Bulk Generation
- Generate payroll for all employees in one click
- Month/Year selection
- Automatic validation and error handling
- Skip duplicate payrolls

#### Payment Processing
- Mark as paid
- Auto-update loan installments
- Auto-update advance deductions
- Link OT/Bonus to payroll
- Record payment date

### 7. Reporting & Outputs

#### Salary Slips
- PDF generation
- Professional template
- Detailed breakdown (earnings vs deductions)
- Company branding
- Download/Email

#### Bank Transfer Files
- NPSB format (Bangladesh)
- BEFTN format (Bangladesh)
- Custom CSV/TXT formats
- Batch processing

#### Salary Register
- Consolidated monthly report
- All employees in one view
- Export to Excel/PDF

### 8. Full & Final Settlement

- Calculate pending salary
- Leave encashment
- Gratuity calculation
- Bonus settlement
- Deduct pending loans/advances
- Notice pay recovery
- Generate final statement

### 9. Arrears & Retro Payroll

- Salary revision arrears
- Increment arrears
- Backdated adjustments
- Track payments across months

---

## System Architecture

### Technology Stack

- **Framework**: Laravel 11.x
- **Database**: MySQL 8.0+
- **PDF Generation**: DomPDF
- **Permissions**: Spatie Laravel Permission
- **Frontend**: Blade Templates, Vanilla CSS, Bootstrap 5

### Module Structure

```
app/Modules/HRM/
├── Http/
│   └── Controllers/
│       ├── PayrollController.php (Enhanced with auto-calculation)
│       ├── EmployeeSalaryController.php
│       ├── SalaryComponentController.php
│       ├── SalaryStructureController.php
│       ├── TaxSlabController.php
│       ├── LoanController.php
│       ├── AdvanceController.php
│       ├── OvertimeController.php
│       └── BonusController.php
├── Models/
│   ├── Employee.php
│   ├── EmployeeSalary.php
│   ├── SalaryComponent.php
│   ├── SalaryStructure.php
│   ├── TaxSlab.php
│   ├── Payroll.php
│   ├── PayrollItem.php
│   ├── LoanType.php
│   ├── EmployeeLoan.php
│   ├── LoanInstallment.php
│   ├── EmployeeAdvance.php
│   ├── AdvanceDeduction.php
│   ├── OTPolicy.php
│   ├── EmployeeOvertime.php
│   ├── BonusType.php
│   └── EmployeeBonus.php
├── database/
│   ├── migrations/
│   │   ├── create_hrm_salary_components_table.php
│   │   ├── create_hrm_salary_structures_table.php
│   │   ├── create_hrm_employee_salaries_table.php
│   │   ├── create_hrm_tax_slabs_table.php
│   │   ├── create_hrm_payrolls_table.php
│   │   ├── create_hrm_loans_and_advances_tables.php
│   │   ├── create_hrm_overtime_tables.php
│   │   └── create_hrm_bonus_commission_gratuity_tables.php
│   └── seeders/
│       ├── LoanManagementSeeder.php
│       └── AdvancedPayrollSeeder.php
└── resources/
    └── views/
        └── pages/
            ├── payroll/
            ├── settings/
            ├── loans/
            ├── advances/
            ├── overtime/
            └── bonuses/
```

---

## Database Schema

### Core Tables

#### 1. hrm_salary_components
Stores earnings and deductions definitions
- `name`, `type` (earning/deduction)
- `calculation_type` (fixed/percentage)
- `default_amount`, `default_percentage`
- `is_taxable`, `is_active`

#### 2. hrm_salary_structures
Salary templates/grades
- `name`, `description`
- `is_active`
- **Pivot**: `hrm_salary_structure_components` (with `amount`, `percentage` overrides)

#### 3. hrm_employee_salaries
Employee salary assignments
- `employee_id`, `salary_structure_id`
- `basic_salary`, `gross_salary`
- `effective_date`, `is_active`

#### 4. hrm_tax_slabs
Income tax configuration
- `gender` (male/female/other/all)
- `min_income`, `max_income`
- `tax_rate`, `fixed_deduction`

#### 5. hrm_payrolls
Monthly payroll records
- `employee_id`, `month`, `year`
- `basic_salary`, `allowances`, `bonuses`
- `deductions`, `tax`
- `gross_salary`, `net_salary`
- `status`, `payment_date`

#### 6. hrm_payroll_items
Detailed breakdown
- `payroll_id`, `salary_component_id`
- `component_name`, `type`, `amount`

### Loan Tables

#### 7. hrm_loan_types
- `name`, `max_amount`, `interest_rate`
- `interest_type` (flat/reducing)
- `max_tenure_months`

#### 8. hrm_employee_loans
- `employee_id`, `loan_type_id`
- `loan_amount`, `tenure_months`
- `monthly_installment`, `total_payable`
- `outstanding_balance`, `status`

#### 9. hrm_loan_installments
- `loan_id`, `installment_number`
- `installment_amount`, `due_date`
- `paid_date`, `status`

### Other Tables

- `hrm_employee_advances` & `hrm_advance_deductions`
- `hrm_ot_policies` & `hrm_employee_overtime`
- `hrm_bonus_types` & `hrm_employee_bonuses`
- `hrm_commission_schemes` & `hrm_employee_commissions`
- `hrm_gratuity_config` & `hrm_employee_gratuity`
- `hrm_final_settlements`
- `hrm_payroll_arrears`

---

## Installation & Setup

### Prerequisites

```bash
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js & NPM
```

### Step 1: Install Dependencies

```bash
composer install
npm install && npm run build
```

### Step 2: Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Update database credentials in `.env`:
```env
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

### Step 4: Seed Data

```bash
php artisan db:seed --class=App\Modules\HRM\Database\Seeders\LoanManagementSeeder
php artisan db:seed --class=App\Modules\HRM\Database\Seeders\AdvancedPayrollSeeder
```

### Step 5: Clear Caches

```bash
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

---

## Payroll Processing Journey

### Complete End-to-End Workflow

#### Phase 1: Setup (One-time)

1. **Configure Salary Components** (`/hrm/settings/salary-components`)
   - Create earnings (Basic, HRA, Medical, etc.)
   - Create deductions (PF, Insurance, etc.)
   - Set calculation types and defaults

2. **Create Salary Structures** (`/hrm/settings/salary-structures`)
   - Bundle components into grades
   - Set overrides if needed
   - Activate structures

3. **Configure Tax Slabs** (`/hrm/settings/tax-slabs`)
   - Define income ranges
   - Set tax rates by gender
   - Configure deductions

4. **Setup OT & Bonus Policies**
   - Create OT policies with multipliers
   - Define bonus types

#### Phase 2: Employee Onboarding

1. **Add Employee** (`/hrm/employees/create`)
   - Basic information
   - Department, designation, etc.

2. **Assign Salary** (`/hrm/employees/{id}/salary`)
   - Select salary structure
   - Enter basic salary
   - Set effective date
   - **Live Preview**: See breakdown instantly

#### Phase 3: Monthly Operations

1. **Record Overtime** (`/hrm/overtime`)
   - Add OT hours for employees
   - Auto-calculate amount
   - Approve OT

2. **Process Loans/Advances**
   - Approve pending requests
   - System auto-deducts from salary

3. **Add Bonuses** (`/hrm/bonuses`)
   - Festival bonuses, performance bonuses
   - Approve bonuses

#### Phase 4: Payroll Generation

1. **Run Payroll Wizard** (`/hrm/payroll/create`)
   - Select Month & Year
   - Click "Start Generation Process"

2. **System Auto-Calculates**:
   ```
   FOR each active employee:
     1. Fetch salary structure
     2. Calculate basic + allowances
     3. Add approved OT
     4. Add approved bonuses
     5. Calculate income tax
     6. Deduct loans/advances
     7. Deduct other deductions
     8. Generate net salary
     9. Create payroll record
   ```

3. **Review Generated Payroll** (`/hrm/payroll`)
   - View list of all payrolls
   - Check calculations
   - Download PDFs

#### Phase 5: Payment Processing

1. **Mark as Paid** (per payroll or bulk)
   - Updates status to "Paid"
   - Records payment date
   - Updates loan installments
   - Updates advance deductions
   - Links OT and bonus records

2. **Download/Email Salary Slips**
   - Professional PDF generation
   - Send to employees

---

## Module Documentation

### 1. Salary Components Module

**Route**: `/hrm/settings/salary-components`

**Features**:
- Tabbed interface (Earnings | Deductions)
- Offcanvas form for add/edit
- Real-time status toggle

**Usage**:
```php
// Create a component
SalaryComponent::create([
    'name' => 'House Rent Allowance',
    'type' => 'earning',
    'calculation_type' => 'percentage',
    'default_percentage' => 40.00,
    'is_taxable' => true,
]);
```

### 2. Salary Structures Module

**Route**: `/hrm/settings/salary-structures`

**Usage**:
```php
// Create structure
$structure = SalaryStructure::create([
    'name' => 'Grade A',
    'description' => 'For senior management'
]);

// Attach components with overrides
$structure->components()->attach($componentId, [
    'percentage' => 50.00, // Override default
]);
```

### 3. Payroll Module

**Route**: `/hrm/payroll`

**Key Methods**:

```php
// Auto-generate payroll
PayrollController::bulkGenerate($month, $year)

// Calculate tax automatically
protected function calculateIncomeTax($employee, $grossMonthly)

// Process payment
PayrollController::process($payroll)

// Download PDF
PayrollController::downloadPDF($payroll)
```

### 4. Loan Management

**Route**: `/hrm/loans`

**Workflow**:
1. Create loan request
2. Admin approves → Auto-generates installments
3. Payroll auto-deducts monthly
4. Track outstanding balance

**Usage**:
```php
// Approve loan
$loan->approve();
$loan->generateInstallments(); // Auto creates installment schedule

// Auto-deduction in payroll
$deduction = LoanInstallment::pending()
    ->where('employee_id', $employeeId)
    ->sum('installment_amount');
```

---

## API Reference

### Loan Calculation

```php
/**
 * Calculate loan installment
 * 
 * @param float $amount Loan amount
 * @param float $interestRate Interest rate (%)
 * @param string $interestType 'flat' or 'reducing'
 * @param int $tenureMonths Loan tenure
 * @return array
 */
function calculateLoan($amount, $interestRate, $interestType, $tenureMonths)
{
    $principal = $amount;
    $interest = 0;
    
    if ($interestType === 'flat') {
        $interest = ($amount * $interestRate * $tenureMonths) / (100 * 12);
    } else {
        $interest = ($amount * $interestRate * $tenureMonths) / (100 * 24);
    }
    
    $totalPayable = $principal + $interest;
    $monthlyInstallment = $totalPayable / $tenureMonths;
    
    return [
        'monthly_installment' => $monthlyInstallment,
        'total_interest' => $interest,
        'total_payable' => $totalPayable
    ];
}
```

### Tax Calculation

```php
/**
 * Calculate progressive income tax
 * 
 * @param Employee $employee
 * @param float $monthlyGross
 * @return float Monthly tax
 */
function calculateIncomeTax($employee, $monthlyGross)
{
    $annualIncome = $monthlyGross * 12;
    $gender = $employee->gender ?? 'all';
    
    $taxSlab = TaxSlab::where('gender', $gender)
        ->where('min_income', '<=', $annualIncome)
        ->where('max_income', '>=', $annualIncome)
        ->first();
    
    if (!$taxSlab) return 0;
    
    $taxableIncome = $annualIncome - $taxSlab->min_income;
    $annualTax = ($taxableIncome * $taxSlab->tax_rate) / 100;
    $annualTax += $taxSlab->fixed_deduction ?? 0;
    
    return $annualTax / 12; // Monthly
}
```

---

## User Guide

### For HR Administrators

#### Setting Up Payroll

1. Navigate to **HRM > Settings > Settings Dashboard**
2. Configure components, structures, and tax slabs
3. Assign salaries to employees
4. Setup OT policies and bonus types

#### Running Monthly Payroll

1. Go to **HRM > Payroll**
2. Click "Run Payroll"
3. Select Month and Year
4. Click "Start Generation Process"
5. Wait for completion
6. Review generated payrolls
7. Download PDFs for employees
8. Mark as "Paid" after bank transfer

#### Managing Loans

1. Go to **HRM > Loans**
2. Review pending applications
3. Approve/Reject as needed
4. System handles auto-deduction

### For Employees (Self-Service)

1. View salary structure
2. Check payslip history
3. Download PDF payslips
4. Apply for loans/advances
5. View loan balance

---

## Developer Guide

### Adding New Salary Components

```php
// In a migration or seeder
SalaryComponent::create([
    'name' => 'Special Allowance',
    'type' => 'earning',
    'calculation_type' => 'fixed',
    'default_amount' => 5000,
    'is_taxable' => true,
]);
```

### Custom Payroll Calculation

Extend `PayrollController`:

```php
protected function customCalculation($employee, $month, $year)
{
    // Your custom logic
    $customAmount = 0;
    
    // Example: Weekend allowance
    $weekends = $this->countWeekends($month, $year);
    $customAmount = $weekends * 500;
    
    return $customAmount;
}
```

### Adding New Tax Rules

```php
// Progressive tax on special income
TaxSlab::create([
    'gender' => 'all',
    'min_income' => 500000,
    'max_income' => 1000000,
    'tax_rate' => 15.00,
    'fixed_deduction' => 25000,
]);
```

---

## Permissions

### Complete Permission List

#### Payroll
- `hrm.payroll.view`
- `hrm.payroll.create`
- `hrm.payroll.process`
- `hrm.payroll.delete`

#### Loans
- `hrm.loan-types.view/create/update/delete`
- `hrm.loans.view/create/update/delete/approve/disburse`

#### Advances
- `hrm.advances.view/create/update/delete/approve`

#### Overtime
- `hrm.ot-policies.view/create/update/delete`
- `hrm.overtime.view/create/update/delete/approve`

#### Bonuses
- `hrm.bonus-types.view/create/update/delete`
- `hrm.bonuses.view/create/update/delete/approve`

---

## Troubleshooting

### Common Issues

**Issue**: Payroll generation fails
**Solution**: Check if employees have assigned salaries

**Issue**: Tax not calculating
**Solution**: Ensure tax slabs are configured for employee's gender

**Issue**: Loan not deducting
**Solution**: Verify loan status is "active" and installments exist

**Issue**: PDF not downloading
**Solution**: Check DomPDF installation: `composer require barryvdh/laravel-dompdf`

---

## Support & Contributing

For issues and feature requests, please contact the development team.

---

## License

Proprietary - All Rights Reserved

---

## Changelog

### Version 1.0.0 (2025-12-10)
- ✅ Complete payroll engine
- ✅ Loan & advance management
- ✅ Overtime calculation
- ✅ Bonus management
- ✅ Auto tax calculation
- ✅ PDF salary slips
- ✅ 33+ permissions
- ✅ Comprehensive seeding

---

**Built with ❤️ using Laravel**
