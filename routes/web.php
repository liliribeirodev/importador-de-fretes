<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

Route::get('/', [UploadController::class, 'mostraFormulario'])->name('upload.formulario');
Route::post('/', [UploadController::class, 'uploadCsvs'])->name('upload.salvar');

