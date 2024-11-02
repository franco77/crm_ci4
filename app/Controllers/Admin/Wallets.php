<?php
namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\WalletsModel;
class Wallets extends BaseController{
   use ResponseTrait;

   public function __construct() {
      $this->WalletsModel = new WalletsModel;

   }

   function index(){
      $data = [
         'title' => 'Data Wallets',
         'host' => site_url('admin/wallets/')
      ];
      echo view('admin/wallets/list', $data);
   }

   public function data(){
      try
      {
         $request = esc($this->request->getPost());
         $search = $request['search']['value'];
         $limit = $request['length'];
         $start = $request['start'];

         $orderIndex = $request['order'][0]['column'];
         $orderFields = $request['columns'][$orderIndex]['data'];
         $orderDir = $request['order'][0]['dir'];

         $recordsTotal = $this->WalletsModel->countTotal();
         $data = $this->WalletsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->WalletsModel->countFilter($search);

         $callback = [
            'draw' => $request['draw'],
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
         ];

         return $this->respond($callback);
      }
      catch (\Exception $e)
      {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }

   }

   public function new()
   {
      $data = [];

      echo view('admin/wallets/form', $data);
   }

   public function create()
   {
       $request = [ 
		  'user_id' => $this->request->getPost('user_id'), 
		  'amount' => $this->request->getPost('amount'), 
		  'remaining_amount' => $this->request->getPost('remaining_amount'), 
		  'deposit_date' => $this->request->getPost('deposit_date'), 
		  'payment_method' => $this->request->getPost('payment_method'), 
		  'reference' => $this->request->getPost('reference'), 
		  'support' => $this->request->getPost('support'), 
		  'notes' => $this->request->getPost('notes'), 
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
         try
         {
            $insert = $this->WalletsModel->insert($request);

            if ($insert)
            {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            }
            else
            {
               return $this->fail($this->WalletsModel->errors());
            }
         }
         catch (\Exception $e)
         {
            // return $this->failServerError($e->getMessage());
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }

   }

   public function show($id = null)
   {
      try
      {
         $data = $this->WalletsModel->find($id);
         if ($data)
         {
            // De forma predeterminada, solo muestra datos de la tabla principal.
		 	
         $table = '<table class="table table-striped table-bordered table-sm">'; 
			  $table .= '<tr><th>User Id</th><td>' . $data['user_id'] . '</td></tr>'; 
			  $table .= '<tr><th>Amount</th><td>' . $data['amount'] . '</td></tr>'; 
			  $table .= '<tr><th>Remaining Amount</th><td>' . $data['remaining_amount'] . '</td></tr>'; 
			  $table .= '<tr><th>Deposit Date</th><td>' . $data['deposit_date'] . '</td></tr>'; 
			  $table .= '<tr><th>Payment Method</th><td>' . $data['payment_method'] . '</td></tr>'; 
			  $table .= '<tr><th>Reference</th><td>' . $data['reference'] . '</td></tr>'; 
			  $table .= '<tr><th>Support</th><td>' . $data['support'] . '</td></tr>'; 
			  $table .= '<tr><th>Notes</th><td>' . $data['notes'] . '</td></tr>'; 
			  $table .= '<tr><th>Status</th><td>' . $data['status'] . '</td></tr>'; 
			  $table .= '<tr><th>Created At</th><td>' . $data['created_at'] . '</td></tr>'; 
			  $table .= '<tr><th>Updated At</th><td>' . $data['updated_at'] . '</td></tr>'; 
		    $table .= '</table>';
            return $this->respond($table);;
         }
         else{
            return $this->failNotFound();
         }
      }
      catch (\Exception $e)
      {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }

   }

   public function edit($id = null)
   {
      try
      {
         $data = $this->WalletsModel->find($id);

         if ($data)
         {
            $data = [
               'data_wallets' => $data
            ];

            echo view('admin/wallets/form', $data);
         }
         else
         {
            return $this->failNotFound();
         }
      }
      catch (\Exception $e)
      {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }

   }

   public function update($id = null)
   {
      $request = [ 
		  'user_id' => $this->request->getPost('user_id'), 
		  'amount' => $this->request->getPost('amount'), 
		  'remaining_amount' => $this->request->getPost('remaining_amount'), 
		  'deposit_date' => $this->request->getPost('deposit_date'), 
		  'payment_method' => $this->request->getPost('payment_method'), 
		  'reference' => $this->request->getPost('reference'), 
		  'support' => $this->request->getPost('support'), 
		  'notes' => $this->request->getPost('notes'), 
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
         try
         {
            $update = $this->WalletsModel->update($id, $request);

            if ($update)
            {
               return $this->respondNoContent('Data updated');
            }
            else {
               return $this->fail($this->WalletsModel->errors());
            }
         }
         catch (\Exception $e)
         {
            // return $this->failServerError($e->getMessage());
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }

   public function delete($id = null)
   {
      try
      {
         $data = $this->WalletsModel->find($id);
         if ($data)
         {
            $this->WalletsModel->delete($id);
            return $this->respondDeleted([
               'status' => 200,
               'message' => 'Data deleted.'
            ]);
         }
         else
         {
            return $this->failNotFound();
         }
      }
      catch (\Exception $e)
      {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   private function rules(){
      $this->validation->setRules([
         'user_id' => [
            'label' => 'User Id',
            'rules' => 'required|numeric'
         ],
         'amount' => [
            'label' => 'Amount',
            'rules' => 'required|decimal|max_length[10]'
         ],
         'remaining_amount' => [
            'label' => 'Remaining Amount',
            'rules' => 'required|decimal|max_length[10]'
         ],
         'deposit_date' => [
            'label' => 'Deposit Date',
            'rules' => 'required|valid_date[Y-m-d]'
         ],
         'payment_method' => [
            'label' => 'Payment Method',
            'rules' => 'required|string|max_length[50]'
         ],
         'reference' => [
            'label' => 'Reference',
            'rules' => 'required|string|max_length[100]'
         ],
         'support' => [
            'label' => 'Support',
            'rules' => 'required|string|max_length[250]'
         ],
         'notes' => [
            'label' => 'Notes',
            'rules' => 'required|string'
         ],
         'status' => [
            'label' => 'Status',
            'rules' => 'required|in_list[active, inactive, debited, favor]'
         ],
         'created_at' => [
            'label' => 'Created At',
            'rules' => 'required|valid_date[Y-m-d H:i]'
         ],
         'updated_at' => [
            'label' => 'Updated At',
            'rules' => 'required|valid_date[Y-m-d H:i]'
         ],
      ]);
   }

}