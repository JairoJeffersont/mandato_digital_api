<?php

namespace App\Models;

/**
 * Orgao Model
 * 
 * Handles database operations for the orgaos table.
 * Represents organizations in the system.
 *
 * @package App\Models
 * @version 1.0.0
 */
class Orgao extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'orgaos';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'orgao_id' => ['required' => true],
        'orgao_nome' => ['required' => true],
        'orgao_email' => ['required' => true],
        'orgao_telefone' => ['required' => false],
        'orgao_endereco' => ['required' => false],
        'orgao_bairro' => ['required' => false],
        'orgao_municipio' => ['required' => true],
        'orgao_estado' => ['required' => true],
        'orgao_cep' => ['required' => false],
        'orgao_tipo' => ['required' => true],
        'orgao_informacoes' => ['required' => false],
        'orgao_site' => ['required' => false],
        'orgao_criado_por' => ['required' => true],
        'orgao_gabinete' => ['required' => true]
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