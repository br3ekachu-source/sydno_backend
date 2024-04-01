<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdvertController;
use App\Http\Controllers\AdvertLegalInformationController;
use App\Http\Controllers\AdvertTechnicalInformationController;
use App\Http\Controllers\ConstController;
use App\Http\Controllers\UserAvatarController;
use App\Http\Controllers\UserController;
use App\Http\Services\Consts;
use App\Models\Advert;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;


Route::post('/register', [RegisteredUserController::class, 'store'])
                ->middleware('guest')
                ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
                ->middleware('guest')
                ->name('login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->middleware('guest')
                ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
                ->middleware('guest')
                ->name('password.store');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['auth', 'signed', 'throttle:6,1'])
                ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware(['auth', 'throttle:6,1'])
                ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');


Route::middleware('auth:sanctum')->group( function () {
    Route::post('useredit', [UserController::class, 'update']);
});

Route::middleware('auth:sanctum'/*, 'verified'*/)->group(function () {
    Route::get('adverts/favorites', [AdvertController::class, 'getFavorites']);
    Route::get('adverts/recentlyviews', [AdvertController::class, 'getRecentlyViews']);
});


Route::get('adverts/{id}', [AdvertController::class, 'show']);

Route::get('adverts/{id}/metadata', [AdvertController::class, 'metadata']);//для вкладки

Route::middleware('auth:sanctum'/*, 'verified'*/)->group(function () {
    Route::get('/user', [UserController::class, 'get']);

    Route::post('adverts', [AdvertController::class, 'store']);
    Route::get('adverts/{id}/edit', [AdvertController::class, 'showForEdit']);
    Route::post('adverts/{id}/edit', [AdvertController::class, 'update']);
    Route::get('adverts/{id}/delete', [AdvertController::class, 'delete']);
    Route::get('adverts/{id}/favorite', [AdvertController::class, 'setInFavorite']);
    Route::get('adverts/{id}/unfavorite', [AdvertController::class, 'unsetInFavorite']);
    Route::get('advertsinfo', [AdvertController::class, 'getInfo']);
    Route::get('myadverts/{state}', [AdvertController::class, 'getMyAdverts']);
    Route::resource('advertslegalinformation', AdvertLegalInformationController::class);
    Route::post('advertslegalinformation/{id}/edit', [AdvertLegalInformationController::class, 'update']);
    Route::resource('advertstechnicalinformation', AdvertTechnicalInformationController::class);
    Route::post('advertstechnicalinformation/{id}/edit', [AdvertTechnicalInformationController::class, 'update']);

    Route::post('/user/avatar', [UserAvatarController::class, 'update']);
    Route::get('/user/avatar', [UserAvatarController::class, 'get']);
    Route::delete('/user/avatar', [UserAvatarController::class, 'delete']);
});

Route::get('/user/{id}', [UserController::class, 'show']);

Route::get('test', [AdvertController::class, 'test']);

Route::get('/useradverts/{id}', [AdvertController::class, 'getUserAdverts']);

Route::get('/otheruseradverts', [AdvertController::class, 'getOtherUserAdverts']);

Route::get('/alladverts', [AdvertController::class, 'getAdverts']);

Route::get('selector', [ConstController::class, 'getSelectors']);

Route::get('selector/vesseltypes', function() {
    return response()->json(['message' => [
        'vessel_types' => Consts::getVesselTypes()
    ]]);
});

Route::get('/files/{folder}/{filename}', function($folder, $filename){
    $path = $folder.'/'.$filename;
    if (Storage::exists($path)){
        return Storage::download($path);
    }
    return response()->json(['message' => 'Файл '.$filename.' не найден'], 404);
});
//http://localhost/storage/avatars/
