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
    public function mongoConection()

    {

        $collection = DB::connection('mongodb')->table('actor')->get();
        return $collection;
    }
    //estudiante
    public function sleccionarProyecto(Request $request)
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
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Error de validación',
                        'error' => $validator->errors()
                    ],
                    400
                );
            }
            $fecha_actual = Carbon::now();
            //si la fecha de envio es mayor a la fecha actual
            $proyecto = Proyecto::find($request->proyecto_id);
            if ($fecha_actual > $proyecto->fecha_limite) {
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'La etapa de propuesta ha finalizado',
                    ],
                    400
                );
            }
            
            $collection = DB::connection('mongodb')->table('Propuestas')->insert(
                [
                    'URL_propuesta' => $request->URL_propuesta,
                    'fecha_envio' => Carbon::now(),
                    'estudiante_id' => $request->estudiante_id,
                    'proyecto_id' => $request->proyecto_id,
                    'etapa' => 0,
                    'status' => 1,
                ]
            );
            return response()->json(
                [
                    'status' => 200,
                    'data' => $collection,
                    'msg' => 'Propuesta creada correctamente',
                ],
                200
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(
                [
                    'status' => 400,
                    'data' => [],
                    'msg' => 'Error de validación',

                ],
                400
            );
        }
    }
    
    public function historial($id)
    {
        try{
        //obtener las propuestas a las que el estudiante pertenece
        $collection = DB::connection('mongodb')->table('Propuestas')
            ->where('estudiante_id', $id)->where('status', 1)->first();

        return response()->json(
            [
                'status' => 200,
                'data' => $collection,
                'msg' => 'Propuestas encontradas',
                'id' => $id,
            ], 200);
        
            if (is_null($collection)) {
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
                    'msg' => 'Proyectos disponibles',
                    'error' => []
                ], 200
            );
        }
        elseif($collection->etapa == 0)
        {
            return response()->json(
                [
                    'status' => 200,
                    'data' => $collection,
                    'msg' => 'Propuesta en espera de aceptación',
                ],
                200
            );
        }
        elseif($collection->etapa == 1)
        {
            return response()->json(
                [
                    'status' => 200,
                    'data' => $collection,
                    'msg' => 'Propuesta en espera de vista y/o corrección',
                ],
                200
            );
        }
        elseif($collection->etapa == 2)
        {
            return response()->json(
                [
                    'status' => 200,
                    'data' => $collection,
                    'msg' => 'Propuesta en etapa de desarrollo',
                ],
                200
            );
        }
        elseif($collection->etapa == 3)
        {
            return response()->json(
                [
                    'status' => 200,
                    'data' => $collection,
                    'msg' => 'Propuesta en etapa de finalización',
                ],
                200
            );
        }
    } catch (\Exception $e) {
        Log::error('Exception during historial: ' . $e->getMessage());
        return response()->json(
            [
                'status' => 500,
                'data' => [],
                'msg' => 'Error al obtener el historial.',
                'error' => $e->getMessage()
            ], 500
        );
    }
    }
    public function subirVistas(Request $request)
    {
        try{
        $validator=Validator::make(
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
            return response()->json(
                [
                    'status' => 400,
                    'data' => [],
                    'msg' => 'Error de validación',
                    'error' => $validator->errors()
                ],
                400
            );
        }

        $collection = DB::connection('mongodb')->table('Vistas')->insert(
            [
                'id_propuesta' => $request->id_propuesta,
                'URL_Imagen' => $request->URL_Imagen,
                'fecha_envio' => Carbon::now(),
            ]
        );
        return response()->json(
            [
                'status' => 200,
                'data' => $collection,
                'msg' => 'Vista subida correctamente',
            ],
            200
        );
    } catch (\Exception $e) {
        Log::error('Exception during subirVistas: ' . $e->getMessage());
        return response()->json(
            [
                'status' => 500,
                'data' => [],
                'msg' => 'Error al subir la vista.',
                'error' => $e->getMessage()
            ], 500
        );
    }}

    //admin
    public function getPropuestas($id_proyecto)
    {
        try {
            $collection = DB::connection('mongodb')->table('Propuestas')
                ->where('id_proyecto', $id_proyecto)
                ->where('etapa', '!=', 0)
                ->where('status', 1)
                ->first();
    
                if (is_null($collection)) {
                $collection = DB::connection('mongodb')->table('Propuestas')
                    ->where('id_proyecto', $id_proyecto)
                    ->where('etapa', 0)
                    ->where('status', 1)
                    ->get();
                return response()->json(
                    [
                        'status' => 200,
                        'data' => $collection,
                        'msg' => 'Propuestas encontradas',
                    ],
                    200
                );
            } elseif ($collection->first()->etapa == 1) {
                // Retorna la propuesta con la vista
                $propuesta = $collection->first();
                $vista = DB::connection('mongodb')->table('Vistas')
                    ->where('id_propuesta', $propuesta->_id)
                    ->first();
    
                $propuesta->vista = $vista;
    
                return response()->json(
                    [
                        'status' => 200,
                        'data' => $propuesta,
                        'msg' => 'Propuesta con vista encontrada',
                    ],
                    200
                );
            }
    
            return response()->json(
                [
                    'status' => 200,
                    'data' => $collection,
                    'msg' => 'Propuestas encontradas',
                ],
                200
            );
        } catch (\Exception $e) {
            Log::error('Exception during getPropuestas: ' . $e->getMessage());
            return response()->json(
                [
                    'status' => 500,
                    'data' => [],
                    'msg' => 'Error al obtener las propuestas.',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }
    public function etapasPropuestas(Request $request)
    {
        try{
            $validator=Validator::make(
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
                return response()->json(
                    [
                        'status' => 400,
                        'data' => [],
                        'msg' => 'Error de validación',
                        'error' => $validator->errors()
                    ],
                    400
                );
            }
        //actualiza la etapa de la propuesta a 1
        $collection = DB::connection('mongodb')
        ->collection('Propuestas')  
        ->where('id', $request->id_propuesta) 
        ->update([
            'etapa' => $request->etapa,
            'fecha_envio' => Carbon::now()
        ]); 
        return response()->json(
            [
                'status' => 200,
                'data' => $collection,
                'msg' => 'Etapa actualizada',
            ],
            200
        );
    } catch (\Exception $e) {
        Log::error('Exception during etapasPropuestas: ' . $e->getMessage());
        return response()->json(
            [
                'status' => 500,
                'data' => [],
                'msg' => 'Error al actualizar la etapa.',
                'error' => $e->getMessage()
            ], 500
        );
    }
    }
    
    public function cancelarPropuesta($id_propuesta)
    {
        try{
        //actualiza la etapa de la propuesta a 1
        $collection=DB::connection('mongodb')
            ->collection('Propuestas')  // Usar 'collection' en vez de 'table'
            ->where('id', $id_propuesta) // Filtrar por 'id'
            ->update(['status' => 0]);   
        return response()->json(
            [
                'status' => 200,
                'data' => $collection,
                'msg' => 'Propuesta cancelada',
            ],
            200
        );
    } catch (\Exception $e) {
        Log::error('Exception during cancelarPropuesta: ' . $e->getMessage());
        return response()->json(
            [
                'status' => 500,
                'data' => [],
                'msg' => 'Error al cancelar la propuesta.',
                'error' => $e->getMessage()
            ], 500
        );
    }
    }
    
   
    

}
