<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CardController;
use App\Http\Controllers\API\ColumnController;
use App\Http\Controllers\API\ExportController;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

Route::resource('columns', ColumnController::class);

Route::resource('cards', CardController::class);

Route::post('cards/swap', [CardController::class , 'swap']);
Route::post('cards/sort', [CardController::class , 'sort']);
Route::post('cards/export', [ExportController::class , 'export']);

Route::group(['middleware' => 'token:api'], function () {
    Route::get('list-cards', [CardController::class , 'listCards']);
});
