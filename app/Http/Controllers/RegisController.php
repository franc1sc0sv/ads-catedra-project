<?php

namespace App\Http\Controllers;

use App\Models\Turista;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisController extends BaseController
{
    public function Registro()
    {
        return view('Registro');
    }

    public function validar_registro(Request $request)
    {

        // Validar datos

        $request->validate([
            'Nom_Cli' => ['required', 'regex:/^[\pL\sÑñ]+$/u'],
            'Correo_Cli' => ['required', 'email', Rule::unique('Turista', 'Correo_Cli')->ignore($request->id), 'regex:/^.*\.com$/'],
            'Contra_Cli' => ['required', 'min:8', 'regex:/[A-ZÑ]/', 'regex:/[a-zñ]/', 'regex:/[$!%*&#]/', 'confirmed'],
            'Fecha_Cliente' => ['required', 'date', 'before:today'],
        ], [
            'Nom_Cli.required' => 'El nombre del cliente es obligatorio.',
            'Nom_Cli.regex' => 'El nombre del cliente solo puede contener letras y espacios.',

            'Correo_Cli.required' => 'El correo del cliente es obligatorio.',
            'Correo_Cli.email' => 'El correo debe ser una dirección válida con el símbolo @.',
            'Correo_Cli.unique' => 'Este correo ya está en uso, por favor ingresa uno diferente.',
            'Correo_Cli.regex' => 'El correo debe terminar en ".com".',

            'Contra_Cli.required' => 'La contraseña es obligatoria.',
            'Contra_Cli.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'Contra_Cli.regex' => 'La contraseña debe contener al menos una letra mayúscula (incluyendo Ñ), una letra minúscula (incluyendo ñ) y un carácter especial como $!%*&#.',
            'Contra_Cli.confirmed' => 'La confirmación de la contraseña no coincide.',

            'Fecha_Cliente.required' => 'La fecha de nacimiento es obligatoria.',
            'Fecha_Cliente.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'Fecha_Cliente.before' => 'La fecha de nacimiento debe ser anterior a la fecha actual.',
        ]);

        $fechaCliente = Carbon::parse($request->input('Fecha_Cliente'));
        $edadTurista = $fechaCliente->age;

        if ($edadTurista < 18) {
            return back()->withErrors(['Fecha_Cliente' => 'Debes ser mayor de 18 años para registrarte.'])->withInput();
        }

        // Crear usuario
        $user = new Turista;
        $user->Nom_Cli = $request->input('Nom_Cli');
        $user->Correo_Cli = $request->input('Correo_Cli');
        $user->Contra_Cli = Hash::make($request->input('Contra_Cli'));
        $user->Fecha_Cliente = $request->input('Fecha_Cliente');
        $user->Edad_Turista = $edadTurista;
        $user->save();

        // Asignar rol
        $user->assignRole('Usuario');

        // Autenticar y redireccionar
        Auth::login($user);

        session()->flash('guardado', 'Su registro ha sido guardado');

        return redirect()->route('Index_Turi');
    }
}
