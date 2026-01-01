<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HRDashboardExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new HeadcountSheet($this->data['headcount']),
            new AttritionSheet($this->data['attrition']),
            new DiversitySheet($this->data['diversity']),
            new DepartmentSheet($this->data['departments']),
            new TenureSheet($this->data['tenure']),
        ];
    }
}

class HeadcountSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect([
            ['Total Employees', $this->data['total']],
            ['Active', $this->data['active']],
            ['Confirmed', $this->data['confirmed']],
            ['Probation', $this->data['probation']],
            ['Notice Period', $this->data['notice_period']],
            ['On Leave', $this->data['on_leave']],
        ]);
    }

    public function headings(): array
    {
        return ['Metric', 'Count'];
    }

    public function title(): string
    {
        return 'Headcount';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class AttritionSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect([
            ['Total Exits', $this->data['total_exits']],
            ['Resignations', $this->data['resignations']],
            ['Terminations', $this->data['terminations']],
            ['Retirements', $this->data['retirements']],
            ['Attrition Rate (%)', $this->data['attrition_rate']],
        ]);
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function title(): string
    {
        return 'Attrition';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class DiversitySheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = [];
        
        // Gender
        $rows[] = ['Gender Distribution', ''];
        foreach ($this->data['gender'] as $gender => $count) {
            $rows[] = [$gender, $count];
        }
        
        $rows[] = ['', ''];
        
        // Age
        $rows[] = ['Age Distribution', ''];
        foreach ($this->data['age_ranges'] as $range => $count) {
            $rows[] = [$range, $count];
        }
        
        return collect($rows);
    }

    public function headings(): array
    {
        return ['Category', 'Count'];
    }

    public function title(): string
    {
        return 'Diversity';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class DepartmentSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function($dept) {
            return [$dept['name'], $dept['count']];
        });
    }

    public function headings(): array
    {
        return ['Department', 'Employee Count'];
    }

    public function title(): string
    {
        return 'Departments';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class TenureSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = [
            ['Average Tenure (years)', $this->data['average']],
            ['', ''],
            ['Tenure Distribution', ''],
        ];
        
        foreach ($this->data['ranges'] as $range => $count) {
            $rows[] = [$range, $count];
        }
        
        return collect($rows);
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function title(): string
    {
        return 'Tenure';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
