@extends('layouts.front-app')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="row">
            <div class="col-xl-12 col-sm-12 col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h4>Incident Report Form</h4>
                </div>
                <div class="card-body">
                    @if(!$isReadOnly)
                    <form action="{{ route('incident-report.store') }}" method="POST">
                        @csrf
                        
                        <!-- Row 1: Reported By and Position -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reported_by">Reported By <span class="text-danger">*</span></label>
                                    <select 
                                        id="reported_by" 
                                        name="reported_by" 
                                        class="form-control form-select"
                                        required
                                    >
                                        <option value="">-- Select Employee --</option>
                                        @forelse($employees ?? [] as $emp)
                                            <option value="{{ $emp->id }}" data-position="{{ $emp->position_name }}" @if($employee && $employee->id == $emp->id) selected @endif>
                                                {{ $emp->emp_code }} - {{ $emp->first_name }} {{ $emp->last_name }}
                                            </option>
                                        @empty
                                            <option value="">No employees available</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position">Position <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        id="position" 
                                        name="position" 
                                        class="form-control"
                                        value="@if($employee && isset($employee->position_name)){{ $employee->position_name }}@endif"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                        
                        <!-- Row 2: Date and Time of Report and Incident No -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_time_report">Date and Time of Report <span class="text-danger">*</span></label>
                                    <input 
                                        type="datetime-local" 
                                        id="date_time_report" 
                                        name="date_time_report" 
                                        class="form-control"
                                        required
                                    >
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="incident_no">Incident No. <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        id="incident_no" 
                                        name="incident_no" 
                                        class="form-control"
                                        placeholder="Auto-generated"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        
                        <!-- Row 3: Incident Type and Date of Incident -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="incident_type">Incident Type <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        id="incident_type" 
                                        name="incident_type" 
                                        class="form-control"
                                        placeholder="Enter incident type"
                                        required
                                    >
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_incident">Date of Incident <span class="text-danger">*</span></label>
                                    <input 
                                        type="datetime-local" 
                                        id="date_incident" 
                                        name="date_incident" 
                                        class="form-control"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                        
                        <!-- Row 4: Location -->
                        <div class="form-group">
                            <label for="location">Location <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                id="location" 
                                name="location" 
                                class="form-control"
                                placeholder="Enter location of incident"
                                required
                            >
                        </div>
                        
                        <!-- Row 5: Incident Description -->
                        <div class="form-group">
                            <label for="incident_description">Incident Description <span class="text-danger">*</span></label>
                            <textarea 
                                id="incident_description" 
                                name="incident_description" 
                                rows="3"
                                class="form-control"
                                placeholder="Describe the incident in detail..."
                                required
                            ></textarea>
                        </div>
                        
                        <!-- Row 6: Name of Involved -->
                        <div class="form-group">
                            <label for="name_involved">Name of Involved <span class="text-danger">*</span></label>
                            <select 
                                id="name_involved" 
                                name="name_involved" 
                                class="form-control form-select"
                                required
                            >
                                <option value="">-- Select Employee --</option>
                                @forelse($employees ?? [] as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->emp_code }} - {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                @empty
                                    <option value="">No employees available</option>
                                @endforelse
                            </select>
                        </div>

                        <!-- Row 7: Name of Witness -->
                        <div class="form-group">
                            <label for="name_witness">Name of Witness</label>
                            <select 
                                id="name_witness" 
                                name="name_witness" 
                                class="form-control form-select"
                            >
                                <option value="">-- Select Employee --</option>
                                @forelse($employees ?? [] as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->emp_code }} - {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                @empty
                                    <option value="">No employees available</option>
                                @endforelse
                            </select>
                        </div>
                        
                        <!-- Row 7: Recommended Action -->
                        <div class="form-group">
                            <label for="recommended_action">Recommended Action <span class="text-danger">*</span></label>
                            <textarea 
                                id="recommended_action" 
                                name="recommended_action" 
                                rows="3"
                                class="form-control"
                                placeholder="Describe recommended actions to prevent future incidents..."
                                required
                            ></textarea>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="form-group">
                            <button 
                                type="reset" 
                                class="btn btn-secondary"
                            >
                                Clear
                            </button>
                            <button 
                                type="submit" 
                                class="btn btn-primary"
                            >
                                Submit Report
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> You have read-only access. Below is your incident report history.
                    </div>
                    @endif
                </div>
            </div>
            @if(!$isReadOnly)
            <!-- Incident Reports Table -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Incident Reports</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="incidentReportsTable">
                            <thead>
                                <tr>
                                    <th>Incident No.</th>
                                    <th>Reported By</th>
                                    <th>Incident Type</th>
                                    <th>Date of Incident</th>
                                    <th>Location</th>
                                    <th>Sanction</th>
                                    <th>Report Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incidentReports ?? [] as $report)
                                    <tr data-report-id="{{ $report->id }}">
                                        <td>{{ $report->incident_no ?? 'N/A' }}</td>
                                        <td>{{ $report->reportedByEmployee ? $report->reportedByEmployee->first_name . ' ' . $report->reportedByEmployee->last_name : 'N/A' }}</td>
                                        <td>{{ $report->incident_type ?? 'N/A' }}</td>
                                        <td>{{ $report->date_incident ? date('M d, Y H:i', strtotime($report->date_incident)) : 'N/A' }}</td>
                                        <td>{{ $report->location ?? 'N/A' }}</td>
                                        <td>
                                            @if($report->disciplinaryNote && $report->disciplinaryNote->sanction)
                                                <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $report->disciplinaryNote->sanction)) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $report->date_time_report ? date('M d, Y H:i', strtotime($report->date_time_report)) : 'N/A' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewReport('{{ $report->id }}')" title="View">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            @if(!$isReadOnly)
                                                <a href="{{ route('nte_management', ['incident_id' => $report->id, 'employee_id' => $report->reported_by]) }}" class="btn btn-sm btn-success" title="Add NTE">
                                                    <i class="fa fa-plus"></i> NTE
                                                </a>
                                                <button class="btn btn-sm btn-danger" onclick="deleteReport('{{ $report->id }}')" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fa fa-inbox" style="font-size: 2em; color: #ccc;"></i><br>
                                            <small>No incident reports found.</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Staff Read-Only View: Only their own incident reports -->
            @if($isReadOnly)
            <div class="card">
                <div class="card-header">
                    <h4>My Incident Reports</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Incident No.</th>
                                    <th>Incident Type</th>
                                    <th>Date of Incident</th>
                                    <th>Location</th>
                                    <th>Report Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($myIncidentReports ?? [] as $report)
                                    <tr data-report-id="{{ $report->id }}">
                                        <td>{{ $report->incident_no ?? 'N/A' }}</td>
                                        <td>{{ $report->incident_type ?? 'N/A' }}</td>
                                        <td>{{ $report->date_incident ? date('M d, Y H:i', strtotime($report->date_incident)) : 'N/A' }}</td>
                                        <td>{{ $report->location ?? 'N/A' }}</td>
                                        <td>{{ $report->date_time_report ? date('M d, Y H:i', strtotime($report->date_time_report)) : 'N/A' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewReport('{{ $report->id }}')" title="View">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fa fa-inbox" style="font-size: 2em; color: #ccc;"></i><br>
                                            <small>No incident reports found.</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- View Report Modal -->
<div class="modal fade" id="viewReportModal" tabindex="-1" role="dialog" aria-labelledby="viewReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#2f47ba;color:white;">
                <h5 class="modal-title" id="viewReportModalLabel">Incident Report Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Incident No.:</h6>
                        <p id="modalIncidentNo">N/A</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Reported By:</h6>
                        <p id="modalReportedBy">N/A</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Position:</h6>
                        <p id="modalPosition">N/A</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Date and Time of Report:</h6>
                        <p id="modalDateTimeReport">N/A</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Incident Type:</h6>
                        <p id="modalIncidentType">N/A</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Date of Incident:</h6>
                        <p id="modalDateIncident">N/A</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="font-weight-bold">Location:</h6>
                        <p id="modalLocation">N/A</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="font-weight-bold">Incident Description:</h6>
                        <p id="modalIncidentDescription" style="white-space: pre-wrap;">N/A</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Name of Involved:</h6>
                        <p id="modalNameInvolved">N/A</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Name of Witness:</h6>
                        <p id="modalNameWitness">N/A</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="font-weight-bold">Recommended Action:</h6>
                        <p id="modalRecommendedAction" style="white-space: pre-wrap;">N/A</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize Select2 for employee dropdowns with search
        $('#reported_by, #name_involved, #name_witness').select2({
            placeholder: "-- Select Employee --",
            allowClear: true,
            width: '100%'
        });

        // Auto-populate position when employee is selected in reported_by
        $('#reported_by').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const position = selectedOption.data('position');
            $('#position').val(position || '');
        });

        // Generate incident number on form load
        generateIncidentNumber();
    });

    function generateIncidentNumber() {
        // Generate format: INC-YYYYMMDD-NNNN (where NNNN is a random 4-digit number)
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const randomNum = String(Math.floor(Math.random() * 10000)).padStart(4, '0');
        
        const incidentNo = `INC-${year}${month}${day}-${randomNum}`;
        $('#incident_no').val(incidentNo);
    }

    function viewReport(reportId) {
        // Fetch report details via AJAX
        $.ajax({
            url: "{{ route('incident-report.show', '') }}/" + reportId,
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            dataType: 'json',
            success: function(response) {
                // Populate modal with report details
                $('#reportId').val(response.id);
                $('#modalIncidentNo').text(response.incident_no || 'N/A');
                $('#modalReportedBy').text(response.reported_by_name || 'N/A');
                $('#modalPosition').text(response.position || 'N/A');
                $('#modalDateTimeReport').text(response.date_time_report ? new Date(response.date_time_report).toLocaleString() : 'N/A');
                $('#modalIncidentType').text(response.incident_type || 'N/A');
                $('#modalDateIncident').text(response.date_incident ? new Date(response.date_incident).toLocaleString() : 'N/A');
                $('#modalLocation').text(response.location || 'N/A');
                $('#modalIncidentDescription').text(response.incident_description || 'N/A');
                $('#modalNameInvolved').text(response.name_involved_name || 'N/A');
                $('#modalNameWitness').text(response.name_witness_name || 'N/A');
                $('#modalRecommendedAction').text(response.recommended_action || 'N/A');
                
                // Show the modal
                $('#viewReportModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                alert('Error loading report details. Please try again.');
            }
        });
    }

    function deleteReport(reportId) {
        if (confirm('Are you sure you want to delete this incident report?')) {
            // Get the CSRF token from the meta tag (current session token)
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            $.ajax({
                url: "{{ route('delete_incident_report') }}",
                type: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    _token: csrfToken,
                    id: reportId
                },
                dataType: 'json',
                success: function (response) { 
                    alert(response.message || 'Incident report deleted successfully.');
                    // Find and remove the row from the table
                    $('tr[data-report-id="' + reportId + '"]').fadeOut(300, function() {
                        $(this).remove();
                        
                        // If no more rows, reload to show empty message
                        const tableBody = document.querySelector('#incidentReportsTable tbody');
                        if (tableBody && tableBody.children.length === 0) {
                            location.reload();
                        }
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Delete Error Status:", xhr.status);
                    console.error("Delete Error:", error);
                    console.error("Delete Response:", xhr.responseText);
                    
                    let errorMsg = 'Error deleting incident report.';
                    
                    if (xhr.status === 419) {
                        errorMsg = 'Session expired. Please refresh the page and try again.';
                    } else if (xhr.status === 403) {
                        errorMsg = 'You do not have permission to delete this report.';
                    } else if (xhr.status === 404) {
                        errorMsg = 'Incident report not found.';
                    } else {
                        try {
                            const jsonResponse = JSON.parse(xhr.responseText);
                            if (jsonResponse.message) {
                                errorMsg = jsonResponse.message;
                            }
                        } catch(e) {
                            errorMsg = 'Server error: ' + xhr.status;
                        }
                    }
                    
                    alert(errorMsg);
                }
            });
        }
    }
</script>
@stop
