<?php

namespace App\Models;

/**
 * Postagem Model
 * 
 * Handles database operations for the postagens table.
 * Represents posts in the system with their relationships to status.
 *
 * @package App\Models
 * @version 1.0.0
 */
class Postagem extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'postagens';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'postagem_id' => ['required' => true],
        'postagem_titulo' => ['required' => true],
        'postagem_conteudo' => ['required' => true],
        'postagem_status' => ['required' => true],
        'postagem_data_publicacao' => ['required' => true],
        'postagem_data_atualizacao' => ['required' => false],
        'postagem_imagem' => ['required' => false],
        'postagem_imagem_nome' => ['required' => false],
        'postagem_imagem_tipo' => ['required' => false],
        'postagem_imagem_tamanho' => ['required' => false],
        'postagem_tags' => ['required' => false],
        'postagem_informacoes' => ['required' => false],
        'postagem_criado_por' => ['required' => true],
        'postagem_gabinete' => ['required' => true]
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