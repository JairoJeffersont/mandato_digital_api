<?php

namespace App\Models;

/**
 * PessoaTipo Model
 * 
 * Handles database operations for the pessoas_tipos table.
 * Represents different types of people in the system.
 *
 * @package App\Models
 * @version 1.0.0
 */
class PessoaTipo extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'pessoas_tipos';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'pessoa_tipo_id' => ['required' => true],
        'pessoa_tipo_nome' => ['required' => true],
        'pessoa_tipo_descricao' => ['required' => false],
        'pessoa_tipo_criado_por' => ['required' => true],
        'pessoa_tipo_gabinete' => ['required' => true]
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