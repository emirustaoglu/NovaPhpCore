<?php

namespace App\Controllers;

use NovaCore\Http\Controller;
use NovaCore\Database\DB;
use NovaCore\Security\Security;
use App\Models\User;

class UserController extends Controller
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function index()
    {
        // Query Builder örneği
        $users = DB::table('users')
            ->select(['id', 'name', 'email', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.index', ['users' => $users]);
    }

    public function show($id)
    {
        // Model kullanım örneği
        $user = User::find($id);
        if (!$user) {
            return back()->with('error', 'Kullanıcı bulunamadı');
        }

        // İlişkili verileri yükleme örneği
        $user->load('posts');

        return view('users.show', ['user' => $user]);
    }

    public function store()
    {
        // Input validasyonu ve güvenlik
        $input = $this->security->sanitize($_POST);
        
        // Model ile kayıt oluşturma
        try {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $this->security->hashPassword($input['password'])
            ]);

            return redirect()
                ->route('users.show', ['id' => $user->id])
                ->with('success', 'Kullanıcı oluşturuldu');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Kullanıcı oluşturulamadı')
                ->withInput();
        }
    }

    public function update($id)
    {
        $user = User::find($id);
        if (!$user) {
            return back()->with('error', 'Kullanıcı bulunamadı');
        }

        // Transaction örneği
        DB::beginTransaction();
        try {
            $input = $this->security->sanitize($_POST);
            
            $user->update([
                'name' => $input['name'],
                'email' => $input['email']
            ]);

            if (!empty($input['password'])) {
                $user->update([
                    'password' => $this->security->hashPassword($input['password'])
                ]);
            }

            DB::commit();
            return back()->with('success', 'Kullanıcı güncellendi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Kullanıcı güncellenemedi')
                ->withInput();
        }
    }

    public function delete($id)
    {
        // Soft delete örneği
        try {
            $user = User::find($id);
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', 'Kullanıcı silindi');
        } catch (\Exception $e) {
            return back()->with('error', 'Kullanıcı silinemedi');
        }
    }
}
