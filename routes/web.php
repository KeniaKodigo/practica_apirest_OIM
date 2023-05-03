<?php

use App\Http\Controllers\ClientesController;
use App\Http\Controllers\CursosController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//asignamos la ruta para obtener todos los clientes
Route::get('/clientes', [ClientesController::class, 'index']);
Route::post('/registrarCliente', [ClientesController::class, 'store']);

//rutas de los cursos
Route::get('/cursos', [CursosController::class, 'index']);
Route::post('/registrarCurso', [CursosController::class, 'store']);
Route::get('/cursobyId/{id}', [CursosController::class, 'show']);
Route::put('/actualizarCurso/{id}', [CursosController::class, 'update']);
Route::delete('/eliminarCurso/{id}', [CursosController::class, 'destroy']);