<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\Postagem;
use PDOException;

/**
 * Postagem Controller
 * 
 * Handles all operations related to the Postagem (Post) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new post with UUID generation
 * - Update existing post
 * - Retrieve single or all posts
 * - Delete post with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 * - File handling for post images
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class PostagemController {

    /**
     * Instance of the Postagem model
     *
     * @var Postagem
     */
    private Postagem $postagemModel;

    /**
     * Constructor
     * 
     * Initializes the Postagem model instance
     */
    public function __construct() {
        $this->postagemModel = new Postagem();
    }

    /**
     * Creates a new Post
     * 
     * Generates a UUID for the new post and validates input data
     * before creation. Handles file upload for post image.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Post created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['postagem_id'] = Uuid::uuid4()->toString();

            // Set current date for publication if not provided
            if (!isset($data['postagem_data_publicacao'])) {
                $data['postagem_data_publicacao'] = date('Y-m-d H:i:s');
            }

            $errors = ValidateFields::validateFields($this->postagemModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->postagemModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Postagem criada com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Postagem já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Post
     * 
     * Validates input data and updates the post if found.
     * Handles file upload for post image updates.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the post ID
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

            // Set update date
            $data['postagem_data_atualizacao'] = date('Y-m-d H:i:s');

            $errors = ValidateFields::validateFields($this->postagemModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->postagemModel->update('postagem_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Postagem atualizada com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Posts by Cabinet
     * 
     * Returns a list of all posts in the system for a specific cabinet.
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
            $postagens = $this->postagemModel->getAllByColumn('postagem_gabinete', $gabinete);
            
            if (empty($postagens)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhuma postagem encontrada');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $postagens);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Post by ID
     * 
     * Finds and returns a post by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the post ID
     * 
     * @return Response
     *         200: Success with post data
     *         404: Post not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $postagem = $this->postagemModel->findOne('postagem_id', $id);

            if (!$postagem) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Postagem não encontrada');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $postagem);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Post
     * 
     * Removes a post if it exists and has no dependencies.
     * Also removes the associated image file from storage.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the post ID
     * 
     * @return Response
     *         200: Deletion successful
     *         404: Post not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $postagem = $this->postagemModel->findOne('postagem_id', $id);

            if (!$postagem) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Postagem não encontrada');
            }

            // Delete the associated image file if it exists
            if (isset($postagem['postagem_imagem']) && file_exists($postagem['postagem_imagem'])) {
                unlink($postagem['postagem_imagem']);
            }

            $this->postagemModel->delete('postagem_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Postagem deletada com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Postagem não pode ser deletada pois possui dependências');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 