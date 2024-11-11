<?php

namespace App\Models;

use CodeIgniter\Model;

class WishlistModel extends Model
{
    protected $table = 'wishlist';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'product_id'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';





    // Método opcional para obtener el conteo total
    public function getTotalWishlistItems($userId)
    {
        return $this->where('user_id', $userId)->countAllResults();
    }

    // Verificar si un producto ya está en la wishlist del usuario
    public function isProductInWishlist($userId, $productId)
    {
        return $this->where([
            'user_id' => $userId,
            'product_id' => $productId
        ])->countAllResults() > 0;
    }

    // Obtener todos los productos en la wishlist de un usuario
    /*  public function getUserWishlist($userId)
    {
        return $this->select('wishlist.*, products.*')
            ->join('products', 'products.id = wishlist.product_id')
            ->where('wishlist.user_id', $userId)
            ->findAll();
    } */

    // Agregar producto a la wishlist
    public function addToWishlist($userId, $productId)
    {
        // Verificar si ya existe
        if ($this->isProductInWishlist($userId, $productId)) {
            return [
                'success' => false,
                'message' => 'El producto ya está en tu lista de favoritos'
            ];
        }

        // Insertar nuevo registro
        $data = [
            'user_id' => $userId,
            'product_id' => $productId
        ];

        if ($this->insert($data)) {
            return [
                'success' => true,
                'message' => 'Producto agregado a favoritos correctamente'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al agregar el producto a favoritos'
        ];
    }

    // Eliminar producto de la wishlist
    public function removeFromWishlist($userId, $productId)
    {
        return $this->where([
            'user_id' => $userId,
            'product_id' => $productId
        ])->delete();
    }




    // Método para obtener wishlist con información de productos
    public function getUserWishlist($userId, $page = 1, $perPage = 3)
    {
        return $this->select('wishlist.*, products.*, wishlist.id as wishlist_id')
            ->join('products', 'products.id = wishlist.product_id')
            ->where('wishlist.user_id', $userId)
            ->orderBy('wishlist.created_at', 'DESC');
    }

    // Método para contar items en wishlist
    public function countUserWishlistItems($userId)
    {
        return $this->where('user_id', $userId)->countAllResults();
    }

    // Verificar si un producto está en wishlist
    public function isInWishlist($userId, $productId)
    {
        return $this->where([
            'user_id' => $userId,
            'product_id' => $productId
        ])->countAllResults() > 0;
    }
}