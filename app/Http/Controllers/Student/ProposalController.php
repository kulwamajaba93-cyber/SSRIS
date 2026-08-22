<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ResearchStage;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProposalController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }

    /**
     * Display a listing of the student's proposals
     */
    public function index(Request $request)
    {
        $student = auth()->user();
        $documentType = $request->query('type');
        
        $query = $student->proposals()->latest();
        
        if ($documentType && in_array($documentType, ['proposal', 'data_collection', 'report'])) {
            $query->where('document_type', $documentType);
        }
        
        $proposals = $query->get();
        $pendingFeedbackCount = $student->feedbackReceived()->where('status', 'pending')->count();
        
        return view('student.proposals.index', compact('proposals', 'documentType'))
            ->with('pendingFeedbackCount', $pendingFeedbackCount);
    }

    /**
     * Show the form for creating a new proposal
     */
    public function create()
    {
        $student = auth()->user();
        $pendingFeedbackCount = $student->feedbackReceived()->where('status', 'pending')->count();
        
        return view('student.proposals.create')
            ->with('pendingFeedbackCount', $pendingFeedbackCount);
    }

    /**
     * Store a newly created proposal
     */
    public function store(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:proposal,data_collection,report',
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

        $proposal = Proposal::create([
            'student_id' => $student->id,
            'title' => $request->title,
            'abstract' => $request->description,
            'file_path' => $filePath,
            'original_filename' => $request->file('file')->getClientOriginalName(),
            'version' => $newVersion,
            'document_type' => $request->document_type,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Automatically update research stage to "proposal_submitted" only for proposal documents
        if ($request->document_type === 'proposal') {
            $researchStage = $student->researchStage ?? $student->researchStage()->create([
                'stage' => 'initial stage',
                'notes' => 'Research stage initialized'
            ]);

            $researchStage->update([
                'stage' => 'proposal_submitted',
                'notes' => 'Proposal submitted automatically - version ' . $newVersion,
            ]);
        }

        // Send SMS notification to supervisor
        try {
            $smsService = new SmsService();
            $documentTypeName = ucfirst(str_replace('_', ' ', $request->document_type));
            $smsService->sendDocumentSubmissionNotification($student, $documentTypeName, $proposal->id);
        } catch (\Exception $e) {
            // Log error but don't fail the submission
            \Log::error('SMS notification failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('student.proposals.index')
            ->with('success', ucfirst(str_replace('_', ' ', $request->document_type)) . ' submitted successfully as version ' . $newVersion);
    }

    /**
     * Display the specified proposal
     */
    public function show(Proposal $proposal)
    {
        $student = auth()->user();
        
        // Verify the proposal belongs to this student
        if ($proposal->student_id !== $student->id) {
            abort(403, 'This proposal does not belong to you.');
        }
        
        $pendingFeedbackCount = $student->feedbackReceived()->where('status', 'pending')->count();
        
        return view('student.proposals.show', compact('proposal'))
            ->with('pendingFeedbackCount', $pendingFeedbackCount);
    }

    /**
     * Download the proposal file
     */
    public function download(Proposal $proposal)
    {
        $student = auth()->user();
        
        // Verify the proposal belongs to this student
        if ($proposal->student_id !== $student->id) {
            abort(403, 'This proposal does not belong to you.');
        }
        
        if (!$proposal->file_path) {
            return back()->with('error', 'No file available for download');
        }

        return Storage::disk('public')->download($proposal->file_path, $proposal->original_filename);
    }

    /**
     * Remove the specified proposal
     */
    public function destroy(Proposal $proposal)
    {
        $student = auth()->user();
        
        // Verify the proposal belongs to this student
        if ($proposal->student_id !== $student->id) {
            abort(403, 'This proposal does not belong to you.');
        }
        
        // Check if proposal is already reviewed
        if ($proposal->isReviewed()) {
            return back()->with('error', 'Cannot remove a proposal that has already been reviewed.');
        }
        
        // Delete associated feedback first
        $proposal->feedback()->delete();
        
        // Delete the file if it exists
        if ($proposal->file_path && Storage::disk('public')->exists($proposal->file_path)) {
            Storage::disk('public')->delete($proposal->file_path);
        }
        
        // Delete the proposal
        $proposal->delete();
        
        return redirect()->route('student.proposals.index')
            ->with('success', 'Proposal removed successfully');
    }
}
