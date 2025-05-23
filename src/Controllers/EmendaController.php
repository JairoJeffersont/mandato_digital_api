<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\Emenda;
use PDOException;

/**
 * Emenda Controller
 * 
 * Handles all operations related to the Emenda (Amendment) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new amendment with UUID generation
 * - Update existing amendment
 * - Retrieve single or all amendments
 * - Delete amendment with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class EmendaController {

    /**
     * Instance of the Emenda model
     *
     * @var Emenda
     */
    private Emenda $emendaModel;

    /**
     * Constructor
     * 
     * Initializes the Emenda model instance
     */
    public function __construct() {
        $this->emendaModel = new Emenda();
    }

    /**
     * Creates a new Amendment
     * 
     * Generates a UUID for the new amendment and validates input data
     * before creation.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Amendment created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['emenda_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->emendaModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->emendaModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Emenda criada com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Emenda já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Amendment
     * 
     * Validates input data and updates the amendment if found.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the amendment ID
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

            $errors = ValidateFields::validateFields($this->emendaModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->emendaModel->update('emenda_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Emenda atualizada com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Amendments by Cabinet
     * 
     * Returns a list of all amendments in the system for a specific cabinet.
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
            $emendas = $this->emendaModel->getAllByColumn('emenda_gabinete', $gabinete);
            
            if (empty($emendas)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhuma emenda encontrada');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $emendas);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Amendment by ID
     * 
     * Finds and returns an amendment by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the amendment ID
     * 
     * @return Response
     *         200: Success with amendment data
     *         404: Amendment not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $emenda = $this->emendaModel->findOne('emenda_id', $id);

            if (!$emenda) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Emenda não encontrada');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $emenda);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes an Amendment
     * 
     * Removes an amendment if it exists and has no dependencies.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the amendment ID
     * 
     * @return Response
     *         200: Deletion successful
     *         404: Amendment not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $emenda = $this->emendaModel->findOne('emenda_id', $id);

            if (!$emenda) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Emenda não encontrada');
            }

            $this->emendaModel->delete('emenda_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Emenda deletada com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Emenda não pode ser deletada pois possui dependências');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 