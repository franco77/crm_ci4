<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'id';
    protected $allowedFields = ['parent_id', 'sender_id', 'receiver_id', 'subject', 'message', 'created_at', 'read_at'];
    protected $returnType = 'array';
}
