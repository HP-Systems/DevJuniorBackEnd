<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProyectosController extends Controller
{
    public function obtenerProyectos(){
        try {
            $hoy = Carbon::now('America/Monterrey')->toDateString();
    

            // Realizar la consulta para obtener los proyectos
            $proyectos = Proyecto::where('fecha_creacion', '<=', $hoy)
                ->where('fecha_limite', '>=', $hoy)
                ->where('status', 1)
                ->get();

            return response()->json(
                [
                    'status' => 200,
                    'data' => $proyectos,
                    'msg' => 'Proyectos obtenidos correctamente.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
            Log::error('Exception during obtenerProyectos: ' . $e->getMessage());
            return response()->json(
                [
                    'status' => 500,
                    'data' => [],
                    'msg' => 'Error al obtener los proyectos.',
                    'error' => $e->getMessage()
                ], 500
            );
        }
    }
    
    public function crearProyecto(Request $request){
        try{
            $fecha_creacion = Carbon::now('America/Monterrey')->format('Y-m-d H:i:s');
        
            $validation = Validator::make(
                $request->all(),
                [
                    "empresa_id" => "required|integer",
                    "titulo" => "required",
                    "descripcion" => "required",
                    "fecha_limite" => "required|date|after_or_equal:" . $fecha_creacion,
                ],
                [
                    "fecha_limite.after_or_equal" => "La fecha límite no puede ser menor a la fecha de hoy.",
                ]
            );

            if ($validation->fails()) {
                return response()->json(
                    [
                        'data' => [],
                        'msg' => 'Error en las validaciones.',
                        'error' => $validation->errors(),
                    ],
                    400
                );
            }

            $proyecto = Proyecto::create([
                "empresa_id" => $request->empresa_id,
                "titulo" => $request->titulo,
                "descripcion" => $request->descripcion,
                "fecha_creacion" => $fecha_creacion,
                "fecha_limite" => $request->fecha_limite,
                "status" => 1,
            ]);

            return response()->json([
                'status' => 200,
                'data' => $proyecto,
                'msg' => 'Proyecto creado exitosamente.',
                'error' => []
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception during crearProyecto: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'data' => [],
                'msg' => 'Error de servidor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function editarProyecto(Request $request, $id){
        try{
            $fecha_creacion = Carbon::now('America/Monterrey')->format('Y-m-d H:i:s');
        
            $validation = Validator::make(
                $request->all(),
                [
                    "titulo" => "required",
                    "descripcion" => "required",
                    "fecha_limite" => "required|date|after_or_equal:" . $fecha_creacion,
                ],
                [
                    "fecha_limite.after_or_equal" => "La fecha límite no puede ser menor a la fecha de hoy.",
                ]
            );

            if ($validation->fails()) {
                return response()->json(
                    [
                        'data' => [],
                        'msg' => 'Error en las validaciones.',
                        'error' => $validation->errors(),
                    ],
                    400
                );
            }

            $proyecto = Proyecto::findOrFail($id);
            $proyecto->titulo = $request->titulo;
            $proyecto->descripcion = $request->descripcion;
            $proyecto->fecha_limite = $request->fecha_limite;
            $proyecto->save();

            return response()->json([
                'status' => 200,
                'data' => $proyecto,
                'msg' => 'Proyecto editado exitosamente.',
                'error' => []
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception during editarProyecto: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'data' => [],
                'msg' => 'Error de servidor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function cambiarStatusProyecto($id)
    {
        try {
            $proyecto = Proyecto::findOrFail($id);

            if($proyecto->status == 0){
                $proyecto->status = 1;
            } else{
                $proyecto->status = 0;
            }

            $proyecto->save();

            return response()->json([
                'status' => 200,
                'data' => $proyecto,
                'msg' => 'Proyecto desactivado exitosamente.',
                'error' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'data' => [],
                'msg' => 'Error de servidor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
