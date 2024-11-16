<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketActivityModel extends Model
{
    protected $table = 'ticket_activity';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'ticket_id',
        'user_id',
        'activity_type',
        'description',
        'created_at'
    ];
    protected $useTimestamps = true;
    protected $updatedField = '';

    public function getRecentActivity($limit = 10)
    {
        return $this->select('ticket_activity.*, users.username, tickets.title as ticket_title')
            ->join('users', 'users.id = ticket_activity.user_id')
            ->join('tickets', 'tickets.id = ticket_activity.ticket_id')
            ->orderBy('ticket_activity.created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    public function logActivity($ticketId, $userId, $activityType, $description)
    {
        return $this->insert([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description
        ]);
    }
}
