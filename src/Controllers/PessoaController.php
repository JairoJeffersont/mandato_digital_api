<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\Pessoa;
use PDOException;

/**
 * Pessoa Controller
 * 
 * Handles all operations related to the Pessoa (Person) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new person with UUID generation
 * - Update existing person
 * - Retrieve single or all people
 * - Delete person with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class PessoaController {

    /**
     * Instance of the Pessoa model
     *
     * @var Pessoa
     */
    private Pessoa $pessoaModel;

    /**
     * Constructor
     * 
     * Initializes the Pessoa model instance
     */
    public function __construct() {
        $this->pessoaModel = new Pessoa();
    }

    /**
     * Creates a new Person
     * 
     * Generates a UUID for the new person and validates input data
     * before creation.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Person created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['pessoa_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->pessoaModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->pessoaModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Pessoa criada com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Pessoa já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Person
     * 
     * Validates input data and updates the person if found.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the person ID
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

            $errors = ValidateFields::validateFields($this->pessoaModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->pessoaModel->update('pessoa_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Pessoa atualizada com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all People by Cabinet
     * 
     * Returns a list of all people in the system for a specific cabinet.
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
            $pessoas = $this->pessoaModel->getAllByColumn('pessoa_gabinete', $gabinete);
            
            if (empty($pessoas)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhuma pessoa encontrada');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $pessoas);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Person by ID
     * 
     * Finds and returns a person by their UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the person ID
     * 
     * @return Response
     *         200: Success with person data
     *         404: Person not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $pessoa = $this->pessoaModel->findOne('pessoa_id', $id);

            if (!$pessoa) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Pessoa não encontrada');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $pessoa);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Person
     * 
     * Removes a person if they exist and have no dependencies.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the person ID
     * 
     * @return Response
     *         200: Deletion successful
     *         404: Person not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $pessoa = $this->pessoaModel->findOne('pessoa_id', $id);

            if (!$pessoa) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Pessoa não encontrada');
            }

            $this->pessoaModel->delete('pessoa_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Pessoa deletada com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Pessoa não pode ser deletada pois possui dependências');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 