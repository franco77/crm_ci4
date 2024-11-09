<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ExpensesModel;

class Expenses extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->ExpensesModel = new ExpensesModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Expenses',
         'host' => site_url('admin/expenses/')
      ];
      echo view('admin/expenses/list', $data);
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

         $recordsTotal = $this->ExpensesModel->countTotal();
         $data = $this->ExpensesModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->ExpensesModel->countFilter($search);

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

      echo view('admin/expenses/form', $data);
   }

   public function create()
   {
      $request = [
         'title'      => $this->request->getPost('title'),
         'amount'     => $this->request->getPost('amount'),
         'notes'      => $this->request->getPost('notes'),
         'created_at' => date('Y-m-d H:i:s')
      ];
      $this->rules();
      $supportFile = $this->request->getFile('support');
      if ($supportFile->isValid() && !$supportFile->hasMoved()) {
         $supportFile->move(FCPATH . 'uploads/expenses/', $supportFile->getRandomName());
         $request['support'] = $supportFile->getName();
      } else {
         return $this->respond([
            'status'  => 400,
            'error'   => 400,
            'message' => 'Please upload a valid support file (PDF, DOCX, image).'
         ], 400);
      }
      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status'  => 400,
            'error'   => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            $insert = $this->ExpensesModel->insert($request);
            if ($insert) {
               return $this->respondCreated([
                  'status'  => 201,
                  'message' => 'Data created successfully.',
               ]);
            } else {
               return $this->fail($this->ExpensesModel->errors());
            }
         } catch (\Exception $e) {
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function show($id = null)
   {
      try {
         $data = $this->ExpensesModel->find($id);
         if ($data) {

            $table = '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
            $table .= '<tr><th>Title</th><td>' . $data['title'] . '</td></tr>';
            $table .= '<tr><th>Amount</th><td>' . $data['amount'] . '</td></tr>';
            $table .= '<tr><th>Support</th><td>' . $data['support'] . '</td></tr>';
            $table .= '<tr><th>Notes</th><td>' . $data['notes'] . '</td></tr>';
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
         $data = $this->ExpensesModel->find($id);
         if ($data) {
            $data = [
               'data_expenses' => $data
            ];
            echo view('admin/expenses/form', $data);
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
         'title'      => $this->request->getPost('title'),
         'amount'     => $this->request->getPost('amount'),
         'notes'      => $this->request->getPost('notes'),
         'updated_at' => date('Y-m-d H:i:s')
      ];
      $this->rules();
      $supportFile = $this->request->getFile('support');
      if ($supportFile->isValid() && !$supportFile->hasMoved()) {
         $existingRecord = $this->ExpensesModel->find($id);
         $newFileName = $supportFile->getRandomName();
         $supportFile->move(FCPATH . 'uploads', $newFileName);
         $request['support'] = $newFileName;
         if (!empty($existingRecord['support']) && file_exists(FCPATH . 'uploads/' . $existingRecord['support'])) {
            unlink(FCPATH . 'uploads/' . $existingRecord['support']);
         }
      } else {
         $existingRecord = $this->ExpensesModel->find($id);
         $request['support'] = $existingRecord['support'];
      }
      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status'   => 400,
            'error'    => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            $update = $this->ExpensesModel->update($id, $request);
            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->ExpensesModel->errors());
            }
         } catch (\Exception $e) {
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }


   public function downloadSupport($id)
   {
      // Obtener el registro desde la base de datos para encontrar el nombre del archivo
      $data = $this->ExpensesModel->find($id);

      // Verificar si el registro existe y contiene el archivo 'support'
      if (!$data || empty($data['support'])) {
         return $this->failNotFound('El archivo de soporte no se encontró o no existe.');
      }

      // Construir la ruta completa del archivo
      $filePath = FCPATH . 'uploads/expenses/' . $data['support'];

      // Verificar si el archivo existe en el servidor
      if (!file_exists($filePath)) {
         return $this->failNotFound('El archivo de soporte no existe en el servidor.');
      }

      // Devolver el archivo como una respuesta de descarga
      return $this->response->download($filePath, null)->setFileName($data['support']);
   }

   public function delete($id = null)
   {
      try {
         $data = $this->ExpensesModel->find($id);
         if ($data) {
            $this->ExpensesModel->delete($id);
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
            'label' => 'Title',
            'rules' => 'required|string|max_length[50]'
         ],
         'amount' => [
            'label' => 'Amount',
            'rules' => 'required|decimal|max_length[10]'
         ],
         'support' => [
            'label' => 'Support',
            'rules' => 'required|string|max_length[250]'
         ],
         'notes' => [
            'label' => 'Notes',
            'rules' => 'required|string'
         ]
      ]);
   }
}