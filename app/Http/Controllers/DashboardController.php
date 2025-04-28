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
        
        // // Total Documents
        // $totalDocuments = Documents::count();
        
        // // Total documents published per month and per years
        // $documentsPerMonth = Documents::selectRaw('DATE_FORMAT(published_at, "%Y-%m") as month, COUNT(*) as count')
        //     ->groupBy('month')
        //     ->orderBy('month', 'desc')
        //     ->get();
       
        // // Distribution of documents across categories
        // $documentsPerCategory = Documents::selectRaw('categories.name as category_name, COUNT(documents.id) as count')
        //     ->join('categories', 'documents.category_id', '=', 'categories.id')
        //     ->groupBy('categories.name')
        //     ->orderBy('count', 'desc')
        //     ->get();
            
        
            
        return Inertia::render('Dashboard', [
            
            'breadcrumbs' => $breadcrumbs,
            'header' => 'Dashboard',
            
        ]);
    }
}