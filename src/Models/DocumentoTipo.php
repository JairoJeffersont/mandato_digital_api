<?php

namespace App\Models;

/**
 * DocumentoTipo Model
 * 
 * Handles database operations for the documentos_tipos table.
 * Represents different types of documents in the system.
 *
 * @package App\Models
 * @version 1.0.0
 */
class DocumentoTipo extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'documentos_tipos';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'documento_tipo_id' => ['required' => true],
        'documento_tipo_nome' => ['required' => true],
        'documento_tipo_descricao' => ['required' => false],
        'documento_tipo_criado_por' => ['required' => true],
        'documento_tipo_gabinete' => ['required' => true]
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