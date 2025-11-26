@extends('layouts/contentNavbarLayout')

@section('title', 'Property Management')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h4 class="mb-1">Property Management</h4>
            <p class="text-muted mb-0">Manage and review all property listings</p>
          </div>
          <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
              <i class='bx bx-arrow-back'></i> Back to Dashboard
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Total Properties</p>
            <h4 class="mb-0">{{ number_format($properties->total()) }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
              <i class='bx bx-building-house bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Active</p>
            <h4 class="mb-0 text-success">{{ number_format($properties->where('is_active', 1)->count()) }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-success rounded p-2">
              <i class='bx bx-check-circle bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Pending</p>
            <h4 class="mb-0 text-warning">{{ number_format($properties->where('is_active', 0)->count()) }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-warning rounded p-2">
              <i class='bx bx-time-five bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">This Month</p>
            <h4 class="mb-0 text-info">{{ number_format($properties->filter(function($p) { return $p->created_at->isCurrentMonth(); })->count()) }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-2">
              <i class='bx bx-calendar bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters and Search -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.properties.index') }}">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Search</label>
              <input type="text" name="search" class="form-control" placeholder="Search by title or address..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Active</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">&nbsp;</label>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class='bx bx-search'></i> Search
                </button>
                <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary">
                  <i class='bx bx-reset'></i> Reset
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Properties Table -->
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Properties</h5>
        <span class="badge bg-label-primary">{{ $properties->total() }} Total</span>
      </div>
      <div class="card-body">
        @if($properties->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Property</th>
                  <th>Landlord</th>
                  <th>Price</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($properties as $property)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        @if($property->images && $property->images->count() > 0)
                          <img src="{{ asset('storage/' . $property->images->first()->image_path) }}" 
                               alt="{{ $property->title }}" 
                               class="rounded me-3" 
                               style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                          <div class="avatar avatar-md me-3">
                            <span class="avatar-initial rounded bg-label-info">
                              <i class='bx bx-building-house'></i>
                            </span>
                          </div>
                        @endif
                        <div>
                          <strong>{{ Str::limit($property->title, 30) }}</strong><br>
                          <small class="text-muted">
                            <i class='bx bx-map'></i> {{ Str::limit($property->address, 40) }}
                          </small>
                        </div>
                      </div>
                    </td>
                    <td>
                      @if($property->landlord && $property->landlord->user)
                        <div>
                          <strong>{{ $property->landlord->user->first_name }} {{ $property->landlord->user->last_name }}</strong><br>
                          <small class="text-muted">{{ $property->landlord->user->email }}</small>
                        </div>
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      <strong class="text-primary">₱{{ number_format($property->price, 2) }}</strong><br>
                      <small class="text-muted">per month</small>
                    </td>
                    <td>
                      @if($property->property_type)
                        <span class="badge bg-label-secondary">{{ $property->property_type }}</span>
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      @if($property->is_active)
                        <span class="badge bg-success">
                          <i class='bx bx-check'></i> Active
                        </span>
                      @else
                        <span class="badge bg-warning">
                          <i class='bx bx-time'></i> Pending
                        </span>
                      @endif
                    </td>
                    <td>
                      <small>{{ $property->created_at->format('M d, Y') }}</small><br>
                      <small class="text-muted">{{ $property->created_at->diffForHumans() }}</small>
                    </td>
                    <td>
                      <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-icon btn-outline-primary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i class='bx bx-dots-vertical-rounded'></i>
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="{{ route('properties.view', $property->id) }}" target="_blank">
                            <i class='bx bx-show me-1'></i> View Property
                          </a>
                          @if(!$property->is_active)
                            <form action="{{ route('admin.properties.approve', $property->id) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="dropdown-item text-success">
                                <i class='bx bx-check me-1'></i> Approve
                              </button>
                            </form>
                          @else
                            <form action="{{ route('admin.properties.reject', $property->id) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="dropdown-item text-warning">
                                <i class='bx bx-x me-1'></i> Deactivate
                              </button>
                            </form>
                          @endif
                          <div class="dropdown-divider"></div>
                          <form action="{{ route('admin.properties.delete', $property->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this property? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                              <i class='bx bx-trash me-1'></i> Delete
                            </button>
                          </form>
                        </div>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

<div class="mt-4 d-flex justify-content-center">
  <ul class="pagination pagination-sm mb-0 shadow-sm rounded">
    {{-- Previous Page Link --}}
    @if ($properties->onFirstPage())
      <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
    @else
      <li class="page-item"><a class="page-link" href="{{ $properties->previousPageUrl() }}" rel="prev">&laquo;</a></li>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($properties->links()->elements[0] ?? [] as $page => $url)
      @if ($page == $properties->currentPage())
        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
      @else
        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
      @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($properties->hasMorePages())
      <li class="page-item"><a class="page-link" href="{{ $properties->nextPageUrl() }}" rel="next">&raquo;</a></li>
    @else
      <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
    @endif
  </ul>
</div>

        @else
          <div class="text-center py-5">
            <i class='bx bx-building-house bx-lg text-muted'></i>
            <p class="text-muted mt-3">No properties found</p>
            @if(request()->hasAny(['search', 'status']))
              <a href="{{ route('admin.properties.index') }}" class="btn btn-sm btn-outline-primary">
                Clear Filters
              </a>
            @endif
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@if(session('success'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // You can add toast notification here if you have one
      alert('{{ session('success') }}');
    });
  </script>
@endif

@if(session('error'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      alert('{{ session('error') }}');
    });
  </script>
@endif
@endsection