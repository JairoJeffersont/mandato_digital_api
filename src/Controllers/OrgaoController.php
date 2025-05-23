<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\Orgao;
use PDOException;

/**
 * Orgao Controller
 * 
 * Handles all operations related to the Orgao (Organization) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new organization with UUID generation
 * - Update existing organization
 * - Retrieve single or all organizations
 * - Delete organization
 * - Input validation and XSS prevention
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class OrgaoController {

    /**
     * Instance of the Orgao model
     *
     * @var Orgao
     */
    private Orgao $orgaoModel;

    /**
     * Constructor
     * 
     * Initializes the Orgao model instance
     */
    public function __construct() {
        $this->orgaoModel = new Orgao();
    }

    /**
     * Creates a new Organization
     * 
     * Generates a UUID for the new organization and validates input data
     * before creation.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Organization created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['orgao_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->orgaoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->orgaoModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Órgão criado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Órgão já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Organization
     * 
     * Validates input data and updates the organization if found.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the organization ID
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

            $errors = ValidateFields::validateFields($this->orgaoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->orgaoModel->update('orgao_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Órgão atualizado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Organizations by Cabinet
     * 
     * Returns a list of all organizations in the system for a specific cabinet.
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
            $orgaos = $this->orgaoModel->getAllByColumn('orgao_gabinete', $gabinete);
            
            if (empty($orgaos)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhum órgão encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $orgaos);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Organization by ID
     * 
     * Finds and returns an organization by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the organization ID
     * 
     * @return Response
     *         200: Success with organization data
     *         404: Organization not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $orgao = $this->orgaoModel->findOne('orgao_id', $id);

            if (!$orgao) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Órgão não encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $orgao);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes an Organization
     * 
     * Removes an organization if it exists.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the organization ID
     * 
     * @return Response
     *         200: Deletion successful
     *         404: Organization not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $orgao = $this->orgaoModel->findOne('orgao_id', $id);

            if (!$orgao) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Órgão não encontrado');
            }

            $this->orgaoModel->delete('orgao_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Órgão deletado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 