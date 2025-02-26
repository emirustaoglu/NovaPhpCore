<?php

namespace App\Controllers;

use NovaCore\Upload\Upload;
use NovaCore\View\View;

class UploadExampleController
{
    /**
     * Upload form sayfasını göster
     */
    public function index()
    {
        return View::render('upload-example');
    }

    /**
     * Resim yükleme örneği
     */
    public function uploadImage()
    {
        try {
            // Upload instance oluştur
            $upload = new Upload([
                'storage_path' => storage_path('upload/images')
            ]);

            // Resim yükle
            $file = $upload
                ->onlyImages()              // Sadece resim dosyalarına izin ver
                ->maxSize(2 * 1024 * 1024)  // Max 2MB
                ->upload('image')           // 'image' form alanından dosyayı al
                ->to('profile-photos');     // 'profile-photos' klasörüne kaydet

            // Başarılı sonuç döndür
            return [
                'success' => true,
                'message' => 'Resim başarıyla yüklendi',
                'data' => [
                    'original_name' => $file->getOriginalName(),
                    'stored_name' => $file->getStoredName(),
                    'stored_path' => $file->getFullStoredPath(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * PDF yükleme örneği
     */
    public function uploadDocument()
    {
        try {
            $upload = new Upload([
                'storage_path' => storage_path('upload/documents')
            ]);

            $file = $upload
                ->onlyPdf()                // Sadece PDF dosyalarına izin ver
                ->maxSize(5 * 1024 * 1024) // Max 5MB
                ->upload('pdf')            // 'pdf' form alanından dosyayı al
                ->to('contracts', 'contract-' . date('Y-m-d')); // Özel isimle kaydet

            return [
                'success' => true,
                'message' => 'PDF başarıyla yüklendi',
                'data' => [
                    'original_name' => $file->getOriginalName(),
                    'stored_name' => $file->getStoredName(),
                    'stored_path' => $file->getFullStoredPath(),
                    'size' => $file->getSize()
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Çoklu dosya yükleme örneği
     */
    public function uploadMultiple()
    {
        try {
            $upload = new Upload([
                'storage_path' => storage_path('upload/multiple'),
                'allowed_types' => [        // İzin verilen dosya tipleri
                    'image/jpeg',
                    'image/png',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ],
                'max_size' => 10 * 1024 * 1024 // Max 10MB
            ]);

            $uploadedFiles = [];

            // Her dosya için döngü
            foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
                $fileArray = [
                    'name' => $_FILES['files']['name'][$key],
                    'type' => $_FILES['files']['type'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $_FILES['files']['error'][$key],
                    'size' => $_FILES['files']['size'][$key]
                ];

                // Dosyayı yükle
                $file = $upload
                    ->upload($fileArray)
                    ->to('batch-' . date('Y-m'));

                $uploadedFiles[] = [
                    'original_name' => $file->getOriginalName(),
                    'stored_path' => $file->getFullStoredPath(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }

            return [
                'success' => true,
                'message' => count($uploadedFiles) . ' dosya başarıyla yüklendi',
                'data' => $uploadedFiles
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Özel upload örneği (Firma bazlı)
     */
    public function uploadForCompany($companyId)
    {
        try {
            // Firma bazlı storage path
            $upload = new Upload([
                'storage_path' => storage_path("upload/companies/{$companyId}")
            ]);

            // Özel mime type listesi
            $upload->allowed([
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]);

            // Excel dosyasını yükle
            $file = $upload
                ->upload('excel_file')
                ->to('reports', 'financial-report-' . date('Y-m'));

            // Veritabanına kaydet
            // $this->saveToDatabase($file, $companyId);

            return [
                'success' => true,
                'message' => 'Dosya başarıyla yüklendi',
                'data' => [
                    'path' => $file->getFullStoredPath(),
                    'size' => $file->getSize()
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
