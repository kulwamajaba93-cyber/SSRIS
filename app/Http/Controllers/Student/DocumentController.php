<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ResearchStage;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }

    /**
     * Display a listing of the student's documents
     */
    public function index(Request $request)
    {
        $student = auth()->user();
        $documentType = $request->query('type');
        
        // Default to proposal if no type is specified
        if (!$documentType) {
            $documentType = 'proposal';
        }
        
        $query = $student->proposals()->latest();
        
        if ($documentType && in_array($documentType, ['concept_notes', 'proposal', 'data_collection', 'report'])) {
            $query->where('document_type', $documentType);
        }
        
        $documents = $query->get();
        $pendingFeedbackCount = $student->feedbackReceived()->where('status', 'pending')->count();
        
        return view('student.documents.index', compact('documents', 'documentType'))
            ->with('pendingFeedbackCount', $pendingFeedbackCount);
    }

    /**
     * Show the form for creating a new document
     */
    public function create()
    {
        $student = auth()->user();
        $pendingFeedbackCount = $student->feedbackReceived()->where('status', 'pending')->count();
        
        return view('student.documents.create')
            ->with('pendingFeedbackCount', $pendingFeedbackCount);
    }

    /**
     * Store a newly created document
     */
    public function store(Request $request, SmsService $smsService)
    {
        $request->validate([
            'document_type' => 'required|in:concept_notes,proposal,data_collection,report',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $student = auth()->user();
        
        // Get the latest version number for this student and document type
        $latestVersion = $student->proposals()->where('document_type', $request->document_type)->max('version') ?? 0;
        $newVersion = $latestVersion + 1;

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $request->document_type . '_' . $student->id . '_v' . $newVersion . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('proposals', $fileName, 'public');
        }

        $document = Proposal::create([
            'student_id' => $student->id,
            'project_id' => null,
            'title' => $request->title,
            'abstract' => $request->description,
            'file_path' => $filePath,
            'original_filename' => $request->file('file')->getClientOriginalName(),
            'version' => $newVersion,
            'document_type' => $request->document_type,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Send SMS notification to supervisor about document submission
        try {
            $documentTypeName = ucfirst(str_replace('_', ' ', $request->document_type));
            $smsService->sendDocumentSubmissionNotification($student, $document->title, $documentTypeName, $document->id);
        } catch (\Exception $e) {
            // Log error but don't fail the document submission
            \Log::error('SMS notification failed', ['error' => $e->getMessage()]);
        }

        // Update progress for corresponding stage when document is submitted (set to pending)
        $stepNumber = \App\Models\Proposal::getStepNumberForDocumentType($request->document_type);
        if ($stepNumber) {
            $stage = \App\Models\ResearchStage::where('step_number', $stepNumber)->first();
            if ($stage) {
                \App\Models\StudentProgress::updateOrCreate(
                    ['student_id' => $student->id, 'stage_id' => $stage->id],
                    ['status' => 'pending']
                );
            }
        }

        // Log interaction for document submission
        if ($student->supervisor_id) {
            \App\Models\Interaction::create([
                'student_id' => $student->id,
                'supervisor_id' => $student->supervisor_id,
                'proposal_id' => $document->id,
                'action_type' => \App\Models\Interaction::ACTION_SUBMISSION,
                'notes' => $request->description,
                'status' => \App\Models\Interaction::STATUS_IN_REVIEW,
                'document_reference' => ucfirst(str_replace('_', ' ', $request->document_type)) . ' v' . $newVersion,
            ]);
        }

        return redirect()->route('student.documents.index')
            ->with('success', ucfirst(str_replace('_', ' ', $request->document_type)) . ' submitted successfully as version ' . $newVersion);
    }

    /**
     * Display the specified document
     */
    public function show(Proposal $document)
    {
        $student = auth()->user();
        
        // Verify the document belongs to this student
        if ($document->student_id !== $student->id) {
            abort(403, 'This document does not belong to you.');
        }
        
        $pendingFeedbackCount = $student->feedbackReceived()->where('status', 'pending')->count();
        
        return view('student.documents.show', compact('document'))
            ->with('pendingFeedbackCount', $pendingFeedbackCount);
    }

    /**
     * Download the document file
     */
    public function download(Proposal $document)
    {
        $student = auth()->user();
        
        // Verify the document belongs to this student
        if ($document->student_id !== $student->id) {
            abort(403, 'This document does not belong to you.');
        }
        
        if (!$document->file_path) {
            return back()->with('error', 'No file available for download');
        }

        return Storage::disk('public')->download($document->file_path, $document->original_filename);
    }

    /**
     * Remove the specified document
     */
    public function destroy(Proposal $document)
    {
        $student = auth()->user();
        
        // Verify the document belongs to this student
        if ($document->student_id !== $student->id) {
            abort(403, 'This document does not belong to you.');
        }
        
        // Check if document is already reviewed
        if ($document->isReviewed()) {
            return back()->with('error', 'Cannot remove a document that has already been reviewed.');
        }
        
        // Delete associated feedback first
        $document->feedback()->delete();
        
        // Delete the file if it exists
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        // Delete the document
        $document->delete();
        
        return redirect()->route('student.documents.index')
            ->with('success', 'Document removed successfully');
    }
}
