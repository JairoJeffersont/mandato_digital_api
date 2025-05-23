<?php

namespace App\Controllers;

use App\Helpers\ResponseBuild;
use App\Helpers\ValidateFields;
use App\Helpers\Sanitize;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use App\Models\Documento;
use PDOException;

/**
 * Documento Controller
 * 
 * Handles all operations related to the Documento (Document) entity.
 * Provides CRUD operations and proper error handling with standardized responses.
 *
 * Features:
 * - Create new document with UUID generation
 * - Update existing document
 * - Retrieve single or all documents
 * - Delete document with foreign key constraint checking
 * - Input validation and XSS prevention
 * - Standardized error responses
 * - File handling for document attachments
 *
 * @package App\Controllers
 * @version 1.0.0
 */
class DocumentoController {

    /**
     * Instance of the Documento model
     *
     * @var Documento
     */
    private Documento $documentoModel;

    /**
     * Constructor
     * 
     * Initializes the Documento model instance
     */
    public function __construct() {
        $this->documentoModel = new Documento();
    }

    /**
     * Creates a new Document
     * 
     * Generates a UUID for the new document and validates input data
     * before creation. Handles file upload for document attachment.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * 
     * @return Response
     *         201: Document created successfully
     *         400: Invalid input data
     *         409: Duplicate entry
     *         500: Server error
     */
    public function create(Request $request, Response $response): Response {
        try {
            $data = json_decode($request->getBody(), true);
            
            // Sanitize input data
            $data = Sanitize::clean($data);
            $data['documento_id'] = Uuid::uuid4()->toString();

            $errors = ValidateFields::validateFields($this->documentoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            if (!empty($errors['missing_required'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos obrigatórios faltando', [], [], ['message' => 'Campos obrigatórios faltando: ' . implode(', ', $errors['missing_required'])]);
            }

            $this->documentoModel->create($data);

            return ResponseBuild::buildResponse($response, 'created', 201, 'Documento criado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ResponseBuild::buildResponse($response, 'conflict', 409, 'Documento já existe');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Updates an existing Document
     * 
     * Validates input data and updates the document if found.
     * Handles file upload for document attachment updates.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the document ID
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

            $errors = ValidateFields::validateFields($this->documentoModel->getColumns(), $data);

            if (!empty($errors['not_allowed'])) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Campos não permitidos: ' . implode(', ', $errors['not_allowed']));
            }

            $this->documentoModel->update('documento_id', $id, $data);

            return ResponseBuild::buildResponse($response, 'success', 200, 'Documento atualizado com sucesso');
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves all Documents by Cabinet
     * 
     * Returns a list of all documents in the system for a specific cabinet.
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
            $documentos = $this->documentoModel->getAllByColumn('documento_gabinete', $gabinete);
            
            if (empty($documentos)) {
                return ResponseBuild::buildResponse($response, 'empty', 200, 'Nenhum documento encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $documentos);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves a specific Document by ID
     * 
     * Finds and returns a document by its UUID.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the document ID
     * 
     * @return Response
     *         200: Success with document data
     *         404: Document not found
     *         500: Server error
     */
    public function getById(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);
            $documento = $this->documentoModel->findOne('documento_id', $id);

            if (!$documento) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Documento não encontrado');
            }
            return ResponseBuild::buildResponse($response, 'success', 200, '', $documento);
        } catch (PDOException $e) {
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Document
     * 
     * Removes a document if it exists and has no dependencies.
     * Also removes the associated file from storage.
     *
     * @param Request  $request  The HTTP request object
     * @param Response $response The HTTP response object
     * @param array    $args     Route arguments containing the document ID
     * 
     * @return Response
     *         200: Deletion successful
     *         404: Document not found
     *         500: Server error
     */
    public function delete(Request $request, Response $response, array $args): Response {
        try {
            $id = Sanitize::clean($args['id']);

            $documento = $this->documentoModel->findOne('documento_id', $id);

            if (!$documento) {
                return ResponseBuild::buildResponse($response, 'not_found', 404, 'Documento não encontrado');
            }

            // Delete the associated file if it exists
            if (isset($documento['documento_arquivo']) && file_exists($documento['documento_arquivo'])) {
                unlink($documento['documento_arquivo']);
            }

            $this->documentoModel->delete('documento_id', $id);
            return ResponseBuild::buildResponse($response, 'success', 200, 'Documento deletado com sucesso');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Documento não pode ser deletado pois possui dependências');
            }
            return ResponseBuild::buildResponse($response, 'internal_server_error', 500, 'Erro interno do servidor', [], [], ['message' => $e->getMessage()]);
        }
    }
} 