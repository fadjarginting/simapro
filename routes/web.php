<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Auth/Login', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//         'canResetPassword' => Route::has('password.request'),
//     ]);
// });

//Redirect to Login Page
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route User Management
Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');    
});

// Route ERF Management
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/erfs', function () {
        return Inertia::render('ErfManagement/ErfManage');
    })->name('erfs');
    Route::get('/erfs/create', function () {
        return Inertia::render('ErfManagement/CreateErf');
    })->name('erfs.create');
    Route::get('/erfs/{erf}/edit', function () {
        return Inertia::render('ErfManagement/EditErf');
    })->name('erfs.edit');
});

// Route Morning Report
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/morning', function () {
        return Inertia::render('MorningReport/Morning');
    })->name('morning');
    
});

// Route Progress Report
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/progress', function () {
        return Inertia::render('ProgressReport/Progress');
    })->name('progress');
    Route::get('/progress/create', function () {
        return Inertia::render('ProgressReport/CreateProgress');
    })->name('progress.create');
    Route::get('/progress/{progres}/edit', function () {
        return Inertia::render('Progress/EditProgress');
    })->name('progress.edit');
});

// Profile Route
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route for Role Management
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/roles',[RolesController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RolesController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RolesController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RolesController::class, 'edit'])->name('roles.edit');
    Route::patch('/roles/{role}', [RolesController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RolesController::class, 'destroy'])->name('roles.destroy');
});

// Route for Work Audit Trail
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/workaudit', function () {
        return Inertia::render('WorkAuditTrail/WorkAudit');
    })->name('workaudit');
});

// Route for Login Audit Trail
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/loginaudit', function () {
        return Inertia::render('LoginAuditLogs/LoginAudit');
    })->name('loginaudit');
});

require __DIR__.'/auth.php';
