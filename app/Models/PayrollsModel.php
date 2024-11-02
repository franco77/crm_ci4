<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollsModel extends Model
{
    protected $table = 'payrolls';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'employee_id',
        'payroll_date',
        'gross_salary',
        'loan_deductions',
        'deductfix',
        'bonus',
        'net_salary'
    ];

    protected $useTimestamps = true;

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPayrollResults()
    {

        $employeesModel = new EmployeesModel();
        $employees = $employeesModel->where('status', 'Active')->findAll();

        if (empty($employees)) {
            return [];
        }

        $deductionsModel = new DeductionsModel();
        $employeeLoansModel = new EmployeeLoansModel();

        $payrollResults = [];

        foreach ($employees as $employee) {

            $grossSalary = $employee['salary'];

            $loanDeductions = 0;
            $loans = $employeeLoansModel->where('employee_id', $employee['id'])
                ->where('status', 'pending')
                ->findAll();

            foreach ($loans as $loan) {
                $loanDeductions += $loan['quotas_of'];
            }

            $deductionsFixed = $deductionsModel->findAll();
            $deductionDetails = [];
            $deductFix = 0;

            foreach ($deductionsFixed as $fixed) {

                $deductionPercentage = floatval($fixed['amount']);

                if ($deductionPercentage > 0) {
                    $deductionAmount = ($deductionPercentage / 100) * $grossSalary;
                    $deductFix += $deductionAmount;

                    $deductionDetails[] = [
                        'description' => $fixed['description'],
                        'amount' => round($deductionAmount, 2)
                    ];
                }
            }

            $netSalary = round($grossSalary - $loanDeductions - $deductFix, 2);

            $payrollResults[] = [
                'employee_id' => $employee['id'],
                'employee' => $employee['first_name'] . ' ' . $employee['last_name'],
                'gross_salary' => $grossSalary,
                'deductions' => $deductionDetails,
                'loan_deductions' => round($loanDeductions, 2),
                'net_salary' => $netSalary
            ];
        }

        return $payrollResults;
    }
}
