<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('payments')->group(function () {
    Route::post('/mtn', [TransactionController::class, 'mtn']);
    Route::post('/airtel', [TransactionController::class, 'airtel']);
    Route::get('/', [TransactionController::class, 'index']); // view all transactions
    Route::get('/ova/{ova_account}', [TransactionController::class, 'showOvaTransactions']);
    Route::get('/wallet/{account_number}', [TransactionController::class, 'showWalletTransactions']);

    // Route::post('/create', [PsoController::class, 'registerpso']);
    // Route::put('/approve', [PsoController::class, 'approve']);
});
