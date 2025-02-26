@extends('layouts.app')

@section('title', 'Dosya Upload')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto">
        <h1 class="text-3xl font-bold mb-6">Dosya Yükleme</h1>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('upload.handle') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                <!-- CSRF Token -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <!-- Dosya Seçimi -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="file">
                        Dosya Seç
                    </label>
                    <input 
                        type="file" 
                        name="file" 
                        id="file"
                        class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100"
                        required
                    >
                </div>

                <!-- Bilgi Metni -->
                <div class="text-sm text-gray-600">
                    <p>İzin verilen dosya tipleri: JPG, PNG, GIF</p>
                    <p>Maksimum dosya boyutu: 5MB</p>
                </div>

                <!-- Upload Butonu -->
                <div>
                    <button 
                        type="submit"
                        class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200"
                    >
                        Dosyayı Yükle
                    </button>
                </div>
            </form>
        </div>

        <!-- Yüklenen Dosyalar -->
        @if(isset($files) && count($files) > 0)
        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Yüklenen Dosyalar</h2>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="space-y-4">
                    @foreach($files as $file)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img src="{{ $file->url }}" alt="{{ $file->name }}" class="w-12 h-12 object-cover rounded">
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ $file->name }}</p>
                                <p class="text-sm text-gray-500">{{ $file->size }}</p>
                            </div>
                        </div>
                        <a href="{{ $file->url }}" target="_blank" class="text-blue-500 hover:text-blue-700">
                            Görüntüle
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Dosya tipi kontrolü
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Geçersiz dosya tipi. Lütfen JPG, PNG veya GIF dosyası seçin.');
            this.value = '';
            return;
        }

        // Dosya boyutu kontrolü (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Dosya boyutu çok büyük. Maksimum 5MB yükleyebilirsiniz.');
            this.value = '';
            return;
        }
    }
});
</script>
@endpush
