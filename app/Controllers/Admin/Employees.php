<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\EmployeesModel;
use App\Models\PositionsModel;

class Employees extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->EmployeesModel = new EmployeesModel;
      $this->PositionsModel = new PositionsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Employees',
         'host' => site_url('admin/employees/')
      ];

      echo view('admin/employees/list', $data);
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

         $recordsTotal = $this->EmployeesModel->countTotal();
         $data = $this->EmployeesModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->EmployeesModel->countFilter($search);

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
         'data_positions' => $this->PositionsModel->findAll(),
      ];

      echo view('admin/employees/form', $data);
   }

   public function create()
   {
      $request = [
         'ic' => $this->request->getPost('ic'),
         'id_position' => $this->request->getPost('id_position'),
         'first_name' => $this->request->getPost('first_name'),
         'last_name' => $this->request->getPost('last_name'),
         'hire_date' => $this->request->getPost('hire_date'),
         'salary' => $this->request->getPost('salary'),
         'email' => $this->request->getPost('email'),
         'phone_number' => $this->request->getPost('phone_number'),
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
            $insert = $this->EmployeesModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->EmployeesModel->errors());
            }
         } catch (\Exception $e) {

            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function getEmployeeData()
   {
      $employee_id = $this->request->getPost('employee_id');

      if (!$employee_id) {
         return $this->response->setJSON(['error' => 'ID de empleado no proporcionado']);
      }

      $employee = $this->EmployeesModel->getEmployee($employee_id);

      if (!$employee) {
         return $this->response->setJSON(['error' => 'Empleado no encontrado']);
      }
      $data = [
         'name' => $employee->first_name . ' ' . $employee->last_name,
         'ic' => $employee->ic,
         'email' => $employee->email,
         'salary' => $employee->salary,
         'num_pending_loans' => $employee->num_pending_loans,
         'totalAmount' => $employee->totalAmount,
      ];

      return $this->response->setJSON($data);
   }

   public function show($id = null)
   {
      try {
         $data = $this->EmployeesModel->select('e.id, e.ic, e.id_position, e.first_name, e.last_name, e.hire_date, e.salary, e.email, e.phone_number, e.created_at, e.updated_at, p.id AS position_id, p.title AS position_title')
            ->from('employees e')
            ->join('positions p', 'p.id = e.id_position')
            ->find($id);
         if ($data) {

            $table = '<table class="table table-striped table-bordered table-sm">';
            $table .= '<tr><th>Cédula</th><td>' . $data['ic'] . '</td></tr>';
            $table .= '<tr><th>Cargo</th><td>' . $data['position_title'] . '</td></tr>';
            $table .= '<tr><th>Nombre</th><td>' . $data['first_name'] . ' ' . $data['last_name'] . '</td></tr>';
            $table .= '<tr><th>Fecha Ingreso</th><td>' . $data['hire_date'] . '</td></tr>';
            $table .= '<tr><th>Salario</th><td>' . $data['salary'] . '</td></tr>';
            $table .= '<tr><th>Email</th><td>' . $data['email'] . '</td></tr>';
            $table .= '<tr><th>Teléfono</th><td>' . $data['phone_number'] . '</td></tr>';
            $table .= '<tr><th>Registrado El</th><td>' . $data['created_at'] . '</td></tr>';
            $table .= '<tr><th>Actualiza El</th><td>' . $data['updated_at'] . '</td></tr>';
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
         $data = $this->EmployeesModel->find($id);

         if ($data) {
            $data = [
               'data_positions' => $this->PositionsModel->findAll(),
               'data_employees' => $data
            ];

            echo view('admin/employees/form', $data);
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
         'ic' => $this->request->getPost('ic'),
         'id_position' => $this->request->getPost('id_position'),
         'first_name' => $this->request->getPost('first_name'),
         'last_name' => $this->request->getPost('last_name'),
         'hire_date' => $this->request->getPost('hire_date'),
         'salary' => $this->request->getPost('salary'),
         'email' => $this->request->getPost('email'),
         'phone_number' => $this->request->getPost('phone_number'),
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
            $update = $this->EmployeesModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->EmployeesModel->errors());
            }
         } catch (\Exception $e) {

            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function delete($id = null)
   {
      try {
         $data = $this->EmployeesModel->find($id);
         if ($data) {
            $this->EmployeesModel->delete($id);
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
      $id = $this->request->getPost('id');
      $this->validation->setRules([
         'ic' => [
            'label' => 'Cédula',
            'rules' => 'required|is_unique[employees.ic,id,' . $id . ']',
            'errors' => [
               'is_unique' => 'El campo {field} debe ser único.'
            ]
         ],
         'id_position' => [
            'label' => 'Cargo',
            'rules' => 'required|numeric'
         ],
         'first_name' => [
            'label' => 'Nombre',
            'rules' => 'required|string|max_length[150]'
         ],
         'last_name' => [
            'label' => 'Apellido',
            'rules' => 'required|string|max_length[150]'
         ],
         'hire_date' => [
            'label' => 'Fecha Ingreso',
            'rules' => 'required|valid_date[Y-m-d]'
         ],
         'salary' => [
            'label' => 'Salario',
            'rules' => 'required|decimal|max_length[12]'
         ],
         'email' => [
            'label' => 'Email',
            'rules' => 'required|string|max_length[255]'
         ],
         'phone_number' => [
            'label' => 'Teléfono',
            'rules' => 'required|string|max_length[15]'
         ]
      ]);
   }
}