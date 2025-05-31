<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Filament admin panel routes are automatically registered
// You can access it at /admin after logging in
