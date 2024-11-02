<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoicesModel extends Model
{
   protected $table      = 'invoices';
   protected $primaryKey = 'id';
   protected $allowedFields = ['client_id', 'date_invoice', 'invoice_total', 'invoice_subtotal', 'tax', 'amount_paid', 'amount_due', 'notes', 'created_at', 'updated_at', 'uuid'];
   protected $searchFields = ['customers.first_name', 'customers.last_name', 'invoices.invoice_total', 'invoices.invoice_subtotal', 'invoices.tax', 'invoices.amount_paid', 'invoices.uuid'];

   public function filter($search = null, $limit = null, $start = null, $orderField = null, $orderDir = null)
   {
      $builder = $this->table($this->table);

      // Validar que $limit y $start sean enteros
      $limit = is_numeric($limit) ? (int)$limit : 10;  // Por defecto, limit = 10
      $start = is_numeric($start) ? (int)$start : 0;   // Por defecto, start = 0

      // Asegurarse de que $orderField y $orderDir no estén vacíos y sean válidos
      $orderField = in_array($orderField, $this->allowedFields) ? $orderField : 'date_invoice';
      $orderDir = in_array(strtolower($orderDir), ['asc', 'desc']) ? $orderDir : 'asc';

      // Aplicar filtro de búsqueda
      if ($search) {
         $builder->groupStart();
         foreach ($this->searchFields as $i => $column) {
            if ($i === 0) {
               $builder->like($column, $search);
            } else {
               $builder->orLike($column, $search);
            }
         }
         $builder->groupEnd();
      }

      // Muestra datos menores o iguales a las primeras 6 columnas.

      $builder->select('invoices.id, invoices.client_id, invoices.date_invoice, invoices.invoice_total, invoices.invoice_subtotal, invoices.tax, invoices.amount_paid, invoices.amount_due, invoices.notes, invoices.created_at, invoices.updated_at, invoices.uuid, customers.id AS owId, customers.ic, customers.first_name, customers.last_name, customers.email, customers.address, customers.phone_number')
         ->join('customers', 'customers.id = invoices.client_id')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
         $query[$index]['first_name'] = $query[$index]['first_name'] . ' ' . $query[$index]['last_name'];

         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<a class="btn btn-sm btn-xs btn-danger" href="' . base_url('invoices/printvoucherView/') . $query[$index][$this->primaryKey] . '"><i class="bi bi-file-earmark-pdf"></i></a> <button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="bi bi-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="' . $query[$index][$this->primaryKey] . '"><i class="bi bi-pencil-square"></i></button> <button class="btn btn-sm btn-xs btn-info send-email" item-id="' . $query[$index][$this->primaryKey] . '" style="border-radius: 1px;"><i class="bi bi-envelope"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      return $this->table($this->table)
         ->join('customers', 'customers.id = invoices.client_id')
         ->countAll();
   }

   public function countFilter($search)
   {
      $builder = $this->table($this->table)
         ->join('customers', 'customers.id = invoices.client_id');

      $i = 0;
      foreach ($this->searchFields as $column) {
         if ($search) {
            if ($i == 0) {
               $builder->groupStart()
                  ->like($column, $search);
            } else {
               $builder->orLike($column, $search);
            }

            if (count($this->searchFields) - 1 == $i) $builder->groupEnd();
         }
         $i++;
      }

      return $builder->countAllResults();
   }

   public function getInvoiceDetails($id)
   {
      // Obtener los datos principales de la factura y los datos del cliente (owner)
      $invoiceData = $this->db->table('invoices')
         ->select('invoices.id, invoices.client_id, invoices.date_invoice, invoices.invoice_total, invoices.invoice_subtotal, invoices.tax, invoices.amount_paid, invoices.amount_due, invoices.notes, invoices.created_at, invoices.updated_at, invoices.uuid,
                  users.ic, users.first_name, users.last_name, users.email, users.address, users.phone')
         ->join('users', 'users.id = invoices.client_id') // Unimos la tabla 'customers' en base a la relación con 'client_id'
         ->where('invoices.id', $id)
         ->get()
         ->getRowArray(); // Obtener solo una fila como array

      // Si no se encuentra la factura, retornar null
      if (!$invoiceData) {
         return null;
      }

      // Obtener los detalles de la factura (productos, cantidad, precio)
      $invoiceDetails = $this->db->table('invoice_details')
         ->select('invoice_details.product_name, invoice_details.quantity, invoice_details.price')
         ->where('invoice_details.invoice_id', $id)
         ->get()
         ->getResultArray(); // Obtener múltiples filas como array

      // Combinar la información de la factura con los detalles de la factura
      $invoiceData['details'] = $invoiceDetails;

      // Retornar el array completo que incluye la factura y los detalles
      return $invoiceData;
   }
}
