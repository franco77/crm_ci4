<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use App\Models\MessageModel;
use IonAuth\Libraries\IonAuth;

class Messages extends BaseController
{
    protected $messageModel;
    protected $ionAuth;
    protected $db;

    public function __construct()
    {
        $this->messageModel = new MessageModel();
        $this->ionAuth = new IonAuth();
        $this->db = \Config\Database::connect();
    }

    private function ensureLoggedIn()
    {
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('auth/login');
        }
    }

    private function ensureAjaxRequest()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Solicitud no válida']);
        }
    }

    public function index()
    {
        $this->ensureLoggedIn();

        $currentUserId = $this->ionAuth->user()->row()->id;

        // Obtener mensajes con información del remitente
        $messages = $this->fetchUserMessages($currentUserId);

        // Obtener usuarios para el select
        $users = $this->getActiveUsers($currentUserId);

        $data = [
            'title' => 'Mensajería',
            'messages' => $messages,
            'users' => $users,
            'current_user' => $this->ionAuth->user()->row()
        ];

        return view('admin/messages/inbox', $data);
    }

    private function fetchUserMessages($userId)
    {
        return $this->db->table('messages')
            ->select('messages.*, u1.username as sender_username, u1.first_name as sender_first_name, u1.last_name as sender_last_name, u1.email as sender_email')
            ->join('users as u1', 'u1.id = messages.sender_id')
            ->where('messages.receiver_id', $userId)
            ->orderBy('messages.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function getActiveUsers($excludeUserId)
    {
        return $this->db->table('users')
            ->select('id, username, email, first_name, last_name')
            ->where('active', 1)
            ->where('id !=', $excludeUserId)
            ->get()
            ->getResult();
    }

    public function send()
    {
        $this->ensureAjaxRequest();

        // Validación de datos del formulario
        $rules = [
            'receiver_id' => 'required|numeric|is_not_unique[users.id]',
            'subject' => 'required|min_length[3]|max_length[255]',
            'message' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $currentUserId = $this->ionAuth->user()->row()->id;
        $data = [
            'sender_id' => $currentUserId,
            'receiver_id' => $this->request->getPost('receiver_id'),
            'subject' => esc($this->request->getPost('subject')),
            'message' => esc($this->request->getPost('message')),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->messageModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Mensaje enviado correctamente'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Error al enviar el mensaje'
        ]);
    }

    public function getMessages($userId = null)
    {
        $this->ensureAjaxRequest();

        $currentUserId = $userId ?? $this->ionAuth->user()->row()->id;
        $messages = $this->fetchUserMessages($currentUserId);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    public function searchUsers()
    {
        $this->ensureAjaxRequest();

        $term = esc($this->request->getGet('term'));
        $currentUserId = $this->ionAuth->user()->row()->id;

        $users = $this->db->table('users')
            ->select('id, username, email, first_name, last_name')
            ->where('active', 1)
            ->where('id !=', $currentUserId)
            ->groupStart()
            ->like('username', $term)
            ->orLike('email', $term)
            ->orLike('first_name', $term)
            ->orLike('last_name', $term)
            ->groupEnd()
            ->get()
            ->getResult();

        return $this->response->setJSON($users);
    }

    public function markAsRead($messageId)
    {
        $this->ensureAjaxRequest();

        $success = $this->messageModel->update($messageId, ['read_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? 'Mensaje marcado como leído' : 'Error al marcar el mensaje'
        ]);
    }

    public function getMessage($messageId)
    {
        $this->ensureAjaxRequest();

        $currentUserId = $this->ionAuth->user()->row()->id;
        $message = $this->db->table('messages')
            ->select('messages.*, u1.username as sender_username, u1.first_name as sender_first_name, u1.last_name as sender_last_name, u1.email as sender_email')
            ->join('users as u1', 'u1.id = messages.sender_id')
            ->where('messages.id', $messageId)
            ->where('messages.receiver_id', $currentUserId)
            ->get()
            ->getRowArray();

        if ($message) {
            if (empty($message['read_at'])) {
                $this->markAsRead($messageId);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $message
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Mensaje no encontrado'
        ]);
    }


    public function reply()
    {
        // Aseguramos que sea una solicitud AJAX
        $this->ensureAjaxRequest();

        // Validación de los datos recibidos
        $rules = [
            'parent_id' => 'required|numeric',
            'receiver_id' => 'required|numeric|is_not_unique[users.id]',
            'subject' => 'required|min_length[3]|max_length[255]',
            'message' => 'required|min_length[10]'
        ];

        // Verificamos si la validación falla y retornamos errores específicos
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Verificamos que el `parent_id` esté siendo enviado y recibido correctamente
        $parentId = $this->request->getPost('parent_id');
        if (empty($parentId)) {
            // Devolvemos un mensaje de error específico si `parent_id` está vacío
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'El campo parent_id no se recibió correctamente.'
            ]);
        }

        // Recogemos los datos a insertar, asegurándonos de que `parent_id` esté incluido
        $data = [
            'parent_id' => $parentId,
            'sender_id' => $this->ionAuth->user()->row()->id,
            'receiver_id' => $this->request->getPost('receiver_id'),
            'subject' => esc($this->request->getPost('subject')),
            'message' => esc($this->request->getPost('message')),
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Intentamos insertar el mensaje de respuesta en la base de datos
        if ($this->messageModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Respuesta enviada correctamente'
            ]);
        }

        // En caso de error en la inserción, devolvemos un mensaje de error
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Error al enviar la respuesta'
        ]);
    }
}
