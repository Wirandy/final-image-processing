<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AuthController;

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

Route::get('/', function () { return view('home'); })->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/qna', 'qna')->name('qna');

Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
Route::post('/patients', [PatientController::class, 'store'])->middleware('auth')->name('patients.store');
Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');

Route::post('/patients/{patient}/images', [ImageController::class, 'upload'])->middleware('auth')->name('images.upload');
Route::post('/images/{image}/process', [ImageController::class, 'process'])->middleware('auth')->name('images.process');
Route::delete('/images/{image}', [ImageController::class, 'destroy'])->middleware('auth')->name('images.destroy');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.post');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('auth.logout');
