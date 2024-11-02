<?php

namespace App\Controllers\Customers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    protected $ionAuth;
    protected $session;

    public function __construct()
    {
        // Cargar IonAuth usando el servicio de CodeIgniter
        $this->ionAuth = service('ionAuth');

        // Cargar la sesión
        $this->session = session();
    }

    public function index()
    {
        // Verificar si el usuario está logueado
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('/auth/login');
        }

        // Verificar si el usuario pertenece al grupo de clientes
        if (!$this->ionAuth->inGroup('customers')) {
            throw new \Exception('No tienes permiso para acceder a esta sección.');
        }

        // Obtener información del usuario
        $data['title'] = 'Panel de Cliente';
        $data['message'] = $this->session->getFlashdata('message');
        $data['user'] = $this->ionAuth->user()->row();

        // Renderizar la vista del panel de cliente
        return view('customers/dashboard/dashboard', $data);
    }

    public function editProfile()
    {
        // Lógica adicional para que el cliente edite su perfil
        $data['title'] = 'Editar Perfil';
        $data['user'] = $this->ionAuth->user()->row();

        return view('customers/dashboard/edit_profile', $data);
    }
}
