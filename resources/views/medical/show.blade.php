{{-- @extends('layouts.app') --}}
@extends('layouts.sidebar')
@section('title', 'Student Record Details')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Student Record Details</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('medical.index') }}">Records</a></li>
                        <li class="breadcrumb-item active">{{ $record->reg_number }}</li>
                    </ol>
                </nav>
            </div>

            <div class="btn-group">
                <a href="{{ route('medical.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>

                @if($record->status === 'failed')
                    <form method="POST" action="{{ route('medical.retry', $record->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-redo me-1"></i>Retry Processing
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('medical.destroy', $record->id) }}"
                      class="d-inline" onsubmit="return confirmDelete()">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-1"></i>
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>
</div>

<div class="row">
    <!-- Student Information -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>Student Information
                </h5>

                <!-- Status Badge -->
                @switch($record->status)
                    @case('success')
                        <span class="badge bg-success fs-6">
                            <i class="fas fa-check me-1"></i>Successfully Processed
                        </span>
                        @break
                    @case('failed')
                        <span class="badge bg-danger fs-6">
                            <i class="fas fa-times me-1"></i>Processing Failed
                        </span>
                        @break
                    @case('processing')
                        <span class="badge bg-info fs-6">
                            <i class="fas fa-spinner me-1"></i>Currently Processing
                        </span>
                        @break
                    @default
                        <span class="badge bg-warning fs-6">
                            <i class="fas fa-clock me-1"></i>Pending Processing
                        </span>
                @endswitch
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Basic Information</h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Registration Number:</label>
                            <p class="mb-0">{{ $record->reg_number }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name:</label>
                            <p class="mb-0">
                                @if($record->firstnames || $record->surname)
                                    {{ $record->firstnames }} {{ $record->surname }}
                                @else
                                    <span class="text-muted">Not fetched from API</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Date of Birth:</label>
                            <p class="mb-0">
                                @if($record->dob)
                                    {{ $record->dob->format('F d, Y') }}
                                    <small class="text-muted">(Age: {{ $record->dob->age }} years)</small>
                                @else
                                    <span class="text-muted">Not available</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Gender:</label>
                            <p class="mb-0">
                                @if($record->gender)
                                    <span class="badge bg-{{ $record->gender == 'male' ? 'primary' : 'pink' }}">
                                        {{ ucfirst($record->gender) }}
                                    </span>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Contact Information</h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold">National ID:</label>
                            <p class="mb-0">{{ $record->nationalid ?? 'Not available' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mobile Number:</label>
                            <p class="mb-0">
                                @if($record->mobile)
                                    <a href="tel:{{ $record->mobile }}">{{ $record->mobile }}</a>
                                @else
                                    <span class="text-muted">Not available</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Address:</label>
                            <p class="mb-0">
                                @if($record->address)
                                    {{ nl2br(e($record->address)) }}
                                @else
                                    <span class="text-muted">Not available</span>
                                @endif
                            </p>
                        </div>

                        <div class="mt-2">
                            @if($record->isManual())
                                <span class="badge bg-secondary fs-6">
                                    <i class="fas fa-user-edit me-1"></i>Manually Added
                                </span>
                            @else
                                <span class="badge bg-info fs-6">
                                    <i class="fas fa-cloud me-1"></i>From API
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Message (if any) -->
        @if($record->error_message)
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Error Details
                    </h5>
                </div>
                <div class="card-body">
                    <pre class="mb-0 text-danger">{{ $record->error_message }}</pre>
                </div>
            </div>
        @endif

        <!-- API Response Details -->
        @if($record->api_response)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-code me-2"></i>API Response Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="apiResponseAccordion">
                        @if(isset($record->api_response['student_details']))
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="studentDetailsHeader">
                                    <button class="accordion-button" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#studentDetailsCollapse">
                                        Student Details API Response
                                        @if($record->api_response['student_details']['success'])
                                            <span class="badge bg-success ms-2">Success</span>
                                        @else
                                            <span class="badge bg-danger ms-2">Failed</span>
                                        @endif
                                    </button>
                                </h2>
                                <div id="studentDetailsCollapse" class="accordion-collapse collapse show"
                                     data-bs-parent="#apiResponseAccordion">
                                    <div class="accordion-body">
                                        <div class="json-viewer">
                                            <pre>{{ json_encode($record->api_response['student_details'], JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(isset($record->api_response['payment_submission']))
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="paymentSubmissionHeader">
                                    <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#paymentSubmissionCollapse">
                                        Payment Submission API Response
                                        @if($record->api_response['payment_submission']['success'])
                                            <span class="badge bg-success ms-2">Success</span>
                                        @else
                                            <span class="badge bg-danger ms-2">Failed</span>
                                        @endif
                                    </button>
                                </h2>
                                <div id="paymentSubmissionCollapse" class="accordion-collapse collapse"
                                     data-bs-parent="#apiResponseAccordion">
                                    <div class="accordion-body">
                                        <div class="json-viewer">
                                            <pre>{{ json_encode($record->api_response['payment_submission'], JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        @if($record->api_response)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-code me-2"></i>API Response Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="apiResponseAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="studentDetailsHeader">
                                <button class="accordion-button" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#studentDetailsCollapse">
                                    Student Details API Response
                                    @if($record->api_response['success'])
                                        <span class="badge bg-success ms-2">Success</span>
                                    @else
                                        <span class="badge bg-danger ms-2">Failed</span>
                                    @endif
                                </button>
                            </h2>
                            <div id="studentDetailsCollapse" class="accordion-collapse collapse show"
                                data-bs-parent="#apiResponseAccordion">
                                <div class="accordion-body">
                                    <div class="json-viewer">
                                        <pre>{{ json_encode($record->api_response, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Record Metadata -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Record Information
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Record ID:</label>
                    <p class="mb-0">#{{ $record->id }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Created:</label>
                    <p class="mb-0">
                        {{ $record->created_at->format('F d, Y H:i') }}
                        <br><small class="text-muted">{{ $record->created_at->diffForHumans() }}</small>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Last Updated:</label>
                    <p class="mb-0">
                        {{ $record->updated_at->format('F d, Y H:i') }}
                        <br><small class="text-muted">{{ $record->updated_at->diffForHumans() }}</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('medical.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add Another Student
                    </a>

                    @if($record->status === 'failed')
                        <form method="POST" action="{{ route('medical.retry', $record->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-redo me-1"></i>Retry Processing
                            </button>
                        </form>
                    @endif

                    <button type="button" class="btn btn-outline-secondary"
                            onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Print Record
                    </button>
                </div>
                <div class="btn-group">
                    <a href="{{ route('medical.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>

                    @if($record->isManual())
                        <a href="{{ route('medical.edit', $record->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Edit Details
                        </a>
                    @endif

                    @if($record->status === 'failed' && $record->isFromApi())
                        <form method="POST" action="{{ route('medical.retry', $record->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-redo me-1"></i>Retry API
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('medical.destroy', $record->id) }}"
                        class="d-inline" onsubmit="return confirmDelete()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
@media print {
    .btn, .card-header, nav, footer {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
