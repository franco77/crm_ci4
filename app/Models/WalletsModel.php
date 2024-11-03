<?php
namespace App\Models;
use CodeIgniter\Model;

class WalletsModel extends Model{
   protected $table      = 'wallets';
   protected $primaryKey = 'id';
   protected $allowedFields = ['user_id', 'amount', 'remaining_amount', 'deposit_date', 'payment_method', 'reference', 'support', 'notes', 'status', 'created_at', 'updated_at'];
   protected $searchFields = ['user_id', 'amount', 'remaining_amount', 'deposit_date', 'payment_method'];

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

	  
      $builder->select('id, user_id, amount, remaining_amount, deposit_date, payment_method')
              ->orderBy($orderField, $orderDir)
              ->limit($limit, $start);

      $query = $builder->get()->getResultArray();

      foreach ($query as $index => $value) {
                  
         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="'.$query[$index][$this->primaryKey].'">';
         $query[$index]['column_action'] = '<button class="btn btn-sm btn-xs btn-success form-action" item-id="'.$query[$index][$this->primaryKey].'" purpose="detail"><i class="far fa-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="'.$query[$index][$this->primaryKey].'"><i class="far fa-edit"></i></button>';
      }
      return $query;
   }

   public function countTotal(){
      return $this->table($this->table)
                  ->countAll();
   }

   public function countFilter($search){
      $builder = $this->table($this->table);

      $i = 0;
      foreach ($this->searchFields as $column)
      {
            if($search)
            {
               if($i == 0)
               {
                  $builder->groupStart()
                          ->like($column, $search);
               }
               else
               {
                  $builder->orLike($column, $search);
               }

               if(count($this->searchFields) - 1 == $i) $builder->groupEnd();

            }
            $i++;
      }

      return $builder->countAllResults();
   }

}