<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\Clipping;
use PDOException;

/**
 * Clipping Controller
 * 
 * Handles all operations related to the Clipping (Media Clipping) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new clipping with UUID generation
 * - Update existing clipping
 * - Retrieve single or all clippings
 * - Delete clipping with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 * - File handling for clipping attachments
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class ClippingController {

    /**
     * Instance of the Clipping model
     *
     * @var Clipping
     */
    private Clipping $clippingModel;

    /**
     * Constructor
     * 
     * Initializes the Clipping model instance
     */
    public function __construct() {
        $this->clippingModel = new Clipping();
    }

    /**
     * Creates a new Clipping
     * 
     * Generates a UUID for the new clipping and validates input data
     * before creation. Handles file upload for clipping attachment.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Clipping created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['clipping_id'] = Uuid::uuid4()->toString();

            // Set current date if not provided
            if (!isset($data['clipping_data'])) {
                $data['clipping_data'] = date('Y-m-d H:i:s');
            }

            $errors = ValidateFields::validateFields($this->clippingModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->clippingModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Clipping criado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Clipping já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Clipping
     * 
     * Validates input data and updates the clipping if found.
     * Handles file upload for clipping attachment updates.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the clipping ID
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

            $errors = ValidateFields::validateFields($this->clippingModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->clippingModel->update('clipping_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Clipping atualizado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Clippings by Cabinet
     * 
     * Returns a list of all clippings in the system for a specific cabinet.
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
            $clippings = $this->clippingModel->getAllByColumn('clipping_gabinete', $gabinete);
            
            if (empty($clippings)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhum clipping encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $clippings);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Clipping by ID
     * 
     * Finds and returns a clipping by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the clipping ID
     * 
     * @return Response
     *         200: Success with clipping data
     *         404: Clipping not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $clipping = $this->clippingModel->findOne('clipping_id', $id);

            if (!$clipping) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Clipping não encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $clipping);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Clipping
     * 
     * Removes a clipping if it exists and has no dependencies.
     * Also removes the associated file from storage.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the clipping ID
     * 
     * @return Response
     *         200: Deletion successful
     *         404: Clipping not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $clipping = $this->clippingModel->findOne('clipping_id', $id);

            if (!$clipping) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Clipping não encontrado');
            }

            // Delete the associated file if it exists
            if (isset($clipping['clipping_arquivo']) && file_exists($clipping['clipping_arquivo'])) {
                unlink($clipping['clipping_arquivo']);
            }

            $this->clippingModel->delete('clipping_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Clipping deletado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Clipping não pode ser deletado pois possui dependências');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 