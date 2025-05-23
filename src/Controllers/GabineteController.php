<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\Gabinete;
use PDOException;

/**
 * Gabinete Controller
 * 
 * Handles all operations related to the Gabinete (Cabinet) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new gabinete with UUID generation
 * - Update existing gabinete
 * - Retrieve single or all gabinetes
 * - Delete gabinete with foreign key constraint checking
 * - Input validation
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class GabineteController {

    /**
     * Instance of the Gabinete model
     *
     * @var Gabinete
     */
    private Gabinete $gabineteModel;

    /**
     * Constructor
     * 
     * Initializes the Gabinete model instance
     */
    public function __construct() {
        $this->gabineteModel = new Gabinete();
    }

    /**
     * Creates a new Gabinete
     * 
     * Generates a UUID for the new gabinete and validates input data
     * before creation.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Gabinete created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            $data = Sanitize::clean($data);

            $data['gabinete_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->gabineteModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->gabineteModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Gabinete criado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Gabinete já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Gabinete
     * 
     * Validates input data and updates the gabinete if found.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the gabinete ID
     * 
     * @return Response
     *         200: Update successful
     *         400: Invalid input data
     *         500: Server error
     */
    public function update(Request $request, Response $response, array $args): Response {
        try {
            $id = $args['id'];
            $data = json_decode($request->getBody(), true);

            $data = Sanitize::clean($data);

            
            $errors = ValidateFields::validateFields($this->gabineteModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->gabineteModel->update('gabinete_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Gabinete atualizado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Gabinetes
     * 
     * Returns a list of all gabinetes in the system.
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
            $gabinetes = $this->gabineteModel->getAll();
            if (empty($gabinetes)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhum gabinete encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $gabinetes);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Gabinete by ID
     * 
     * Finds and returns a gabinete by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the gabinete ID
     * 
     * @return Response
     *         200: Success with gabinete data
     *         404: Gabinete not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = $args['id'];
            $gabinete = $this->gabineteModel->findOne('gabinete_id', $id);

            if (!$gabinete) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Gabinete não encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $gabinete);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Gabinete
     * 
     * Removes a gabinete if it exists and has no associated users.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the gabinete ID
     * 
     * @return Response
     *         200: Deletion successful
     *         400: Cannot delete due to foreign key constraints
     *         404: Gabinete not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = $args['id'];

            $gabinete = $this->gabineteModel->findOne('gabinete_id', $id);

            if (!$gabinete) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Gabinete não encontrado');
            }

            $this->gabineteModel->delete('gabinete_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Gabinete deletado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Gabinete não pode ser deletado pois possui usuários associados');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
}
