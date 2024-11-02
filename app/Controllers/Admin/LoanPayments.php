<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\LoanPaymentsModel;
use App\Models\EmployeeLoansModel;

class LoanPayments extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->LoanPaymentsModel = new LoanPaymentsModel;
      $this->EmployeeLoansModel = new EmployeeLoansModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Loan Payments',
         'host' => site_url('admin/loanpayments/')
      ];

      echo view('admin/loan_payments/list', $data);
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

         $recordsTotal = $this->LoanPaymentsModel->countTotal();
         $data = $this->LoanPaymentsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->LoanPaymentsModel->countFilter($search);

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

   public function getLoanPayData()
   {
      if ($this->request->isAJAX()) {
         $loan_id = $this->request->getPost('loan_id');

         $employee = $this->LoanPaymentsModel->getLoanPayDetails($loan_id);

         if ($employee) {

            $response = [
               'status' => 'success',
               'data' => $employee
            ];
         } else {

            $response = [
               'status' => 'error',
               'message' => 'No se encontraron datos para el préstamo seleccionado.'
            ];
         }
         return $this->response->setJSON($response);
      }

      return redirect()->to('/');
   }

   public function new()
   {
      $data = [
         'title' => 'Hola',
         'data_employee_loans' => $this->LoanPaymentsModel->getLoanDetails(),
      ];

      echo view('admin/loan_payments/form', $data);
   }

   public function create()
   {
      $request = [
         'loan_id' => $this->request->getPost('loan_id'),
         'payment_date' => $this->request->getPost('payment_date'),
         'amount' => $this->request->getPost('amount'),
         'description' => $this->request->getPost('description'),
         'status' => $this->request->getPost('status'),
         'created_at' => date('Y-m-d h:i:sa'),
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
            $insert = $this->LoanPaymentsModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->LoanPaymentsModel->errors());
            }
         } catch (\Exception $e) {

            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function show($id = null)
   {
      try {
         $data = $this->LoanPaymentsModel->select([
            'lp.id',
            'lp.loan_id',
            'lp.payment_date',
            'lp.amount AS payment_amount',
            'lp.description',
            'lp.status',
            'lp.created_at',
            'lp.updated_at',
            'el.id AS empId',
            'el.employee_id',
            'el.total_quotas',
            'el.quotas_of',
            'e.id AS employee_id',
            'e.ic',
            'e.id_position',
            'e.first_name',
            'e.last_name',
         ])
            ->from('loan_payments AS lp')
            ->join('employee_loans AS el', 'el.id = lp.loan_id')
            ->join('employees e', 'e.id = el.employee_id')
            ->find($id);
         if ($data) {

            $table = '<table class="table table-striped table-bordered table-sm">';
            $table .= '<tr><th style="width: 25%;">Loan #</th><td>' . $data['loan_id'] . '</td></tr>';
            $table .= '<tr><th style="width: 25%;">Ic Empleado</th><td>' . $data['ic'] . '</td></tr>';
            $table .= '<tr><th style="width: 25%;">Nombre Empleado</th><td>' . $data['first_name'] . ' ' . $data['last_name'] . '</td></tr>';
            $table .= '<tr><th>Fecha Abono</th><td>' . $data['payment_date'] . '</td></tr>';
            $table .= '<tr><th>Amount</th><td>' . $data['payment_amount'] . '</td></tr>';
            $table .= '<tr><th>Description</th><td>' . $data['description'] . '</td></tr>';
            $table .= '<tr><th>Status</th><td>' . $data['status'] . '</td></tr>';
            $table .= '<tr><th>Created At</th><td>' . $data['created_at'] . '</td></tr>';
            $table .= '<tr><th>Updated At</th><td>' . $data['updated_at'] . '</td></tr>';
            $table .= '</table>';
            return $this->respond($table);;
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {

         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function edit($id = null)
   {
      try {
         $data = $this->LoanPaymentsModel->find($id);

         if ($data) {
            $data = [
               'data_employee_loans' => $this->EmployeeLoansModel->findAll(),
               'data_loan_payments' => $data
            ];

            echo view('admin/loan_payments/form', $data);
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
         'loan_id' => $this->request->getPost('loan_id'),
         'payment_date' => $this->request->getPost('payment_date'),
         'amount' => $this->request->getPost('amount'),
         'description' => $this->request->getPost('description'),
         'status' => $this->request->getPost('status'),
         'created_at' => $this->request->getPost('created_at'),
         'updated_at' => $this->request->getPost('updated_at'),
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
            $update = $this->LoanPaymentsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->LoanPaymentsModel->errors());
            }
         } catch (\Exception $e) {

            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function delete($id = null)
   {
      try {
         $data = $this->LoanPaymentsModel->find($id);
         if ($data) {
            $this->LoanPaymentsModel->delete($id);
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
         'loan_id' => [
            'label' => 'Loan Id',
            'rules' => 'required|numeric'
         ],
         'payment_date' => [
            'label' => 'Payment Date',
            'rules' => 'required|valid_date[Y-m-d]'
         ],
         'amount' => [
            'label' => 'Amount',
            'rules' => 'required|decimal|max_length[10]'
         ],
         'description' => [
            'label' => 'Description',
            'rules' => 'required|string'
         ],
         'status' => [
            'label' => 'Status',
            'rules' => 'required|in_list[pending, paid, canceled]'
         ]
      ]);
   }
}
