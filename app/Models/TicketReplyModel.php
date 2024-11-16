<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketReplyModel extends Model
{
    protected $table = 'ticket_replies';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'ticket_id',
        'user_id',
        'message',
        'created_at'
    ];
    protected $useTimestamps = true;
    protected $updatedField = '';

    protected $validationRules = [
        'ticket_id' => 'required|integer',
        'message' => 'required'
    ];

    public function getRepliesWithUser($ticketId)
    {
        return $this->select('ticket_replies.*, users.username')
            ->join('users', 'users.id = ticket_replies.user_id')
            ->where('ticket_id', $ticketId)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }
}
