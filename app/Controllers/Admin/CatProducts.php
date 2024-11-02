<?php
namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CatProductsModel;
class CatProducts extends BaseController{
   use ResponseTrait;

   public function __construct() {
      $this->CatProductsModel = new CatProductsModel;

   }

   function index(){
      $data = [
         'title' => 'Data Cat Products',
         'host' => site_url('admin/catproducts/')
      ];
      echo view('admin/cat_products/list', $data);
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

         $recordsTotal = $this->CatProductsModel->countTotal();
         $data = $this->CatProductsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->CatProductsModel->countFilter($search);

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

      echo view('admin/cat_products/form', $data);
   }

   public function create()
   {
       $request = [ 
		  'name' => $this->request->getPost('name'), 
		  'description' => $this->request->getPost('description'), 
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
            $insert = $this->CatProductsModel->insert($request);

            if ($insert)
            {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            }
            else
            {
               return $this->fail($this->CatProductsModel->errors());
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
         $data = $this->CatProductsModel->find($id);
         if ($data)
         {
            // De forma predeterminada, solo muestra datos de la tabla principal.
		 	
         $table = '<table class="table table-striped table-bordered table-sm">'; 
			  $table .= '<tr><th>Name</th><td>' . $data['name'] . '</td></tr>'; 
			  $table .= '<tr><th>Description</th><td>' . $data['description'] . '</td></tr>'; 
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
         $data = $this->CatProductsModel->find($id);

         if ($data)
         {
            $data = [
               'data_cat_products' => $data
            ];

            echo view('admin/cat_products/form', $data);
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
		  'name' => $this->request->getPost('name'), 
		  'description' => $this->request->getPost('description'), 
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
            $update = $this->CatProductsModel->update($id, $request);

            if ($update)
            {
               return $this->respondNoContent('Data updated');
            }
            else {
               return $this->fail($this->CatProductsModel->errors());
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
         $data = $this->CatProductsModel->find($id);
         if ($data)
         {
            $this->CatProductsModel->delete($id);
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
         'name' => [
            'label' => 'Name',
            'rules' => 'required|string|max_length[250]'
         ],
         'description' => [
            'label' => 'Description',
            'rules' => 'required|string'
         ],
      ]);
   }

}