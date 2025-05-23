<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\PostagemStatus;
use PDOException;

/**
 * PostagemStatus Controller
 * 
 * Handles all operations related to the PostagemStatus (Post Status) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new post status with UUID generation
 * - Update existing post status
 * - Retrieve single or all post statuses
 * - Delete post status with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class PostagemStatusController {

    /**
     * Instance of the PostagemStatus model
     *
     * @var PostagemStatus
     */
    private PostagemStatus $postagemStatusModel;

    /**
     * Constructor
     * 
     * Initializes the PostagemStatus model instance
     */
    public function __construct() {
        $this->postagemStatusModel = new PostagemStatus();
    }

    /**
     * Creates a new Post Status
     * 
     * Generates a UUID for the new post status and validates input data
     * before creation.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Post status created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['postagem_status_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->postagemStatusModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->postagemStatusModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Status de postagem criado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Status de postagem já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Post Status
     * 
     * Validates input data and updates the post status if found.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the post status ID
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

            $errors = ValidateFields::validateFields($this->postagemStatusModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->postagemStatusModel->update('postagem_status_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Status de postagem atualizado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Post Statuses by Cabinet
     * 
     * Returns a list of all post statuses in the system for a specific cabinet.
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
            $status = $this->postagemStatusModel->getAllByColumn('postagem_status_gabinete', $gabinete);
            
            if (empty($status)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhum status de postagem encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $status);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Post Status by ID
     * 
     * Finds and returns a post status by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the post status ID
     * 
     * @return Response
     *         200: Success with post status data
     *         404: Post status not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $status = $this->postagemStatusModel->findOne('postagem_status_id', $id);

            if (!$status) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Status de postagem não encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $status);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Post Status
     * 
     * Removes a post status if it exists and has no associated posts.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the post status ID
     * 
     * @return Response
     *         200: Deletion successful
     *         400: Cannot delete due to foreign key constraints
     *         404: Post status not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $status = $this->postagemStatusModel->findOne('postagem_status_id', $id);

            if (!$status) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Status de postagem não encontrado');
            }

            $this->postagemStatusModel->delete('postagem_status_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Status de postagem deletado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Status de postagem não pode ser deletado pois possui postagens associadas');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 