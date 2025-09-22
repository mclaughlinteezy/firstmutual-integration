{{-- @extends('layouts.app') --}}
@extends('layouts.sidebar')
@section('title', 'Add New Student')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>
                    Add New Student
                </h4>
            </div>
            <div class="card-body">
                <!-- Success/Error Messages -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('medical.store') }}" id="student-form">
                    @csrf

                    <div class="mb-4">
                        <label for="reg_number" class="form-label">
                            Registration Number <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control form-control-lg @error('reg_number') is-invalid @enderror"
                               id="reg_number"
                               name="reg_number"
                               value="{{ old('reg_number') }}"
                               placeholder="e.g., R171767H"
                               required>
                        <div class="form-text">
                            Enter the student's registration number to fetch their details from First Mutual API
                        </div>
                        @error('reg_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Process Information -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>What happens next:</strong>
                        <ol class="mb-0 mt-2">
                            <li>System will search for student in First Mutual API</li>
                            <li>If found, details will be automatically stored</li>
                            <li>If not found, you'll be prompted to add details manually</li>
                            <li>All responses are logged for debugging</li>
                        </ol>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('medical.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>

                        <div>
                            <a href="{{ route('medical.create-manual') }}" class="btn btn-outline-success btn-lg me-2">
                                <i class="fas fa-user-edit me-1"></i>Add Manually
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                <i class="fas fa-search me-1"></i>
                                Search API
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('student-form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submit-btn');

    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Searching...';

    // Re-enable button after 15 seconds (in case of timeout)
    setTimeout(function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-search me-1"></i>Search API';
    }, 15000);
});
</script>
@endsection
