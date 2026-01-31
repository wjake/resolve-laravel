<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\TicketController;
use App\Http\Controllers\Web\CommentController;

// Redirect root to dashboard for authenticated users
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Protected web routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('tickets', TicketController::class)->names([
        'index' => 'web.tickets.index',
        'create' => 'web.tickets.create',
        'store' => 'web.tickets.store',
        'show' => 'web.tickets.show',
        'edit' => 'web.tickets.edit',
        'update' => 'web.tickets.update',
        'destroy' => 'web.tickets.destroy',
    ]);
    Route::post('tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('web.tickets.assign');
    Route::post('tickets/{ticket}/comments', [CommentController::class, 'store'])->name('web.tickets.comments.store');
});

require __DIR__.'/auth.php';
