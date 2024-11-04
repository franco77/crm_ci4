<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProductsModel;
use App\Models\VendorsModel;
use App\Models\CatProductsModel;

class Products extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->ProductsModel = new ProductsModel;
      $this->VendorsModel = new VendorsModel;
      $this->CatProductsModel = new CatProductsModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Products',
         'host' => site_url('admin/products/')
      ];
      echo view('admin/products/list', $data);
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

         $recordsTotal = $this->ProductsModel->countTotal();
         $data = $this->ProductsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->ProductsModel->countFilter($search);

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


   public function autocomplete()
   {
      // Recoge los datos enviados vía POST
      $type = $this->request->getPost('type');
      $name_startsWith = $this->request->getPost('name_startsWith');

      // Llama al método del modelo para obtener los productos
      $productsModel = new \App\Models\ProductsModel();
      $data = $productsModel->getProductsForAutocomplete($type, $name_startsWith);

      // Devuelve los resultados como JSON
      return $this->response->setJSON($data);
   }

   public function new()
   {
      $data = [
         'data_vendors' => $this->VendorsModel->findAll(),
         'data_category' => $this->CatProductsModel->findAll(),
      ];

      echo view('admin/products/form', $data);
   }

   public function create()
   {
      $id = $this->request->getPost('id');
      // Definir las reglas de validación para los campos de texto
      $this->validation->setRules([
         'productCode' => 'required|is_unique[products.productCode,id,' . $id . ']',
         'productName' => 'required',
         'productLine' => 'required',
         'productVendor' => 'required',
         'productDescription' => 'required',
         'quantityInStock' => 'required|integer',
         'buyPrice' => 'required|decimal',
         'productImage' => 'uploaded[productImage]|is_image[productImage]|mime_in[productImage,image/jpg,image/jpeg,image/png]|max_size[productImage,4096]',
      ]);

      // Obtener los datos POST
      $request = [
         'productCode' => $this->request->getPost('productCode'),
         'productName' => $this->request->getPost('productName'),
         'productLine' => $this->request->getPost('productLine'),
         'productVendor' => $this->request->getPost('productVendor'),
         'productDescription' => $this->request->getPost('productDescription'),
         'quantityInStock' => $this->request->getPost('quantityInStock'),
         'buyPrice' => $this->request->getPost('buyPrice'),
      ];

      // Verificar si la validación falla
      if (!$this->validation->run($request)) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400); // En caso de error de validación
      }

      // Manejo de la imagen subida
      $file = $this->request->getFile('productImage');

      if ($file->isValid() && !$file->hasMoved()) {
         $newFileName = $file->getRandomName(); // Nombre único para evitar colisiones de archivos

         // Redimensionar la imagen a 200x200 píxeles
         $this->imageService->withFile($file->getTempName())
            ->resize(200, 200, true, 'height')
            ->save(FCPATH . 'uploads/products/' . $newFileName);

         // Agregar la imagen al array de request para insertar en la base de datos
         $request['productImage'] = $newFileName;
      } else {
         return $this->failValidationError('The image upload failed. Please try again.'); // Devolver un error de validación si la imagen no es válida
      }

      // Intentar insertar los datos en la base de datos
      try {
         $insert = $this->ProductsModel->insert($request);

         if ($insert) {
            return $this->respondCreated([
               'status' => 201,
               'message' => 'Product created successfully.',
            ]);
         } else {
            return $this->fail($this->ProductsModel->errors());
         }
      } catch (\Exception $e) {
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function list()
   {


      // Obtener productos paginados
      $products = $this->ProductsModel->paginate(20);  // 12 productos por página
      $pager = $this->ProductsModel->pager;

      // Obtener categorías
      $categories = $this->ProductsModel->select('productLine, COUNT(*) as count')
         ->groupBy('productLine')
         ->findAll();

      // Crear rangos de precios (esto es un ejemplo, ajusta según tus necesidades)
      $priceRanges = [
         ['id' => 1, 'min' => 5, 'max' => 15],
         ['id' => 2, 'min' => 16, 'max' => 26],
         ['id' => 3, 'min' => 1300, 'max' => 999999]
      ];

      foreach ($priceRanges as &$range) {
         $range['count'] = $this->ProductsModel->where('buyPrice >=', $range['min'])
            ->where('buyPrice <=', $range['max'])
            ->countAllResults();
      }

      return view('admin/products/index', [
         'title' => 'Lista',
         'pager' => $pager,
         'products' => $products,
         'categories' => $categories,
         'priceRanges' => $priceRanges,
         'pager' => $this->ProductsModel->pager
      ]);
   }

   public function show($id = null)
   {
      try {
         $data = $this->ProductsModel->find($id);
         if ($data) {
            // De forma predeterminada, solo muestra datos de la tabla principal.

            $table = '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
            $table .= '<tr><th>Nombre</th><td>' . $data['productName'] . '</td></tr>';
            $table .= '<tr><th>Categoría</th><td>' . $data['productLine'] . '</td></tr>';
            $table .= '<tr><th>Marca</th><td>' . $data['productVendor'] . '</td></tr>';
            $table .= '<tr><th>Descripción</th><td>' . $data['productDescription'] . '</td></tr>';
            $table .= '<tr><th>Inventario</th><td>' . $data['quantityInStock'] . '</td></tr>';
            $table .= '<tr><th>Precio</th><td>' . $data['buyPrice'] . '</td></tr>';
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
         $data = $this->ProductsModel->find($id);

         if ($data) {
            $data = [
               'data_vendors' => $this->VendorsModel->findAll(),
               'data_category' => $this->CatProductsModel->findAll(),
               'data_products' => $data
            ];

            echo view('admin/products/form', $data);
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }


   public function search()
   {
      $searchTerm = $this->request->getGet('term');

      $products = $this->ProductsModel->like('productName', $searchTerm)
         ->orLike('productDescription', $searchTerm)
         ->findAll();

      // Asegúrate de que los productos tengan todos los campos necesarios
      $formattedProducts = array_map(function ($product) {
         return [
            'id' => $product['id'],
            'productName' => $product['productName'],
            'productDescription' => $product['productDescription'] ?? '',
            'productImage' => $product['productImage'] ?? 'default.jpg',
            'buyPrice' => $product['buyPrice'],
            'quantityInStock' => $product['quantityInStock']
         ];
      }, $products);

      return $this->response->setJSON($formattedProducts);
   }




   public function update($id = null)
   {
      // Definir las reglas de validación para los campos de texto
      $this->validation->setRules([
         'productCode' => 'required|is_unique[products.productCode,id,' . $id . ']',
         'productName' => 'required',
         'productLine' => 'required',
         'productVendor' => 'required',
         'productDescription' => 'required',
         'quantityInStock' => 'required|integer',
         'buyPrice' => 'required|decimal',
         // El campo productImage ya no es obligatorio, se elimina la regla 'uploaded[productImage]'
         'productImage' => 'is_image[productImage]|mime_in[productImage,image/jpg,image/jpeg,image/png]|max_size[productImage,4096]',
      ]);

      // Obtener los datos POST
      $request = [
         'productCode' => $this->request->getPost('productCode'),
         'productName' => $this->request->getPost('productName'),
         'productLine' => $this->request->getPost('productLine'),
         'productVendor' => $this->request->getPost('productVendor'),
         'productDescription' => $this->request->getPost('productDescription'),
         'quantityInStock' => $this->request->getPost('quantityInStock'),
         'buyPrice' => $this->request->getPost('buyPrice'),
      ];

      // Verificar si la validación falla
      if (!$this->validation->run($request)) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400); // En caso de error de validación
      }

      // Manejo de la imagen subida
      $file = $this->request->getFile('productImage');

      // Si no se sube ninguna imagen, mantener la imagen actual
      if ($file && $file->isValid() && !$file->hasMoved()) {
         $newFileName = $file->getRandomName(); // Nombre único para evitar colisiones de archivos

         // Redimensionar la imagen a 200x200 píxeles
         $this->imageService->withFile($file->getTempName())
            ->resize(200, 200, true, 'height')
            ->save(FCPATH . 'uploads/products/' . $newFileName);

         // Agregar la nueva imagen al array de request para actualizar en la base de datos
         $request['productImage'] = $newFileName;
      } else {
         // Si no se carga ninguna nueva imagen, obtener la imagen actual de la base de datos
         $currentProduct = $this->ProductsModel->find($id);
         if ($currentProduct) {
            $request['productImage'] = $currentProduct['productImage']; // Mantener la imagen existente
         } else {
            return $this->failNotFound('Product not found');
         }
      }

      try {
         $update = $this->ProductsModel->update($id, $request);

         if ($update) {
            return $this->respondNoContent('Data updated');
         } else {
            return $this->fail($this->ProductsModel->errors());
         }
      } catch (\Exception $e) {
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }


   public function delete($id = null)
   {
      try {
         $data = $this->ProductsModel->find($id);
         if ($data) {
            $this->ProductsModel->delete($id);
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
         'productCode' => [
            'label' => 'productCode',
            'rules' => 'required|string|is_unique[products.id]'
         ],
         'productName' => [
            'label' => 'ProductName',
            'rules' => 'required|string|max_length[70]'
         ],
         'productLine' => [
            'label' => 'ProductLine',
            'rules' => 'required|string|max_length[50]'
         ],
         'productVendor' => [
            'label' => 'ProductVendor',
            'rules' => 'required|string|max_length[50]'
         ],
         'productDescription' => [
            'label' => 'ProductDescription',
            'rules' => 'required|string'
         ],
         'quantityInStock' => [
            'label' => 'QuantityInStock',
            'rules' => 'required|numeric'
         ],
         'buyPrice' => [
            'label' => 'BuyPrice',
            'rules' => 'required|decimal|max_length[10]'
         ]
      ]);
   }
}