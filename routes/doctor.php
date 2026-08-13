<?php

use Illuminate\Support\Facades\Route;

Route::middleware('role:doctor')->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
