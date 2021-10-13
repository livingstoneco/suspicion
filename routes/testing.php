<?php

use Livingstoneco\Suspicion\Http\Middleware\IsRequestSuspicious;

Route::post('contact', function () {
    
})->middleware(IsRequestSuspicious::class);