<?php

use App\Http\Controllers\InviteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceMemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('workspace')->name('workspace.')->group(function () {
    Route::get('/create', [WorkspaceController::class, 'create'])->name('create');
    Route::post('/', [WorkspaceController::class, 'store'])->name('store');
    Route::get('/{workspace:slug}/edit', [WorkspaceController::class, 'edit'])->name('edit');
    Route::put('/{workspace:slug}', [WorkspaceController::class, 'update'])->name('update');
    Route::delete('/{workspace:slug}', [WorkspaceController::class, 'destroy'])->name('destroy');
    Route::post('/switch/{workspace:slug}', [WorkspaceController::class, 'switch'])->name('switch');
    Route::get('/', [WorkspaceController::class, 'index'])->name('index');
    Route::get('/{workspace:slug}', [WorkspaceController::class, 'show'])->name('show');

    Route::post('/{workspace:slug}/invites', [InviteController::class, 'store'])->name('invites.store');

    Route::delete('/{workspace:slug}/leave', [WorkspaceMemberController::class, 'leave'])->name('leave');
    Route::post('/{workspace:slug}/transfer-ownership', [WorkspaceMemberController::class, 'transferOwnership'])->name('transfer-ownership');
    Route::delete('/{workspace:slug}/members/{member}', [WorkspaceMemberController::class, 'remove'])->name('members.remove');
});

Route::get('/invite/{token}', [InviteController::class, 'accept'])->name('invites.accept');

require __DIR__.'/auth.php';
