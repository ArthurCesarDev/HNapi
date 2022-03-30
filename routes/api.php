<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\AuthController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\FoundAndLostController;
use App\Http\Controllers\ResevationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\WarningController;

Route::get('/ping', function(){
return ['pong'=>eu];
});

// acesso autenticação Login
Route::get('/401',[AuthController::class, 'unauthorized'])->name('login');

// acesso cadastro Login 
Route::post('/auth/login',[AuthController::class, 'login']);
Route::post('/auth/register',[AuthController::class, 'register']);


// grupo de rotas composta a autorização do login e token


// logout

Route::middleware('auth:api')->group(function(){
    Route::post('/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

// Mural de Avisos

Route::get('/walls',[WallController::class, 'getAll']);
Route::post('/wall/{id}/like',[WallController::class, 'like']);

//Documentos

Route::get('/docs',[DocController::class, 'getMydocs']);

//Livro de ocorrências

Route::get('/warnings',[WarningController::class, 'getMyWarnings']);
Route::post('/warning',[WarningController::class, 'setWarning']);
Route::post('/warning/file',[WarningController::class, 'addWarningFile']);

// Boletos

Route::get('/billets',[BilletController::class, 'getAll']);

// Achados e Perdidos

Route::get('/foundandlost',[FoundAndLostController::class, 'getAll']);
Route::post('/foundandlost',[FoundAndLostController::class, 'insert']);
Route::put('/foundandlosts/{id}', [FoundAndLostController::class, 'update']);

// Unidade

Route::get('/unit/{id}',[UnitController::class, 'getInfo']);
Route::post('/unit/{id}/addperson',[UnitController::class, 'addPerson']);
Route::post('/unit/{id}/addvehicle',[UnitController::class, 'addVehicle']);
Route::post('/unit/{id}/addpet',[UnitController::class, 'addPet']);
Route::post('/unit/{id}/removeperson',[UnitController::class, 'removePerson']);
Route::post('/unit/{id}/removevehicle',[UnitController::class, 'removeVehicle']);
Route::post('/unit/{id}/removepet',[UnitController::class, 'removePet']);


//Reservas

Route::get('/reservations',[ResevationController::class, 'getReservations']);
Route::post('/reservation/{id}',[ResevationController::class, 'setReservations']);

Route::get('/reservation/{id}/disableddates',[ResevationController::class, 'getDisableDates']);
Route::get('/reservation/{id}/times',[ResevationController::class, 'getTimes']);


Route::get('/myreservation',[ResevationController::class, 'getMyReservations']);
Route::delete('/myreservation/{id}',[ResevationController::class, 'delReservations']);


});
