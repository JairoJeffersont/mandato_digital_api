<?php

namespace App\Models;

/**
 * Pessoa Model
 * 
 * Handles database operations for the pessoas table.
 * Represents people in the system with their relationships to types and professions.
 *
 * @package App\Models
 * @version 1.0.0
 */
class Pessoa extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'pessoas';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'pessoa_id' => ['required' => true],
        'pessoa_nome' => ['required' => true],
        'pessoa_email' => ['required' => true],
        'pessoa_telefone' => ['required' => false],
        'pessoa_endereco' => ['required' => false],
        'pessoa_bairro' => ['required' => false],
        'pessoa_municipio' => ['required' => false],
        'pessoa_estado' => ['required' => false],
        'pessoa_cep' => ['required' => false],
        'pessoa_sexo' => ['required' => false],
        'pessoa_facebook' => ['required' => false],
        'pessoa_instagram' => ['required' => false],
        'pessoa_x' => ['required' => false],
        'pessoa_foto' => ['required' => false],
        'pessoa_tipo' => ['required' => true],
        'pessoa_profissao' => ['required' => true],
        'pessoa_partido' => ['required' => false],
        'pessoa_informacoes' => ['required' => false],
        'pessoa_aniversario' => ['required' => false],
        'pessoa_orgao' => ['required' => true],
        'pessoa_criada_por' => ['required' => true],
        'pessoa_gabinete' => ['required' => true]
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