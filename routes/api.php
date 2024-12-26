<?php

use App\Http\Controllers\Core\API\Auth\AuthAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        // Route::post('register', function() {
        //     dd('ddd');
        // });
		Route::post('register', [AuthAPIController::class, 'register']);
		Route::post('login', [AuthAPIController::class, 'login']);
		// Route::post('verify/phone', [AuthAPIController::class, 'verifyPhone']);
		// Route::post('verify/email', [AuthAPIController::class, 'verifyEmail']);
		Route::post('reset-password', [AuthAPIController::class, 'resetPassword']);
		Route::post('logout', [AuthAPIController::class, 'logout'])->middleware(['auth:sanctum']);
		Route::post('validate-token', [AuthAPIController::class, 'validateToken']);
		// Route::post('send-otp/phone', [AuthAPIController::class, 'sendPhoneOTP']);
		// Route::post('send-otp/email', [AuthAPIController::class, 'sendEmailOTP']);
	});
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::post('/tokens/create', function (Request $request) {
//     $token = $request->user()->createToken($request->token_name);
 
//     return ['token' => $token->plainTextToken];
// });