<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Upload Örneği</title>
    <style>
        .upload-form {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .preview {
            max-width: 200px;
            margin-top: 10px;
            display: none;
        }
        .error {
            color: red;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="upload-form">
        <h2>Dosya Yükleme Örnekleri</h2>
        
        <!-- Resim Yükleme -->
        <form action="/upload/image" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <h3>Resim Yükleme</h3>
                <label for="image">Resim Seçin (Max 2MB)</label>
                <input type="file" id="image" name="image" accept="image/*">
                <img id="imagePreview" class="preview">
                <div class="error" id="imageError"></div>
            </div>
            <button type="submit">Resmi Yükle</button>
        </form>

        <!-- PDF Yükleme -->
        <form action="/upload/document" method="post" enctype="multipart/form-data" style="margin-top: 30px;">
            <div class="form-group">
                <h3>PDF Yükleme</h3>
                <label for="pdf">PDF Seçin (Max 5MB)</label>
                <input type="file" id="pdf" name="pdf" accept="application/pdf">
                <div class="error" id="pdfError"></div>
            </div>
            <button type="submit">PDF Yükle</button>
        </form>

        <!-- Çoklu Dosya Yükleme -->
        <form action="/upload/multiple" method="post" enctype="multipart/form-data" style="margin-top: 30px;">
            <div class="form-group">
                <h3>Çoklu Dosya Yükleme</h3>
                <label for="files">Dosyaları Seçin (Max 10MB)</label>
                <input type="file" id="files" name="files[]" multiple>
                <div class="error" id="filesError"></div>
            </div>
            <button type="submit">Dosyaları Yükle</button>
        </form>
    </div>

    <script>
        // Resim önizleme
        document.getElementById('image').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
