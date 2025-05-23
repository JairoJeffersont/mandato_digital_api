<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\UsuarioTipo;
use PDOException;

/**
 * UsuarioTipo Controller
 * 
 * Handles all operations related to the UsuarioTipo (User Type) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new user type with UUID generation
 * - Update existing user type
 * - Retrieve single or all user types
 * - Delete user type with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class UsuarioTipoController {

    /**
     * Instance of the UsuarioTipo model
     *
     * @var UsuarioTipo
     */
    private UsuarioTipo $usuarioTipoModel;

    /**
     * Constructor
     * 
     * Initializes the UsuarioTipo model instance
     */
    public function __construct() {
        $this->usuarioTipoModel = new UsuarioTipo();
    }

    /**
     * @OA\Post(
     *     path="/usuario-tipo",
     *     summary="Criar novo tipo de usuário",
     *     tags={"Tipos de Usuário"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"usuario_tipo_nome","usuario_tipo_descricao"},
     *             @OA\Property(property="usuario_tipo_nome", type="string"),
     *             @OA\Property(property="usuario_tipo_descricao", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de usuário criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Tipo de usuário criado com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/UsuarioTipo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['usuario_tipo_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->usuarioTipoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->usuarioTipoModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Tipo de usuário criado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Tipo de usuário já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Put(
     *     path="/usuario-tipo/{id}",
     *     summary="Atualizar tipo de usuário",
     *     tags={"Tipos de Usuário"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do tipo de usuário",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="usuario_tipo_nome", type="string"),
     *             @OA\Property(property="usuario_tipo_descricao", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de usuário atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Tipo de usuário atualizado com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/UsuarioTipo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de usuário não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function update(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);

            $errors = ValidateFields::validateFields($this->usuarioTipoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->usuarioTipoModel->update('usuario_tipo_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Tipo de usuário atualizado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/usuario-tipo",
     *     summary="Listar todos os tipos de usuário",
     *     tags={"Tipos de Usuário"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tipos de usuário",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/UsuarioTipo")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getAll(Request $request, Response $response): Response {
        try {
            $tipos = $this->usuarioTipoModel->getAll();
            if (empty($tipos)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhum tipo de usuário encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $tipos);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/usuario-tipo/{id}",
     *     summary="Buscar tipo de usuário por ID",
     *     tags={"Tipos de Usuário"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do tipo de usuário",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de usuário encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/UsuarioTipo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de usuário não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $tipo = $this->usuarioTipoModel->findOne('usuario_tipo_id', $id);

            if (!$tipo) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Tipo de usuário não encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $tipo);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/usuario-tipo/{id}",
     *     summary="Excluir tipo de usuário",
     *     tags={"Tipos de Usuário"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do tipo de usuário",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de usuário excluído com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Tipo de usuário excluído com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de usuário não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $tipo = $this->usuarioTipoModel->findOne('usuario_tipo_id', $id);

            if (!$tipo) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Tipo de usuário não encontrado');
            }

            $this->usuarioTipoModel->delete('usuario_tipo_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Tipo de usuário deletado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Tipo de usuário não pode ser deletado pois possui usuários associados');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 