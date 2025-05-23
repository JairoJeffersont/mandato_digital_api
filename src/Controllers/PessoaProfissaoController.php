<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\PessoaProfissao;
use PDOException;

/**
 * PessoaProfissao Controller
 * 
 * Handles all operations related to the PessoaProfissao (Person Profession) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new profession with UUID generation
 * - Update existing profession
 * - Retrieve single or all professions
 * - Delete profession with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class PessoaProfissaoController {

    /**
     * Instance of the PessoaProfissao model
     *
     * @var PessoaProfissao
     */
    private PessoaProfissao $pessoaProfissaoModel;

    /**
     * Constructor
     * 
     * Initializes the PessoaProfissao model instance
     */
    public function __construct() {
        $this->pessoaProfissaoModel = new PessoaProfissao();
    }

    /**
     * Creates a new Profession
     * 
     * Generates a UUID for the new profession and validates input data
     * before creation.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Profession created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['pessoas_profissoes_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->pessoaProfissaoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->pessoaProfissaoModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Profissão criada com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Profissão já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Profession
     * 
     * Validates input data and updates the profession if found.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the profession ID
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

            $errors = ValidateFields::validateFields($this->pessoaProfissaoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->pessoaProfissaoModel->update('pessoas_profissoes_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Profissão atualizada com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Professions by Cabinet
     * 
     * Returns a list of all professions in the system for a specific cabinet.
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
            $profissoes = $this->pessoaProfissaoModel->getAllByColumn('pessoas_profissoes_gabinete', $gabinete);
            
            if (empty($profissoes)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhuma profissão encontrada');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $profissoes);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Profession by ID
     * 
     * Finds and returns a profession by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the profession ID
     * 
     * @return Response
     *         200: Success with profession data
     *         404: Profession not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $profissao = $this->pessoaProfissaoModel->findOne('pessoas_profissoes_id', $id);

            if (!$profissao) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Profissão não encontrada');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $profissao);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Profession
     * 
     * Removes a profession if it exists and has no associated people.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the profession ID
     * 
     * @return Response
     *         200: Deletion successful
     *         400: Cannot delete due to foreign key constraints
     *         404: Profession not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $profissao = $this->pessoaProfissaoModel->findOne('pessoas_profissoes_id', $id);

            if (!$profissao) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Profissão não encontrada');
            }

            $this->pessoaProfissaoModel->delete('pessoas_profissoes_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Profissão deletada com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Profissão não pode ser deletada pois possui pessoas associadas');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 