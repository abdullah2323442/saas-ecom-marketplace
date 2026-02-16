<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/', function () {
        return view('welcome');
    })->name('central.home');
});
