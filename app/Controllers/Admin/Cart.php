<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use App\Models\CartModel;
use App\Models\ProductsModel;

class Cart extends BaseController
{
    protected $cartModel;
    protected $productModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductsModel();
    }

    public function index()
    {
        $data['cartItems'] = $this->cartModel->getCartItems();
        return view('admin/cart/index', $data);
    }

    public function add()
    {
        $productId = $this->request->getPost('product_id');
        $quantity = 1; // Default quantity

        $product = $this->productModel->find($productId);

        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Product not found']);
        }

        $result = $this->cartModel->addToCart($productId, $quantity);

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Product added to cart']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to add product to cart']);
        }
    }

    public function update()
    {
        $cartItemId = $this->request->getPost('cart_item_id');
        $quantity = $this->request->getPost('quantity');

        // Actualizar la cantidad del artículo en el carrito
        $result = $this->cartModel->updateCartItemQuantity($cartItemId, $quantity);

        if ($result) {
            // Obtener el ítem actualizado del carrito
            $cartItem = $this->cartModel->getUpdatedCartItem($cartItemId);

            if (!$cartItem) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cart item not found',
                ]);
            }

            // Calcular el total del ítem (precio * cantidad)
            $itemTotal = $cartItem['buyPrice'] * $cartItem['quantity'];

            // Recalcular el resumen del carrito
            $summary = $this->calculateCartSummary();

            // Devolver la respuesta JSON con todos los datos necesarios
            return $this->response->setJSON([
                'success' => true,
                'itemTotal' => number_format($itemTotal, 2),
                'subtotal' => number_format($summary['subtotal'], 2),
                'shipping' => number_format($summary['shipping'], 2),
                'tax' => number_format($summary['tax'], 2),
                'total' => number_format($summary['total'], 2),
                'message' => 'Cart updated successfully',
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update cart',
            ]);
        }
    }

    public function summary()
    {
        // Calcular el resumen del carrito
        $summary = $this->calculateCartSummary();

        // Devolver la respuesta en formato JSON
        return $this->response->setJSON([
            'success' => true,
            'subtotal' => number_format($summary['subtotal'], 2),
            'shipping' => number_format($summary['shipping'], 2),
            'tax' => number_format($summary['tax'], 2),
            'total' => number_format($summary['total'], 2),
        ]);
    }

    private function calculateTax($subtotal)
    {
        $taxRate = 0.08; // 8% de impuestos
        return $subtotal * $taxRate;
    }

    private function calculateCartSummary()
    {
        $cartItems = $this->cartModel->getCartItems();
        $subtotal = 0;

        // Calcular el subtotal
        foreach ($cartItems as $item) {
            $subtotal += $item['buyPrice'] * $item['quantity'];
        }

        // Establecer el valor fijo del envío
        $shipping = 20.00;

        // Calcular el impuesto
        $tax = $this->calculateTax($subtotal);

        // Calcular el total final
        $total = $subtotal + $shipping + $tax;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
        ];
    }

    public function remove()
    {
        $cartItemId = $this->request->getPost('cart_item_id');

        // Elimina el ítem del carrito
        $result = $this->cartModel->removeFromCart($cartItemId);

        if ($result) {
            // Obtener todos los ítems restantes del carrito
            $summary = $this->calculateCartSummary();

            // Si el carrito está vacío
            if ($summary['subtotal'] == 0) {
                return $this->response->setJSON([
                    'success' => true,
                    'subtotal' => '0.00',
                    'shipping' => '0.00',
                    'tax' => '0.00',
                    'total' => '0.00',
                    'message' => 'The cart is empty',
                ]);
            }

            // Devolver la respuesta JSON con los valores actualizados
            return $this->response->setJSON([
                'success' => true,
                'subtotal' => number_format($summary['subtotal'], 2),
                'shipping' => number_format($summary['shipping'], 2),
                'tax' => number_format($summary['tax'], 2),
                'total' => number_format($summary['total'], 2),
                'message' => 'Item removed successfully',
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to remove item from cart',
            ]);
        }
    }
}