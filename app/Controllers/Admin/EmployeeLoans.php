<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\EmployeeLoansModel;
use App\Models\EmployeesModel;

class EmployeeLoans extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->EmployeeLoansModel = new EmployeeLoansModel;
      $this->EmployeesModel = new EmployeesModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Employee Loans',
         'host' => site_url('admin/employeeloans/')
      ];

      echo view('admin/employee_loans/list', $data);
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

         $recordsTotal = $this->EmployeeLoansModel->countTotal();
         $data = $this->EmployeeLoansModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->EmployeeLoansModel->countFilter($search);

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
      $data = [
         'data_employees' => $this->EmployeesModel->findAll(),
      ];

      echo view('admin/employee_loans/form', $data);
   }

   public function create()
   {
      $request = [
         'employee_id' => $this->request->getPost('employee_id'),
         'amount' => $this->request->getPost('amount'),
         'total_quotas' => $this->request->getPost('total_quotas'),
         'quotas_of' => $this->request->getPost('quotas_of'),
         'start_date' => $this->request->getPost('start_date'),
         'end_date' => $this->request->getPost('end_date'),
         'type' => $this->request->getPost('type'),
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
            $insert = $this->EmployeeLoansModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->EmployeeLoansModel->errors());
            }
         } catch (\Exception $e) {

            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function show($id = null)
   {
      try {
         $data = $this->EmployeeLoansModel->select('
         employee_loans.id as loan_id,
         employee_loans.employee_id,
         employee_loans.amount,
         employee_loans.total_quotas,
         employee_loans.quotas_of,
         employee_loans.start_date,
         employee_loans.end_date,
         employee_loans.type,
         employee_loans.status,
         employee_loans.description,
         employee_loans.created_at,
         employee_loans.updated_at,
         employees.id as employee_id,
         employees.ic,
         employees.first_name,
         employees.last_name
     ')
            ->join('employees', 'employees.id = employee_loans.employee_id')
            ->find($id);
         if ($data) {

            $table = '<table class="table table-striped table-bordered table-sm">';
            $table .= '<tr><th style="width: 25%;">Employee</th><td>' . $data['first_name'] . ' ' . $data['last_name'] . '</td></tr>';
            $table .= '<tr><th>Amount</th><td>' . $data['amount'] . '</td></tr>';
            $table .= '<tr><th>Nuemero De Quotas</th><td>' . $data['total_quotas'] . '</td></tr>';
            $table .= '<tr><th>Cuotas De</th><td>' . $data['quotas_of'] . '</td></tr>';
            $table .= '<tr><th>Start Date</th><td>' . $data['start_date'] . '</td></tr>';
            $table .= '<tr><th>End Date</th><td>' . $data['end_date'] . '</td></tr>';
            $table .= '<tr><th>Type</th><td>' . $data['type'] . '</td></tr>';
            $table .= '<tr><th>Description</th><td>' . $data['description'] . '</td></tr>';

            if ($data['status'] == 'paid') {
               $table .= '<tr><th>Status</th><td><div class="ps-4 bg-success text-white">Paid</div></td></tr>';
            } elseif ($data['status'] == 'pending') {
               $table .= '<tr><th>Status</th><td><div class="ps-4 bg-warning text-dark">Pending</div></td></tr>';
            } else {
               $table .= '<tr><th>Status</th><td><div class="ps-4 bg-danger text-white">Canceled</div></td></tr>';
            }

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
         $data = $this->EmployeeLoansModel->find($id);

         if ($data) {
            $data = [
               'data_employees' => $this->EmployeesModel->findAll(),
               'data_employee_loans' => $data
            ];

            echo view('admin/employee_loans/form', $data);
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
         'amount' => $this->request->getPost('amount'),
         'total_quotas' => $this->request->getPost('total_quotas'),
         'quotas_of' => $this->request->getPost('quotas_of'),
         'start_date' => $this->request->getPost('start_date'),
         'end_date' => $this->request->getPost('end_date'),
         'type' => $this->request->getPost('type'),
         'description' => $this->request->getPost('description'),
         'status' => $this->request->getPost('status'),
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
            $update = $this->EmployeeLoansModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->EmployeeLoansModel->errors());
            }
         } catch (\Exception $e) {

            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function delete($id = null)
   {
      try {
         $data = $this->EmployeeLoansModel->find($id);
         if ($data) {
            $this->EmployeeLoansModel->delete($id);
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
         'employee_id' => [
            'label' => lang('app.employee_loans.employee_id'),
            'rules' => 'required|numeric'
         ],
         'amount' => [
            'label' => lang('app.employee_loans.amount'),
            'rules' => 'required|decimal|max_length[10]'
         ],
         'total_quotas' => [
            'label' => lang('app.employee_loans.total_quotas'),
            'rules' => 'required'
         ],
         'quotas_of' => [
            'label' => lang('app.employee_loans.quotas_of'),
            'rules' => 'required'
         ],
         'start_date' => [
            'label' => lang('app.employee_loans.start_date'),
            'rules' => 'required|valid_date[Y-m-d]'
         ],
         'end_date' => [
            'label' => lang('app.employee_loans.end_date'),
            'rules' => 'required|valid_date[Y-m-d]'
         ],
         'type' => [
            'label' => lang('app.employee_loans.type'),
            'rules' => 'required|string|max_length[50]'
         ],
         'description' => [
            'label' => lang('app.employee_loans.description'),
            'rules' => 'required|string'
         ],
         'status' => [
            'label' => lang('app.employee_loans.status'),
            'rules' => 'required|in_list[pending, paid, canceled]'
         ]
      ]);
   }
}