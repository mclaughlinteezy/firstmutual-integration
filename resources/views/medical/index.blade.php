{{-- @extends('layouts.app') --}}
@extends('layouts.sidebar')
@section('title', 'First Mutual Records')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-tachometer-alt me-2"></i>
        Medical Aide Records Dashboard
    </h1>
    <a href="{{ route('medical.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add New Student
    </a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-primary">
                    <i class="fas fa-users fa-2x mb-2"></i>
                </div>
                <h4 class="mb-0">{{ number_format($stats['total']) }}</h4>
                <small class="text-muted">Total Records</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-success">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                </div>
                <h4 class="mb-0">{{ number_format($stats['success']) }}</h4>
                <small class="text-muted">Successful</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-danger">
                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                </div>
                <h4 class="mb-0">{{ number_format($stats['failed']) }}</h4>
                <small class="text-muted">Failed</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-warning">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                </div>
                <h4 class="mb-0">{{ number_format($stats['pending']) }}</h4>
                <small class="text-muted">Pending</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-info">
                    <i class="fas fa-cloud fa-2x mb-2"></i>
                </div>
                <h5 class="mb-0">{{ number_format($stats['api']) }}</h5>
                <small class="text-muted">From API</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-secondary">
                    <i class="fas fa-user-edit fa-2x mb-2"></i>
                </div>
                <h5 class="mb-0">{{ number_format($stats['manual']) }}</h5>
                <small class="text-muted">Manual Entry</small>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="search-form">
    <form method="GET" action="{{ route('medical.index') }}" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control"
                   placeholder="Reg Number, Name, ID..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">From Date</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">To Date</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-search"></i> Search
            </button>
            <a href="{{ route('medical.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times"></i> Clear
            </a>
        </div>
        <div class="col-md-2">
            <label class="form-label">Source</label>
            <select name="source" class="form-select">
                <option value="">All Sources</option>
                <option value="api" {{ request('source') == 'api' ? 'selected' : '' }}>API</option>
                <option value="manual" {{ request('source') == 'manual' ? 'selected' : '' }}>Manual</option>
            </select>
        </div>
    </form>
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

<!-- Bulk Actions Form -->
<form method="POST" action="{{ route('medical.bulk-action') }}" id="bulk-form">
    @csrf

    <!-- Bulk Actions Bar (Hidden by default) -->
    <div id="bulk-actions" class="card mb-3" style="display: none;">
        <div class="card-body py-2">
            <div class="d-flex align-items-center">
                <span class="me-3">
                    <i class="fas fa-tasks"></i>
                    Bulk Actions:
                </span>
                <select name="action" class="form-select form-select-sm me-2" style="width: auto;">
                    <option value="">Choose action...</option>
                    <option value="retry">Retry Failed</option>
                    <option value="delete">Delete Selected</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary me-2"
                        onclick="return confirm('Are you sure you want to perform this bulk action?')">
                    Execute
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        onclick="document.querySelectorAll('input[name=\'selected_records[]\']').forEach(cb => cb.checked = false); updateBulkActions();">
                    Clear Selection
                </button>
            </div>
        </div>
    </div>

    <!-- Records Table -->
    <div class="card">
        <div class="card-body">
            @if($records->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input"
                                        onchange="toggleAllCheckboxes(this)">
                                </th>
                                <th>Reg Number</th>
                                <th>Student Name</th>
                                <th>National ID</th>
                                <th>Mobile</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>

                        <!-- Update table body -->
                        <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_records[]"
                                            value="{{ $record->id }}" class="form-check-input"
                                            onchange="updateBulkActions()">
                                    </td>
                                    <td>
                                        <strong>{{ $record->reg_number }}</strong>
                                    </td>
                                    <td>
                                        {{ $record->firstnames }} {{ $record->surname }}
                                        @if(!$record->firstnames && !$record->surname)
                                            <small class="text-muted">Not fetched</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $record->nationalid ?? '-' }}
                                    </td>
                                    <td>
                                        @if($record->mobile)
                                            <a href="tel:{{ $record->mobile }}">{{ $record->mobile }}</a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($record->status)
                                            @case('success')
                                                <span class="badge bg-success status-badge">
                                                    <i class="fas fa-check me-1"></i>Success
                                                </span>
                                                @break
                                            @case('failed')
                                                <span class="badge bg-danger status-badge">
                                                    <i class="fas fa-times me-1"></i>Failed
                                                </span>
                                                @break
                                            @case('processing')
                                                <span class="badge bg-info status-badge">
                                                    <i class="fas fa-spinner me-1"></i>Processing
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-warning status-badge">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <strong>{{ $record->reg_number }}</strong>
                                        @if($record->isManual())
                                            <span class="badge bg-secondary ms-1" title="Manually Added">
                                                <i class="fas fa-user-edit"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-info ms-1" title="From API">
                                                <i class="fas fa-cloud"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $record->created_at->format('M d, Y') }}<br>
                                            {{ $record->created_at->format('H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('medical.show', $record->id) }}"
                                            class="btn btn-outline-primary btn-action"
                                            title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($record->status === 'failed')
                                                <form method="POST" action="{{ route('medical.retry', $record->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-warning btn-action"
                                                            title="Retry">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('medical.destroy', $record->id) }}"
                                                class="d-inline"
                                                onsubmit="return confirmDelete()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-action"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $records->firstItem() }} to {{ $records->lastItem() }}
                        of {{ $records->total() }} results
                    </div>
                    {{ $records->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No records found</h5>
                    <p class="text-muted">Start by adding a new student record.</p>
                    <a href="{{ route('medical.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add First Student
                    </a>
                </div>
            @endif
        </div>
    </div>
</form>
@endsection
