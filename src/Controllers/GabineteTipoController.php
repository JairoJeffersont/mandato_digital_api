<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\GabineteTipo;
use PDOException;

/**
 * GabineteTipo Controller
 * 
 * Handles all operations related to the GabineteTipo (Cabinet Type) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new cabinet type with UUID generation
 * - Update existing cabinet type
 * - Retrieve single or all cabinet types
 * - Delete cabinet type with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class GabineteTipoController {

    /**
     * Instance of the GabineteTipo model
     *
     * @var GabineteTipo
     */
    private GabineteTipo $gabineteTipoModel;

    /**
     * Constructor
     * 
     * Initializes the GabineteTipo model instance
     */
    public function __construct() {
        $this->gabineteTipoModel = new GabineteTipo();
    }

    /**
     * Creates a new Cabinet Type
     * 
     * Generates a UUID for the new cabinet type and validates input data
     * before creation.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Cabinet type created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['gabinete_tipo_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->gabineteTipoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->gabineteTipoModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Tipo de gabinete criado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Tipo de gabinete já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Cabinet Type
     * 
     * Validates input data and updates the cabinet type if found.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the cabinet type ID
     * 
     * @return Response
     *         200: Update successful
     *         400: Invalid input data
     *         500: Server error
     */
    public function update(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);

            $errors = ValidateFields::validateFields($this->gabineteTipoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->gabineteTipoModel->update('gabinete_tipo_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Tipo de gabinete atualizado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Cabinet Types
     * 
     * Returns a list of all cabinet types in the system.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         200: Success with data or empty message
     *         500: Server error
     */
    public function getAll(Request $request, Response $response): Response {
        try {
            $tipos = $this->gabineteTipoModel->getAll();
            if (empty($tipos)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhum tipo de gabinete encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $tipos);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Cabinet Type by ID
     * 
     * Finds and returns a cabinet type by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the cabinet type ID
     * 
     * @return Response
     *         200: Success with cabinet type data
     *         404: Cabinet type not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $tipo = $this->gabineteTipoModel->findOne('gabinete_tipo_id', $id);

            if (!$tipo) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Tipo de gabinete não encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $tipo);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Cabinet Type
     * 
     * Removes a cabinet type if it exists and has no associated cabinets.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the cabinet type ID
     * 
     * @return Response
     *         200: Deletion successful
     *         400: Cannot delete due to foreign key constraints
     *         404: Cabinet type not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $tipo = $this->gabineteTipoModel->findOne('gabinete_tipo_id', $id);

            if (!$tipo) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Tipo de gabinete não encontrado');
            }

            $this->gabineteTipoModel->delete('gabinete_tipo_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Tipo de gabinete deletado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Tipo de gabinete não pode ser deletado pois possui gabinetes associados');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 