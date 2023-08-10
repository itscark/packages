<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SatisController;
use App\Http\Middleware\BearerToken;
use Illuminate\Support\Facades\Route;

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

/*
 * These routes are used by the Satis package manager
 */
Route::get('/', SatisController::class . '@index');
Route::get('/dist/{path}', SatisController::class . '@dist')->middleware(BearerToken::class)->where('path', '.*');
Route::get('/packages.json', SatisController::class . '@packagesJson');
Route::get('/{prefix}/{path}', SatisController::class . '@serveFile')->where(['prefix' => 'p2|include', 'path' => '.*']);
