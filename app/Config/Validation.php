<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    public $tickets = [
        'title' => [
            'rules' => 'required|min_length[5]|max_length[255]',
            'errors' => [
                'required' => 'El título es requerido',
                'min_length' => 'El título debe tener al menos 5 caracteres',
                'max_length' => 'El título no puede exceder los 255 caracteres'
            ]
        ],
        'description' => [
            'rules' => 'required|min_length[10]',
            'errors' => [
                'required' => 'La descripción es requerida',
                'min_length' => 'La descripción debe tener al menos 10 caracteres'
            ]
        ],
        'priority' => [
            'rules' => 'required|in_list[low,medium,high]',
            'errors' => [
                'required' => 'La prioridad es requerida',
                'in_list' => 'La prioridad debe ser baja, media o alta'
            ]
        ],
        'status' => [
            'rules' => 'permit_empty|in_list[open,in_progress,resolved,closed]',
            'errors' => [
                'in_list' => 'El estado debe ser válido'
            ]
        ]
    ];
}
