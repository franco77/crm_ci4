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

	  
      $builder->select('
    wallets.id,
    wallets.user_id,
    wallets.amount,
    wallets.remaining_amount,
    wallets.deposit_date,
    wallets.payment_method,
    wallets.support,
    users.id AS usId,
    users.ic,
    users.first_name,
    users.last_name
')
->join('users', 'users.id = wallets.user_id')  // Cambiado a LEFT JOIN y corregida la condición
->orderBy($orderField, $orderDir)
->limit($limit, $start);

      $query = $builder->get()->getResultArray();
      $basePath = base_url('admin/wallets/downloadSupport');
      foreach ($query as $index => $value) {
         $query[$index]['user_id'] = $query[$index]['first_name'] .' '.$query[$index]['last_name'];  
         if (!empty($query[$index]['support'])) {
            // Si hay archivo, generar el enlace de descarga
            $query[$index]['support'] = '<a href="' . $basePath . '/' . $query[$index][$this->primaryKey] . '" target="_blank"><i class="bi bi-download"></i> Descargar Soporte</a>';
        } else {
            // Si no hay archivo, mostrar el mensaje correspondiente
            $query[$index]['support'] = '<i class="bi bi-slash-circle"></i> No hay soporte';
        }
         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="'.$query[$index][$this->primaryKey].'">';
         $query[$index]['column_action'] = '<button class="btn btn-sm btn-xs btn-success form-action" item-id="'.$query[$index][$this->primaryKey].'" purpose="detail"><i class="bi bi-eye"></i></button> <button class="btn btn-sm btn-xs btn-warning form-action" purpose="edit" item-id="'.$query[$index][$this->primaryKey].'"><i class="bi bi-pencil"></i></button>';
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


   public function getUserWallets($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('status', 'active')
                    ->findAll();
    }
    
    public function updateRemainingAmount($walletId, $amount)
    {
        $wallet = $this->find($walletId);
        if (!$wallet) return false;
        
        $newRemaining = $wallet['remaining_amount'] - $amount;
        if ($newRemaining < 0) return false;
        
        return $this->update($walletId, ['remaining_amount' => $newRemaining]);
    }


   public function getSumRemainingAmountByUser()
{
    $builder = $this->db->table('wallets');
    $builder->select('
        wallets.user_id,
        SUM(wallets.remaining_amount) AS total_remaining_amount,
        users.id AS usId,
        users.ic,
        users.first_name,
        users.last_name
    ');
    $builder->join('users', 'wallets.user_id = users.id');
    $builder->where('wallets.status', 'active');
    $builder->groupBy('wallets.user_id');

    $query = $builder->get();
    return $query->getResult();
}

}