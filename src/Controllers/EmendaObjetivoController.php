<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\EmendaObjetivo;
use PDOException;

/**
 * EmendaObjetivo Controller
 * 
 * Handles all operations related to the EmendaObjetivo (Amendment Objective) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new amendment objective with UUID generation
 * - Update existing amendment objective
 * - Retrieve single or all amendment objectives
 * - Delete amendment objective with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class EmendaObjetivoController {

    /**
     * Instance of the EmendaObjetivo model
     *
     * @var EmendaObjetivo
     */
    private EmendaObjetivo $emendaObjetivoModel;

    /**
     * Constructor
     * 
     * Initializes the EmendaObjetivo model instance
     */
    public function __construct() {
        $this->emendaObjetivoModel = new EmendaObjetivo();
    }

    /**
     * Creates a new Amendment Objective
     * 
     * Generates a UUID for the new amendment objective and validates input data
     * before creation.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Amendment objective created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['emenda_objetivo_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->emendaObjetivoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->emendaObjetivoModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Objetivo de emenda criado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Objetivo de emenda já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Amendment Objective
     * 
     * Validates input data and updates the amendment objective if found.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the amendment objective ID
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

            $errors = ValidateFields::validateFields($this->emendaObjetivoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->emendaObjetivoModel->update('emenda_objetivo_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Objetivo de emenda atualizado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Amendment Objectives by Cabinet
     * 
     * Returns a list of all amendment objectives in the system for a specific cabinet.
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
            $objetivos = $this->emendaObjetivoModel->getAllByColumn('emenda_objetivo_gabinete', $gabinete);
            
            if (empty($objetivos)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhum objetivo de emenda encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $objetivos);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Amendment Objective by ID
     * 
     * Finds and returns an amendment objective by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the amendment objective ID
     * 
     * @return Response
     *         200: Success with amendment objective data
     *         404: Amendment objective not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $objetivo = $this->emendaObjetivoModel->findOne('emenda_objetivo_id', $id);

            if (!$objetivo) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Objetivo de emenda não encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $objetivo);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes an Amendment Objective
     * 
     * Removes an amendment objective if it exists and has no associated amendments.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the amendment objective ID
     * 
     * @return Response
     *         200: Deletion successful
     *         400: Cannot delete due to foreign key constraints
     *         404: Amendment objective not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $objetivo = $this->emendaObjetivoModel->findOne('emenda_objetivo_id', $id);

            if (!$objetivo) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Objetivo de emenda não encontrado');
            }

            $this->emendaObjetivoModel->delete('emenda_objetivo_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Objetivo de emenda deletado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Objetivo de emenda não pode ser deletado pois possui emendas associadas');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 