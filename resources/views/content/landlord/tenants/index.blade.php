@extends('layouts/contentNavbarLayout')

@section('title', 'My Tenants')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-1">👥 My Tenants</h4>
        <p class="mb-0 text-muted">View and manage your current tenants</p>
      </div>
    </div>
  </div>

  <!-- Stats Overview -->
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <p class="card-text">Total Tenants</p>
            <h4 class="mb-0">{{ $totalTenants }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
              <i class='bx bx-user bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <p class="card-text">Active Leases</p>
            <h4 class="mb-0">{{ $activeLeases }}</h4>
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

  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <p class="card-text">Move-ins This Month</p>
            <h4 class="mb-0">{{ $moveInsThisMonth }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-2">
              <i class='bx bx-calendar-plus bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <p class="card-text">Ending Soon</p>
            <h4 class="mb-0">{{ $endingSoon }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-warning rounded p-2">
              <i class='bx bx-calendar-exclamation bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('landlord.tenants.index') }}" method="GET">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Property</label>
              <select name="property_id" class="form-select">
                <option value="">All Properties</option>
                @foreach($properties as $property)
                  <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                    {{ $property->title }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="ending_soon" {{ request('status') == 'ending_soon' ? 'selected' : '' }}>Ending Soon</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Search</label>
              <input type="text" name="search" class="form-control" placeholder="Search tenant name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
              <label class="form-label">&nbsp;</label>
              <button type="submit" class="btn btn-primary w-100">
                <i class='bx bx-search'></i> Filter
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Tenants List -->
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Tenant</th>
                <th>Property</th>
                <th>Contact</th>
                <th>Move-in Date</th>
                <th>Contract End</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($tenants as $tenant)
                <tr>
                  <td>
                    <div class="d-flex flex-column">
                      <span class="fw-semibold">
                        @if($tenant->user->first_name && $tenant->user->last_name)
                          {{ $tenant->user->first_name }} {{ $tenant->user->last_name }}
                        @elseif($tenant->user->name)
                          {{ $tenant->user->name }}
                        @else
                          N/A
                        @endif
                      </span>
                      <small class="text-muted">{{ $tenant->user->student_number ?? $tenant->user->studID ?? 'N/A' }}</small>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex flex-column">
                      <span>{{ $tenant->property->title }}</span>
                      <small class="text-muted">{{ Str::limit($tenant->property->address, 30) }}</small>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex flex-column">
                      <small>{{ $tenant->user->email }}</small>
                      <small class="text-muted">{{ $tenant->user->contact_number ?? 'N/A' }}</small>
                    </div>
                  </td>
                  <td>{{ $tenant->move_in_date ? \Carbon\Carbon::parse($tenant->move_in_date)->format('M d, Y') : 'Not set' }}</td>
                  <td>{{ $tenant->move_out_date ? \Carbon\Carbon::parse($tenant->move_out_date)->format('M d, Y') : 'Not set' }}</td>
                  <td>
                    @if($tenant->status === 'Active')
                      <span class="badge bg-label-success">Active</span>
                    @elseif($tenant->status === 'Approved')
                      <span class="badge bg-label-info">Approved</span>
                    @elseif($tenant->status === 'Completed')
                      <span class="badge bg-label-secondary">Completed</span>
                    @else
                      <span class="badge bg-label-warning">{{ $tenant->status }}</span>
                    @endif
                  </td>
                  <td>
                    <div class="dropdown">
                      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('landlord.tenants.show', $tenant->booking_id) }}">
                          <i class="bx bx-show me-1"></i> View Details
                        </a>
                        @if($tenant->status === 'Active')
                          <form action="{{ route('landlord.bookings.complete', $tenant->booking_id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="dropdown-item" onclick="return confirm('Mark this booking as completed?')">
                              <i class="bx bx-check me-1"></i> Complete Lease
                            </button>
                          </form>
                        @endif
                        @if(in_array($tenant->status, ['Active', 'Approved']))
                          <form action="{{ route('landlord.bookings.cancel', $tenant->booking_id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">
                              <i class="bx bx-x me-1"></i> Cancel Booking
                            </button>
                          </form>
                        @endif
                      </div>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-5">
                    <i class='bx bx-user-x display-1 text-muted mb-3'></i>
                    <h5 class="text-muted">No Tenants Found</h5>
                    <p class="text-muted">Approved bookings will appear here</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if($tenants->hasPages())
          <div class="mt-3">
            {{ $tenants->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection