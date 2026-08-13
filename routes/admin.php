<?php

use Illuminate\Support\Facades\Route;

Route::middleware('role:admin')->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
