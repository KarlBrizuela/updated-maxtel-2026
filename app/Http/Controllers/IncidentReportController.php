<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\IncidentReport;
use Illuminate\Support\Facades\Auth;

class IncidentReportController extends Controller
{
    /**
     * Display a listing of the incident reports.
     */
    public function index()
    {
        // Get user permission for this page
        $routeName = 'incident_report';
        $userAccess = Auth::user()->access[$routeName] ?? null;
        $userPermission = $userAccess['access'] ?? null;
        
        // Check if user has read-only permission (value 3 = "R" only, without C or U)
        $isReadOnly = $userPermission === '3' || (preg_match("/R/i", $userPermission ?? '') && !preg_match("/C|U/i", $userPermission ?? ''));
        
        $role_id = Auth::user()->role_id;
        
        // Filter employees based on role-based group management
        $query = Employee::leftJoin('lib_position', 'tbl_employee.position_id', '=', 'lib_position.id')
            ->select('tbl_employee.id', 'tbl_employee.emp_code', 'tbl_employee.first_name', 'tbl_employee.last_name', 'lib_position.name as position_name')
            ->where('tbl_employee.is_active', 1);
        
        if ($role_id === 1) {
            // Admin sees all employees
            $employees = $query->get();
        } elseif ($role_id === 4) { // HR Group D
            $employees = $query->where(function ($q) {
                $q->where("tbl_employee.hr_group", "group_d")
                ->orWhere("tbl_employee.user_id", Auth::user()->id);
            })->get();
        } elseif ($role_id === 5) { // HR Group B,C,E
            $employees = $query->where(function ($q) {
                $q->whereIn("tbl_employee.hr_group", ["group_b","group_c","group_e"])
                ->orWhere("tbl_employee.user_id", Auth::user()->id);
            })->get();
        } elseif ($role_id === 14) { // HR Group B,C
            $employees = $query->where(function ($q) {
                $q->whereIn("tbl_employee.hr_group", ["group_b","group_c"])
                ->orWhere("tbl_employee.user_id", Auth::user()->id);
            })->get();
        } elseif ($role_id === 15) { // HR Group C,E
            $employees = $query->where(function ($q) {
                $q->whereIn("tbl_employee.hr_group", ["group_c","group_e"])
                ->orWhere("tbl_employee.user_id", Auth::user()->id);
            })->get();
        } else {
            // For other roles, only show their own record
            $employees = $query->where("tbl_employee.user_id", Auth::user()->id)->get();
        }
        
        // Get current user's employee record
        $currentEmployeeId = null;
        if (Auth::check()) {
            $currentEmployee = Employee::where('user_id', Auth::id())->first();
            $currentEmployeeId = $currentEmployee ? $currentEmployee->id : null;
        }
        
        // Fetch incident reports
        if ($isReadOnly && $currentEmployeeId) {
            // Staff sees only their own reports
            $myIncidentReports = IncidentReport::where('reported_by', $currentEmployeeId)
                ->with(['reportedByEmployee', 'involvedEmployee', 'witnessEmployee'])
                ->orderBy('date_time_report', 'desc')
                ->get();
            $incidentReports = collect();
        } else {
            // Admin/Manager sees reports for employees in their managed groups
            $myIncidentReports = collect();
            
            $reportQuery = IncidentReport::with(['reportedByEmployee', 'involvedEmployee', 'witnessEmployee']);
            
            // Filter by role-based groups
            if ($role_id === 1) {
                // Admin sees all reports
                $incidentReports = $reportQuery->orderBy('date_time_report', 'desc')->get();
            } elseif ($role_id === 4) { // HR Group D
                $incidentReports = $reportQuery->whereHas('reportedByEmployee', function($q) {
                    $q->where(function($subQ) {
                        $subQ->where("hr_group", "group_d")
                        ->orWhere("user_id", Auth::user()->id);
                    });
                })->orderBy('date_time_report', 'desc')->get();
            } elseif ($role_id === 5) { // HR Group B,C,E
                $incidentReports = $reportQuery->whereHas('reportedByEmployee', function($q) {
                    $q->where(function($subQ) {
                        $subQ->whereIn("hr_group", ["group_b","group_c","group_e"])
                        ->orWhere("user_id", Auth::user()->id);
                    });
                })->orderBy('date_time_report', 'desc')->get();
            } elseif ($role_id === 14) { // HR Group B,C
                $incidentReports = $reportQuery->whereHas('reportedByEmployee', function($q) {
                    $q->where(function($subQ) {
                        $subQ->whereIn("hr_group", ["group_b","group_c"])
                        ->orWhere("user_id", Auth::user()->id);
                    });
                })->orderBy('date_time_report', 'desc')->get();
            } elseif ($role_id === 15) { // HR Group C,E
                $incidentReports = $reportQuery->whereHas('reportedByEmployee', function($q) {
                    $q->where(function($subQ) {
                        $subQ->whereIn("hr_group", ["group_c","group_e"])
                        ->orWhere("user_id", Auth::user()->id);
                    });
                })->orderBy('date_time_report', 'desc')->get();
            } else {
                // For other roles, only show reports they created
                $incidentReports = $reportQuery->where('reported_by', $currentEmployeeId)
                    ->orderBy('date_time_report', 'desc')->get();
            }
        }
        
        return view('incident_report.index', compact('employees', 'isReadOnly', 'incidentReports', 'myIncidentReports'));
    }

    /**
     * Show the form for creating a new incident report.
     */
    public function create()
    {
        return view('incident_report.create');
    }

    /**
     * Store a newly created incident report in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reported_by' => 'required|integer',
            'position' => 'required|string|max:255',
            'date_time_report' => 'required|date_format:Y-m-d\TH:i',
            'incident_no' => 'nullable|string|max:255',
            'incident_type' => 'required|string|max:255',
            'date_incident' => 'required|date_format:Y-m-d\TH:i',
            'location' => 'required|string|max:255',
            'incident_description' => 'required|string',
            'name_involved' => 'required|integer',
            'name_witness' => 'nullable|integer',
            'recommended_action' => 'required|string',
        ]);

        // Create the incident report
        IncidentReport::create($validated);

        return redirect()->route('incident-report.index')->with('success', 'Incident report submitted successfully.');
    }

    /**
     * Display the specified incident report.
     */
    public function show($id)
    {
        $report = IncidentReport::with(['reportedByEmployee', 'involvedEmployee', 'witnessEmployee'])
            ->findOrFail($id);
        
        return response()->json([
            'id' => $report->id,
            'incident_no' => $report->incident_no,
            'reported_by_name' => $report->reportedByEmployee ? $report->reportedByEmployee->first_name . ' ' . $report->reportedByEmployee->last_name : 'N/A',
            'position' => $report->position,
            'date_time_report' => $report->date_time_report,
            'incident_type' => $report->incident_type,
            'date_incident' => $report->date_incident,
            'location' => $report->location,
            'incident_description' => $report->incident_description,
            'name_involved_name' => $report->involvedEmployee ? $report->involvedEmployee->first_name . ' ' . $report->involvedEmployee->last_name : 'N/A',
            'name_witness_name' => $report->witnessEmployee ? $report->witnessEmployee->first_name . ' ' . $report->witnessEmployee->last_name : 'N/A',
            'recommended_action' => $report->recommended_action,
        ]);
    }

    /**
     * Show the form for editing the specified incident report.
     */
    public function edit($id)
    {
        // $report = IncidentReport::findOrFail($id);
        // return view('incident_report.edit', compact('report'));
    }

    /**
     * Update the specified incident report in storage.
     */
    public function update(Request $request, $id)
    {
        // Validation and update logic here
    }

    /**
     * Remove the specified incident report from storage.
     */
    public function destroy(Request $request, $id = null)
    {
        $reportId = null;
        try {
            // Handle both URL parameter and POST data
            $reportId = $id ?? $request->input('id');
            
            // Write debug info to a file
            $debugInfo = "=== Delete Incident Report " . date('Y-m-d H:i:s') . " ===\n";
            $debugInfo .= "Report ID: " . print_r($reportId, true) . "\n";
            $debugInfo .= "Request Method: " . $request->method() . "\n";
            $debugInfo .= "Request Data: " . json_encode($request->all()) . "\n";
            file_put_contents(storage_path('logs/delete_debug.log'), $debugInfo, FILE_APPEND);
            
            \Log::info("Attempting to delete incident report with ID: " . $reportId);
            
            if (!$reportId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No report ID provided.'
                ], 400);
            }
            
            $report = IncidentReport::findOrFail($reportId);
            \Log::info("Found incident report: " . $report->id);
            
            $report->delete();
            \Log::info("Successfully deleted incident report: " . $reportId);
            
            file_put_contents(storage_path('logs/delete_debug.log'), "SUCCESS: Report $reportId deleted\n", FILE_APPEND);
            
            return response()->json([
                'success' => true,
                'message' => 'Incident report deleted successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning("Incident report not found: " . ($reportId ?? 'unknown'));
            file_put_contents(storage_path('logs/delete_debug.log'), "NOT FOUND: " . $e->getMessage() . "\n", FILE_APPEND);
            return response()->json([
                'success' => false,
                'message' => 'Incident report not found.'
            ], 404);
        } catch (\Throwable $e) {
            $errorMsg = "ERROR: " . get_class($e) . " - " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
            file_put_contents(storage_path('logs/delete_debug.log'), $errorMsg, FILE_APPEND);
            
            \Log::error("Error deleting incident report: " . $e->getMessage());
            \Log::error("Exception Type: " . get_class($e));
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
