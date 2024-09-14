<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    /*public function registrarEstudiante(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "nombre" => "required",
                    "apellido" => "required",
                    "fecha_nacimiento" => "required",
                    "universidad" => "required",
                    "clave_estudiante" => "required",
                    "periodo" => "required",
                    "n_periodo" =>"required",
                    "foto_credencial" => "required",

                    "email.email" => "El campo :attribute es incorrecto",
                    "password.min" => "La contraseña debe tener al menos :min caracteres",
                ]
            );

            if ($validation->fails()) {
                return response()->json(['error' => $validation->errors()], 400);
            }

            $huesped = Huesped::create([
                "nombre" => $request->nombre,
                "apellido" => $request->apellido,
                "telefono" => $request->telefono,
            ]);

            $user = User::create([
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "userable_id" => $huesped->id,
                "userable_type" => 2,
            ]);


            return response()->json([
                'message' => 'Usuario creado con exito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al crear el usuario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }*/
}
