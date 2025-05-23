<?php

namespace App\Models;

/**
 * Usuario Model
 * 
 * Handles database operations for the usuario table.
 * Represents users in the system with their relationships to user types and offices.
 *
 * @package App\Models
 * @version 1.0.0
 */
class Usuario extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'usuario';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'usuario_id' => [
            'required' => true
        ],
        'usuario_tipo' => [
            'required' => true
        ],
        'usuario_gabinete' => [
            'required' => true
        ],
        'usuario_nome' => [
            'required' => true
        ],
        'usuario_email' => [
            'required' => true
        ],
        'usuario_aniversario' => [
            'required' => false
        ],
        'usuario_telefone' => [
            'required' => true
        ],
        'usuario_senha' => [
            'required' => true
        ],
        'usuario_token' => [
            'required' => false
        ],
        'usuario_foto' => [
            'required' => false
        ],
        'usuario_ativo' => [
            'required' => true
        ],
        'usuario_gestor' => [
            'required' => true
        ],
        'usuario_criado_em' => [
            'required' => false
        ],
        'usuario_atualizado_em' => [
            'required' => false
        ]
    ];

    /**
     * Get the column definitions with validation rules
     *
     * @return array
     */
    public function getColumns(): array {
        return $this->columns;
    }

    
} 