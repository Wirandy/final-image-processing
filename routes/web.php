<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForensicAnalysisController;
use App\Http\Controllers\AnnotationController;

Route::get('/', function () { return view('home'); })->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/qna', 'qna')->name('qna');

Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
Route::post('/patients', [PatientController::class, 'store'])->middleware('auth')->name('patients.store');
Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
Route::put('/patients/{patient}', [PatientController::class, 'update'])->middleware('auth')->name('patients.update');
Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->middleware('auth')->name('patients.destroy');

Route::post('/patients/{patient}/images', [ImageController::class, 'upload'])->middleware('auth')->name('images.upload');
Route::post('/images/{image}/process', [ImageController::class, 'process'])->middleware('auth')->name('images.process');
Route::post('/images/{image}/forensic-analyze', [ForensicAnalysisController::class, 'analyze'])->middleware('auth')->name('images.forensic.analyze');
Route::delete('/images/{image}', [ImageController::class, 'destroy'])->middleware('auth')->name('images.destroy');

// Report generation route (without annotation feature)
Route::post('/images/{image}/generate-report', [AnnotationController::class, 'generateReport'])->middleware('auth')->name('images.report.generate');

Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.post');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('auth.logout');
