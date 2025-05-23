<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\ClippingTipo;
use PDOException;

/**
 * ClippingTipo Controller
 * 
 * Handles all operations related to the ClippingTipo (Media Clipping Type) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new clipping type with UUID generation
 * - Update existing clipping type
 * - Retrieve single or all clipping types
 * - Delete clipping type with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class ClippingTipoController {

    /**
     * Instance of the ClippingTipo model
     *
     * @var ClippingTipo
     */
    private ClippingTipo $clippingTipoModel;

    /**
     * Constructor
     * 
     * Initializes the ClippingTipo model instance
     */
    public function __construct() {
        $this->clippingTipoModel = new ClippingTipo();
    }

    /**
     * Creates a new Clipping Type
     * 
     * Generates a UUID for the new clipping type and validates input data
     * before creation.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Clipping type created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['clipping_tipo_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->clippingTipoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->clippingTipoModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Tipo de clipping criado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Tipo de clipping já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Clipping Type
     * 
     * Validates input data and updates the clipping type if found.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the clipping type ID
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

            $errors = ValidateFields::validateFields($this->clippingTipoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->clippingTipoModel->update('clipping_tipo_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Tipo de clipping atualizado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Clipping Types by Cabinet
     * 
     * Returns a list of all clipping types in the system for a specific cabinet.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the cabinet ID
     * 
     * @return Response
     *         200: Success with data or empty message
     *         500: Server error
     */
    public function getAll(Request $request, Response $response, array $args): Response {
        try {
            $gabinete = Sanitize::clean($args['gabinete']);
            $tipos = $this->clippingTipoModel->getAllByColumn('clipping_tipo_gabinete', $gabinete);
            
            if (empty($tipos)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhum tipo de clipping encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $tipos);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Clipping Type by ID
     * 
     * Finds and returns a clipping type by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the clipping type ID
     * 
     * @return Response
     *         200: Success with clipping type data
     *         404: Clipping type not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $tipo = $this->clippingTipoModel->findOne('clipping_tipo_id', $id);

            if (!$tipo) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Tipo de clipping não encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $tipo);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Clipping Type
     * 
     * Removes a clipping type if it exists and has no associated clippings.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the clipping type ID
     * 
     * @return Response
     *         200: Deletion successful
     *         400: Cannot delete due to foreign key constraints
     *         404: Clipping type not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $tipo = $this->clippingTipoModel->findOne('clipping_tipo_id', $id);

            if (!$tipo) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Tipo de clipping não encontrado');
            }

            $this->clippingTipoModel->delete('clipping_tipo_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Tipo de clipping deletado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Tipo de clipping não pode ser deletado pois possui clippings associados');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 