<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Documents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // breadcrumb
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('dashboard')],
        ];

        return Inertia::render('Dashboard', [

            'breadcrumbs' => $breadcrumbs,
            'header' => 'Dashboard',

        ]);
    }
}
