<?php

namespace App\Controllers;

use NovaCore\Http\Controller;
use NovaCore\Security\Security;

class UploadController extends Controller
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function showForm()
    {
        return view('upload.form');
    }

    public function handle()
    {
        $file = $_FILES['file'] ?? null;
        if (!$file) {
            return back()->with('error', 'Dosya seçilmedi');
        }

        // Güvenlik kontrolü
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!$this->security->validateFileUpload($file, $allowedTypes, $maxSize)) {
            return back()->with('error', 'Geçersiz dosya');
        }

        // Dosya yükleme
        $fileName = time() . '_' . $this->security->generateSecureString(8) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $uploadPath = dirname(__DIR__, 2) . '/storage/uploads/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return back()->with('error', 'Dosya yüklenemedi');
        }

        return back()->with('success', 'Dosya başarıyla yüklendi');
    }
}
