<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Cms;
use App\Http\Livewire\Images;
use App\Http\Livewire\Labels;
use App\Http\Livewire\Projects;
use App\Http\Livewire\Scripts;
use App\Http\Livewire\Firmwares;
use App\Http\Livewire\Settings;
use App\Http\Controllers\AddImageController;
use App\Http\Controllers\ScriptExecuteController;
use App\Http\Controllers\SitePasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (empty(config('app.password'))) {
        return redirect('/dashboard');
    }
    return redirect()->route('login');
});

Route::get('/login', [SitePasswordController::class, 'showForm'])->name('login');
Route::post('/login', [SitePasswordController::class, 'login']);
Route::post('/logout', [SitePasswordController::class, 'logout'])->name('logout');

Route::middleware(['site.password'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard', ['log' => \App\Models\Cmlog::orderBy('id','desc')->limit(100)->get()] );
    })->name('dashboard');

    Route::any('/cms', Cms::class)->name('cms');
    Route::any('/images', Images::class)->name('images');
    Route::post('/addImage', [AddImageController::class, 'store']);
    Route::any('/projects', Projects::class)->name('projects');
    Route::any('/scripts', Scripts::class)->name('scripts');
    Route::any('/labels', Labels::class)->name('labels');
    Route::any('/firmware', Firmwares::class)->name('firmware');
    Route::any('/settings', Settings::class)->name('settings');
});

Route::any('/scriptexecute', ScriptExecuteController::class);
