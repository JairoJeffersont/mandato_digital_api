<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\OrgaoTipo;
use PDOException;

/**
 * OrgaoTipo Controller
 * 
 * Handles all operations related to the OrgaoTipo (Organization Type) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new organization type with UUID generation
 * - Update existing organization type
 * - Retrieve single or all organization types
 * - Delete organization type with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class OrgaoTipoController {

    /**
     * Instance of the OrgaoTipo model
     *
     * @var OrgaoTipo
     */
    private OrgaoTipo $orgaoTipoModel;

    /**
     * Constructor
     * 
     * Initializes the OrgaoTipo model instance
     */
    public function __construct() {
        $this->orgaoTipoModel = new OrgaoTipo();
    }

    /**
     * Creates a new Organization Type
     * 
     * Generates a UUID for the new organization type and validates input data
     * before creation.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Organization type created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['orgao_tipo_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->orgaoTipoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->orgaoTipoModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Tipo de órgão criado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Tipo de órgão já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Organization Type
     * 
     * Validates input data and updates the organization type if found.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the organization type ID
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

            $errors = ValidateFields::validateFields($this->orgaoTipoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->orgaoTipoModel->update('orgao_tipo_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Tipo de órgão atualizado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Organization Types
     * 
     * Returns a list of all organization types in the system.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         200: Success with data or empty message
     *         500: Server error
     */
    public function getAll(Request $request, Response $response, array $args): Response {
        try {
            $gabinete = Sanitize::clean($args['gabinete']);
            $tipos = $this->orgaoTipoModel->getAllByColumn('orgao_tipo_gabinete', $gabinete);
            if (empty($tipos)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhum tipo de órgão encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $tipos);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Organization Type by ID
     * 
     * Finds and returns an organization type by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the organization type ID
     * 
     * @return Response
     *         200: Success with organization type data
     *         404: Organization type not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $tipo = $this->orgaoTipoModel->findOne('orgao_tipo_id', $id);

            if (!$tipo) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Tipo de órgão não encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $tipo);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes an Organization Type
     * 
     * Removes an organization type if it exists and has no associated organizations.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the organization type ID
     * 
     * @return Response
     *         200: Deletion successful
     *         400: Cannot delete due to foreign key constraints
     *         404: Organization type not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $tipo = $this->orgaoTipoModel->findOne('orgao_tipo_id', $id);

            if (!$tipo) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Tipo de órgão não encontrado');
            }

            $this->orgaoTipoModel->delete('orgao_tipo_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Tipo de órgão deletado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Tipo de órgão não pode ser deletado pois possui órgãos associados');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 