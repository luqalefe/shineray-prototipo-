<?php

use App\Livewire\Catalog;
use App\Livewire\MotoDetail;
use Illuminate\Support\Facades\Route;

Route::get('/', Catalog::class)->name('home');
Route::get('/moto/{moto:slug}', MotoDetail::class)->name('moto.show');
