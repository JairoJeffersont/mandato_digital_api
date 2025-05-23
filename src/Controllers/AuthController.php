<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\Sanitize;
use App\Models\Usuario;
use Firebase\JWT\JWT;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Auth Controller
 * 
 * Handles user authentication and JWT token generation.
 * Provides secure login functionality with password verification.
 *
 * Features:
 * - User authentication via email and password
 * - JWT token generation
 * - Input validation and sanitization
 * - Secure password verification
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class AuthController {
    
    /**
     * Configuration array
     * @var array
     */
    private array $config;

    /**
     * Instance of the Usuario model
     * @var Usuario
     */
    private Usuario $usuarioModel;

    /**
     * Constructor
     * 
     * Initializes the Usuario model instance
     */
    public function __construct() {
        $this->config = require __DIR__ . '../../Config/config.php';
        $this->usuarioModel = new Usuario();
    }

    /**
     * Authenticate user and generate JWT token
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         200: Authentication successful, returns JWT token
     *         400: Invalid input data
     *         401: Invalid credentials
     *         500: Server error
     */
    public function login(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);

            // Validate required fields
            if (!isset($data['email']) || !isset($data['senha'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Email e senha são obrigatórios');
            }

            // Sanitize input
            $email = Sanitize::clean($data['email']);
            $senha = $data['senha']; // Don't sanitize password before verification

            // Find user by email
            $usuario = $this->usuarioModel->findOne('usuario_email', $email);

            if (!$usuario) {
                return ResponseBuild::buildResponse($response, 'unauthorized', 401, 'Usuário não encontrado');
            }

            // Verify password
            if (!password_verify($senha, $usuario['usuario_senha'])) {
                return ResponseBuild::buildResponse($response, 'unauthorized', 401, 'Senha inválida');
            }

            // Check if user is active
            if (!$usuario['usuario_ativo']) {
                return ResponseBuild::buildResponse($response, 'unauthorized', 401, 'Usuário inativo');
            }

            // Generate JWT token
            $token = $this->generateToken($usuario);

            // Prepare response data
            $responseData = [
                'id' => $usuario['usuario_id'],
                'nome' => $usuario['usuario_nome'],
                'email' => $usuario['usuario_email'],
                'gabinete' => $usuario['usuario_gabinete'],
                'tipo' => $usuario['usuario_tipo'],
                'gestor' => (bool)$usuario['usuario_gestor'],
                'token' => $token
            ];

            return ResponseBuild::buildResponse($response, 'success', 200, 'Login realizado com sucesso', $responseData);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Generate JWT token for user
     *
     * @param array $usuario User data
     * @return string JWT token
     */
    private function generateToken(array $usuario): string {
        $issuedAt = time();
        $expire = $issuedAt + $this->config['app']['jwt']['expiration'];

        $payload = [
            'iat' => $issuedAt,     // Issued at
            'exp' => $expire,        // Expiration
            'sub' => $usuario['usuario_id'],  // Subject (user ID)
            'email' => $usuario['usuario_email'],
            'nome' => $usuario['usuario_nome'],
            'gabinete' => $usuario['usuario_gabinete'],
            'tipo' => $usuario['usuario_tipo'],
            'gestor' => (bool)$usuario['usuario_gestor']
        ];

        return JWT::encode($payload, $this->config['app']['jwt']['secret'], 'HS256');
    }
}