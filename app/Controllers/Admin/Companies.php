<?php
namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CompaniesModel;
class Companies extends BaseController{
   use ResponseTrait;

   public function __construct() {
      $this->CompaniesModel = new CompaniesModel;

   }

   function index(){
      $data = [
         'title' => 'Data Companies',
         'host' => site_url('admin/companies/')
      ];
      echo view('admin/companies/list', $data);
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

         $recordsTotal = $this->CompaniesModel->countTotal();
         $data = $this->CompaniesModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->CompaniesModel->countFilter($search);

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

      echo view('admin/companies/form', $data);
   }

   public function create()
   {
       $request = [ 
		  'cr' => $this->request->getPost('cr'), 
		  'name' => $this->request->getPost('name'), 
		  'industry' => $this->request->getPost('industry'), 
		  'email' => $this->request->getPost('email'), 
		  'phone_number' => $this->request->getPost('phone_number'), 
		  'address' => $this->request->getPost('address'), 
		  'city' => $this->request->getPost('city'), 
		  'state' => $this->request->getPost('state'), 
		  'postal_code' => $this->request->getPost('postal_code'), 
		  'country' => $this->request->getPost('country'), 
		  'website' => $this->request->getPost('website'), 
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
         try
         {
            $insert = $this->CompaniesModel->insert($request);

            if ($insert)
            {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            }
            else
            {
               return $this->fail($this->CompaniesModel->errors());
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
         $data = $this->CompaniesModel->find($id);
         if ($data)
         {
            // De forma predeterminada, solo muestra datos de la tabla principal.
		 	
         $table = '<table class="table table-striped table-bordered table-sm">'; 
			  $table .= '<tr><th>Cr</th><td>' . $data['cr'] . '</td></tr>'; 
			  $table .= '<tr><th>Name</th><td>' . $data['name'] . '</td></tr>'; 
			  $table .= '<tr><th>Industry</th><td>' . $data['industry'] . '</td></tr>'; 
			  $table .= '<tr><th>Email</th><td>' . $data['email'] . '</td></tr>'; 
			  $table .= '<tr><th>Phone Number</th><td>' . $data['phone_number'] . '</td></tr>'; 
			  $table .= '<tr><th>Address</th><td>' . $data['address'] . '</td></tr>'; 
			  $table .= '<tr><th>City</th><td>' . $data['city'] . '</td></tr>'; 
			  $table .= '<tr><th>State</th><td>' . $data['state'] . '</td></tr>'; 
			  $table .= '<tr><th>Postal Code</th><td>' . $data['postal_code'] . '</td></tr>'; 
			  $table .= '<tr><th>Country</th><td>' . $data['country'] . '</td></tr>'; 
			  $table .= '<tr><th>Website</th><td>' . $data['website'] . '</td></tr>'; 
			  $table .= '<tr><th>Created At</th><td>' . $data['created_at'] . '</td></tr>'; 
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
         $data = $this->CompaniesModel->find($id);

         if ($data)
         {
            $data = [
               'data_companies' => $data
            ];

            echo view('admin/companies/form', $data);
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
		  'cr' => $this->request->getPost('cr'), 
		  'name' => $this->request->getPost('name'), 
		  'industry' => $this->request->getPost('industry'), 
		  'email' => $this->request->getPost('email'), 
		  'phone_number' => $this->request->getPost('phone_number'), 
		  'address' => $this->request->getPost('address'), 
		  'city' => $this->request->getPost('city'), 
		  'state' => $this->request->getPost('state'), 
		  'postal_code' => $this->request->getPost('postal_code'), 
		  'country' => $this->request->getPost('country'), 
		  'website' => $this->request->getPost('website'), 
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
         try
         {
            $update = $this->CompaniesModel->update($id, $request);

            if ($update)
            {
               return $this->respondNoContent('Data updated');
            }
            else {
               return $this->fail($this->CompaniesModel->errors());
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
         $data = $this->CompaniesModel->find($id);
         if ($data)
         {
            $this->CompaniesModel->delete($id);
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
         'cr' => [
            'label' => 'Cr',
            'rules' => 'required|string|max_length[150]'
         ],
         'name' => [
            'label' => 'Name',
            'rules' => 'required|string|max_length[255]'
         ],
         'industry' => [
            'label' => 'Industry',
            'rules' => 'required|string|max_length[255]'
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
         'website' => [
            'label' => 'Website',
            'rules' => 'required|string|max_length[255]'
         ],
         'created_at' => [
            'label' => 'Created At',
            'rules' => 'required|string'
         ],
      ]);
   }

}