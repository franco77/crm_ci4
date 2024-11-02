<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PayrollsListModel;
use App\Models\DeductionsModel;

class PayrollsList extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->PayrollsListModel = new PayrollsListModel;
      $this->DeductionsModel = new DeductionsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Payrolls',
         'host' => site_url('admin/payrollslist/')
      ];

      echo view('admin/payrolls/list', $data);
   }

   public function data()
   {
      try {
         $request = esc($this->request->getPost());
         $search = $request['search']['value'];
         $limit = $request['length'];
         $start = $request['start'];

         $orderIndex = $request['order'][0]['column'];
         $orderFields = $request['columns'][$orderIndex]['data'];
         $orderDir = $request['order'][0]['dir'];

         $recordsTotal = $this->PayrollsListModel->countTotal();
         $data = $this->PayrollsListModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->PayrollsListModel->countFilter($search);

         $callback = [
            'draw' => $request['draw'],
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
         ];

         return $this->respond($callback);
      } catch (\Exception $e) {

         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function new()
   {
      $data = [];

      echo view('admin/payrolls/form', $data);
   }

   public function create()
   {
      $request = [
         'employee_id' => $this->request->getPost('employee_id'),
         'payroll_date' => $this->request->getPost('payroll_date'),
         'gross_salary' => $this->request->getPost('gross_salary'),
         'loan_deductions' => $this->request->getPost('loan_deductions'),
         'deductfix' => $this->request->getPost('deductfix'),
         'bonus' => $this->request->getPost('bonus'),
         'net_salary' => $this->request->getPost('net_salary'),
      ];
      $this->rules();

      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            $insert = $this->PayrollsListModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->PayrollsListModel->errors());
            }
         } catch (\Exception $e) {

            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }



   function printVoucher($id = null)
   {
      if ($id === null) {
         throw new \InvalidArgumentException('ID no proporcionado.');
      }

      $payrollDetails = $this->PayrollsListModel->getPayrollsDetails($id);
      $deductions = $this->DeductionsModel->findAll();
      if (!$payrollDetails) {
         throw new \RuntimeException('Pago no encontrado.');
      }
      $viewData = [
         'data' => $payrollDetails,
         'deductions' => $deductions,
      ];
      $options = new \Dompdf\Options();
      $options->set('isRemoteEnabled', true);
      $dompdf = new \Dompdf\Dompdf($options);
      $html = view('admin/payrolls/print', $viewData);
      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'landscape');
      $dompdf->render();
      $dompdf->stream('recibo_nomina_' . $id . '.pdf', ['Attachment' => 0]);
   }


   public function show($id = null)
   {
      try {

         $dataDe = $this->DeductionsModel->findAll();

         $data = $this->PayrollsListModel
            ->select('p.id, p.employee_id, p.payroll_date, p.gross_salary, p.loan_deductions, 
                            p.deductfix, p.bonus, p.net_salary, p.created_at, p.updated_at,
                            e.id AS employee_id, e.ic, e.id_position, e.first_name, e.last_name')
            ->from('payrolls p')
            ->join('employees e', 'e.id = p.employee_id')
            ->where('p.id', $id)
            ->get()
            ->getRowArray();

         if ($data) {

            $table = '<table class="table  table-bordered table-sm">';
            $table .= '<tr><th>Employee ID</th><td>' . esc($data['employee_id']) . '</td></tr>';
            $table .= '<tr><th>Name</th><td>' . esc($data['first_name'] . ' ' . $data['last_name']) . '</td></tr>';
            $table .= '<tr><th>Payroll Date</th><td>' . $data['payroll_date'] . '</td></tr>';
            $table .= '<tr><th>Gross Salary</th><td>' . $data['gross_salary'] . '</td></tr>';
            $table .= '<tr><th>Loan Deductions</th><td class="table-danger">' . $data['loan_deductions'] . '</td></tr>';

            $totalDeductions = 0;

            foreach ($dataDe as $dD) {
               $table .= '<tr><th>' . esc($dD['description']) . '</th>';

               if ($data['gross_salary'] > 0) {

                  $montoDeduccion = ($dD['amount'] / 100) * $data['gross_salary'];

                  $totalDeductions += $montoDeduccion;

                  $table .= '<td class="table-danger">' . $montoDeduccion . '</td></tr>';
               } else {
                  $table .= '<td>0.00 (0%)</td></tr>';
               }
            }

            $table .= '<tr><th>Total Deductions</th><td class="table-info">' . $totalDeductions . '</td></tr>';

            $table .= '<tr><th>Bonus</th><td>' . $data['bonus'] . '</td></tr>';
            $table .= '<tr><th>Net Salary</th><td>' . $data['net_salary'] . '</td></tr>';
            $table .= '<tr><th>Created At</th><td>' . $data['created_at'] . '</td></tr>';
            $table .= '<tr><th>Updated At</th><td>' . $data['updated_at'] . '</td></tr>';
            $table .= '</table>';

            return $this->respond($table);
         } else {

            return $this->failNotFound('No payroll record found with ID: ' . esc($id));
         }
      } catch (\Exception $e) {

         log_message('error', $e->getMessage());
         return $this->failServerError('An unexpected error occurred. Please contact the administrator.');
      }
   }

   public function edit($id = null)
   {
      try {
         $data = $this->PayrollsListModel->find($id);

         if ($data) {
            $data = [
               'data_payrolls' => $data
            ];

            echo view('admin/payrolls/form', $data);
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {

         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function update($id = null)
   {
      $request = [
         'employee_id' => $this->request->getPost('employee_id'),
         'payroll_date' => $this->request->getPost('payroll_date'),
         'gross_salary' => $this->request->getPost('gross_salary'),
         'loan_deductions' => $this->request->getPost('loan_deductions'),
         'deductfix' => $this->request->getPost('deductfix'),
         'bonus' => $this->request->getPost('bonus'),
         'net_salary' => $this->request->getPost('net_salary'),
      ];
      $this->rules();

      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            $update = $this->PayrollsListModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->PayrollsListModel->errors());
            }
         } catch (\Exception $e) {

            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function delete($id = null)
   {
      try {
         $data = $this->PayrollsListModel->find($id);
         if ($data) {
            $this->PayrollsListModel->delete($id);
            return $this->respondDeleted([
               'status' => 200,
               'message' => 'Data deleted.'
            ]);
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {

         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   private function rules()
   {
      $this->validation->setRules([

         'payroll_date' => [
            'label' => 'Payroll Date',
            'rules' => 'required|valid_date[Y-m-d]'
         ],
         'gross_salary' => [
            'label' => 'Gross Salary',
            'rules' => 'required|decimal|max_length[12]'
         ],
         'loan_deductions' => [
            'label' => 'Loan Deductions',
            'rules' => 'required|decimal|max_length[12]'
         ],
         'deductfix' => [
            'label' => 'Deductfix',
            'rules' => 'required|string'
         ],
         'bonus' => [
            'label' => 'Bonus',
            'rules' => 'required|decimal|max_length[9]'
         ],
         'net_salary' => [
            'label' => 'Net Salary',
            'rules' => 'required|decimal|max_length[10]'
         ]
      ]);
   }
}