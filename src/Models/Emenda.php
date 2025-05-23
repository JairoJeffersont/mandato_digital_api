<?php

namespace App\Models;

/**
 * Emenda Model
 * 
 * Handles database operations for the emendas table.
 * Represents amendments in the system with their relationships to status and objectives.
 *
 * @package App\Models
 * @version 1.0.0
 */
class Emenda extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'emendas';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'emenda_id' => ['required' => true],
        'emenda_numero' => ['required' => true],
        'emenda_ano' => ['required' => true],
        'emenda_valor' => ['required' => false],
        'emenda_descricao' => ['required' => true],
        'emenda_status' => ['required' => true],
        'emenda_orgao' => ['required' => true],
        'emenda_municipio' => ['required' => true],
        'emenda_estado' => ['required' => true],
        'emenda_objetivo' => ['required' => true],
        'emenda_informacoes' => ['required' => false],
        'emenda_tipo' => ['required' => true],
        'emenda_criado_por' => ['required' => true],
        'emenda_gabinete' => ['required' => true],
        'emenda_criada_em' => ['required' => false],
        'emenda_atualizada_em' => ['required' => false]
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