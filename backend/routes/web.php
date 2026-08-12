<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'CarHub API',
        'docs' => '/docs',
    ]);
});

Route::get('/docs', function () {
    return redirect('/docs/index.html');
});
