<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\CommentController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/comments', [CommentController::class, 'store']);
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
