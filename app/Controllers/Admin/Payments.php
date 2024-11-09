<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PaymentsModel;

class Payments extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->PaymentsModel = new PaymentsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Payments',
         'host' => site_url('admin/payments/')
      ];
      echo view('admin/payments/list', $data);
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

         $recordsTotal = $this->PaymentsModel->countTotal();
         $data = $this->PaymentsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->PaymentsModel->countFilter($search);

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

   public function invoiceTotals()
   {
      try {
         $request = esc($this->request->getPost());
         $search = $request['search']['value'];
         $limit = $request['length'];
         $start = $request['start'];

         $orderIndex = $request['order'][0]['column'];
         $orderFields = $request['columns'][$orderIndex]['data'];
         $orderDir = $request['order'][0]['dir'];

         $recordsTotal = $this->PaymentsModel->countTotalInvoices();
         $data = $this->PaymentsModel->getInvoiceTotals($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->PaymentsModel->countFilteredInvoices($search);

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

      echo view('admin/payments/form', $data);
   }

   public function create()
   {
      $request = [
         'invoice_id' => $this->request->getPost('invoice_id'),
         'amount_paid' => $this->request->getPost('amount_paid'),
         'payment_date' => $this->request->getPost('payment_date'),
         'payment_reference' => $this->request->getPost('payment_reference'),
         'paid_by' => $this->request->getPost('paid_by'),
         'amount_usd' => $this->request->getPost('amount_usd'),
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
            $insert = $this->PaymentsModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->PaymentsModel->errors());
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
         $data = $this->PaymentsModel->find($id);
         if ($data) {
            // De forma predeterminada, solo muestra datos de la tabla principal.

            $table = '<table class="table table-striped table-bordered table-sm">';
            $table .= '<tr><th>Numero Factura</th><td>' . $data['invoice_id'] . '</td></tr>';
            $table .= '<tr><th>Monto</th><td>' . $data['amount_paid'] . '</td></tr>';
            $table .= '<tr><th>Fecha de Pago</th><td>' . $data['payment_date'] . '</td></tr>';
            $table .= '<tr><th>Referencia de Pago</th><td>' . $data['payment_reference'] . '</td></tr>';
            $table .= '<tr><th>Pago Por</th><td>' . $data['paid_by'] . '</td></tr>';
            $table .= '<tr><th>Mono</th><td>' . $data['amount_usd'] . '</td></tr>';
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
         $data = $this->PaymentsModel->find($id);

         if ($data) {
            $data = [
               'data_payments' => $data
            ];

            echo view('admin/payments/form', $data);
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
         'invoice_id' => $this->request->getPost('invoice_id'),
         'amount_paid' => $this->request->getPost('amount_paid'),
         'payment_date' => $this->request->getPost('payment_date'),
         'payment_reference' => $this->request->getPost('payment_reference'),
         'paid_by' => $this->request->getPost('paid_by'),
         'amount_usd' => $this->request->getPost('amount_usd'),
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
            $update = $this->PaymentsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->PaymentsModel->errors());
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
         $data = $this->PaymentsModel->find($id);
         if ($data) {
            $this->PaymentsModel->delete($id);
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
      $this->validation->setRules([
         'invoice_id' => [
            'label' => 'Invoice Id',
            'rules' => 'required|numeric'
         ],
         'amount_paid' => [
            'label' => 'Amount Paid',
            'rules' => 'required|decimal|max_length[10]'
         ],
         'payment_date' => [
            'label' => 'Payment Date',
            'rules' => 'required|valid_date[Y-m-d]'
         ],
         'payment_reference' => [
            'label' => 'Payment Reference',
            'rules' => 'required|string|max_length[255]'
         ],
         'paid_by' => [
            'label' => 'Paid By',
            'rules' => 'required|string|max_length[255]'
         ],
         'amount_usd' => [
            'label' => 'Amount Usd',
            'rules' => 'required|decimal|max_length[10]'
         ],
         'created_at' => [
            'label' => 'Created At',
            'rules' => 'required|string'
         ],
      ]);
   }
}