<?php

namespace App\Models;

/**
 * PostagemStatus Model
 * 
 * Handles database operations for the postagem_status table.
 * Represents different status types for posts in the system.
 *
 * @package App\Models
 * @version 1.0.0
 */
class PostagemStatus extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'postagem_status';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'postagem_status_id' => ['required' => true],
        'postagem_status_nome' => ['required' => true],
        'postagem_status_descricao' => ['required' => false],
        'postagem_status_criado_por' => ['required' => true],
        'postagem_status_gabinete' => ['required' => true]
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