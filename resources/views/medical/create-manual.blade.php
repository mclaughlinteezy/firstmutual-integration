{{-- @extends('layouts.app') --}}
@extends('layouts.sidebar')
@section('title', 'Add Student Manually')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Add Student Manually
                </h4>
            </div>
            <div class="card-body">
                <!-- API Error Notice -->
                @if(session('api_error'))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>API Lookup Failed:</strong> {{ session('api_error') }}
                        <br><small>Please enter the student details manually below.</small>
                    </div>
                @endif

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

                <form method="POST" action="{{ route('medical.store-manual') }}" id="manual-student-form">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reg_number" class="form-label">
                                    Registration Number <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('reg_number') is-invalid @enderror"
                                       id="reg_number"
                                       name="reg_number"
                                       value="{{ old('reg_number', session('reg_number') ?? $regNumber ?? '') }}"
                                       placeholder="e.g., R171767H"
                                       required>
                                @error('reg_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nationalid" class="form-label">
                                    National ID
                                </label>
                                <input type="text"
                                       class="form-control @error('nationalid') is-invalid @enderror"
                                       id="nationalid"
                                       name="nationalid"
                                       value="{{ old('nationalid') }}"
                                       placeholder="e.g., 29-295057-C-27">
                                @error('nationalid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="firstnames" class="form-label">
                                    First Names <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('firstnames') is-invalid @enderror"
                                       id="firstnames"
                                       name="firstnames"
                                       value="{{ old('firstnames') }}"
                                       placeholder="e.g., John Peter"
                                       required>
                                @error('firstnames')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="surname" class="form-label">
                                    Surname <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('surname') is-invalid @enderror"
                                       id="surname"
                                       name="surname"
                                       value="{{ old('surname') }}"
                                       placeholder="e.g., Smith"
                                       required>
                                @error('surname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dob" class="form-label">
                                    Date of Birth
                                </label>
                                <input type="date"
                                       class="form-control @error('dob') is-invalid @enderror"
                                       id="dob"
                                       name="dob"
                                       value="{{ old('dob') }}"
                                       max="{{ date('Y-m-d') }}">
                                @error('dob')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="gender" class="form-label">
                                    Gender
                                </label>
                                <select class="form-select @error('gender') is-invalid @enderror"
                                        id="gender"
                                        name="gender">
                                    <option value="">Choose gender...</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="mobile" class="form-label">
                            Mobile Number
                        </label>
                        <input type="text"
                               class="form-control @error('mobile') is-invalid @enderror"
                               id="mobile"
                               name="mobile"
                               value="{{ old('mobile') }}"
                               placeholder="e.g., 0779400263">
                        @error('mobile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">
                            Address
                        </label>
                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  id="address"
                                  name="address"
                                  rows="3"
                                  placeholder="Enter full address">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Information Notice -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Manual Entry:</strong>
                        This student will be marked as manually added and can be edited later if needed.
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('medical.create') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to API Search
                            </a>
                            <a href="{{ route('medical.index') }}" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-list me-1"></i>View All Records
                            </a>
                        </div>

                        <button type="submit" class="btn btn-success" id="submit-btn">
                            <i class="fas fa-save me-1"></i>
                            Add Student Manually
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('manual-student-form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submit-btn');

    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Adding Student...';

    // Re-enable button after 10 seconds (in case of timeout)
    setTimeout(function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Add Student Manually';
    }, 10000);
});
</script>
@endsection
