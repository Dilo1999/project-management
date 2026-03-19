<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.attempt');
    Route::get('/register/pending', fn () => view('register-pending'))->name('register.pending');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [\App\Http\Controllers\ProjectController::class, 'index'])->name('home');
    Route::get('/projects/my-projects', [\App\Http\Controllers\ProjectController::class, 'myProjects'])->name('projects.my');
    Route::get('/projects/add', [\App\Http\Controllers\ProjectController::class, 'create'])->name('projects.add');
    Route::post('/projects', [\App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [\App\Http\Controllers\ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::patch('/projects/{project}/status', [\App\Http\Controllers\ProjectController::class, 'updateStatus'])->name('projects.updateStatus');
    Route::get('/book-ticket', [\App\Http\Controllers\TicketController::class, 'create'])->name('book-ticket');
    Route::post('/book-ticket', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
    Route::get('/my-messages', [\App\Http\Controllers\TicketController::class, 'myMessages'])->name('my-messages');
    Route::post('/tickets/{ticket}/accept', [\App\Http\Controllers\TicketController::class, 'accept'])->name('tickets.accept');
    Route::post('/tickets/{ticket}/reject', [\App\Http\Controllers\TicketController::class, 'reject'])->name('tickets.reject');
    Route::post('/tickets/{ticket}/done', [\App\Http\Controllers\TicketController::class, 'markDone'])->name('tickets.markDone');
    Route::middleware('super_admin')->group(function () {
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/approve', [\App\Http\Controllers\UserController::class, 'approve'])->name('users.approve');
        Route::get('/reports/user-work', [\App\Http\Controllers\ReportController::class, 'userWork'])->name('reports.user-work');
        Route::get('/reports/user-work/{user}', [\App\Http\Controllers\ReportController::class, 'userWorkShow'])->name('reports.user-work.show');
    });
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages', [\App\Http\Controllers\ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat', [\App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
