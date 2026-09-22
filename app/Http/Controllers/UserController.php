<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data User',
            'user' => User::with('posisi')->where('nonaktif', 'T')->get(),
            'posisi' => Posisi::all()
        ];
        return view('user.user', $data);
    }

    public function create(Request $r)
    {
        User::create([
            'name' => $r->name, 
            'email' => $r->email,
            'posisi_id' => $r->posisi_id,
            'password' => bcrypt($r->password),
        ]);

        return redirect()->route('user.index')->with('sukses', 'Data Berhasil Dibuat');
    }
    
    public function delete(Request $r)
    {
        User::find($r->id_user)->delete();
        return redirect()->route('user.index')->with('sukses', 'Data Berhasil Dihapus');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $posisi = Posisi::all();
        return view('user.edit', compact('user', 'posisi'));
    }

    public function update(Request $r)
    {
        $r->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $r->id,
            'posisi_id' => 'required|exists:tb_posisi,id_posisi',
            'password' => 'nullable|string|min:4',
        ]);

        $user = User::findOrFail($r->id);
        $user->name = $r->name;
        $user->email = $r->email;
        $user->posisi_id = $r->posisi_id;
        if (!empty($r->password)) {
            $user->password = bcrypt($r->password);
        }
        $user->save();

        return redirect()->route('user.index')->with('sukses', 'Data Berhasil Diupdate');
    }
}
