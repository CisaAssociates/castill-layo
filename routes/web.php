<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RFIDController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
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
Route::get('/latest-scan', [RFIDController::class, 'latest'])->name('latest-scan');

Route::get('/', [RFIDController::class, 'index']);
