<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\ErfDocument;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class WorkDocumentController extends Controller
{
    public function store(Request $request, Work $work): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,txt|max:10240', // 10MB max
        ]);

        try {
            // Upload file
            $file = $request->file('file');
            $fileName = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('work-documents/' . $work->id, $fileName, 'public');

            // Create document record
            $document = ErfDocument::create([
                'work_id' => $work->id,
                'document_name' => $request->title,
                'file_url' => Storage::url($filePath),
                'uploaded_at' => now(),
                'uploaded_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Document uploaded successfully.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload document. Please try again.');
        }
    }
}
