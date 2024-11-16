<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use App\Models\TicketModel;
use App\Models\TicketReplyModel;
use App\Models\TicketAttachmentModel;
use App\Models\TicketActivityModel; // Agregar esta línea
use IonAuth\Libraries\IonAuth;

class Tickets extends BaseController
{
    protected $ticketModel;
    protected $replyModel;
    protected $attachmentModel;
    protected $activityModel; // Agregar esta línea
    protected $ionAuth;

    public function __construct()
    {
        $this->ticketModel = new TicketModel();
        $this->replyModel = new TicketReplyModel();
        $this->attachmentModel = new TicketAttachmentModel();
        $this->activityModel = new TicketActivityModel(); // Agregar esta línea
        $this->ionAuth = new IonAuth();
    }

    public function index()
    {

        if ($this->request->isAJAX()) {
            return $this->filterTickets();
        }

        $data = [
            'tickets' => $this->ticketModel->getTicketsWithDetails(),
            'stats' => $this->ticketModel->getTicketStats(),
            'recent_activity' => $this->activityModel->getRecentActivity(),
        ];

        return view('admin/tickets/index', $data);
    }


    private function filterTickets()
    {
        $status = $this->request->getGet('status');
        $priority = $this->request->getGet('priority');
        $search = $this->request->getGet('search');

        $builder = $this->ticketModel->select('tickets.*, users.username as created_by, assigned.username as assigned_to_name')
            ->join('users', 'users.id = tickets.user_id')
            ->join('users as assigned', 'assigned.id = tickets.assigned_to', 'left');

        // Aplicar filtros
        if (!empty($status)) {
            $builder->where('tickets.status', $status);
        }
        if (!empty($priority)) {
            $builder->where('tickets.priority', $priority);
        }
        if (!empty($search)) {
            $builder->groupStart()
                ->like('tickets.title', $search)
                ->orLike('tickets.description', $search)
                ->groupEnd();
        }

        $tickets = $builder->orderBy('tickets.created_at', 'DESC')->findAll();

        // Devolver solo la vista parcial con los tickets filtrados
        return view('admin/tickets/_tickets_list', ['tickets' => $tickets]);
    }

    public function create()
    {
        if (!$this->ionAuth->loggedIn()) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $rules = [
            'title' => 'required|min_length[3]',
            'description' => 'required',
            'priority' => 'required|in_list[low,medium,high]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['error' => $this->validator->getErrors()])->setStatusCode(400);
        }

        $userId = $this->ionAuth->user()->row()->id;
        $ticketData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'priority' => $this->request->getPost('priority'),
            'status' => 'open',
            'user_id' => $userId
        ];

        $this->ticketModel->insert($ticketData);
        $ticketId = $this->ticketModel->insertID();

        // Registrar la actividad de creación
        $this->activityModel->logActivity(
            $ticketId,
            $userId,
            'create',
            'Nuevo ticket creado: ' . $ticketData['title']
        );

        // Manejar archivos adjuntos
        $files = $this->request->getFiles();
        if (!empty($files['attachments'])) {
            foreach ($files['attachments'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads/tickets', $newName);

                    $this->attachmentModel->insert([
                        'ticket_id' => $ticketId,
                        'file_name' => $file->getClientName(),
                        'file_path' => 'uploads/tickets/' . $newName,
                        'file_type' => $file->getClientMimeType(),
                        'uploaded_by' => $userId
                    ]);

                    // Registrar la actividad de archivo adjunto
                    $this->activityModel->logActivity(
                        $ticketId,
                        $userId,
                        'attachment',
                        'Archivo adjunto agregado: ' . $file->getClientName()
                    );
                }
            }
        }

        return $this->response->setJSON(['success' => true, 'ticket_id' => $ticketId]);
    }





    public function details($ticketId)
    {
        if (!$this->ionAuth->loggedIn()) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        // Obtener detalles del ticket
        $ticket = $this->ticketModel->select('tickets.*, 
            creator.username as created_by, 
            assigned.username as assigned_to_name')
            ->join('users as creator', 'creator.id = tickets.user_id')
            ->join('users as assigned', 'assigned.id = tickets.assigned_to', 'left')
            ->find($ticketId);

        if (!$ticket) {
            return $this->response->setJSON(['error' => 'Ticket no encontrado'])->setStatusCode(404);
        }

        // Obtener respuestas del ticket
        $replies = $this->replyModel->select('ticket_replies.*, users.username')
            ->join('users', 'users.id = ticket_replies.user_id')
            ->where('ticket_id', $ticketId)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        // Obtener archivos adjuntos
        $attachments = $this->attachmentModel->where('ticket_id', $ticketId)
            ->findAll();

        // Formatear las URLs de los archivos adjuntos
        foreach ($attachments as &$attachment) {
            $attachment['download_url'] = base_url('tickets/download/' . $attachment['id']);
        }

        // Preparar la respuesta
        $response = [
            'id' => $ticket['id'],
            'title' => $ticket['title'],
            'description' => $ticket['description'],
            'status' => $ticket['status'],
            'priority' => $ticket['priority'],
            'created_by' => $ticket['created_by'],
            'assigned_to' => $ticket['assigned_to_name'],
            'created_at' => $ticket['created_at'],
            'updated_at' => $ticket['updated_at'],
            'replies' => array_map(function ($reply) {
                return [
                    'id' => $reply['id'],
                    'message' => $reply['message'],
                    'username' => $reply['username'],
                    'created_at' => $reply['created_at']
                ];
            }, $replies),
            'attachments' => array_map(function ($attachment) {
                return [
                    'id' => $attachment['id'],
                    'file_name' => $attachment['file_name'],
                    'file_path' => $attachment['download_url'],
                    'file_type' => $attachment['file_type'],
                    'created_at' => $attachment['created_at']
                ];
            }, $attachments)
        ];

        return $this->response->setJSON($response);
    }

    // Método para descargar archivos adjuntos
    public function download($attachmentId)
    {
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('/auth/login');
        }

        $attachment = $this->attachmentModel->find($attachmentId);

        if (!$attachment) {
            return $this->response->setJSON(['error' => 'Archivo no encontrado'])->setStatusCode(404);
        }

        $path = WRITEPATH . $attachment['file_path'];

        if (!file_exists($path)) {
            return $this->response->setJSON(['error' => 'Archivo no encontrado'])->setStatusCode(404);
        }

        return $this->response->download($path, null)->setFileName($attachment['file_name']);
    }




    // Modificar el método reply para registrar la actividad
    public function reply($ticketId)
    {
        if (!$this->ionAuth->loggedIn()) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $rules = ['message' => 'required'];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['error' => $this->validator->getErrors()])->setStatusCode(400);
        }

        $userId = $this->ionAuth->user()->row()->id;
        $replyData = [
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'message' => $this->request->getPost('message')
        ];

        $this->replyModel->insert($replyData);

        // Registrar la actividad de respuesta
        $this->activityModel->logActivity(
            $ticketId,
            $userId,
            'reply',
            'Nueva respuesta agregada por ' . $this->ionAuth->user()->row()->username
        );

        // Actualizar el estado del ticket si es necesario
        if ($newStatus = $this->request->getPost('status')) {
            $this->ticketModel->update($ticketId, ['status' => $newStatus]);

            // Registrar la actividad de cambio de estado
            $this->activityModel->logActivity(
                $ticketId,
                $userId,
                'status',
                'Estado actualizado a: ' . $newStatus
            );
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function updateStatus($ticketId)
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $status = $this->request->getPost('status');
        if (!in_array($status, ['open', 'in_progress', 'resolved', 'closed'])) {
            return $this->response->setJSON(['error' => 'Estado no válido'])->setStatusCode(400);
        }

        $this->ticketModel->update($ticketId, ['status' => $status]);
        return $this->response->setJSON(['success' => true]);
    }

    public function assign($ticketId)
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $assignedTo = $this->request->getPost('user_id');
        $this->ticketModel->update($ticketId, ['assigned_to' => $assignedTo]);

        return $this->response->setJSON(['success' => true]);
    }
}
