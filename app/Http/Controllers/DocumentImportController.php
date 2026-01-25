<?php

namespace App\Http\Controllers;

use App\Models\DocumentImport;
use App\Models\Module;
use App\Services\DocumentImportService;
use App\Jobs\ProcessDocumentImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentImportController extends Controller
{
    protected $documentImportService;

    public function __construct(DocumentImportService $documentImportService)
    {
        $this->middleware('auth');
        $this->documentImportService = $documentImportService;
    }

    /**
     * Display a listing of document imports
     */
    public function index(Request $request)
    {
        $query = DocumentImport::with('user', 'lesson')
            ->where('user_id', auth()->id());

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $imports = $query->latest()->paginate(20);

        $statistics = $this->documentImportService->getStatistics(auth()->id());

        return view('document-imports.index', compact('imports', 'statistics'));
    }

    /**
     * Show the form for creating a new import
     */
    public function create()
    {
        return view('document-imports.create');
    }

    /**
     * Store a newly uploaded document
     */
    public function store(Request $request)
    {
        $request->validate([
            'document' => [
                'required',
                'file',
                'mimes:doc,docx',
                'max:10240', // Max 10MB
            ],
        ]);

        try {
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            // Generate unique filename
            $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) 
                . '-' . time() 
                . '.' . $extension;
            
            // Store file
            $path = $file->storeAs('document-imports/uploads', $filename, 'public');
            
            // Create import record
            $import = DocumentImport::create([
                'user_id' => auth()->id(),
                'original_filename' => $originalName,
                'file_path' => $path,
                'file_type' => $extension,
                'file_size' => $file->getSize(),
                'status' => 'pending',
            ]);

            // Dispatch job to process the document
            ProcessDocumentImport::dispatch($import);

            return redirect()
                ->route('document-imports.show', $import)
                ->with('success', 'Document uploaded successfully! Processing will start shortly.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to upload document: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified import
     */
    public function show(DocumentImport $documentImport)
    {
        // Authorization check
        if ($documentImport->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized access to this import.');
        }

        $documentImport->load('user', 'lesson.module.course');

        return view('document-imports.show', compact('documentImport'));
    }

    /**
     * Delete the specified import
     */
    public function destroy(DocumentImport $documentImport)
    {
        // Authorization check
        if ($documentImport->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized access to this import.');
        }

        try {
            // Delete the file
            if (Storage::disk('public')->exists($documentImport->file_path)) {
                Storage::disk('public')->delete($documentImport->file_path);
            }

            // Delete extracted images
            if ($documentImport->extracted_images) {
                foreach ($documentImport->extracted_images as $image) {
                    if (isset($image['path']) && Storage::disk('public')->exists($image['path'])) {
                        Storage::disk('public')->delete($image['path']);
                    }
                }
            }

            $documentImport->delete();

            return redirect()
                ->route('document-imports.index')
                ->with('success', 'Document import deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete import: ' . $e->getMessage());
        }
    }

    /**
     * Retry a failed import
     */
    public function retry(DocumentImport $documentImport)
    {
        // Authorization check
        if ($documentImport->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized access to this import.');
        }

        if (!$documentImport->isFailed()) {
            return back()->with('error', 'Only failed imports can be retried.');
        }

        // Reset status and clear error
        $documentImport->update([
            'status' => 'pending',
            'error_message' => null,
            'error_details' => null,
            'started_at' => null,
            'completed_at' => null,
        ]);

        // Dispatch job again
        ProcessDocumentImport::dispatch($documentImport);

        return back()->with('success', 'Import retry initiated.');
    }

    /**
     * Create a lesson from a completed import
     */
    public function createLesson(Request $request, DocumentImport $documentImport)
    {
        // Authorization check
        if ($documentImport->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized access to this import.');
        }

        if (!$documentImport->isCompleted()) {
            return back()->with('error', 'Import must be completed before creating a lesson.');
        }

        if ($documentImport->lesson_id) {
            return back()->with('error', 'A lesson has already been created from this import.');
        }

        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        try {
            $lesson = $this->documentImportService->createLessonFromImport(
                $documentImport,
                $request->module_id,
                $request->only(['title', 'description', 'order', 'duration_minutes'])
            );

            return redirect()
                ->route('lessons.show', $lesson)
                ->with('success', 'Lesson created successfully from imported document!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create lesson: ' . $e->getMessage());
        }
    }

    /**
     * Get import status via AJAX
     */
    public function status(DocumentImport $documentImport)
    {
        // Authorization check
        if ($documentImport->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        return response()->json([
            'status' => $documentImport->status,
            'progress' => $documentImport->processing_time_seconds,
            'word_count' => $documentImport->word_count,
            'image_count' => $documentImport->image_count,
            'error_message' => $documentImport->error_message,
        ]);
    }
}
