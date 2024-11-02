<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['class', 'key', 'value', 'type', 'context'];

    public function get_setting($key)
    {
        $result = $this->where('key', $key)->first();
        if ($result) {
            return $result['value'];
        }
        return null;
    }

    public function save_setting($key, $value, $type = 'string')
    {
        $data = [
            'class' => "Config\App",
            'key' => $key,
            'value' => $value,
            'type' => $type,
            'updated_at' => date("Y-m-d H:i:s"),
        ];

        $existing = $this->where('key', $key)->first();

        if (!$existing) {
            $data['created_at'] = date("Y-m-d H:i:s");
            return $this->insert($data);
        } else {
            return $this->where('key', $key)->set($data)->update();
        }
    }
}
