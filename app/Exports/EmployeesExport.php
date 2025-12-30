<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Auth;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    protected $positions;
    protected $departments;
    protected $companies;

    public function __construct()
    {
        $this->positions = DB::connection("intra_payroll")
            ->table("lib_position")
            ->where("is_active", 1)
            ->get()
            ->keyBy("id");

        $this->departments = DB::connection("intra_payroll")
            ->table("tbl_department")
            ->where("is_active", 1)
            ->get()
            ->keyBy("id");
    }

    public function collection()
    {
        $role_id = Auth::user()->role_id;
        $hr_group = ['group_a', 'group_b'];
        if ($role_id === 4) { // HR Group A
            $hr_group = ['group_a'];
        } elseif ($role_id === 5) { // HR Group B
            $hr_group = ['group_b'];
        }

        return DB::connection("intra_payroll")->table("tbl_employee")
            ->where('is_active', 1) 
            ->whereIn('hr_group',$hr_group)
            ->select([
                'emp_code', 
                'last_name', 
                'first_name', 
                'middle_name', 
                'ext_name',
                'contact_no', 
                'sss_number', 
                'philhealth_number', 
                'hdmf_number',
                'tin_number', 
                'fix_sss',
                'fix_divisor',
                'fix_philhealth',
                'fix_hdmf',
                'fix_tax_rate',
                'position_id', 
                'department', 
                'start_date', 
                'date_of_birth',
                'address', 
                'salary_type', 
                'salary_rate', 
                'is_mwe', 
                'is_active'
            ])
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->emp_code,
            $row->last_name,
            $row->first_name,
            $row->middle_name,
            $row->ext_name,
            $row->contact_no,
            $row->sss_number,
            $row->philhealth_number,
            $row->hdmf_number,
            $row->tin_number,
            $row->fix_divisor ?? '',
            $row->fix_sss ?? '',
            $row->fix_philhealth ?? '',
            $row->fix_hdmf ?? '',
            $row->fix_tax_rate ?? '',
            $this->positions[$row->position_id]->name ?? '-',
            $this->departments[$row->department]->department ?? '-',
            $row->start_date ? date('Y-m-d', strtotime($row->start_date)) : '',
            $row->date_of_birth ? date('Y-m-d', strtotime($row->date_of_birth)) : '',
            $row->address,
            $row->salary_type,
            $row->salary_rate,
            $row->is_mwe == 1 ? 'Yes' : 'No',
            $row->is_active == 1 ? 'Active' : 'Inactive'
        ];
    }

    public function headings(): array
    {
        return [
            'Company ID Number', 
            'Last Name', 
            'First Name', 
            'Middle Name', 
            'Extension Name',
            'Contact Number', 
            'SSS No.', 
            'PhilHealth No.', 
            'HDMF No.', 
            'TIN No.',
            'Fix Divisor',
            'Fix SSS',
            'Fix Philhealth',
            'Fix HDMF',
            'Fix Tax Rate.',
            'Position', 
            'Department', 
            'Start Date', 
            'Date of Birth', 
            'Address',
            'Salary Type', 
            'Salary Rate', 
            'Minimum Wage Earner', 
            'Status'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'Q' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Start Date
            'R' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Date of Birth
        ];
    }
}


