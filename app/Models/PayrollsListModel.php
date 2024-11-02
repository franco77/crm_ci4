<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollsListModel extends Model
{
   protected $table      = 'payrolls';
   protected $primaryKey = 'id';
   protected $allowedFields = ['employee_id', 'payroll_date', 'gross_salary', 'loan_deductions', 'deductfix', 'bonus', 'net_salary', 'created_at', 'updated_at'];
   protected $searchFields = ['employee_id', 'payroll_date', 'gross_salary', 'loan_deductions', 'deductfix'];

   public function filter($search = null, $limit = null, $start = null, $orderField = null, $orderDir = null)
   {
      $builder = $this->table($this->table);

      // Validar que $limit y $start sean enteros
      $limit = is_numeric($limit) ? (int)$limit : 10;  // Por defecto, limit = 10
      $start = is_numeric($start) ? (int)$start : 0;   // Por defecto, start = 0

      // Asegurarse de que $orderField y $orderDir no estén vacíos y sean válidos
      $orderField = in_array($orderField, $this->allowedFields) ? $orderField : 'payroll_date';
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

      $deductionsBuilder = $this->db->table('deductions')
         ->select('id, description, amount');
      $deductions = $deductionsBuilder->get()->getResultArray();

      $builder->select('payrolls.id, payrolls.employee_id, payrolls.payroll_date, payrolls.gross_salary, payrolls.loan_deductions, payrolls.deductfix, payrolls.bonus, payrolls.net_salary, payrolls.created_at, payrolls.updated_at, employees.id AS emploId, employees.ic, employees.id_position, employees.first_name, employees.last_name')
         ->join('employees', 'employees.id = payrolls.employee_id')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {

         $query[$index]['first_name'] = $query[$index]['first_name'] . ' ' . $query[$index]['last_name'];
         $query[$index]['gross_salary'] = $query[$index]['gross_salary'];
         $query[$index]['net_salary'] = $query[$index]['net_salary'];
         $query[$index]['bonus'] = $query[$index]['bonus'];
         $query[$index]['payroll_date'] = $query[$index]['payroll_date'];

         $deductionList = '';
         foreach ($deductions as $deduction) {
            $deductionList .= esc($deduction['description']) . ': ' . '<br>';
         }

         $query[$index]['deductfix'] = $deductionList;

         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<a class="btn btn-sm btn-xs btn-danger" href="' . base_url('admin/payrollslist/printvoucher/') . $query[$index][$this->primaryKey] . '"><i class="bi bi-file-earmark-pdf"></i></a> <button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="bi bi-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="' . $query[$index][$this->primaryKey] . '"><i class="bi bi-pencil"></i></button>';
      }
      return $query;
   }

   public function countTotal()
   {
      return $this->table($this->table)
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

      return $builder->countAllResults();
   }


   public function getPayrollsDetails($id)
   {
      return $this->db->table('payrolls')
         ->select('payrolls.id, payrolls.employee_id, payrolls.payroll_date, payrolls.gross_salary, payrolls.loan_deductions, payrolls.deductfix, payrolls.bonus, payrolls.net_salary, payrolls.created_at, payrolls.updated_at, employees.id AS emploId, employees.ic, employees.id_position, employees.first_name, employees.last_name, employees.email, employees.phone_number')
         ->join('employees', 'employees.id = payrolls.employee_id')
         ->where('payrolls.id', $id)
         ->get()
         ->getRowArray();
   }
}
