<?php

namespace App\Helpers;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * Response Builder Helper Class
 * 
 * This class provides a standardized way to build JSON API responses across
 * the application. It follows a consistent structure and includes support
 * for development-specific information.
 *
 * Response Structure:
 * {
 *   "status": string,
 *   "status_code": integer,
 *   "message": string (optional),
 *   "data": array (optional),
 *   "links": array (optional),
 *   "errors": array (only in development mode)
 * }
 *
 * @package App\Helpers
 * @version 1.0.0
 */
class ResponseBuild {

    /**
     * Builds a standardized JSON response
     *
     * Creates a consistent JSON response structure with optional elements based on
     * the provided parameters. In development mode, it can include additional error
     * information.
     *
     * @param Response $response      The PSR-7 response object
     * @param string   $status        Response status (default: 'success')
     * @param int      $status_code   HTTP status code (default: 200)
     * @param string   $message       Optional message to include in response
     * @param array    $data          Optional data payload
     * @param array    $links         Optional HATEOAS links
     * @param array    $errors        Optional error information (shown only in development)
     * 
     * @return Response PSR-7 response with JSON payload
     */
    public static function buildResponse(
        Response $response,
        string $status = 'success',
        int $status_code = 200,
        string $message = '',
        array $data = [],
        array $links = [],
        array $errors = []
    ): Response {
        $config = require __DIR__ . '/../Config/config.php';
        $isDev = $config['app']['development'] ?? false;

        $payload = [
            'status' => $status,
            'status_code' => $status_code
        ];

        if (!empty($message)) {
            $payload['message'] = $message;
        }

        if (!empty($data)) {
            $payload['data'] = $data;
        }

        if (!empty($links)) {
            $payload['links'] = $links;
        }

        if (!empty($errors) && $isDev) {
            $payload['errors'] = $errors;
        }

        $response->getBody()->write(json_encode($payload));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status_code);
    }
}
