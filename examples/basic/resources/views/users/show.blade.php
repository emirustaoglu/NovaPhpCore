@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Kullanıcı Detayları -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-start mb-6">
                <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
                <div class="flex space-x-2">
                    <button 
                        onclick="openEditModal({{ $user->id }})"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    >
                        Düzenle
                    </button>
                    <button 
                        onclick="deleteUser({{ $user->id }})"
                        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                    >
                        Sil
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">E-posta</p>
                    <p class="font-medium">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Kayıt Tarihi</p>
                    <p class="font-medium">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                </div>
                @if($user->email_verified_at)
                <div>
                    <p class="text-gray-600">E-posta Doğrulama</p>
                    <p class="font-medium text-green-600">Doğrulanmış</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Kullanıcı Gönderileri -->
        @if($user->posts->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4">Gönderiler</h2>
            
            <div class="space-y-4">
                @foreach($user->posts as $post)
                <div class="border-b pb-4 last:border-b-0 last:pb-0">
                    <h3 class="text-lg font-semibold">{{ $post->title }}</h3>
                    <p class="text-gray-600 mt-1">{{ $post->excerpt }}</p>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-sm text-gray-500">
                            {{ $post->created_at->format('d.m.Y H:i') }}
                        </span>
                        <a href="/posts/{{ $post->id }}" class="text-blue-500 hover:text-blue-700">
                            Devamını Oku
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4">Kullanıcı Düzenle</h2>
            
            <form id="editForm" method="POST" class="space-y-4">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Ad Soyad
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        value="{{ $user->name }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        required
                    >
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        E-posta
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        value="{{ $user->email }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        required
                    >
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Yeni Şifre
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    >
                    <p class="text-sm text-gray-500 mt-1">Şifreyi değiştirmek istemiyorsanız boş bırakın</p>
                </div>

                <div class="flex justify-end space-x-4">
                    <button 
                        type="button"
                        onclick="closeEditModal()"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                    >
                        İptal
                    </button>
                    <button 
                        type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    >
                        Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(userId) {
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function deleteUser(userId) {
    if (confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')) {
        fetch(`/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => {
            if (response.ok) {
                window.location.href = '{{ route("users.index") }}';
            }
        });
    }
}

// Modal dışına tıklandığında kapat
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endpush
