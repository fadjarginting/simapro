<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Contracts\Role;
use Illuminate\Foundation\Application;
use App\Http\Controllers\EatController;
use App\Http\Controllers\ErfController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\MorningController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisciplinesController;
use App\Http\Controllers\EatScheduleController;
use App\Http\Controllers\WorkDocumentController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ActivityProgressController;

// Route::get('/', function () {
//     return Inertia::render('Auth/Login', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//         'canResetPassword' => Route::has('password.request'),
//     ]);
// });

// Redirect to login page
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});


// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');
});

// User Management Routes
Route::middleware(['auth'])->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])
        ->middleware('permission:user_management.view')
        ->name('index');
    Route::get('/create', [UserManagementController::class, 'create'])
        ->middleware('permission:user_management.create')
        ->name('create');
    Route::post('/', [UserManagementController::class, 'store'])
        ->middleware('permission:user_management.create')
        ->name('store');
    Route::get('/{user}/edit', [UserManagementController::class, 'edit'])
        ->middleware('permission:user_management.edit')
        ->name('edit');
    Route::put('/{user}', [UserManagementController::class, 'update'])
        ->middleware('permission:user_management.edit')
        ->name('update');
    Route::delete('/{user}', [UserManagementController::class, 'destroy'])
        ->middleware('permission:user_management.delete')
        ->name('destroy');
    Route::get('/by-discipline/{discipline}', [UserManagementController::class, 'getUsersByDiscipline'])
        ->name('by-discipline');
    Route::get('/for-discipline', [UserManagementController::class, 'getUsersForDiscipline'])
        ->name('for-discipline');
});

// works management
Route::middleware(['auth'])->prefix('works')->name('works.')->group(function () {
    Route::get('/', [WorkController::class, 'index'])
        ->name('index');
    Route::get('/create', [WorkController::class, 'create'])
        ->name('create');
    Route::post('/', [WorkController::class, 'store'])
        ->name('store');
    Route::get('/{work}/edit', [WorkController::class, 'edit'])
        ->name('edit');
    Route::put('/{work}', [WorkController::class, 'update'])
        ->name('update');
    Route::put('/{work}/update-basic-info', [WorkController::class, 'updateBasicInfo'])
        ->name('update-basic-info');
    Route::put('/{work}/update-status', [WorkController::class, 'updateStatus'])
        ->name('update-status');
    Route::delete('/{work}', [WorkController::class, 'destroy'])
        ->name('destroy');
    Route::get('/{work}/show', [WorkController::class, 'detail'])
        ->name('show');
    Route::put('/{work}/update-timeline', [WorkController::class, 'updateTimeline'])
        ->name('update-timeline');
});

Route::prefix('works/{work}/documents')->name('works.documents.')->group(function () {
    Route::get('/', [WorkDocumentController::class, 'index'])->name('index');
    Route::post('/', [WorkDocumentController::class, 'store'])->name('store');
    Route::get('/{document}', [WorkDocumentController::class, 'show'])->name('show');
    Route::get('/{document}/download', [WorkDocumentController::class, 'download'])->name('download');
    Route::delete('/{document}', [WorkDocumentController::class, 'destroy'])->name('destroy'); 
    // API endpoint untuk mendapatkan documents sebagai JSON
    Route::get('/api/list', [WorkDocumentController::class, 'getDocuments'])->name('api.list');
});

// EAT Management
Route::middleware(['auth'])->prefix('eat')->name('eat.')->group(function () {
    Route::post('/', [EatController::class, 'store'])
        ->name('store');
    Route::put('/{eat}', [EatController::class, 'update'])
        ->name('update');
    Route::post('/{eat}/approve', [EatController::class, 'approve'])
        ->name('approve');
    Route::get('/by-work/{workId}', [EatController::class, 'getEATByWork'])
        ->name('by-work');
    Route::get('/{eat}/export-pdf', [EatController::class, 'exportPdf'])
        ->name('export-pdf');
    Route::get('/disciplines', [EatController::class, 'getDisciplines'])
        ->name('disciplines');
    Route::get('/users', [EatController::class, 'getUsers'])
        ->name('users');
});

Route::middleware(['auth'])->prefix('activities')->name('activities.')->group(function () {
    Route::post('/{activity}/progress', [ActivityProgressController::class, 'addProgress'])
        ->name('add-progress');
});

// Search users API
Route::get('/api/users/search', [UserManagementController::class, 'search'])->name('users.search');

// disciplines management
Route::middleware(['auth'])->prefix('disciplines')->name('disciplines.')->group(function () {
    Route::get('/', [DisciplinesController::class, 'index'])
        ->name('index');
    Route::post('/', [DisciplinesController::class, 'store'])
        ->name('store');
    Route::put('/{discipline}', [DisciplinesController::class, 'update'])
        ->name('update');
    Route::delete('/{discipline}', [DisciplinesController::class, 'destroy'])
        ->name('destroy');
    Route::get('/api/all', [DisciplinesController::class, 'getAllDisciplines'])
        ->name('api.all');
});



// Route ERF Management
Route::middleware(['auth'])->group(function () {
    Route::get('/erfs', [ErfController::class, 'index'])
        ->middleware('permission:erf_management.view')
        ->name('erfs.index');
    Route::get('/erfs/create', [ErfController::class, 'create'])
        ->middleware('permission:erf_management.create')
        ->name('erfs.create');
    Route::post('/erfs', [ErfController::class, 'store'])
        ->middleware('permission:erf_management.create')
        ->name('erfs.store');
    Route::get('/erfs/{erf}/edit', [ErfController::class, 'edit'])
        ->middleware('permission:erf_management.edit')
        ->name('erfs.edit');
    Route::put('/erfs/{erf}', [ErfController::class, 'update'])
        ->middleware('permission:erf_management.edit')
        ->name('erfs.update');
    Route::delete('/erfs/{erf}', [ErfController::class, 'destroy'])
        ->middleware('permission:erf_management.delete')
        ->name('erfs.destroy');
});

// Route Morning Report
Route::middleware(['auth'])->group(function () {
    Route::get('/morning', [MorningController::class, 'index'])
        ->middleware('permission:morning_report.view')
        ->name('morning.index');
    Route::get('/morning/create', [MorningController::class, 'create'])
        ->middleware('permission:morning_report.create')
        ->name('morning.create');
    Route::post('/morning', [MorningController::class, 'store'])
        ->middleware('permission:morning_report.create')
        ->name('morning.store');
    Route::get('/morning/{morning}/edit', [MorningController::class, 'edit'])
        ->middleware('permission:morning_report.edit')
        ->name('morning.edit');
    Route::put('/morning/{morning}', [MorningController::class, 'update'])
        ->middleware('permission:morning_report.edit')
        ->name('morning.update');
    Route::delete('/morning/{morning}', [MorningController::class, 'destroy'])
        ->middleware('permission:morning_report.delete')
        ->name('morning.destroy');
});

// Route Key Performance Indicator
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/kpis', function () {
        return Inertia::render('KpiManagement/Kpi');
    })->name('kpis');
});


// Route Progress Report
// Route::middleware(['auth'])->group(function () {
//     Route::get('/progress', [ProgressController::class, 'index'])
//         ->middleware('permission:progress_report.view')
//         ->name('progress.index');
//     Route::get('/progress/create', [ProgressController::class, 'create'])
//         ->middleware('permission:progress_report.create')
//         ->name('progress.create');
//     Route::post('/progress', [ProgressController::class, 'store'])
//         ->middleware('permission:progress_report.create')
//         ->name('progress.store');
//     Route::get('/progress/{progress}/show', [ProgressController::class, 'show'])
//         ->middleware('permission:progress_report.show')
//         ->name('progress.show');
//     Route::get('/progress/{progress}/edit', [ProgressController::class, 'edit'])
//         ->middleware('permission:progress_report.edit')
//         ->name('progress.edit');
//     Route::put('/progress/{progress}', [ProgressController::class, 'update'])
//         ->middleware('permission:progress_report.edit')
//         ->name('progress.update');
//     Route::delete('/progress/{progress}', [ProgressController::class, 'destroy'])
//         ->middleware('permission:progress_report.delete')
//         ->name('progress.destroy');
// });

// Resource routes for CRUD operations
Route::middleware(['auth'])->group(function () {
    Route::resource('eat-schedules', EatScheduleController::class);
    Route::post('eat-schedules/{eatSchedule}/complete', [EatScheduleController::class, 'markAsCompleted'])->name('eat-schedules.complete');
});


// Plants Routes
Route::middleware(['auth', 'verified'])->prefix('plants')->name('plants.')->group(function () {
    Route::get('/', [PlantController::class, 'index'])
        ->name('index');
    Route::post('/', [PlantController::class, 'store'])
        ->name('store');
    Route::put('/{plant}', [PlantController::class, 'update'])
        ->name('update');
    Route::delete('/{plant}', [PlantController::class, 'destroy'])
        ->name('destroy');
});

// Notes Routes
Route::middleware(['auth', 'verified'])->prefix('notes')->name('notes.')->group(function () {
    Route::get('/', [NoteController::class, 'index'])
        ->name('index');
    Route::post('/', [NoteController::class, 'store'])
        ->name('store');
    Route::put('/{note}', [NoteController::class, 'update'])
        ->name('update');
    Route::delete('/{note}', [NoteController::class, 'destroy'])
        ->name('destroy');
});

// Work Priority Routes
Route::middleware(['auth', 'verified'])->prefix('priorities')->name('priorities.')->group(function () {
    Route::get('/', [PriorityController::class, 'index'])
        ->name('index');
    Route::post('/', [PriorityController::class, 'store'])
        ->name('store');
    Route::put('/{priority}', [PriorityController::class, 'update'])
        ->name('update');
    Route::delete('/{priority}', [PriorityController::class, 'destroy'])
        ->name('destroy');
});

// Work Category Routes
Route::middleware(['auth', 'verified'])->prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])
        ->name('index');
    Route::post('/', [CategoryController::class, 'store'])
        ->name('store');
    Route::put('/{category}', [CategoryController::class, 'update'])
        ->name('update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])
        ->name('destroy');
});

// Profile Route
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Roles Routes
Route::middleware(['auth', 'verified'])->prefix('roles')->name('roles.')->group(function () {
    Route::get('/', [RolesController::class, 'index'])
        ->middleware('permission:role_management.view')
        ->name('index');
    Route::get('/create', [RolesController::class, 'create'])
        ->middleware('permission:role_management.create')
        ->name('create');
    Route::post('/', [RolesController::class, 'store'])
        ->middleware('permission:role_management.create')
        ->name('store');
    Route::get('/{role}/edit', [RolesController::class, 'edit'])
        ->middleware('permission:role_management.edit')
        ->name('edit');
    Route::patch('/{role}', [RolesController::class, 'update'])
        ->middleware('permission:role_management.edit')
        ->name('update');
    Route::delete('/{role}', [RolesController::class, 'destroy'])
        ->middleware('permission:role_management.delete')
        ->name('destroy');
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

require __DIR__ . '/auth.php';
