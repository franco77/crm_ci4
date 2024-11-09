<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\WalletsModel;
use App\Models\PaymentsModel;
use App\Models\InvoicesModel;
use IonAuth\Libraries\IonAuth;

class Wallets extends BaseController
{
   use ResponseTrait;
   protected $ionAuth;

   public function __construct()
   {
      $this->WalletsModel = new WalletsModel();
      $this->PaymentsModel = new PaymentsModel();
      $this->InvoicesModel = new InvoicesModel();
      $this->ionAuth = new IonAuth();
   }

   function index()
   {
      $data = [
         'title' => 'Data Wallets',
         'host' => site_url('admin/wallets/')
      ];
      echo view('admin/wallets/list', $data);
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
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function viewWallet()
   {
      $users = $this->ionAuth->users()->result();
      $data = [
         'title' => 'Wallets',
         'users' => $users
      ];
      return view('admin/wallets/index', $data);
   }

   public function getUserWallets()
   {
      $userId = $this->request->getGet('user_id');
      $wallets = $this->WalletsModel->getUserWallets($userId);


      return $this->response->setJSON([
         'success' => true,
         'wallets' => $wallets
      ]);
   }



   public function makePayment()
   {
      try {
         $rules = [
            'wallet_id' => 'required|numeric',
            'invoice_id' => 'required|numeric',
            'amount' => 'required|numeric|greater_than[0]'
         ];

         if (!$this->validate($rules)) {
            return $this->response->setJSON([
               'success' => false,
               'message' => 'Datos de entrada inválidos',
               'errors' => $this->validator->getErrors()
            ]);
         }

         $walletId = $this->request->getPost('wallet_id');
         $invoiceId = $this->request->getPost('invoice_id');
         $amount = $this->request->getPost('amount');


         $result = $this->PaymentsModel->makePayment($invoiceId, $walletId, $amount);

         if ($result['success']) {
            // Obtener el wallet actualizado para devolver datos frescos
            $walletsModel = new WalletsModel();
            $updatedWallet = $walletsModel->find($walletId);

            return $this->response->setJSON([
               'success' => true,
               'message' => $result['message'],
               'payment_id' => $result['payment_id'],
               'wallet' => [
                  'id' => $updatedWallet['id'],
                  'remaining_amount' => $updatedWallet['remaining_amount']
               ]
            ]);
         } else {
            return $this->response->setJSON([
               'success' => false,
               'message' => $result['message']
            ]);
         }
      } catch (\Exception $e) {
         return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al procesar el pago: ' . $e->getMessage()
         ]);
      }
   }


   public function getInvoiceDetails()
   {
      $invoiceId = $this->request->getGet('invoice_id');

      $invoice = $this->InvoicesModel->find($invoiceId);

      if (!$invoice) {
         return $this->response->setJSON([
            'success' => false,
            'message' => 'Factura no encontrada'
         ]);
      }

      return $this->response->setJSON([
         'success' => true,
         'invoice' => $invoice
      ]);
   }



   public function new()
   {
      $users = $this->ionAuth->users()->result();
      $data = ['data_customer' => $users,];

      echo view('admin/wallets/form', $data);
   }

   public function create()
   {
      $this->validation->setRules([
         'support' => [
            'rules' => 'uploaded[support]|ext_in[support,jpg,jpeg,png,pdf]|max_size[support,2048]',
            'errors' => [
               'uploaded' => 'Debe seleccionar un archivo de soporte.',
               'ext_in' => 'El archivo debe ser una imagen (jpg, jpeg, png) o un PDF.',
               'max_size' => 'El archivo de soporte no debe superar los 2MB.',
            ],
         ],
      ]);

      // Recoger los datos del formulario
      $request = [
         'user_id' => $this->request->getPost('user_id'),
         'amount' => $this->request->getPost('amount'),
         'remaining_amount' => $this->request->getPost('remaining_amount'),
         'deposit_date' => $this->request->getPost('deposit_date'),
         'payment_method' => $this->request->getPost('payment_method'),
         'reference' => $this->request->getPost('reference'),
         'notes' => $this->request->getPost('notes'),
         'status' => $this->request->getPost('status'),
         'created_at' => date('Y-m-d H:i:s'),
      ];

      // Verificar si el archivo es válido y moverlo al directorio especificado
      // Subir archivo
      $support = $this->request->getFile('support');

      if ($support && $support->isValid() && !$support->hasMoved()) {
         // Generar un nombre único para evitar conflictos
         $newName = $support->getRandomName();

         // Mover el archivo a la carpeta 'public/uploads/supports'
         $support->move(FCPATH . 'uploads/supports', $newName);

         // Guardar la ruta del archivo subido en la base de datos
         $request['support'] = $newName;
      } else {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => 'El archivo de soporte no es válido o no se pudo subir.'
         ], 400);
      }
      // Intentar insertar los datos en la base de datos
      try {
         $insert = $this->WalletsModel->insert($request);

         if ($insert) {
            return $this->respondCreated([
               'status' => 201,
               'message' => 'Datos creados correctamente.',
            ]);
         } else {
            return $this->fail($this->WalletsModel->errors());
         }
      } catch (\Exception $e) {
         return $this->failServerError('Lo sentimos, ocurrió un error. Por favor, contacte al administrador.');
      }
   }



   public function show($id = null)
   {
      try {
         $data = $this->WalletsModel->find($id);
         if ($data) {
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
         $data = $this->WalletsModel->find($id);
         $users = $this->ionAuth->users()->result();
         if ($data) {
            $data = [
               'data_customer' => $users,
               'data_wallets' => $data
            ];

            echo view('admin/wallets/form', $data);
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function downloadSupport($id)
   {
      // Obtener el registro desde la base de datos para encontrar el nombre del archivo
      $data = $this->WalletsModel->find($id);

      // Verificar si el registro existe y contiene el archivo 'support'
      if (!$data || empty($data['support'])) {
         return $this->failNotFound('El archivo de soporte no se encontró o no existe.');
      }

      // Construir la ruta completa del archivo
      $filePath = FCPATH . 'uploads/supports/' . $data['support'];

      // Verificar si el archivo existe en el servidor
      if (!file_exists($filePath)) {
         return $this->failNotFound('El archivo de soporte no existe en el servidor.');
      }

      // Devolver el archivo como una respuesta de descarga
      return $this->response->download($filePath, null)->setFileName($data['support']);
   }



   public function update($id = null)
   {
      // Obtener el registro actual para mantener el valor del archivo existente
      $currentData = $this->WalletsModel->find($id);

      // Crear un array de datos para actualizar
      $request = [
         'user_id' => $this->request->getPost('user_id'),
         'amount' => $this->request->getPost('amount'),
         'remaining_amount' => $this->request->getPost('remaining_amount'),
         'deposit_date' => $this->request->getPost('deposit_date'),
         'payment_method' => $this->request->getPost('payment_method'),
         'reference' => $this->request->getPost('reference'),
         'notes' => $this->request->getPost('notes'),
         'status' => $this->request->getPost('status'),
         'updated_at' => date('Y-m-d H:i:s'),
      ];

      // Verificar si se ha cargado un archivo en el campo 'support'
      $supportFile = $this->request->getFile('support');
      if ($supportFile && $supportFile->isValid() && !$supportFile->hasMoved()) {
         // Si se ha subido un archivo y es válido, procesarlo
         $newFileName = $supportFile->getRandomName(); // Generar un nombre aleatorio para evitar colisiones
         $supportFile->move(FCPATH . 'uploads/supports', $newFileName);
         $request['support'] = $newFileName; // Añadir el nuevo nombre de archivo a la solicitud
      } else {
         // Si no se ha subido un archivo, conservar el archivo existente
         $request['support'] = $currentData['support'];
      }

      // Ejecutar las reglas de validación
      $this->rules();
      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            // Actualizar el registro en la base de datos
            $update = $this->WalletsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->WalletsModel->errors());
            }
         } catch (\Exception $e) {
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }


   public function delete($id = null)
   {
      try {
         $data = $this->WalletsModel->find($id);
         if ($data) {
            $this->WalletsModel->delete($id);
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
      ]);
   }
}
