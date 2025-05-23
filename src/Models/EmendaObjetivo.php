<?php

namespace App\Models;

/**
 * EmendaObjetivo Model
 * 
 * Handles database operations for the emendas_objetivos table.
 * Represents different objectives for amendments in the system.
 *
 * @package App\Models
 * @version 1.0.0
 */
class EmendaObjetivo extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'emendas_objetivos';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'emenda_objetivo_id' => ['required' => true],
        'emenda_objetivo_nome' => ['required' => true],
        'emenda_objetivo_descricao' => ['required' => false],
        'emenda_objetivo_criado_por' => ['required' => true],
        'emenda_objetivo_gabinete' => ['required' => true]
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