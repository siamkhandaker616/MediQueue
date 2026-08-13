<?php

use Illuminate\Support\Facades\Route;

Route::middleware('role:patient')->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
