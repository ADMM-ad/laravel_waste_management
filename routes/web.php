<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('laporan.index');
});

Route::get('/laporan', function () {
    return view('laporan.index');
})->name('laporan.index');
