<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProyectosController extends Controller
{
    public function obtenerProyectos(){
        try{
            $hoy = Carbon::now('America/Monterrey')->toDateString();

            $proyectos = DB::table('proyectos')
                ->where('fecha_creacion', '<=', $hoy)
                ->where('fecha_limite', '>=', $hoy)
                ->where('status', 1)
                ->get();

            return response()->json(
                [
                    'status' => 200,
                    'data' => $proyectos,
                    'msg' => 'Proyectos obtenidos con éxito.',
                    'error' => []
                ], 200
            );
        } catch (\Exception $e) {
            Log::error('Exception during obtenerProyectos: ' . $e->getMessage());
            return response()->json(
                [
                    'status' => 500,
                    'data' => [],
                    'msg' => 'Error de servidor.',
                    
                ], 500
            );
        }
    }

    public function crearProyecto(){
        
    }


}
