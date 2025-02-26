<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - NovaPHP Example</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Styles -->
    @stack('styles')
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">
                    NovaPHP
                </a>
                
                <div class="flex space-x-4">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-800">Ana Sayfa</a>
                    <a href="{{ route('upload.form') }}" class="text-gray-600 hover:text-gray-800">Upload</a>
                    <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800">Kullanıcılar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-8">
        <div class="container mx-auto px-4 py-6">
            <p class="text-center text-gray-600">
                &copy; {{ date('Y') }} NovaPHP Framework. Tüm hakları saklıdır.
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>
