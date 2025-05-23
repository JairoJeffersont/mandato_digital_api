<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\Usuario;
use PDOException;

/**
 * Usuario Controller
 * 
 * Handles all operations related to the Usuario (User) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new user with UUID generation
 * - Update existing user
 * - Retrieve single or all users with related data
 * - Delete user
 * - Password hashing
 * - Input validation and XSS prevention
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class UsuarioController {

    /**
     * Instance of the Usuario model
     *
     * @var Usuario
     */
    private Usuario $usuarioModel;

    /**
     * Constructor
     * 
     * Initializes the Usuario model instance
     */
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Creates a new User
     * 
     * Generates a UUID for the new user and validates input data
     * before creation. Hashes the password for security.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: User created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);

            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['usuario_id'] = Uuid::uuid4()->toString();

            // Hash password
            if (isset($data['usuario_senha'])) {
                $data['usuario_senha'] = password_hash($data['usuario_senha'], PASSWORD_DEFAULT);
            }

            // Set default values
            $data['usuario_ativo'] = $data['usuario_ativo'] ?? 1;

            $errors = ValidateFields::validateFields($this->usuarioModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->usuarioModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Usuário criado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if (strpos($e->getMessage(), 'usuario_email') !== false) {
                    return ResponseBuild::buildResponse($response, 'conflict', 409, 'Email já cadastrado');
                }
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Usuário já existe');
            }
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Gabinete inválido ou tipo de usuário não encontrados');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
        
    }

    /**
     * Updates an existing User
     * 
     * Validates input data and updates the user if found.
     * Handles password updates separately.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the user ID
     * 
     * @return Response
     *         200: Update successful
     *         400: Invalid input data
     *         404: User not found
     *         500: Server error
     */
    public function update(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $data = json_decode($request->getBody(), true);

            // Check if user exists
            $usuario = $this->usuarioModel->findOne('usuario_id', $id);
            if (!$usuario) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Usuário não encontrado');
            }

            // Sanitize input data
            $data = Sanitize::clean($data);

            // Hash password if provided
            if (isset($data['usuario_senha'])) {
                $data['usuario_senha'] = password_hash($data['usuario_senha'], PASSWORD_DEFAULT);
            }

            $errors = ValidateFields::validateFields($this->usuarioModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->usuarioModel->update('usuario_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Usuário atualizado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'usuario_email') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Email já cadastrado');
            }
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                if (strpos($e->getMessage(), 'fk_usuario_tipo') !== false) {
                    return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Tipo de usuário inválido');
                }
                if (strpos($e->getMessage(), 'fk_usuario_gabinete') !== false) {
                    return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Gabinete inválido');
                }
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all users associated with a specific office (gabinete).
     * 
     * This method fetches all users linked to the provided office ID from the route arguments.
     * Returns a successful response with user data, or an appropriate message if no users are found
     * or if the input is invalid.
     *
     * @param Request  $request   The HTTP request object
     * @param Response $response  The HTTP response object
     * @param array    $args      Route arguments, must include 'gabinete' (office ID)
     * 
     * @return Response
     *         200: Success with user data or message indicating no users found
     *         400: Bad request if 'gabinete' is missing
     *         500: Internal server error in case of database or server issues
     * 
     * @throws PDOException If a database error occurs
     */
    public function getAll(Request $request, Response $response, array $args): Response {
        try {

            if (!isset($args['gabinete']) || empty($args['gabinete'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'ID do gabinete não informado');
            }

            $gabinete = $args['gabinete'];

            $usuarios = $this->usuarioModel->getAllByColumn('usuario_gabinete', $gabinete);
            if (empty($usuarios)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhum usuário encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $usuarios);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific User by ID
     * 
     * Finds and returns a user with their related data by UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the user ID
     * 
     * @return Response
     *         200: Success with user data
     *         404: User not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $usuario = $this->usuarioModel->findOne('usuario_id', $id);

            if (!$usuario) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Usuário não encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $usuario);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a User
     * 
     * Removes a user if it exists.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the user ID
     * 
     * @return Response
     *         200: Deletion successful
     *         404: User not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $usuario = $this->usuarioModel->findOne('usuario_id', $id);

            if (!$usuario) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Usuário não encontrado');
            }

            $this->usuarioModel->delete('usuario_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Usuário deletado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
}