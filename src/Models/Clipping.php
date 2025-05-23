<?php

namespace App\Models;

/**
 * Clipping Model
 * 
 * Handles database operations for the clippings table.
 * Represents media clippings in the system with their relationships to types.
 *
 * @package App\Models
 * @version 1.0.0
 */
class Clipping extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'clippings';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'clipping_id' => ['required' => true],
        'clipping_titulo' => ['required' => true],
        'clipping_conteudo' => ['required' => true],
        'clipping_tipo' => ['required' => true],
        'clipping_fonte' => ['required' => true],
        'clipping_data' => ['required' => true],
        'clipping_link' => ['required' => false],
        'clipping_arquivo' => ['required' => false],
        'clipping_arquivo_nome' => ['required' => false],
        'clipping_arquivo_tipo' => ['required' => false],
        'clipping_arquivo_tamanho' => ['required' => false],
        'clipping_tags' => ['required' => false],
        'clipping_informacoes' => ['required' => false],
        'clipping_criado_por' => ['required' => true],
        'clipping_gabinete' => ['required' => true]
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