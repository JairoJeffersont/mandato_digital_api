<?php

namespace App\Models;

/**
 * PessoaProfissao Model
 * 
 * Handles database operations for the pessoas_profissoes table.
 * Represents different professions of people in the system.
 *
 * @package App\Models
 * @version 1.0.0
 */
class PessoaProfissao extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'pessoas_profissoes';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'pessoas_profissoes_id' => ['required' => true],
        'pessoas_profissoes_nome' => ['required' => true],
        'pessoas_profissoes_descricao' => ['required' => false],
        'pessoas_profissoes_criado_por' => ['required' => true],
        'pessoas_profissoes_gabinete' => ['required' => true]
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