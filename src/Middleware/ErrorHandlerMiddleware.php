<?php

namespace App\Middleware;

use App\Helpers\ResponseBuild;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (HttpNotFoundException $e) {
            $response = new \Slim\Psr7\Response();
            return ResponseBuild::buildResponse(
                $response,
                'not_found',
                404,
                'Rota não encontrada'
            );
        } catch (HttpMethodNotAllowedException $e) {
            $response = new \Slim\Psr7\Response();
            return ResponseBuild::buildResponse(
                $response,
                'method_not_allowed',
                405,
                'Método não permitido para esta rota',
                [],
                []
            );
        } catch (\Exception $e) {
            $response = new \Slim\Psr7\Response();
            return ResponseBuild::buildResponse(
                $response,
                'internal_server_error',
                500,
                'Erro interno do servidor',
                [],
                [],
                ['message' => $e->getMessage()]
            );
        }
    }
} 