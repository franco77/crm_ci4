<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use IonAuth\Libraries\IonAuth;

class AuthFilter implements FilterInterface
{
    protected $ionAuth;

    public function __construct()
    {
        $this->ionAuth = new IonAuth();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar si el usuario no está logueado
        if (!$this->ionAuth->loggedIn()) {
            // Redirigir al login si no está autenticado
            return redirect()->to('/auth/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacer nada después de la ejecución
    }
}
