<?php

use App\Http\Controllers\mongoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProyectosController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [UsersController::class, 'register']);
Route::post('/login', [UsersController::class, 'login']);
Route::get('/mongo', [mongoController::class, 'mongoConection']);

Route::get('/proyectos', [ProyectosController::class, 'obtenerProyectos']);
Route::get('/proyectos/empresa/{id}', [ProyectosController::class, 'obtenerProyectosEmpresa']);
Route::get('/proyecto/{id}', [ProyectosController::class, 'obtenerInfoProyecto']);
Route::post('/proyecto/create', [ProyectosController::class, 'crearProyecto']);
Route::put('/proyecto/edit/{id}', [ProyectosController::class, 'editarProyecto']);
Route::put('/proyecto/status/{id}', [ProyectosController::class, 'cambiarStatusProyecto']);




Route::post('/proyecto/seleccion', [mongoController::class, 'sleccionarProyecto']);
Route::get('/proyectos/historial/{id}', [mongoController::class, 'historial']);
Route::post('/proyectos/vistas', [mongoController::class, 'subirVistas']);

Route::get('/propuestas/{id}', [mongoController::class, 'getPropuestas']);
Route::get('/propuesta/aceptada/{id}', [mongoController::class, 'getPropuestasAceptadas']);
Route::post('/propuesta/etapa', [mongoController::class, 'etapasPropuestas']);
Route::post('/propuesta/cancelacion', [mongoController::class, 'cancelarPropuesta']);
