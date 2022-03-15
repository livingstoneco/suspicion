<?php

use Livingstoneco\Suspicion\Http\Middleware\IsRepeatOffender;
use Livingstoneco\Suspicion\Http\Middleware\IsRequestSuspicious;

Route::post('contact', function () {
})->middleware(IsRequestSuspicious::class);

Route::get('/', function () {
})->middleware(IsRepeatOffender::class);
