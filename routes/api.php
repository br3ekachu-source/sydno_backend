<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdvertController;
use App\Http\Controllers\AdvertLegalInformationController;
use App\Http\Controllers\AdvertTechnicalInformationController;
use App\Http\Controllers\ConstController;
use App\Http\Controllers\UserAvatarController;
use App\Http\Controllers\UserController;
use App\Http\Services\Consts;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Models\Advert;
use Illuminate\Support\Facades\Request;

Route::middleware('guest')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['auth', 'signed', 'throttle:6,1'])
                ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware(['auth', 'throttle:6,1'])
                ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');

Route::get('adverts/recentlyviews', [AdvertController::class, 'getRecentlyViews']);
Route::get('adverts/{id}/metadata', [AdvertController::class, 'metadata']);//для вкладки
Route::get('/useradverts/{id}', [AdvertController::class, 'getUserAdverts']);
Route::get('/otheruseradverts', [AdvertController::class, 'getOtherUserAdverts']);
Route::get('/alladverts', [AdvertController::class, 'getAdverts']);

Route::middleware('auth:sanctum'/*, 'verified'*/)->group(function () {
    Route::get('/user', [UserController::class, 'get']);
    Route::put('user/profile-information', [UserController::class, 'update']);
    Route::put('user/password', [UserController::class, 'updatePassword']);

    Route::post('adverts', [AdvertController::class, 'store']);
    Route::get('adverts/favorites', [AdvertController::class, 'getFavorites']);
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

Route::get('adverts/{id}', [AdvertController::class, 'show']);

Route::get('/user/{id}', [UserController::class, 'show']);
Route::get('selector', [ConstController::class, 'getSelectors']);
Route::get('selector/vesseltypes', function() {
    return response()->json(['message' => [
        'vessel_types' => Consts::getVesselTypes()
    ]]);
});

Route::get('/getavatar', function($folder, $filename){
    $path = $folder.'/'.$filename;
    if (Storage::exists($path)){
        return Storage::download($path);
    }
    return response()->json(['message' => 'Файл '.$filename.' не найден'], 404);
});

Route::get('/files/{folder}/{filename}', function($folder, $filename){
    $path = $folder.'/'.$filename;
    if (Storage::exists($path)){
        return Storage::download($path);
    }
    return response()->json(['message' => 'Файл '.$filename.' не найден'], 404);
});

