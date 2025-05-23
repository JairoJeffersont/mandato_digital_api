<?php

namespace App\Models;

/**
 * OrgaoTipo Model
 * 
 * Handles database operations for the orgaos_tipos table.
 * Represents different types of organizations in the system.
 *
 * @package App\Models
 * @version 1.0.0
 */
class OrgaoTipo extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'orgaos_tipos';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'orgao_tipo_id' => ['required' => true],
        'orgao_tipo_nome' => ['required' => true],
        'orgao_tipo_descricao' => ['required' => false],
        'orgao_tipo_criado_por' => ['required' => true],
        'orgao_tipo_gabinete' => ['required' => true]
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