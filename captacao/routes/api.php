<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\iso4217Controller;

Route::post('/iso4217/store', [iso4217Controller::class, "store"]);