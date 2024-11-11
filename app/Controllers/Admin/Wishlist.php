<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use App\Models\WishlistModel;
use App\Models\ProductsModel;

class Wishlist extends BaseController
{
    protected $wishlistModel;
    protected $productModel;

    public function __construct()
    {
        $this->wishlistModel = new WishlistModel();
        $this->productModel = new ProductsModel();
    }

    // Mostrar la lista de favoritos


    public function index()
    {
        try {
            // Verificar autenticación
            $userId = session()->get('user_id');


            // Configuración de paginación
            $page = $this->request->getVar('page') ?? 1;
            $perPage = 12; // Productos por página

            // Obtener wishlist paginada
            $wishlist = $this->wishlistModel
                ->select('wishlist.*, products.*, wishlist.id as wishlist_id')
                ->join('products', 'products.id = wishlist.product_id')
                ->where('wishlist.user_id', $userId)
                ->orderBy('wishlist.created_at', 'DESC');

            // Aplicar paginación
            $wishlistData = $wishlist->paginate($perPage, 'group1');
            $pager = $wishlist->pager;

            // Preparar datos para la vista
            $data = [
                'title' => 'Mi Lista de Favoritos',
                'wishlist' => $wishlistData,
                'pager' => $pager,
                'totalItems' => count($wishlistData),
                'currentPage' => $page
            ];

            // Verificar si hay mensajes flash
            if (session()->getFlashdata('success')) {
                $data['success'] = session()->getFlashdata('success');
            }
            if (session()->getFlashdata('error')) {
                $data['error'] = session()->getFlashdata('error');
            }

            // Retornar vista
            return view('admin/wishlist/index', $data);
        } catch (\Exception $e) {
            log_message('error', '[Wishlist::index] Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al cargar la lista de favoritos');
        }
    }

    // Agregar producto a favoritos
    public function add()
    {
        // Verificar si es una petición AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acceso no permitido'
            ]);
        }

        $userId = session()->get('user_id');
        $productId = $this->request->getPost('product_id');

        // Validar que el producto existe
        $product = $this->productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]);
        }

        // Agregar a wishlist
        $result = $this->wishlistModel->addToWishlist($userId, $productId);

        return $this->response->setJSON($result);
    }

    // Eliminar producto de favoritos


    public function remove()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        try {
            $userId = session()->get('user_id');
            $productId = $this->request->getPost('product_id');

            // Log para debugging
            log_message('debug', "Attempting to remove product ID: {$productId} for user ID: {$userId}");

            // Validar datos
            if (!$userId || !$productId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            // Intentar eliminar el registro
            $deleted = $this->wishlistModel->where([
                'user_id' => $userId,
                'product_id' => $productId
            ])->delete();

            // Log el resultado
            log_message('debug', "Delete result: " . ($deleted ? 'success' : 'failed'));

            if ($deleted) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Producto eliminado correctamente',
                    'remainingItems' => $this->wishlistModel->where('user_id', $userId)->countAllResults()
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se pudo eliminar el producto'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error removing from wishlist: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    // Verificar si un producto está en favoritos
    public function check($productId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acceso no permitido'
            ]);
        }

        $userId = session()->get('user_id');
        $isInWishlist = $this->wishlistModel->isProductInWishlist($userId, $productId);

        return $this->response->setJSON([
            'success' => true,
            'inWishlist' => $isInWishlist
        ]);
    }
}