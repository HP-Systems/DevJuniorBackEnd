<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function registrarEstudiante(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "email" => "required|email|unique:users,email",
                    "password" => "required|min:8",
                    "nombre" => "required",
                    "apellido" => "required",
                    "telefono" => "required|max:10",
                    "fecha_nacimiento" => "required",
                    "universidad" => "required",
                    "clave_estudiante" => "required",
                    "periodo" => "required",
                    "n_periodo" => "required",
                    "foto_credencial" => "required",
                ],
                [
                    "email.email" => "El campo correo es incorrecto.",
                    "email.unique" => "El correo ya ha sido registrado.",
                    "password.min" => "La contraseña debe tener al menos :min caracteres",

                ]
            );

            if ($validation->fails()) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Error de validación',
                        'error' => $validation->errors()
                    ], 400
                );
            }

            $huesped = Estudiante::create([
                "nombre" => $request->nombre,
                "apellido" => $request->apellido,
                "telefono" => $request->telefono,
                "fecha_nacimiento" => $request->fecha_nacimiento,
                "universidad" => $request->universidad,
                "clave_estudiante" => $request->clave_estudiante,
                "periodo" => $request->periodo,
                "n_periodo" => $request->n_periodo,
                "foto_credencial" => $request->foto_credencial,
            ]);


            $user = User::create([
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "persona_id" => $huesped->id,
                "tipo_usuario" => 2,
            ]);

            return response()->json(
                [
                    'status' => 200,
                    'data' => $user,
                    'msg' => 'Usuario creado con exito.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
            Log::error('Exception during registrarEstudiante: ' . $e->getMessage());
            return response()->json(
                [
                    'status' => 500,
                    'data' => [],
                    'msg' => 'Error de servidor.',
                    'error' => $e->getMessage(),
                ], 500
            );
        }
    }
}
