<?php

namespace App\Controllers;

use App\Helpers\FileUploader;
use App\Helpers\ResponseBuild;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UploadController
{
    private FileUploader $fileUploader;

    public function __construct()
    {
        $this->fileUploader = new FileUploader();
    }

    public function upload(Request $request, Response $response): Response
    {
        $uploadedFiles = $request->getUploadedFiles();
        
        if (empty($uploadedFiles['file'])) {
            return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Nenhum arquivo enviado');
        }

        $uploadedFile = $uploadedFiles['file'];
        
        $directory = __DIR__ . '/../../public/uploads';
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png', 'application/psd', 'image/vnd.adobe.photoshop', 'application/ai', 'application/illustrator', 'application/postscript', 'application/pdf', 'application/eps', 'application/vnd.adobe.illustrator', 'application/cdr', 'application/x-cdr', 'application/coreldraw', 'image/x-coreldraw'];
        $maxSize = 500;
        
        $file = [
            'name' => $uploadedFile->getClientFilename(),
            'type' => $uploadedFile->getClientMediaType(),
            'tmp_name' => $uploadedFile->getStream()->getMetadata('uri'),
            'error' => $uploadedFile->getError(),
            'size' => $uploadedFile->getSize()
        ];
        
        $uploadResult = $this->fileUploader->uploadFile($directory, $file, $allowedTypes, $maxSize);

        if ($uploadResult['status'] === 'success') {
            return ResponseBuild::buildResponse($response, 'success', 200, 'Arquivo enviado com sucesso', ['file_path' => '/public'.$uploadResult['file_path']]);
        }

        return ResponseBuild::buildResponse($response, 'bad_request', 400, 'Erro ao enviar arquivo', [], [], ['message' => $uploadResult['message']]);
    }
} 