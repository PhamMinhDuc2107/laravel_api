<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\v1\AuthController;

Route::prefix('v1')->group(function () {
   // Auth
   Route::post('/register', [AuthController::class, 'register']);
   Route::post('/login', [AuthController::class, 'login']);
   Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
   Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
   Route::post('/reset-password', [AuthController::class, 'resetPassword']);

   Route::post('/email/verification-notification', function (Request $request) {
      if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email đã được xác minh']);
      }

      $request->user()->sendEmailVerificationNotification();

      return response()->json(['message' => 'Email xác minh đã được gửi']);
   })->middleware(['auth:sanctum'])->name('verification.send');

   Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
      $request->fulfill();

      return response()->json([
         'message' => 'Xác minh email thành công',
         'verified' => true,
         'user' => $request->user()
      ]);
   })->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

   Route::get('/email/verify', function (Request $request) {
      return response()->json([
            'verified' => $request->user()->hasVerifiedEmail(),
      ]);
   })->middleware(['auth:sanctum'])->name('verification.notice');
});

