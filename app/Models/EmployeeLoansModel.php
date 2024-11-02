<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeLoansModel extends Model
{
   protected $table      = 'employee_loans';
   protected $primaryKey = 'id';
   protected $allowedFields = ['employee_id', 'amount', 'total_quotas', 'quotas_of', 'start_date', 'end_date', 'type', 'description', 'status', 'created_at', 'updated_at'];
   protected $searchFields = ['employee_id', 'amount', 'start_date', 'end_date', 'type'];

   public function filter($search = null, $limit = null, $start = null, $orderField = null, $orderDir = null)
   {
      $builder = $this->table($this->table);

      // Validar que $limit y $start sean enteros
      $limit = is_numeric($limit) ? (int)$limit : 10;  // Por defecto, limit = 10
      $start = is_numeric($start) ? (int)$start : 0;   // Por defecto, start = 0

      // Asegurarse de que $orderField y $orderDir no estén vacíos y sean válidos
      $orderField = in_array($orderField, $this->allowedFields) ? $orderField : 'amount';
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

      $builder = $this->db->table('employee_loans AS el');
      $builder->select([
         'el.id',
         'el.employee_id',
         'el.amount',
         'el.total_quotas',
         'el.quotas_of',
         'el.start_date',
         'el.end_date',
         'el.type',
         'el.status',
         'e.id AS empId',
         'e.ic',
         'e.first_name',
         'e.last_name'
      ])
         ->join('employees AS e', 'e.id = el.employee_id')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);


      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
         $query[$index]['first_name'] = $query[$index]['first_name'] . ' ' . $query[$index]['last_name'];
         $query[$index]['amount'] = $query[$index]['amount'];
         $query[$index]['start_date'] = $query[$index]['start_date'];
         $query[$index]['end_date'] = $query[$index]['end_date'];

         if ($query[$index]['status'] == 'paid') {
            $query[$index]['status'] = '<div class="ps-4 bg-success text-white">Paid</div>';
         } elseif ($query[$index]['status'] == 'pending') {
            $query[$index]['status'] = '<div class="ps-4 bg-warning text-dark">Pending</div>';
         } else {
            $query[$index]['status'] = '<div class="ps-4 bg-danger text-white">Canceled</div>';
         }

         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="bi bi-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="' . $query[$index][$this->primaryKey] . '"><i class="bi bi-pencil"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      return $this->table($this->table)
         ->join('employees', 'employees.id = employee_loans.employee_id')
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

      return $builder->join('employees', 'employees.id = employee_loans.employee_id')
         ->countAllResults();
   }
}
