<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Foundation\Application;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Models\Permission;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard');
});

// route user management
Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])
        ->middleware('permission:user_management.view')
        ->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])
        ->middleware('permission:user_management.create')
        ->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])
        ->middleware('permission:user_management.create')
        ->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
        ->middleware('permission:user_management.edit')
        ->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])
        ->middleware('permission:user_management.edit')
        ->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
        ->middleware('permission:user_management.delete')
        ->name('users.destroy');
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

// Route Key Performance Indicator
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/kpis', function () {
        return Inertia::render('KpiManagement/Kpi');
    })->name('kpis');
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

// Route for role management
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/roles',[RolesController::class, 'index'])
        ->middleware('permission:roles_management.view')
        ->name('roles.index');
    Route::get('/roles/create', [RolesController::class, 'create'])
        ->middleware('permission:roles_management.create')
        ->name('roles.create');
    Route::post('/roles', [RolesController::class, 'store'])
        ->middleware('permission:roles_management.create')
        ->name('roles.store');
    Route::get('/roles/{role}/edit', [RolesController::class, 'edit'])
        ->middleware('permission:roles_management.edit')
        ->name('roles.edit');
    Route::patch('/roles/{role}', [RolesController::class, 'update'])
        ->middleware('permission:roles_management.edit')
        ->name('roles.update');
    Route::delete('/roles/{role}', [RolesController::class, 'destroy'])
        ->middleware('permission:roles_management.delete')
        ->name('roles.destroy');
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
