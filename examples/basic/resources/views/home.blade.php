@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold mb-4">{{ $title }}</h1>
    <p class="text-lg mb-8">{{ $description }}</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Upload Örneği -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Dosya Upload</h2>
            <p class="mb-4">Güvenli dosya yükleme örneği</p>
            <a href="{{ route('upload.form') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Upload Sayfası
            </a>
        </div>

        <!-- Kullanıcı İşlemleri -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Kullanıcı İşlemleri</h2>
            <p class="mb-4">CRUD operasyonları ve ilişki örnekleri</p>
            <a href="{{ route('users.index') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Kullanıcılar
            </a>
        </div>

        <!-- API Örneği -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">API Endpoints</h2>
            <p class="mb-4">RESTful API örnekleri</p>
            <a href="/api/users" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                API Dokümantasyonu
            </a>
        </div>
    </div>
</div>
@endsection
