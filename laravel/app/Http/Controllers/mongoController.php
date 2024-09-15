<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class mongoController extends Controller
{
    // Probar la conexión con MongoDB
    public function mongoConection()
    {
        $collection = DB::connection('mongodb')->collection('actor')->get();
        return $collection;
    }

    // Seleccionar proyecto
    public function seleccionarProyecto(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'estudiante_id' => 'required',
                    'proyecto_id' => 'required',
                    'URL_propuesta' => 'required',
                ],
                [
                    'estudiante_id.required' => 'El campo estudiante_id es requerido',
                    'proyecto_id.required' => 'El campo proyecto_id es requerido',
                    'URL_propuesta.required' => 'El campo URL_propuesta es requerido',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'data' => [],
                    'msg' => 'Error de validación',
                    'error' => $validator->errors()
                ], 400);
            }

            $fecha_actual = Carbon::now();
            $proyecto = Proyecto::find($request->proyecto_id);

            if (!$proyecto) {
                return response()->json([
                    'status' => 400,
                    'msg' => 'Proyecto no encontrado',
                ], 400);
            }

            if ($fecha_actual > $proyecto->fecha_limite) {
                return response()->json([
                    'status' => 400,
                    'msg' => 'La etapa de propuesta ha finalizado',
                ], 400);
            }

            $collection = DB::connection('mongodb')->collection('Propuestas')->insert([
                'URL_propuesta' => $request->URL_propuesta,
                'fecha_envio' => Carbon::now(),
                'estudiante_id' => $request->estudiante_id,
                'proyecto_id' => $request->proyecto_id,
                'etapa' => 0,
                'status' => 1,
            ]);

            return response()->json([
                'status' => 200,
                'data' => $collection,
                'msg' => 'Propuesta creada correctamente',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception during seleccionarProyecto: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'msg' => 'Error al crear la propuesta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtener historial
    public function historial($estudiante_id)
    {
        try {
            $collection = DB::connection('mongodb')->collection('Propuestas')
                ->where('estudiante_id', $estudiante_id)
                ->where('status', 1)
                ->first();

            if (!$collection) {
                $hoy = Carbon::now('America/Monterrey')->toDateString();
                $proyectos = Proyecto::where('fecha_creacion', '<=', $hoy)
                    ->where('fecha_limite', '>=', $hoy)
                    ->where('status', 1)
                    ->get();

                return response()->json([
                    'status' => 200,
                    'data' => $proyectos,
                    'msg' => 'Proyectos disponibles',
                ], 200);
            }

            $msg = match ($collection->etapa) {
                0 => 'Propuesta en espera de aceptación',
                1 => 'Propuesta en espera de vista y/o corrección',
                2 => 'Propuesta en etapa de desarrollo',
                3 => 'Propuesta en etapa de finalización',
                default => 'Etapa desconocida',
            };

            return response()->json([
                'status' => 200,
                'data' => $collection,
                'msg' => $msg,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception during historial: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'msg' => 'Error al obtener el historial.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Subir vistas
    public function subirVistas(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'id_propuesta' => 'required',
                    'URL_Imagen' => 'required',
                ],
                [
                    'id_propuesta.required' => 'El campo id_propuesta es requerido',
                    'URL_Imagen.required' => 'El campo URL_Imagen es requerido',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'data' => [],
                    'msg' => 'Error de validación',
                    'error' => $validator->errors()
                ], 400);
            }

            $collection = DB::connection('mongodb')->collection('Vistas')->insert([
                'id_propuesta' => $request->id_propuesta,
                'URL_Imagen' => $request->URL_Imagen,
                'fecha_envio' => Carbon::now(),
            ]);

            return response()->json([
                'status' => 200,
                'data' => $collection,
                'msg' => 'Vista subida correctamente',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception during subirVistas: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'msg' => 'Error al subir la vista.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtener propuestas para un proyecto
    public function getPropuestas($id_proyecto)
    {
        try {
            $collection = DB::connection('mongodb')->collection('Propuestas')
                ->where('id_proyecto', $id_proyecto)
                ->where('etapa', '!=', 0)
                ->where('status', 1)
                ->first();

            if (!$collection) {
                $collection = DB::connection('mongodb')->collection('Propuestas')
                    ->where('id_proyecto', $id_proyecto)
                    ->where('etapa', 0)
                    ->where('status', 1)
                    ->get();

                return response()->json([
                    'status' => 200,
                    'data' => $collection,
                    'msg' => 'Propuestas encontradas',
                ], 200);
            }

            if ($collection->etapa == 1) {
                $vista = DB::connection('mongodb')->collection('Vistas')
                    ->where('id_propuesta', $collection->_id)
                    ->first();
                $collection->vista = $vista;
            }

            return response()->json([
                'status' => 200,
                'data' => $collection,
                'msg' => 'Propuestas encontradas',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception during getPropuestas: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'msg' => 'Error al obtener las propuestas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Actualizar etapas de propuestas
    public function etapasPropuestas(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'id_propuesta' => 'required',
                    'etapa' => 'required',
                ],
                [
                    'id_propuesta.required' => 'El campo id_propuesta es requerido',
                    'etapa.required' => 'El campo etapa es requerido',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'data' => [],
                    'msg' => 'Error de validación',
                    'error' => $validator->errors()
                ], 400);
            }

            $collection = DB::connection('mongodb')->collection('Propuestas')
                ->where('id', $request->id_propuesta)
                ->update([
                    'etapa' => $request->etapa,
                    'fecha_envio' => Carbon::now(),
                ]);

            return response()->json([
                'status' => 200,
                'data' => $collection,
                'msg' => 'Etapa actualizada',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception during etapasPropuestas: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'msg' => 'Error al actualizar la etapa.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Cancelar propuesta
    public function cancelarPropuesta($id_propuesta)
    {
        try {
            $collection = DB::connection('mongodb')->collection('Propuestas')
                ->where('id', $id_propuesta)
                ->update(['status' => 0]);

            return response()->json([
                'status' => 200,
                'data' => $collection,
                'msg' => 'Propuesta cancelada',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception during cancelarPropuesta: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'msg' => 'Error al cancelar la propuesta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
