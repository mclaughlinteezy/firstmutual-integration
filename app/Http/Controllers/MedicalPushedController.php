<?php

namespace App\Http\Controllers;

use App\Models\MedicalPushed;
use App\Services\FirstMutualService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MedicalPushedController extends Controller
{
    private $firstMutualService;

    public function __construct(FirstMutualService $firstMutualService)
    {
        $this->firstMutualService = $firstMutualService;
    }

    /**
     * Process student and fetch details from First Mutual
     */
    public function processStudent(Request $request)
    {
        $request->validate([
            'reg_number' => 'required|string|max:20'
        ]);

        DB::beginTransaction();

        try {
            $regNumber = $request->reg_number;

            // Check if record already exists
            $existingRecord = MedicalPushed::where('reg_number', $regNumber)->first();
            if ($existingRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student with this registration number already exists',
                    'data' => $existingRecord
                ], 409);
            }

            // Create initial record
            $medicalRecord = MedicalPushed::create([
                'reg_number' => $regNumber,
                'status' => 'processing'
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

                return response()->json([
                    'success' => true,
                    'message' => 'Student details fetched and stored successfully',
                    'data' => $medicalRecord->fresh()
                ]);

            } else {
                // Store failed response
                $medicalRecord->update([
                    'status' => 'failed',
                    'error_message' => $studentResponse['error'] ?? 'Unknown error',
                    'api_response' => $studentResponse
                ]);

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch student details: ' . ($studentResponse['error'] ?? 'Unknown error'),
                    'data' => $medicalRecord->fresh()
                ], 422);
            }

        } catch (\Exception $e) {
            DB::rollback();

            // Update record with error if it exists
            if (isset($medicalRecord)) {
                $medicalRecord->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            Log::error('Medical Processing Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Processing failed: ' . $e->getMessage(),
                'data' => $medicalRecord ?? null
            ], 500);
        }
    }

    /**
     * Get all processed records
     */
    public function index()
    {
        $records = MedicalPushed::orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $records
        ]);
    }

    /**
     * Get specific record
     */
    public function show($id)
    {
        $record = MedicalPushed::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $record
        ]);
    }

    /**
     * Retry failed processing
     */
    public function retry($id)
    {
        $record = MedicalPushed::findOrFail($id);

        if ($record->status !== 'failed') {
            return response()->json([
                'success' => false,
                'message' => 'Only failed records can be retried'
            ], 400);
        }

        // Create new request object
        $request = new Request([
            'reg_number' => $record->reg_number
        ]);

        // Reset the record before retrying
        $record->update([
            'status' => 'processing',
            'error_message' => null
        ]);

        return $this->processStudent($request);
    }
}
