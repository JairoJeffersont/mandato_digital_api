<?php

namespace App\Models;

/**
 * UsuarioTipo Model
 * 
 * Handles database operations for the usuario_tipo table.
 * Represents different types of users in the system.
 *
 * @package App\Models
 * @version 1.0.0
 */
class UsuarioTipo extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'usuario_tipo';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'usuario_tipo_id' => [
            'required' => true
        ],
        'usuario_tipo_nome' => [
            'required' => true
        ],
        'usuario_tipo_descricao' => [
            'required' => true
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