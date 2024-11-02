<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table = 'cart_items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'product_id', 'quantity'];

    public function getCartItems($userId = null)
    {
        // If you have user authentication, use $userId instead of 1
        $userId = $userId ?? 1;

        return $this->select('cart_items.id as cart_item_id, products.id as product_id, products.productName, products.buyPrice, products.productImage, products.quantityInStock, cart_items.quantity')
            ->join('products', 'products.id = cart_items.product_id')
            ->where('cart_items.user_id', $userId)
            ->findAll();
    }

    public function addToCart($productId, $quantity, $userId = null)
    {
        // If you have user authentication, use $userId instead of 1
        $userId = $userId ?? 1;

        $existingItem = $this->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            return $this->update($existingItem['id'], ['quantity' => $existingItem['quantity'] + $quantity]);
        } else {
            return $this->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }

    public function updateCartItemQuantity($cartItemId, $quantity)
    {
        return $this->update($cartItemId, ['quantity' => $quantity]);
    }

    public function removeFromCart($cartItemId)
    {
        return $this->delete($cartItemId);
    }

    public function getUpdatedCartItem($cartItemId)
    {
        return $this->select('cart_items.id as cart_item_id, products.id as product_id, products.productName, products.buyPrice, cart_items.quantity')
            ->join('products', 'products.id = cart_items.product_id')
            ->where('cart_items.id', $cartItemId)
            ->first();
    }
}
