<?php

namespace App\Http\Controllers;

use App\Models\MedicalPushed;
use App\Services\FirstMutualService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebMedicalController extends Controller
{
    private $firstMutualService;

    public function __construct(FirstMutualService $firstMutualService)
    {
        $this->firstMutualService = $firstMutualService;
    }

    /**
     * Display dashboard with all records
     */
    public function index(Request $request)
    {
        $query = MedicalPushed::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reg_number', 'LIKE', "%{$search}%")
                  ->orWhere('firstnames', 'LIKE', "%{$search}%")
                  ->orWhere('surname', 'LIKE', "%{$search}%")
                  ->orWhere('nationalid', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $stats = [
            'total' => MedicalPushed::count(),
            'success' => MedicalPushed::where('status', 'success')->count(),
            'failed' => MedicalPushed::where('status', 'failed')->count(),
            'pending' => MedicalPushed::whereIn('status', ['pending', 'processing'])->count(),
            'manual' => MedicalPushed::where('source', 'manual')->count(),
            'api' => MedicalPushed::where('source', 'api')->count(),
        ];

        return view('medical.index', compact('records', 'stats'));
    }

    /**
     * Show form to add new student
     */
    public function create()
    {
        return view('medical.create');
    }

    /**
     * Show manual form to add student details
     */
    public function createManual(Request $request)
    {
        $regNumber = $request->get('reg_number');
        $apiError = $request->get('api_error');

        return view('medical.create-manual', compact('regNumber', 'apiError'));
    }

    /**
     * Process new student submission (API lookup)
     */
    public function store(Request $request)
    {
        $request->validate([
            'reg_number' => 'required|string|max:20'
        ]);

        DB::beginTransaction();

        try {
            $regNumber = $request->reg_number;

            // Check if record already exists
            $existing = MedicalPushed::where('reg_number', $regNumber)->first();
            if ($existing) {
                return redirect()->back()
                    ->withErrors(['reg_number' => 'Student with this registration number already exists.'])
                    ->withInput();
            }

            // Create initial record
            $medicalRecord = MedicalPushed::create([
                'reg_number' => $regNumber,
                'status' => 'processing',
                'source' => 'api'
            ]);

            // Fetch student details from API
            $studentResponse = $this->firstMutualService->getStudentDetails($regNumber);

            if ($studentResponse['success']) {
                // Extract student information
                $studentInfo = $studentResponse['data']['studentInformation'] ?? [];

                // Update record with student info
                $medicalRecord->update([
                    'firstnames' => $studentInfo['firstnames'] ?? null,
                    'surname' => $studentInfo['surname'] ?? null,
                    'dob' => isset($studentInfo['dob']) ? $studentInfo['dob'] : null,
                    'mobile' => $studentInfo['mobile'] ?? null,
                    'nationalid' => $studentInfo['nationalid'] ?? null,
                    'gender' => $studentInfo['gender'] ?? null,
                    'address' => $studentInfo['address'] ?? null,
                    'api_response' => $studentResponse,
                    'status' => 'success'
                ]);

                DB::commit();

                return redirect()->route('medical.show', $medicalRecord->id)
                    ->with('success', 'Student details fetched and stored successfully!');

            } else {
                // Store failed response but don't commit yet
                $medicalRecord->update([
                    'status' => 'failed',
                    'error_message' => $studentResponse['error'] ?? 'Unknown error',
                    'api_response' => $studentResponse
                ]);

                DB::commit();

                // Redirect to manual form with error details
                return redirect()->route('medical.create-manual')
                    ->with('error', 'Student not found in API. Please add manually.')
                    ->with('reg_number', $regNumber)
                    ->with('api_error', $studentResponse['error'] ?? 'Student not found');
            }

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Medical Processing Error: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Processing failed: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Store manually added student
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'reg_number' => 'required|string|max:20',
            'firstnames' => 'required|string|max:100',
            'surname' => 'required|string|max:100',
            'dob' => 'nullable|date|before:today',
            'mobile' => 'nullable|string|max:20',
            'nationalid' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            $regNumber = $request->reg_number;

            // Check if record already exists
            $existing = MedicalPushed::where('reg_number', $regNumber)->first();
            if ($existing) {
                return redirect()->back()
                    ->withErrors(['reg_number' => 'Student with this registration number already exists.'])
                    ->withInput();
            }

            // Create manual record
            $medicalRecord = MedicalPushed::create([
                'reg_number' => $regNumber,
                'firstnames' => $request->firstnames,
                'surname' => $request->surname,
                'dob' => $request->dob,
                'mobile' => $request->mobile,
                'nationalid' => $request->nationalid,
                'gender' => $request->gender,
                'address' => $request->address,
                'status' => 'success',
                'source' => 'manual',
                'api_response' => [
                    'success' => true,
                    'message' => 'Student added manually',
                    'added_at' => now()->toISOString()
                ]
            ]);

            DB::commit();

            return redirect()->route('medical.show', $medicalRecord->id)
                ->with('success', 'Student added manually successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Manual Student Addition Error: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Failed to add student: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show specific record details
     */
    public function show($id)
    {
        $record = MedicalPushed::findOrFail($id);
        return view('medical.show', compact('record'));
    }

    /**
     * Show edit form for manual records
     */
    public function edit($id)
    {
        $record = MedicalPushed::findOrFail($id);

        // Only allow editing of manual records
        if (!$record->isManual()) {
            return redirect()->back()
                ->withErrors(['error' => 'Only manually added records can be edited.']);
        }

        return view('medical.edit', compact('record'));
    }

    /**
     * Update manual record
     */
    public function update(Request $request, $id)
    {
        $record = MedicalPushed::findOrFail($id);

        // Only allow editing of manual records
        if (!$record->isManual()) {
            return redirect()->back()
                ->withErrors(['error' => 'Only manually added records can be edited.']);
        }

        $request->validate([
            'firstnames' => 'required|string|max:100',
            'surname' => 'required|string|max:100',
            'dob' => 'nullable|date|before:today',
            'mobile' => 'nullable|string|max:20',
            'nationalid' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500'
        ]);

        try {
            $record->update([
                'firstnames' => $request->firstnames,
                'surname' => $request->surname,
                'dob' => $request->dob,
                'mobile' => $request->mobile,
                'nationalid' => $request->nationalid,
                'gender' => $request->gender,
                'address' => $request->address,
                'api_response' => array_merge($record->api_response ?? [], [
                    'updated_at' => now()->toISOString(),
                    'updated_manually' => true
                ])
            ]);

            return redirect()->route('medical.show', $record->id)
                ->with('success', 'Student details updated successfully!');

        } catch (\Exception $e) {
            Log::error('Manual Student Update Error: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update student: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Retry failed processing
     */
    public function retry($id)
    {
        $record = MedicalPushed::findOrFail($id);

        if ($record->status !== 'failed' || $record->isManual()) {
            return redirect()->back()->withErrors(['error' => 'Only failed API records can be retried']);
        }

        // Reset record status
        $record->update(['status' => 'processing', 'error_message' => null]);

        try {
            // Retry fetching student details
            $studentResponse = $this->firstMutualService->getStudentDetails($record->reg_number);

            if ($studentResponse['success']) {
                // Extract student information
                $studentInfo = $studentResponse['data']['studentInformation'] ?? [];

                $record->update([
                    'firstnames' => $studentInfo['firstnames'] ?? null,
                    'surname' => $studentInfo['surname'] ?? null,
                    'dob' => isset($studentInfo['dob']) ? $studentInfo['dob'] : null,
                    'mobile' => $studentInfo['mobile'] ?? null,
                    'nationalid' => $studentInfo['nationalid'] ?? null,
                    'gender' => $studentInfo['gender'] ?? null,
                    'address' => $studentInfo['address'] ?? null,
                    'api_response' => $studentResponse,
                    'status' => 'success',
                    'error_message' => null
                ]);

                return redirect()->back()->with('success', 'Student details fetched successfully after retry!');

            } else {
                throw new \Exception($studentResponse['error'] ?? 'Unknown error occurred');
            }

        } catch (\Exception $e) {
            $record->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return redirect()->back()->withErrors(['error' => 'Retry failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete record
     */
    public function destroy($id)
    {
        $record = MedicalPushed::findOrFail($id);
        $record->delete();

        return redirect()->route('medical.index')->with('success', 'Record deleted successfully!');
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,retry',
            'selected_records' => 'required|array|min:1',
            'selected_records.*' => 'exists:medicalpushed,id'
        ]);

        $records = MedicalPushed::whereIn('id', $request->selected_records)->get();
        $successCount = 0;
        $errorCount = 0;

        foreach ($records as $record) {
            try {
                if ($request->action === 'delete') {
                    $record->delete();
                    $successCount++;
                } elseif ($request->action === 'retry' && $record->status === 'failed' && $record->isFromApi()) {
                    $this->retry($record->id);
                    $successCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Bulk action error for record {$record->id}: " . $e->getMessage());
            }
        }

        $message = "Bulk action completed. Success: {$successCount}";
        if ($errorCount > 0) {
            $message .= ", Errors: {$errorCount}";
        }

        return redirect()->route('medical.index')->with('success', $message);
    }
}
