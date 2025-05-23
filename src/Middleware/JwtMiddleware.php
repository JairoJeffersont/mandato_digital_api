<?php

namespace App\Middleware;

use App\Helpers\ResponseBuild;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

/**
 * JWT Authentication Middleware
 * 
 * Validates JWT tokens in request headers and provides user authentication.
 * Protects routes by ensuring valid JWT tokens are present.
 *
 * Features:
 * - JWT token validation
 * - Token expiration check
 * - User data extraction from token
 * - Request enrichment with user data
 *
 * @package App\Middleware
 * @version 1.0.0
 */
class JwtMiddleware {
    /**
     * Configuration array
     * @var array
     */
    private array $config;

    /**
     * Constructor
     */
    public function __construct() {
        $this->config = require __DIR__ . '../../Config/config.php';
    }

    /**
     * Process the request
     *
     * @param Request        $request The request object
     * @param RequestHandler $handler The request handler
     * 
     * @return ResponseInterface
     */
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface {
        $response = new Response();

        // Get authorization header
        $authHeader = $request->getHeaderLine('Authorization');

        // Check if authorization header exists
        if (empty($authHeader)) {
            return ResponseBuild::buildResponse($response, 'unauthorized', 401, 'Token não fornecido');
        }

        // Extract token from Bearer schema
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return ResponseBuild::buildResponse($response, 'unauthorized', 401, 'Token inválido');
        }

        $token = $matches[1];

        try {
            // Decode token
            $decoded = JWT::decode(
                $token, 
                new Key($this->config['app']['jwt']['secret'], 'HS256')
            );

            // Add decoded token data to request attributes
            $request = $request->withAttribute('jwt', $decoded);
            $request = $request->withAttribute('user', [
                'id' => $decoded->sub,
                'email' => $decoded->email,
                'nome' => $decoded->nome,
                'gabinete' => $decoded->gabinete,
                'tipo' => $decoded->tipo,
                'gestor' => $decoded->gestor
            ]);

            // Continue with request
            return $handler->handle($request);
        } catch (ExpiredException $e) {
            return ResponseBuild::buildResponse($response, 'unauthorized', 401, 'Token expirado');
        } catch (\Exception $e) {
            return ResponseBuild::buildResponse($response, 'unauthorized', 401, 'Token inválido');
        }
    }
} 