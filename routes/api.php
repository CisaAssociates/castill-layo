<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RFIDController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/rfid', [RFIDController::class, 'store']);
// Route::get('/droidcam/snapshot', function () {
//     $snapshotUrl = 'http://192.168.114.12:4747/shot.jpg'; // your phone DroidCam snapshot
//     try {
//         $image = file_get_contents($snapshotUrl);
//         return response($image)->header('Content-Type', 'image/jpeg');
//     } catch (\Exception $e) {
//         return response('Failed to load snapshot', 500);
//     }
// });
Route::get('/latest-scan', [RFIDController::class, 'latest']);
