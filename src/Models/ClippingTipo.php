<?php

namespace App\Models;

/**
 * ClippingTipo Model
 * 
 * Handles database operations for the clipping_tipos table.
 * Represents different types of media clippings in the system.
 *
 * @package App\Models
 * @version 1.0.0
 */
class ClippingTipo extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'clipping_tipos';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'clipping_tipo_id' => ['required' => true],
        'clipping_tipo_nome' => ['required' => true],
        'clipping_tipo_descricao' => ['required' => false],
        'clipping_tipo_criado_por' => ['required' => true],
        'clipping_tipo_gabinete' => ['required' => true]
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