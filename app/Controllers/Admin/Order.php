<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;
use App\Models\InvoicesModel;
use App\Models\InvoiceDetailsModel;
use App\Models\ProductsModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Order extends ResourceController
{
    protected $InvoicesModel;
    protected $InvoiceDetailsModel;
    protected $ionAuth;
    protected $db;

    public function __construct()
    {
        $this->InvoicesModel = new InvoicesModel();
        $this->InvoiceDetailsModel = new InvoiceDetailsModel();
        $this->ProductsModel = new ProductsModel();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->db = \Config\Database::connect();
        helper(['setting']);
    }

    public function create()
    {
        if (!$this->ionAuth->loggedIn()) {
            return $this->failUnauthorized('Usuario no autenticado.');
        }

        $userId = $this->ionAuth->user()->row()->id;
        log_message('info', 'Usuario autenticado con ID: ' . $userId);

        $data = $this->request->getPost();
        if (!$this->validateOrderData($data)) {
            return $this->failValidationError('Datos de pedido inválidos o incompletos.');
        }

        log_message('info', 'Datos recibidos correctamente: ' . json_encode($data));

        $this->db->transStart();

        try {
            $invoiceId = $this->createInvoice($data, $userId);
            $this->createInvoiceDetails($data, $invoiceId);
            $this->clearUserCart($userId);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new DatabaseException('Error en la transacción al procesar el pedido.');
            }

            log_message('info', 'Transacción confirmada, pedido creado.');

            return $this->respondCreated([
                'status' => 201,
                'message' => 'Pedido creado exitosamente, carrito limpiado.',
                'invoice_id' => $invoiceId
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error durante el proceso: ' . $e->getMessage());
            return $this->failServerError('Error al procesar el pedido: ' . $e->getMessage());
        }
    }

    private function validateOrderData($data)
    {
        $requiredFields = ['invoice_total', 'invoice_subtotal', 'tax', 'amount_paid', 'amount_due', 'product_id', 'product_name', 'quantity', 'price'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || (is_array($data[$field]) && empty($data[$field]))) {
                log_message('error', 'Campo requerido faltante o vacío: ' . $field);
                return false;
            }
        }
        return true;
    }

    private function createInvoice($data, $userId)
    {
        $invoiceData = [
            'client_id' => $userId,
            'date_invoice' => date('Y-m-d'),
            'invoice_total' => $data['invoice_total'],
            'invoice_subtotal' => $data['invoice_subtotal'],
            'tax' => $data['tax'],
            'amount_paid' => $data['amount_paid'],
            'amount_due' => $data['amount_due'],
            'notes' => $data['notes'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'uuid' => uniqid(),
        ];

        $invoiceId = $this->InvoicesModel->insert($invoiceData);
        if (!$invoiceId) {
            throw new \RuntimeException('Error al crear la factura.');
        }

        log_message('info', 'Factura creada con ID: ' . $invoiceId);
        return $invoiceId;
    }

    private function createInvoiceDetails($data, $invoiceId)
    {
        $invoiceDetails = [];
        foreach ($data['product_id'] as $index => $productId) {
            $invoiceDetails[] = [
                'invoice_id' => $invoiceId,
                'product_id' => $productId,
                'product_name' => $data['product_name'][$index],
                'product_image' => $data['product_image'][$index],
                'quantity' => $data['quantity'][$index],
                'price' => $data['price'][$index]
            ];
        }

        if (!$this->InvoiceDetailsModel->insertBatch($invoiceDetails)) {
            throw new \RuntimeException('Error al insertar los detalles de la factura.');
        }

        log_message('info', 'Detalles de factura insertados.');
    }

    private function clearUserCart($userId)
    {
        $cartItems = $this->db->table('cart_items')->where('user_id', $userId)->get()->getResult();
        log_message('info', 'Registros del carrito antes de eliminar: ' . json_encode($cartItems));

        $result = $this->db->table('cart_items')->where('user_id', $userId)->delete();

        if ($this->db->affectedRows() <= 0) {
            log_message('warning', 'No se encontraron registros de carrito para eliminar del usuario ID: ' . $userId);
        } else {
            log_message('info', 'Registros del carrito eliminados para el cliente ID: ' . $userId);
        }
    }


    public function getInvoiceDetails($invoiceId)
    {
        // Obtener los datos de la factura junto con la información del cliente y los detalles de la factura y los productos
        $invoice = $this->InvoicesModel
            ->select('invoices.id AS invoiceId, invoices.date_invoice, invoices.invoice_total, invoices.invoice_subtotal, invoices.tax, invoices.amount_paid, invoices.amount_due, invoices.notes, invoices.created_at, invoices.updated_at, invoices.uuid,
                      users.id AS userId, users.first_name, users.last_name, users.email, users.phone, users.address, users.company, users.avatar,
                      invoice_details.product_id, invoice_details.product_name, invoice_details.quantity, invoice_details.price,
                      products.productCode, products.productName AS fullProductName, products.productVendor, products.productLine, products.productImage')
            ->join('users', 'users.id = invoices.client_id')  // Unir la tabla de usuarios (clientes)
            ->join('invoice_details', 'invoice_details.invoice_id = invoices.id')  // Unir los detalles de la factura
            // Cambia el JOIN dependiendo de la relación correcta:
            // Si product_id está relacionado con id en products:
            ->join('products', 'products.id = invoice_details.product_id', 'left')
            // Si product_id está relacionado con productCode en products:
            // ->join('products', 'products.productCode = invoice_details.product_id', 'left')
            ->where('invoices.id', $invoiceId)
            ->findAll();  // findAll ya que necesitamos los detalles de varios productos

        // Verificar si la factura existe
        if (empty($invoice)) {
            return redirect()->to('/')->with('error', 'Invoice not found');
        }

        // Procesar los datos
        $data = [
            'invoice' => [
                'id' => $invoice[0]['invoiceId'],
                'date_invoice' => $invoice[0]['date_invoice'],
                'invoice_total' => $invoice[0]['invoice_total'],
                'invoice_subtotal' => $invoice[0]['invoice_subtotal'],
                'tax' => $invoice[0]['tax'],
                'amount_paid' => $invoice[0]['amount_paid'],
                'amount_due' => $invoice[0]['amount_due'],
                'notes' => $invoice[0]['notes'],
                'created_at' => $invoice[0]['created_at'],
                'updated_at' => $invoice[0]['updated_at'],
                'uuid' => $invoice[0]['uuid'],
            ],
            'client' => [
                'first_name' => $invoice[0]['first_name'],
                'last_name'  => $invoice[0]['last_name'],
                'email'      => $invoice[0]['email'],
                'phone'      => $invoice[0]['phone'],
                'address'    => $invoice[0]['address'],
                'company'    => $invoice[0]['company'],
                'avatar'     => $invoice[0]['avatar'],
            ],
            'invoiceDetails' => $invoice  // Detalles de la factura y productos
        ];

        // Cargar la vista con los datos
        return view('admin/orders/order_view', $data);
    }
}