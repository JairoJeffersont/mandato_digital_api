<?php

/**
 * API Routes Configuration
 * 
 * This file defines all the API routes for the application using Slim Framework's
 * routing system. Routes are grouped under the '/api' prefix for better organization
 * and versioning capabilities.
 * 
 * @package App
 * @version 1.0.0
 */

use App\Controllers\GabineteController;
use App\Controllers\GabineteTipoController;
use App\Controllers\UsuarioTipoController;
use App\Controllers\UsuarioController;
use App\Controllers\AuthController;
use App\Controllers\OrgaoTipoController;
use App\Controllers\OrgaoController;
use App\Controllers\PessoaTipoController;
use App\Controllers\PessoaProfissaoController;
use App\Controllers\PessoaController;
use App\Controllers\DocumentoTipoController;
use App\Controllers\DocumentoController;
use App\Controllers\EmendaStatusController;
use App\Controllers\EmendaObjetivoController;
use App\Controllers\EmendaController;
use App\Controllers\PostagemStatusController;
use App\Controllers\PostagemController;
use App\Controllers\ClippingTipoController;
use App\Controllers\ClippingController;
use App\Controllers\UploadController;
use App\Middleware\JwtMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->group('/api', function (RouteCollectorProxy $group) {
    // Public routes

    $group->post('/login', [AuthController::class, 'login']);
    $group->get('/gabinete-tipo', [GabineteTipoController::class, 'getAll']);
    $group->post('/usuario', [UsuarioController::class, 'create']);
    $group->post('/gabinete', [GabineteController::class, 'create']);

    // Protected routes
    $group->group('', function (RouteCollectorProxy $group) {
        // Upload route
        $group->post('/upload', [UploadController::class, 'upload']);

        // Gabinete routes
        $group->get('/gabinete', [GabineteController::class, 'getAll']);
        $group->get('/gabinete/{id}', [GabineteController::class, 'getById']);
        $group->delete('/gabinete/{id}', [GabineteController::class, 'delete']);
        $group->put('/gabinete/{id}', [GabineteController::class, 'update']);

        // GabineteTipo routes
        $group->get('/gabinete-tipo/{id}', [GabineteTipoController::class, 'getById']);
        $group->post('/gabinete-tipo', [GabineteTipoController::class, 'create']);
        $group->put('/gabinete-tipo/{id}', [GabineteTipoController::class, 'update']);
        $group->delete('/gabinete-tipo/{id}', [GabineteTipoController::class, 'delete']);

        // UsuarioTipo routes
        $group->get('/usuario-tipo', [UsuarioTipoController::class, 'getAll']);
        $group->get('/usuario-tipo/{id}', [UsuarioTipoController::class, 'getById']);
        $group->post('/usuario-tipo', [UsuarioTipoController::class, 'create']);
        $group->put('/usuario-tipo/{id}', [UsuarioTipoController::class, 'update']);
        $group->delete('/usuario-tipo/{id}', [UsuarioTipoController::class, 'delete']);

        // Usuario routes
        $group->get('/usuario/{gabinete}', [UsuarioController::class, 'getAll']);
        $group->get('/usuario/detalhe/{id}', [UsuarioController::class, 'getById']);
        $group->put('/usuario/{id}', [UsuarioController::class, 'update']);
        $group->delete('/usuario/{id}', [UsuarioController::class, 'delete']);

        // OrgaoTipo routes
        $group->get('/orgao-tipo/{gabinete}', [OrgaoTipoController::class, 'getAll']);
        $group->get('/orgao-tipo/detalhe/{id}', [OrgaoTipoController::class, 'getById']);
        $group->post('/orgao-tipo', [OrgaoTipoController::class, 'create']);
        $group->put('/orgao-tipo/{id}', [OrgaoTipoController::class, 'update']);
        $group->delete('/orgao-tipo/{id}', [OrgaoTipoController::class, 'delete']);

        // Orgao routes
        $group->get('/orgao/{gabinete}', [OrgaoController::class, 'getAll']);
        $group->get('/orgao/detalhe/{id}', [OrgaoController::class, 'getById']);
        $group->post('/orgao', [OrgaoController::class, 'create']);
        $group->put('/orgao/{id}', [OrgaoController::class, 'update']);
        $group->delete('/orgao/{id}', [OrgaoController::class, 'delete']);

        // PessoaTipo routes
        $group->get('/pessoa-tipo/{gabinete}', [PessoaTipoController::class, 'getAll']);
        $group->get('/pessoa-tipo/detalhe/{id}', [PessoaTipoController::class, 'getById']);
        $group->post('/pessoa-tipo', [PessoaTipoController::class, 'create']);
        $group->put('/pessoa-tipo/{id}', [PessoaTipoController::class, 'update']);
        $group->delete('/pessoa-tipo/{id}', [PessoaTipoController::class, 'delete']);

        // PessoaProfissao routes
        $group->get('/pessoa-profissao/{gabinete}', [PessoaProfissaoController::class, 'getAll']);
        $group->get('/pessoa-profissao/detalhe/{id}', [PessoaProfissaoController::class, 'getById']);
        $group->post('/pessoa-profissao', [PessoaProfissaoController::class, 'create']);
        $group->put('/pessoa-profissao/{id}', [PessoaProfissaoController::class, 'update']);
        $group->delete('/pessoa-profissao/{id}', [PessoaProfissaoController::class, 'delete']);

        // Pessoa routes
        $group->get('/pessoa/{gabinete}', [PessoaController::class, 'getAll']);
        $group->get('/pessoa/detalhe/{id}', [PessoaController::class, 'getById']);
        $group->post('/pessoa', [PessoaController::class, 'create']);
        $group->put('/pessoa/{id}', [PessoaController::class, 'update']);
        $group->delete('/pessoa/{id}', [PessoaController::class, 'delete']);

        // DocumentoTipo routes
        $group->get('/documento-tipo/{gabinete}', [DocumentoTipoController::class, 'getAll']);
        $group->get('/documento-tipo/detalhe/{id}', [DocumentoTipoController::class, 'getById']);
        $group->post('/documento-tipo', [DocumentoTipoController::class, 'create']);
        $group->put('/documento-tipo/{id}', [DocumentoTipoController::class, 'update']);
        $group->delete('/documento-tipo/{id}', [DocumentoTipoController::class, 'delete']);

        // Documento routes
        $group->get('/documento/{gabinete}', [DocumentoController::class, 'getAll']);
        $group->get('/documento/detalhe/{id}', [DocumentoController::class, 'getById']);
        $group->post('/documento', [DocumentoController::class, 'create']);
        $group->put('/documento/{id}', [DocumentoController::class, 'update']);
        $group->delete('/documento/{id}', [DocumentoController::class, 'delete']);

        // EmendaStatus routes
        $group->get('/emenda-status/{gabinete}', [EmendaStatusController::class, 'getAll']);
        $group->get('/emenda-status/detalhe/{id}', [EmendaStatusController::class, 'getById']);
        $group->post('/emenda-status', [EmendaStatusController::class, 'create']);
        $group->put('/emenda-status/{id}', [EmendaStatusController::class, 'update']);
        $group->delete('/emenda-status/{id}', [EmendaStatusController::class, 'delete']);

        // EmendaObjetivo routes
        $group->get('/emenda-objetivo/{gabinete}', [EmendaObjetivoController::class, 'getAll']);
        $group->get('/emenda-objetivo/detalhe/{id}', [EmendaObjetivoController::class, 'getById']);
        $group->post('/emenda-objetivo', [EmendaObjetivoController::class, 'create']);
        $group->put('/emenda-objetivo/{id}', [EmendaObjetivoController::class, 'update']);
        $group->delete('/emenda-objetivo/{id}', [EmendaObjetivoController::class, 'delete']);

        // Emenda routes
        $group->get('/emenda/{gabinete}', [EmendaController::class, 'getAll']);
        $group->get('/emenda/detalhe/{id}', [EmendaController::class, 'getById']);
        $group->post('/emenda', [EmendaController::class, 'create']);
        $group->put('/emenda/{id}', [EmendaController::class, 'update']);
        $group->delete('/emenda/{id}', [EmendaController::class, 'delete']);

        // PostagemStatus routes
        $group->get('/postagem-status/{gabinete}', [PostagemStatusController::class, 'getAll']);
        $group->get('/postagem-status/detalhe/{id}', [PostagemStatusController::class, 'getById']);
        $group->post('/postagem-status', [PostagemStatusController::class, 'create']);
        $group->put('/postagem-status/{id}', [PostagemStatusController::class, 'update']);
        $group->delete('/postagem-status/{id}', [PostagemStatusController::class, 'delete']);

        // Postagem routes
        $group->get('/postagem/{gabinete}', [PostagemController::class, 'getAll']);
        $group->get('/postagem/detalhe/{id}', [PostagemController::class, 'getById']);
        $group->post('/postagem', [PostagemController::class, 'create']);
        $group->put('/postagem/{id}', [PostagemController::class, 'update']);
        $group->delete('/postagem/{id}', [PostagemController::class, 'delete']);

        // ClippingTipo routes
        $group->get('/clipping-tipo/{gabinete}', [ClippingTipoController::class, 'getAll']);
        $group->get('/clipping-tipo/detalhe/{id}', [ClippingTipoController::class, 'getById']);
        $group->post('/clipping-tipo', [ClippingTipoController::class, 'create']);
        $group->put('/clipping-tipo/{id}', [ClippingTipoController::class, 'update']);
        $group->delete('/clipping-tipo/{id}', [ClippingTipoController::class, 'delete']);

        // Clipping routes
        $group->get('/clipping/{gabinete}', [ClippingController::class, 'getAll']);
        $group->get('/clipping/detalhe/{id}', [ClippingController::class, 'getById']);
        $group->post('/clipping', [ClippingController::class, 'create']);
        $group->put('/clipping/{id}', [ClippingController::class, 'update']);
        $group->delete('/clipping/{id}', [ClippingController::class, 'delete']);
        //})->add(new JwtMiddleware());
    });
});
