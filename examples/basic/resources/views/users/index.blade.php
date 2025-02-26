@extends('layouts.app')

@section('title', 'Kullanıcılar')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Kullanıcılar</h1>
        
        <button 
            onclick="openCreateModal()"
            class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600"
        >
            Yeni Kullanıcı
        </button>
    </div>

    <!-- Kullanıcı Listesi -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ad Soyad
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        E-posta
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kayıt Tarihi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        İşlemler
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $user->id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $user->name }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">
                            {{ $user->email }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('users.show', $user->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            Görüntüle
                        </a>
                        <button 
                            onclick="openEditModal({{ $user->id }})"
                            class="text-indigo-600 hover:text-indigo-900 mr-3"
                        >
                            Düzenle
                        </button>
                        <button 
                            onclick="deleteUser({{ $user->id }})"
                            class="text-red-600 hover:text-red-900"
                        >
                            Sil
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h2 id="modalTitle" class="text-2xl font-bold mb-4">Yeni Kullanıcı</h2>
            
            <form id="userForm" method="POST" class="space-y-4">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Ad Soyad
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
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
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        required
                    >
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Şifre
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    >
                    <p class="text-sm text-gray-500 mt-1">Düzenleme yaparken boş bırakırsanız şifre değişmez</p>
                </div>

                <div class="flex justify-end space-x-4">
                    <button 
                        type="button"
                        onclick="closeModal()"
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
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Yeni Kullanıcı';
    document.getElementById('userForm').reset();
    document.getElementById('userForm').action = '{{ route("users.store") }}';
    document.getElementById('userForm').method = 'POST';
    document.getElementById('userModal').classList.remove('hidden');
}

function openEditModal(userId) {
    document.getElementById('modalTitle').textContent = 'Kullanıcı Düzenle';
    document.getElementById('userForm').action = `/users/${userId}`;
    document.getElementById('userForm').method = 'PUT';
    
    // Kullanıcı bilgilerini getir
    fetch(`/users/${userId}`)
        .then(response => response.json())
        .then(user => {
            document.getElementById('name').value = user.name;
            document.getElementById('email').value = user.email;
            document.getElementById('password').value = '';
            document.getElementById('userModal').classList.remove('hidden');
        });
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
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
                window.location.reload();
            }
        });
    }
}

// Modal dışına tıklandığında kapat
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endpush
