<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeesModel extends Model
{
   protected $table      = 'employees';
   protected $primaryKey = 'id';
   protected $allowedFields = ['ic', 'id_position', 'first_name', 'last_name', 'hire_date', 'salary', 'email', 'phone_number', 'status', 'created_at', 'updated_at'];
   protected $searchFields = ['id_position', 'first_name', 'last_name', 'hire_date', 'salary'];

   public function filter($search = null, $limit = null, $start = null, $orderField = null, $orderDir = null)
   {
      $builder = $this->table($this->table);

      // Validar que $limit y $start sean enteros
      $limit = is_numeric($limit) ? (int)$limit : 10;  // Por defecto, limit = 10
      $start = is_numeric($start) ? (int)$start : 0;   // Por defecto, start = 0

      // Asegurarse de que $orderField y $orderDir no estén vacíos y sean válidos
      $orderField = in_array($orderField, $this->allowedFields) ? $orderField : 'ic';
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

      $builder->select('employees.id, employees.ic, employees.id_position, employees.first_name, employees.last_name, employees.hire_date, employees.salary, employees.status, positions.id AS positId, positions.title')
         ->join('positions', 'positions.id = employees.id_position')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
         $query[$index]['first_name'] = $query[$index]['first_name'] . ' ' . $query[$index]['last_name'];
         $query[$index]['hire_date'] = $query[$index]['hire_date'];
         $query[$index]['salary'] = $query[$index]['salary'];
         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="bi bi-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="' . $query[$index][$this->primaryKey] . '"><i class="bi bi-pencil"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      return $this->table($this->table)
         ->join('positions', 'positions.id = employees.id_position')
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

      return $builder->join('positions', 'positions.id = employees.id_position')
         ->countAllResults();
   }


   public function getEmployee($employee_id)
   {
      // Obtener información del empleado
      $employee = $this->db->table('employees')
         ->select('id AS emploId, ic, first_name, last_name, email, salary')
         ->where('id', $employee_id)
         ->get()
         ->getRow();

      // Retornar null si el empleado no existe
      if (!$employee) {
         return null;
      }

      // Obtener información de préstamos pendientes del empleado
      $loan = $this->db->table('employee_loans')
         ->select('
               COUNT(id) AS num_pending_loans,
               SUM(amount) AS totalAmount,
               MAX(start_date) AS start_date_loan,
               MAX(end_date) AS end_date_loan,
               MAX(type) AS type_loan,
               MAX(status) AS status_loan
           ')
         ->where('employee_id', $employee_id)
         ->where('status', 'pending')
         ->get()
         ->getRow();

      // Si existen préstamos, agregarlos a la información del empleado
      if ($loan && $loan->num_pending_loans > 0) {
         foreach ($loan as $key => $value) {
            $employee->{$key} = $value;
         }
      }

      return $employee;
   }
}
