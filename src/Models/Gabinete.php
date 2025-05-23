<?php

namespace App\Models;

class Gabinete extends BaseModel {
    protected $table = 'gabinete';  // Sem tipo

    protected array $columns = [
        'gabinete_id' => ['required' => true],
        'gabinete_nome' => ['required' => true],
        'gabinete_estado' => ['required' => true],
        'gabinete_assinaturas' => ['required' => true],
        'gabinete_tipo' => ['required' => true],
        'gabinete_criado_em' => ['required' => false],
        'gabinete_atualizado_em' => ['required' => false],
    ];

    public function getColumns(): array {
        return $this->columns ?? [];
    }
}
