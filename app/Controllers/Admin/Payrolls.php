<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use App\Models\EmployeesModel;
use App\Models\DeductionsModel;
use App\Models\EmployeeLoansModel;
use App\Models\PayrollsModel;
use App\Models\LoanPaymentsModel;

class Payrolls extends BaseController
{
    protected $EmployeesModel;
    protected $DeductionsModel;
    protected $EmployeeLoansModel;
    protected $PayrollsModel;
    protected $LoanPaymentsModel;

    public function __construct()
    {
        $this->EmployeesModel = new EmployeesModel();
        $this->DeductionsModel = new DeductionsModel();
        $this->EmployeeLoansModel = new EmployeeLoansModel();
        $this->PayrollsModel = new PayrollsModel();
        $this->LoanPaymentsModel = new LoanPaymentsModel;
    }

    public function index()
    {
        $payrollResults = $this->PayrollsModel->getPayrollResults();

        return view('admin/payrolls/payrolls', [
            'payrollResults' => $payrollResults,
            'title' => 'Generar Nómina'
        ]);
    }

    public function generatePayroll()
    {
        $generate = $this->request->getPost('generate');
        if (!isset($generate)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Las fechas de inicio y fin son requeridas.']);
        }
        $employees = $this->EmployeesModel->where('status', 'Active')->findAll();
        if (empty($employees)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se encontraron empleados activos.']);
        }

        $payrollResults = [];

        foreach ($employees as $employee) {
            $grossSalary = $employee['salary'];
            $loanDeductions = 0;
            $loans = $this->EmployeeLoansModel->where('employee_id', $employee['id'])
                ->where('status', 'pending')
                ->findAll();

            foreach ($loans as $loan) {
                $loanDeductions += $loan['quotas_of'];
            }

            $deductionsFixed = $this->DeductionsModel->findAll();
            $deductionDetails = [];

            $deductFix = 0;
            foreach ($deductionsFixed as $fixed) {
                $deductionPercentage = $fixed['amount'];
                $deductionAmount = ($deductionPercentage / 100) * $grossSalary;
                $deductFix += $deductionAmount;

                $deductionDetails[] = [
                    'description' => $fixed['description'],
                    'amount' => $deductionAmount
                ];
            }
            $netSalary = $grossSalary - $loanDeductions - $deductFix;

            $payrollResults[] = [
                'employee_id' => $employee['id'],
                'employee' => $employee['first_name'] . ' ' . $employee['last_name'],
                'gross_salary' => $grossSalary,
                'deductions' => $deductionDetails,
                'loan_deductions' => $loanDeductions,
                'net_salary' => $netSalary
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'payrollResults' => $payrollResults,
        ]);
    }

    public function savePayroll()
    {
        $selectedEmployees = $this->request->getPost('selected_employees');
        $bonuses = $this->request->getPost('bonus');
        $payrollResults = json_decode($this->request->getPost('payrollResults'), true);

        if (!is_array($payrollResults)) {
            $payrollResults = [];
        }

        if (empty($selectedEmployees)) {
            return $this->response->setJSON(['error' => 'Debe seleccionar al menos un empleado para guardar la nómina.']);
        }

        $saved = false;

        $this->db->transBegin();

        foreach ($selectedEmployees as $employeeId) {

            $employeeData = array_filter($payrollResults, function ($result) use ($employeeId) {
                return $result['employee_id'] == $employeeId;
            });
            $employeeData = reset($employeeData);

            if ($employeeData) {

                $grossSalary = $employeeData['gross_salary'];
                $loanDeductions = $employeeData['loan_deductions'] ?? 0;

                $deductFix = 0;
                if (isset($employeeData['deductions']) && is_array($employeeData['deductions'])) {
                    foreach ($employeeData['deductions'] as $deduction) {
                        $deductFix += $deduction['amount'];
                    }
                }

                $bonus = isset($bonuses[$employeeId]) ? (float)$bonuses[$employeeId] : 0;

                $netSalary = $grossSalary - $loanDeductions - $deductFix + $bonus;

                $payrollData = [
                    'employee_id' => $employeeId,
                    'payroll_date' => date('Y-m-d'),
                    'gross_salary' => $grossSalary,
                    'loan_deductions' => $loanDeductions,
                    'deductfix' => $deductFix,
                    'bonus' => $bonus,
                    'net_salary' => $netSalary
                ];

                if ($this->PayrollsModel->save($payrollData)) {
                    $saved = true;

                    if ($loanDeductions > 0) {

                        $pendingLoans = $this->EmployeeLoansModel->where([
                            'employee_id' => $employeeId,
                            'status' => 'pending'
                        ])->findAll();

                        $loanDetails = [];
                        foreach ($pendingLoans as $loan) {
                            $loanDetails[] = [
                                'loan_id' => $loan['id'],
                                'amount' => $loan['quotas_of']
                            ];
                        }

                        $employeeData['loan_details'] = $loanDetails;

                        if (!empty($pendingLoans)) {
                            log_message('info', 'Se encontraron préstamos pendientes para el empleado ID: ' . $employeeId);
                        } else {
                            log_message('info', 'No se encontraron préstamos pendientes para el empleado ID: ' . $employeeId);
                        }

                        foreach ($pendingLoans as $loan) {

                            if (isset($employeeData['loan_details']) && is_array($employeeData['loan_details'])) {
                                foreach ($employeeData['loan_details'] as $loanDetail) {
                                    if ($loanDetail['loan_id'] == $loan['id'] && $loanDetail['amount'] > 0) {
                                        $loanPaymentData = [
                                            'loan_id' => $loan['id'],
                                            'payment_date' => date('Y-m-d'),
                                            'amount' => $loanDetail['amount'],
                                            'description' => 'Pago de préstamo deducido de la nómina',
                                            'status' => 'paid',
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')
                                        ];

                                        if (!$this->LoanPaymentsModel->save($loanPaymentData)) {

                                            log_message('error', 'Error al guardar el pago para el préstamo ID: ' . $loan['id'] . '. Error: ' . json_encode($this->LoanPaymentsModel->errors()));
                                            $this->db->transRollback();
                                            return $this->response->setJSON(['error' => 'Error al guardar el pago de préstamo.']);
                                        } else {
                                            log_message('info', 'Pago guardado para el préstamo ID: ' . $loan['id']);
                                        }
                                    }
                                }
                            } else {
                                log_message('error', 'No se encontraron detalles del préstamo para el empleado ID: ' . $employeeId);
                            }
                        }
                    }
                } else {

                    log_message('error', 'Error al guardar la nómina para el empleado ID: ' . $employeeId . '. Error: ' . json_encode($this->PayrollsModel->errors()));
                    $this->db->transRollback();
                    return $this->response->setJSON(['error' => 'Error al guardar la nómina.']);
                }
            }
        }

        if ($saved) {
            $this->db->transCommit();
            return $this->response->setJSON(['success' => 'La nómina ha sido guardada exitosamente.']);
        } else {

            $this->db->transRollback();
            return $this->response->setJSON(['error' => 'No se pudo guardar la nómina. Por favor, intente nuevamente.']);
        }
    }

    public function calculateTotalPayroll()
    {

        $payrollResults = $this->request->getPost('payrollResults');
        $payrollResults = json_decode($payrollResults, true);

        $totalPayroll = 0;

        foreach ($payrollResults as $result) {
            $netSalary = floatval($result['net_salary']);
            $bonus = floatval($result['bonus']);

            $totalPayroll += $netSalary + $bonus;
        }

        return $this->response->setJSON([
            'totalPayroll' => number_format($totalPayroll, 2)
        ]);
    }
}