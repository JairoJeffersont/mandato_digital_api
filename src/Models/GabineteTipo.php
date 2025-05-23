<?php

namespace App\Models;

/**
 * GabineteTipo Model
 * 
 * Handles database operations for the gabinete_tipo table.
 * Represents different types of cabinets in the system.
 *
 * @package App\Models
 * @version 1.0.0
 */
class GabineteTipo extends BaseModel {
    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'gabinete_tipo';

    /**
     * Column definitions with validation rules
     *
     * @var array
     */
    private array $columns = [
        'gabinete_tipo_id' => ['required' => true],
        'gabinete_tipo_nome' => ['required' => true],
        'gabinete_tipo_informacoes' => ['required' => false]
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
