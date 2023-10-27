<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class PerfilController extends Controller
{
    public function __construct()
    {   
        $this->middleware('auth');
    }

    public function index()
    {
        return view('perfil.index');
    }

    public function store(Request $request)
    {
        $request->request->add(['username' => Str::slug($request->username)]);

        $this->validate($request, [
            'username' => ['required', 'unique:users,username,' . auth()->user()->id, 'min:3', 'max:20', 'not_in:editarperfil', ],
            //'email' => ['required', 'unique:users,email', 'email', 'max:60'],
            //'password' => ['required'],
            //'newpPaswword' => ['requiered', 'min:6'],

        ]);

        //if(!Auth::attempt(['password' => $request->password])){
        //    return back()->with('mensaje', 'Contraseña incorrecta');
        //}

        

        if($request->imagen) {
            $imagen = $request->file('imagen');
            $nombreImagen = Str::uuid() . "." . $imagen->extension();
            $imagenServidor = Image::make($imagen);
            $imagenServidor->fit(1000, 1000,);
            $imagentPath = public_path('perfiles') . '/' . $nombreImagen;
            $imagenServidor->save($imagentPath);

        }
        
        //Guardar cambios

        $usuario = User::find(auth()->user()->id);
        $usuario->username = $request->username;
        //$usuario->email = $request->email;
        $usuario->imagen = $nombreImagen ?? auth()->user()->imagen ?? null;
        $usuario->save();

        //redireccionar al usuarios 
        return redirect()->route('posts.index', $usuario->username);
    }
}
