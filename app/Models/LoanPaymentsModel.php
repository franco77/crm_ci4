<?php

namespace App\Models;

use CodeIgniter\Model;

class LoanPaymentsModel extends Model
{
   protected $table      = 'loan_payments';
   protected $primaryKey = 'id';
   protected $allowedFields = ['loan_id', 'payment_date', 'amount', 'description', 'status', 'created_at', 'updated_at'];
   protected $searchFields = ['loan_id', 'employees.first_name', 'employees.last_name', 'payment_date', 'amount', 'description', 'status'];

   public function filter($search = null, $limit = null, $start = null, $orderField = null, $orderDir = null)
   {
      $builder = $this->table($this->table);

      // Validar que $limit y $start sean enteros
      $limit = is_numeric($limit) ? (int)$limit : 10;  // Por defecto, limit = 10
      $start = is_numeric($start) ? (int)$start : 0;   // Por defecto, start = 0

      // Asegurarse de que $orderField y $orderDir no estén vacíos y sean válidos
      $orderField = in_array($orderField, $this->allowedFields) ? $orderField : 'loan_id';
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

      $builder->distinct()->select([
         'lp.id',
         'lp.loan_id',
         'lp.payment_date',
         'lp.amount AS payment_amount',
         'lp.description',
         'lp.status',
         'el.id AS empId',
         'el.employee_id',
         'el.amount AS loan_amount',
         'el.total_quotas',
         'el.quotas_of',
         'e.id AS employee_id',
         'e.ic',
         'e.id_position',
         'e.first_name',
         'e.last_name',
      ])
         ->from('loan_payments AS lp')
         ->join('employee_loans AS el', 'el.id = lp.loan_id')
         ->join('employees e', 'e.id = el.employee_id')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);


      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {


         $query[$index]['first_name'] = $query[$index]['first_name'] . ' ' . $query[$index]['last_name'];
         $query[$index]['payment_date'] = $query[$index]['payment_date'];
         $query[$index]['payment_amount'] = $query[$index]['payment_amount'];

         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="bi bi-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="' . $query[$index][$this->primaryKey] . '"><i class="bi bi-pencil"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      return $this->table($this->table)
         ->join('employee_loans', 'employee_loans.id = loan_payments.loan_id')
         ->countAll();
   }

   public function countFilter($search)
   {
      $builder = $this->table($this->table);

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

      return $builder->join('employee_loans', 'employee_loans.id = loan_payments.loan_id')
         ->countAllResults();
   }


   public function getLoanPayDetails($loan_id)
   {
      // Construir la consulta
      $employee = $this->db->table('employee_loans el')
         ->select('
               el.id AS loan_id,
               el.employee_id,
               el.amount,
               el.total_quotas,
               el.quotas_of,
               el.status AS loan_status,
               e.first_name,
               e.last_name,
               e.ic,
               e.email,
               e.phone_number
           ')
         ->join('employees e', 'e.id = el.employee_id')
         ->where('el.id', $loan_id)
         ->where('el.status', 'pending')
         ->get()
         ->getRowArray();  // Cambiado a getRowArray() para obtener un array asociativo

      if (!$employee) {
         return null;
      }

      return $employee;
   }



   public function getLoanDetails()
   {
      return $this->db->table('employee_loans el')
         ->select('
            el.id AS loan_id,
            el.amount AS loan_amount,
            e.first_name,
            e.last_name
        ')
         ->join('employees e', 'e.id = el.employee_id')
         ->where('el.status', 'pending')
         ->orderBy('el.id')
         ->get()->getResultArray();
   }
}
