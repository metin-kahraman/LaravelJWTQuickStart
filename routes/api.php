<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \Firebase\JWT\JWT;
use App\Http\Controllers\ApiAuthController;
use App\Http\Middleware\JWTVerify;


Route::post('/auth/register', [ApiAuthController::class,'userCreate']);
Route::post('/auth/login', [ApiAuthController::class,'userLogin']);
Route::get('/auth/me', [ApiAuthController::class,'userMe'])->middleware(JWTVerify::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
