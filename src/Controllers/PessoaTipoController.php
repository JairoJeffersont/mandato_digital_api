<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\PessoaTipo;
use PDOException;

/**
 * PessoaTipo Controller
 * 
 * Handles all operations related to the PessoaTipo (Person Type) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new person type with UUID generation
 * - Update existing person type
 * - Retrieve single or all person types
 * - Delete person type with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class PessoaTipoController {

    /**
     * Instance of the PessoaTipo model
     *
     * @var PessoaTipo
     */
    private PessoaTipo $pessoaTipoModel;

    /**
     * Constructor
     * 
     * Initializes the PessoaTipo model instance
     */
    public function __construct() {
        $this->pessoaTipoModel = new PessoaTipo();
    }

    /**
     * Creates a new Person Type
     * 
     * Generates a UUID for the new person type and validates input data
     * before creation.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Person type created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['pessoa_tipo_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->pessoaTipoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->pessoaTipoModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Tipo de pessoa criado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Tipo de pessoa já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Person Type
     * 
     * Validates input data and updates the person type if found.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the person type ID
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

            $errors = ValidateFields::validateFields($this->pessoaTipoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->pessoaTipoModel->update('pessoa_tipo_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Tipo de pessoa atualizado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Person Types by Cabinet
     * 
     * Returns a list of all person types in the system for a specific cabinet.
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
            $tipos = $this->pessoaTipoModel->getAllByColumn('pessoa_tipo_gabinete', $gabinete);
            
            if (empty($tipos)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhum tipo de pessoa encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $tipos);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Person Type by ID
     * 
     * Finds and returns a person type by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the person type ID
     * 
     * @return Response
     *         200: Success with person type data
     *         404: Person type not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $tipo = $this->pessoaTipoModel->findOne('pessoa_tipo_id', $id);

            if (!$tipo) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Tipo de pessoa não encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $tipo);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Person Type
     * 
     * Removes a person type if it exists and has no associated people.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the person type ID
     * 
     * @return Response
     *         200: Deletion successful
     *         400: Cannot delete due to foreign key constraints
     *         404: Person type not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $tipo = $this->pessoaTipoModel->findOne('pessoa_tipo_id', $id);

            if (!$tipo) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Tipo de pessoa não encontrado');
            }

            $this->pessoaTipoModel->delete('pessoa_tipo_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Tipo de pessoa deletado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Tipo de pessoa não pode ser deletado pois possui pessoas associadas');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 