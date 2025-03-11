<?php

use App\Http\Controllers\ProfileController;
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

//redirect to login page
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// route user management
Route::get('/users', function () {
    return Inertia::render('UsersManagement/UserPage');
})->middleware(['auth', 'verified'])->name('users');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route ERF management
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

// Route In Progress Engineering Document
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/morning', function () {
        return Inertia::render('MorningReport/Morning');
    })->name('morning');
    
});

// Route Download Request
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/downloadrequest', function () {
        return Inertia::render('DownloadRequest/DownloadRequest');
    })->name('downloadrequest');
    
});

// route category
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/categories', function () {
        return Inertia::render('CategoriesManagement/Categories');
    })->name('categories');
    Route::get('/categories/create', function () {
        return Inertia::render('CategoriesManagement/CreateCategory');
    })->name('categories.create');
    Route::get('/categories/{category}/edit', function () {
        return Inertia::render('CategoriesManagement/EditCategory');
    })->name('categories.edit');
});

// profile route
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route for role management
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/roles', function () {
        return Inertia::render('RolesManagement/Roles');
    })->name('roles');
    Route::get('/roles/create', function () {
        return Inertia::render('RolesManagement/CreateRole');
    })->name('roles.create');
    Route::get('/roles/{role}/edit', function () {
        return Inertia::render('RolesManagement/EditRole');
    })->name('roles.edit');
});

// Route for Work Audit Trail
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/workaudit', function () {
        return Inertia::render('WorkAuditTrail/WorkAudit');
    })->name('workaudit');
});

// Route login audit trail
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/loginaudit', function () {
        return Inertia::render('LoginAuditLogs/LoginAudit');
    })->name('loginaudit');
});

require __DIR__.'/auth.php';
