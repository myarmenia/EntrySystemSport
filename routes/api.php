<?php

use App\Http\Controllers\Api\Turnstile\EntryCodeController;
use App\Http\Controllers\Api\Turnstile\EntryExitSystemController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



// ======================== turnstile Турникет ======================================
Route::group(['prefix' => 'turnstile'], function ($router) {

    // Route::post('entry-code/store', [EntryCodeController::class, 'store']); // karogh e petq ga

    Route::post('ees', EntryExitSystemController::class);  //  Entry/Exit System
    // Route::post('active-qrs', ActiveQrsController::class);
    // Route::post('qr-black-list', QrBlackListController::class);

});
