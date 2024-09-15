<?php

namespace App\Http\Controllers;

use App\Jobs\sendMail;
use App\Mail\SendMailActivation;
use App\Models\Empresa;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function register(Request $request){
        try{
            $validation = Validator::make(
                $request->all(),
                [
                    "email" => "required|email|unique:users,email",
                    "password" => "min:8",
                    "telefono" => "max:10",
                    "tipo_usuario" => "required",
                ],
                [
                    "email.email" => "El campo correo es incorrecto.",
                    "email.unique" => "El correo ya ha sido registrado.",
                    "password.min" => "La contraseña debe tener al menos :min caracteres",
                    "telefono.min" => "El telefono solo debe tener :max caracteres",

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


            $tipo_usuario = $request->tipo_usuario;
            if($tipo_usuario == 1){
                $validate = Validator::make(
                    $request->all(),
                    [
                        "nombre" => "required",
                        "apellido" => "required",
                        "telefono" => "required",
                        "fecha_nacimiento" => "required",
                        "universidad" => "required",
                        "clave_estudiante" => "required",
                        "periodo" => "required",
                        "n_periodo" => "required",
                        "foto_credencial" => "required",
                    ]
                );
                if($validate->failed()){
                    return response()->json(
                        [
                            'status' => 400,
                            'data' => [],
                            'msg' => 'Error de validación',
                            'error' => $validate->errors()
                        ], 400
                    );
                }
                $persona = Estudiante::create([
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
            } else{
               $validate = Validator::make(
                    $request->all(),
                    [
                        "nombre" => "required",
                        "sector" => "required",
                        "ciudad" => "required",
                        "estado" => "required",
                        "descripcion" => "required",
                        "telefono" => "required",
                    ]
                );
                if($validate->failed()){
                    return response()->json(
                        [
                            'status' => 400,
                            'data' => [],
                            'msg' => 'Error de validación',
                            'error' => $validate->errors()
                        ], 400
                    );
                }
                $persona = Empresa::create([
                    "nombre" => $request->nombre,
                    "sector" => $request->sector,
                    "ciudad" => $request->ciudad,
                    "estado" => $request->estado,
                    "descripcion" => $request->descripcion,
                    "telefono" => $request->telefono,
                ]);
            }

            $user = User::create([
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "persona_id" => $persona->id,
                "tipo_usuario" => $tipo_usuario,
                "status" => 0
            ]);
            $url = URL::temporarySignedRoute('confirm',now()->addMinute(5),['id' => $user->id]);
            sendMail::dispatch($url, $user);

            return response()->json(
                [
                    'status' => 200,
                    'data' => $user,
                    'msg' => 'Verifica tu correo para confirmar tu cuenta.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
            Log::error('Exception during register: ' . $e->getMessage());
            return response()->json(
                [
                    'status' => 500,
                    'data' => [],
                    'msg' => 'Error de servidor.',
                    
                ], 500
            );
        }
    }

    public function login(Request $request){
        try{
            $validate = Validator::make(
                $request->all(),
                [
                    "email" => "required | email",
                    "password" => "required",
                ]
            );

            if ($validate->fails()) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Error de validación',
                        'error' => $validate->errors()
                    ], 400
                );
            }

            $user = User::where('email', $request->email)->first();
            if(!$user || (!Hash::check($request->password, $user->password))){
                return response()->json(
                    [
                        'status' => 404,
                        'data' => [],
                        'msg' => 'Los datos son incorrectos. Inténtalo de nuevo.',
                        'error' => []
                    ], 404
                );
            }

            if($user->status == 0){
                $url = URL::temporarySignedRoute('confirm',now()->addMinute(5),['id' => $user->id]);
                sendMail::dispatch($url, $user);
                return response()->json(
                    [
                        'status' => 403,
                        'data' => [],
                        'msg' => 'Cuenta desactivada.Verifique su correo para activarla.',
                        'error' => []
                    ], 403
                );
            }

            return response()->json(
                [
                    'status' => 200,
                    'data' => $user,
                    'msg' => 'Usuario logueado con exito.',
                    'error' => []
                ], 200
            );

        } catch (\Exception $e) {
            Log::error('Exception during login: ' . $e->getMessage());
            return response()->json(
                [
                    'status' => 500,
                    'data' => [],
                    'msg' => 'Error de servidor.',
                    
                ], 500
            );
        }

    }

    public function confirmEmail(Request $request,$id){
        try{
          
            //si la ruta no está firmada, redirige al formulario de login con un error
            if (!$request->hasValidSignature()) {
                return response()->json(
                    [
                        'status' => 403,
                        'data' => [],
                        'msg' => 'Invalid signature',
                        'error' => []
                    ], 403
                );
            }
            //busca el usuario en la base de datos mediante el id
            $user = User::find($id);
            //si no encuentra el usuario, redirige al formulario de login con un error
            if (!$user) {
                return response()->json(
                    [
                        'status' => 404,
                        'data' => [],
                        'msg' => 'User not found',
                        'error' => []
                    ], 404
                );
            }
            //se verifica el usuario y se guarda en la base de datos
            //se direcciona a la vista de 2FA para la introducir el código
            $user->status = 1;
            $user->save();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $user,
                    'msg' => 'Usuario confirmado con exito.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
            Log::error('Exception during confirmEmail: ' . $e->getMessage());
            return response()->json(
                [
                    'status' => 500,
                    'data' => [],
                    'msg' => 'Error de servidor.',
                    
                ], 500
            );
        }
    }


}
