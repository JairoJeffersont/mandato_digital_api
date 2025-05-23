<?php

namespace App\Models;

/**
 * EmendaStatus Model
 * 
 * Handles database operations for the emendas_status table.
 * Represents different status types for amendments in the system.
 *
 * @package App\Models
 * @version 1.0.0
 */
class EmendaStatus extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'emendas_status';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'emenda_status_id' => ['required' => true],
        'emenda_status_nome' => ['required' => true],
        'emenda_status_descricao' => ['required' => false],
        'emenda_status_criado_por' => ['required' => true],
        'emenda_status_gabinete' => ['required' => true]
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