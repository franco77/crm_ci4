<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketAttachmentModel extends Model
{
    protected $table = 'ticket_attachments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'ticket_id',
        'file_name',
        'file_path',
        'file_type',
        'uploaded_by',
        'created_at'
    ];
    protected $useTimestamps = true;
    protected $updatedField = '';
}
