<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title',
        'description',
        'status',
        'priority',
        'user_id',
        'assigned_to',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;

    // Validación
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'description' => 'required',
        'status' => 'required|in_list[open,in_progress,resolved,closed]',
        'priority' => 'required|in_list[low,medium,high]'
    ];

    public function getTicketsWithDetails()
    {
        return $this->select('tickets.*, users.username as created_by, assigned.username as assigned_to_name')
            ->join('users', 'users.id = tickets.user_id')
            ->join('users as assigned', 'assigned.id = tickets.assigned_to', 'left')
            ->orderBy('tickets.created_at', 'DESC')
            ->findAll();
    }

    public function getTicketStats()
    {
        $db = \Config\Database::connect();

        return [
            'total' => $this->countAll(),
            'pending' => $this->where('status', 'open')->countAllResults(),
            'resolved' => $this->where('status', 'resolved')->countAllResults(),
            'average_time' => $db->query("
                SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_time 
                FROM tickets 
                WHERE status = 'resolved'
            ")->getRow()->avg_time ?? 0
        ];
    }
}
