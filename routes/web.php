<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

Route::get('/', [UploadController::class, 'mostraFormulario'])->name('upload.formulario');
Route::post('/', [UploadController::class, 'uploadCsvs'])->name('upload.salvar');
Route::get('/importacoes/{id}/status', [UploadController::class, 'statusImportacao'])->name('importacao.status');
Route::post('/importacoes/{id}/complete', [UploadController::class, 'completeImportacao'])->name('importacao.complete');

