<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PositionsModel;

class Positions extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->PositionsModel = new PositionsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Positions',
         'host' => site_url('admin/positions/')
      ];

      echo view('admin/positions/list', $data);
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

         $recordsTotal = $this->PositionsModel->countTotal();
         $data = $this->PositionsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->PositionsModel->countFilter($search);

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

      echo view('admin/positions/form', $data);
   }

   public function create()
   {
      $request = [
         'title' => $this->request->getPost('title'),
         'description' => $this->request->getPost('description'),
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
            $insert = $this->PositionsModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->PositionsModel->errors());
            }
         } catch (\Exception $e) {

            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function show($id = null)
   {
      try {
         $data = $this->PositionsModel->find($id);

         if ($data) {

            $table = '<table class="table table-striped table-bordered table-sm">';
            $table .= '<tr><th>Nombre</th><td>' . $data['title'] . '</td></tr>';
            $table .= '<tr><th>Descripción</th><td>' . $data['description'] . '</td></tr>';
            $table .= '<tr><th>Creado El</th><td>' . $data['created_at'] . '</td></tr>';
            $table .= '</table>';
            return $this->respond($table);
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
         $data = $this->PositionsModel->find($id);

         if ($data) {
            $data = [
               'data_positions' => $data
            ];

            echo view('admin/positions/form', $data);
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
         'title' => $this->request->getPost('title'),
         'description' => $this->request->getPost('description'),
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
            $update = $this->PositionsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->PositionsModel->errors());
            }
         } catch (\Exception $e) {

            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function delete($id = null)
   {
      try {
         $data = $this->PositionsModel->find($id);
         if ($data) {
            $this->PositionsModel->delete($id);
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
         'title' => [
            'label' => lang('app.positions.title'),
            'rules' => 'required|string|max_length[250]'
         ],
         'description' => [
            'label' => lang('app.positions.description'),
            'rules' => 'required|string'
         ]
      ]);
   }
}