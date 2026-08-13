<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'mockup.index')->name('index');
Route::view('/queue', 'mockup.queue')->name('queue');
Route::view('/schedule', 'mockup.schedule')->name('schedule');
Route::view('/prescription/create', 'mockup.prescription-compose')->name('prescription.create');
Route::view('/prescriptions', 'mockup.prescriptions')->name('prescriptions');
Route::view('/medications', 'mockup.medications')->name('medications');
Route::view('/reviews', 'mockup.reviews')->name('reviews');
Route::view('/analytics', 'mockup.analytics')->name('analytics');
