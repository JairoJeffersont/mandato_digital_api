<?php

namespace App\Models;

/**
 * Documento Model
 * 
 * Handles database operations for the documentos table.
 * Represents documents in the system with their relationships to document types.
 *
 * @package App\Models
 * @version 1.0.0
 */
class Documento extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'documentos';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'documento_id' => ['required' => true],
        'documento_titulo' => ['required' => true],
        'documento_resumo' => ['required' => false],
        'documento_arquivo' => ['required' => true],
        'documento_ano' => ['required' => true],
        'documento_tipo' => ['required' => true],
        'documento_orgao' => ['required' => true],
        'documento_criado_por' => ['required' => true],
        'documento_gabinete' => ['required' => true],
        'documento_criado_em' => ['required' => false],
        'documento_atualizado_em' => ['required' => false]
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