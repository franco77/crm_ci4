<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CustomersModel;

class Customers extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->CustomersModel = new CustomersModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Customers',
         'host' => site_url('admin/customers/')
      ];
      echo view('admin/customers/list', $data);
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

         $recordsTotal = $this->CustomersModel->countTotal();
         $data = $this->CustomersModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->CustomersModel->countFilter($search);

         $callback = [
            'draw' => $request['draw'],
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
         ];

         return $this->respond($callback);
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function new()
   {
      $data = [];

      echo view('admin/customers/form', $data);
   }

   public function create()
   {
      $request = [
         'ic' => $this->request->getPost('ic'),
         'first_name' => $this->request->getPost('first_name'),
         'last_name' => $this->request->getPost('last_name'),
         'email' => $this->request->getPost('email'),
         'phone_number' => $this->request->getPost('phone_number'),
         'address' => $this->request->getPost('address'),
         'city' => $this->request->getPost('city'),
         'state' => $this->request->getPost('state'),
         'postal_code' => $this->request->getPost('postal_code'),
         'country' => $this->request->getPost('country'),
         'created_at' => $this->request->getPost('created_at'),
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
            $insert = $this->CustomersModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->CustomersModel->errors());
            }
         } catch (\Exception $e) {
            // return $this->failServerError($e->getMessage());
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function show($id = null)
   {
      try {
         $data = $this->CustomersModel->find($id);
         if ($data) {
            // De forma predeterminada, solo muestra datos de la tabla principal.

            $table = '<table class="table table-striped table-bordered table-sm">';
            $table .= '<tr><th>Ic</th><td>' . $data['ic'] . '</td></tr>';
            $table .= '<tr><th>First Name</th><td>' . $data['first_name'] . '</td></tr>';
            $table .= '<tr><th>Last Name</th><td>' . $data['last_name'] . '</td></tr>';
            $table .= '<tr><th>Email</th><td>' . $data['email'] . '</td></tr>';
            $table .= '<tr><th>Phone Number</th><td>' . $data['phone_number'] . '</td></tr>';
            $table .= '<tr><th>Address</th><td>' . $data['address'] . '</td></tr>';
            $table .= '<tr><th>City</th><td>' . $data['city'] . '</td></tr>';
            $table .= '<tr><th>State</th><td>' . $data['state'] . '</td></tr>';
            $table .= '<tr><th>Postal Code</th><td>' . $data['postal_code'] . '</td></tr>';
            $table .= '<tr><th>Country</th><td>' . $data['country'] . '</td></tr>';
            $table .= '<tr><th>Created At</th><td>' . $data['created_at'] . '</td></tr>';
            $table .= '</table>';
            return $this->respond($table);;
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function edit($id = null)
   {
      try {
         $data = $this->CustomersModel->find($id);

         if ($data) {
            $data = [
               'data_customers' => $data
            ];

            echo view('admin/customers/form', $data);
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function update($id = null)
   {
      $request = [
         'ic' => $this->request->getPost('ic'),
         'first_name' => $this->request->getPost('first_name'),
         'last_name' => $this->request->getPost('last_name'),
         'email' => $this->request->getPost('email'),
         'phone_number' => $this->request->getPost('phone_number'),
         'address' => $this->request->getPost('address'),
         'city' => $this->request->getPost('city'),
         'state' => $this->request->getPost('state'),
         'postal_code' => $this->request->getPost('postal_code'),
         'country' => $this->request->getPost('country'),
         'created_at' => $this->request->getPost('created_at'),
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
            $update = $this->CustomersModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->CustomersModel->errors());
            }
         } catch (\Exception $e) {
            // return $this->failServerError($e->getMessage());
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function delete($id = null)
   {
      try {
         $data = $this->CustomersModel->find($id);
         if ($data) {
            $this->CustomersModel->delete($id);
            return $this->respondDeleted([
               'status' => 200,
               'message' => 'Data deleted.'
            ]);
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   private function rules()
   {
      $id = $this->request->getPost('id');
      $this->validation->setRules([
         'ic' => [
            'label' => 'Ic',
            'rules' => 'required|is_unique[customers.ic,id,' . $id . ']',
         ],
         'first_name' => [
            'label' => 'First Name',
            'rules' => 'required|string|max_length[100]'
         ],
         'last_name' => [
            'label' => 'Last Name',
            'rules' => 'required|string|max_length[100]'
         ],
         'email' => [
            'label' => 'Email',
            'rules' => 'required|string|max_length[255]'
         ],
         'phone_number' => [
            'label' => 'Phone Number',
            'rules' => 'required|string|max_length[20]'
         ],
         'address' => [
            'label' => 'Address',
            'rules' => 'required|string|max_length[255]'
         ],
         'city' => [
            'label' => 'City',
            'rules' => 'required|string|max_length[100]'
         ],
         'state' => [
            'label' => 'State',
            'rules' => 'required|string|max_length[100]'
         ],
         'postal_code' => [
            'label' => 'Postal Code',
            'rules' => 'required|string|max_length[20]'
         ],
         'country' => [
            'label' => 'Country',
            'rules' => 'required|string|max_length[100]'
         ],
         'created_at' => [
            'label' => 'Created At',
            'rules' => 'required|string'
         ],
      ]);
   }
}
