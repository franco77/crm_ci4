<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model
{
   protected $table      = 'products';
   protected $primaryKey = 'id';
   protected $allowedFields = ['productCode', 'productName', 'productLine', 'productVendor', 'productDescription', 'quantityInStock', 'buyPrice', 'productImage'];
   protected $searchFields = ['productName', 'productLine', 'productVendor', 'productDescription'];

   public function filter($search = null, $limit = null, $start = null, $orderField = null, $orderDir = null)
   {
      $builder = $this->table($this->table);

      // Validar que $limit y $start sean enteros
      $limit = is_numeric($limit) ? (int)$limit : 10;  // Por defecto, limit = 10
      $start = is_numeric($start) ? (int)$start : 0;   // Por defecto, start = 0

      // Asegurarse de que $orderField y $orderDir no estén vacíos y sean válidos
      $orderField = in_array($orderField, $this->allowedFields) ? $orderField : 'productName';
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

      // Selección de columnas y orden
      $builder->select('id, productCode, productName, productLine, productVendor, productDescription, productImage')
         ->orderBy($orderField, $orderDir)
         ->limit($limit, $start);

      // Ejecutar la consulta y obtener resultados
      $query = $builder->get()->getResultArray();

      // Procesar los resultados
      foreach ($query as $index => $value) {
         // Limitar la descripción a 50 caracteres


         $query[$index]['productImage'] = '<div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded">
                                                                <img src="' . base_url('uploads/products/') . $query[$index]['productImage'] . '" alt="">
                                                            </span>
                                                        </div>
                                                    </div>';

         $query[$index]['productDescription'] = strlen($value['productDescription']) > 50 ? substr($value['productDescription'], 0, 50) . '...' : $value['productDescription'];
         $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $value[$this->primaryKey] . '">';
         $query[$index]['column_action'] = '<button class="btn btn-sm btn-success form-action" item-id="' . $value[$this->primaryKey] . '" purpose="detail"><i class="bi bi-eye"></i></button> ' .
            '<button class="btn btn-sm btn-warning form-action" purpose="edit" item-id="' . $value[$this->primaryKey] . '"><i class="bi bi-pencil"></i></button>';
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



   public function getProductsForAutocomplete($type, $name)
   {
      // Se asegura de que los datos estén en mayúsculas y aplica la lógica de búsqueda
      $builder = $this->db->table($this->table);
      $builder->select('id, productCode, productName, buyPrice, productImage');
      $builder->where('quantityInStock !=', 0);

      // Convierte el tipo y nombre a mayúsculas y aplica la búsqueda
      $name = strtoupper($name);
      $type = strtoupper($type);

      $builder->like("UPPER($type)", $name, 'after');  // 'after' es para que el LIKE busque al inicio

      $query = $builder->get();
      $result = $query->getResultArray();

      $data = [];
      foreach ($result as $row) {
         $name = $row['productCode'] . '|' . $row['productImage'] . '|' . $row['productName'] . '|' . $row['buyPrice'];
         array_push($data, $name);
      }

      return $data;
   }
}
