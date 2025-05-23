<?php

namespace App\Helpers;

/**
 * Field Validation Helper Class
 * 
 * This class provides functionality to validate input data against a defined
 * schema of columns and their validation rules. It checks for required fields
 * and ensures no unauthorized fields are present in the input data.
 *
 * Validation Rules Structure:
 * [
 *     'field_name' => [
 *         'required' => boolean
 *     ]
 * ]
 *
 * Error Response Structure:
 * [
 *     'not_allowed' => array of field names that are not in the schema
 *     'missing_required' => array of required fields that are missing
 * ]
 *
 * @package App\Helpers
 * @version 1.0.0
 */
class ValidateFields {

    /**
     * Validates input data against a column schema
     *
     * This method performs two types of validation:
     * 1. Checks if any fields in the input data are not defined in the schema
     * 2. Verifies that all required fields are present and not empty
     *
     * @param array $columns Schema defining allowed fields and their rules
     *                      Format: ['field_name' => ['required' => bool]]
     * @param mixed $data   Input data to validate (expected to be an array)
     * 
     * @return array Empty array if validation passes, otherwise array with errors:
     *               [
     *                   'not_allowed' => array of invalid fields,
     *                   'missing_required' => array of missing required fields
     *               ]
     */
    public static function validateFields(array $columns, $data): array {
        $errors = [
            'not_allowed' => [],
            'missing_required' => []
        ];
    
        if (!is_array($data) || empty($data)) {
            foreach ($columns as $field => $rules) {
                if (!empty($rules['required']) && $rules['required'] === true) {
                    $errors['missing_required'][] = $field;
                }
            }
            return $errors;
        }
    
        $allowedColumns = array_keys($columns);
    
        foreach (array_keys($data) as $field) {
            if (!in_array($field, $allowedColumns)) {
                $errors['not_allowed'][] = $field;
            }
        }
    
        foreach ($columns as $field => $rules) {
            if (!empty($rules['required']) && $rules['required'] === true) {
                if (!isset($data[$field]) || $data[$field] === null || $data[$field] === '') {
                    $errors['missing_required'][] = $field;
                }
            }
        }
    
        if (empty($errors['not_allowed']) && empty($errors['missing_required'])) {
            return [];
        }
    
        return $errors;
    }
    
}
