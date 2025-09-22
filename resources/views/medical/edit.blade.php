{{-- @extends('layouts.app') --}}
@extends('layouts.sidebar')
@section('title', 'Edit Student Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Edit Student Details
                </h4>
                <small class="text-muted">Registration Number: {{ $record->reg_number }}</small>
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

                <form method="POST" action="{{ route('medical.update', $record->id) }}" id="edit-student-form">
                    @csrf
                    @method('PUT')

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
                                       value="{{ old('firstnames', $record->firstnames) }}"
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
                                       value="{{ old('surname', $record->surname) }}"
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
                                       value="{{ old('dob', $record->dob?->format('Y-m-d')) }}"
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
                                    <option value="male" {{ old('gender', $record->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $record->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $record->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nationalid" class="form-label">
                                    National ID
                                </label>
                                <input type="text"
                                       class="form-control @error('nationalid') is-invalid @enderror"
                                       id="nationalid"
                                       name="nationalid"
                                       value="{{ old('nationalid', $record->nationalid) }}">
                                @error('nationalid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mobile" class="form-label">
                                    Mobile Number
                                </label>
                                <input type="text"
                                       class="form-control @error('mobile') is-invalid @enderror"
                                       id="mobile"
                                       name="mobile"
                                       value="{{ old('mobile', $record->mobile) }}">
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">
                            Address
                        </label>
                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  id="address"
                                  name="address"
                                  rows="3">{{ old('address', $record->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('medical.show', $record->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Details
                            </a>
                            <a href="{{ route('medical.index') }}" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-list me-1"></i>View All Records
                            </a>
                        </div>

                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="fas fa-save me-1"></i>
                            Update Student
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
document.getElementById('edit-student-form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submit-btn');

    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';

    // Re-enable button after 10 seconds (in case of timeout)
    setTimeout(function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Update Student';
    }, 10000);
});
</script>
@endsection
