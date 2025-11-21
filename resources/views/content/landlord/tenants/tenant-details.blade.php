@extends('layouts/contentNavbarLayout')

@section('title', 'Tenant Details')

@section('content')
<div class="row">
  <!-- Back Button -->
  <div class="col-12 mb-4">
    <a href="{{ route('landlord.tenants.index') }}" class="btn btn-sm btn-outline-secondary">
      <i class='bx bx-arrow-back'></i> Back to Tenants
    </a>
  </div>

  <!-- Tenant Overview -->
  <div class="col-md-4 mb-4">
    <div class="card">
      <div class="card-body text-center">
        <div class="mb-3 d-flex justify-content-center">
          @if($tenant->user->profile_picture)
            <img src="{{ asset('storage/' . $tenant->user->profile_picture) }}" 
                 alt="Profile" 
                 class="rounded-circle" 
                 width="120" 
                 height="120"
                 style="object-fit: cover;">
          @else
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-label-primary" style="width: 120px; height: 120px; font-size: 48px;">
              {{ strtoupper(substr($tenant->user->first_name ?? $tenant->user->name ?? 'U', 0, 1)) }}
            </div>
          @endif
        </div>
        
        <h4 class="mb-1">
          @if($tenant->user->first_name && $tenant->user->last_name)
            {{ $tenant->user->first_name }} {{ $tenant->user->last_name }}
          @elseif($tenant->user->name)
            {{ $tenant->user->name }}
          @else
            N/A
          @endif
        </h4>
        <p class="text-muted mb-3">
          @if($tenant->status === 'Active')
            <span class="badge bg-label-success">Active Tenant</span>
          @elseif($tenant->status === 'Approved')
            <span class="badge bg-label-info">Approved</span>
          @elseif($tenant->status === 'Completed')
            <span class="badge bg-label-secondary">Lease Completed</span>
          @else
            <span class="badge bg-label-warning">{{ $tenant->status }}</span>
          @endif
        </p>

        <div class="d-flex justify-content-center gap-2 mb-3">
          <a href="mailto:{{ $tenant->user->email }}" class="btn btn-primary btn-sm">
            <i class='bx bx-envelope'></i> Email
          </a>
          @if($tenant->user->contact_number)
            <a href="tel:{{ $tenant->user->contact_number }}" class="btn btn-outline-primary btn-sm">
              <i class='bx bx-phone'></i> Call
            </a>
          @endif
        </div>

        <div class="text-start mt-4">
          <h6 class="mb-3">Quick Stats</h6>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Move-in Date:</span>
            <span class="fw-semibold">{{ $tenant->move_in_date ? \Carbon\Carbon::parse($tenant->move_in_date)->format('M d, Y') : 'Not set' }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Contract End:</span>
            <span class="fw-semibold">{{ $tenant->move_out_date ? \Carbon\Carbon::parse($tenant->move_out_date)->format('M d, Y') : 'Not set' }}</span>
          </div>
          @if($tenant->move_in_date)
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Days as Tenant:</span>
              <span class="fw-semibold">{{ \Carbon\Carbon::parse($tenant->move_in_date)->diffInDays(\Carbon\Carbon::now()) }} days</span>
            </div>
          @endif
          @if($tenant->move_out_date && $tenant->status === 'Active')
            <div class="d-flex justify-content-between">
              <span class="text-muted">Days Remaining:</span>
              <span class="fw-semibold text-{{ \Carbon\Carbon::parse($tenant->move_out_date)->diffInDays(\Carbon\Carbon::now()) <= 30 ? 'warning' : 'success' }}">
                {{ \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($tenant->move_out_date)) }} days
              </span>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Actions Card -->
    @if(in_array($tenant->status, ['Active', 'Approved']))
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="mb-0">Actions</h5>
        </div>
        <div class="card-body">
          @if($tenant->status === 'Active')
            <form action="{{ route('landlord.bookings.complete', $tenant->booking_id) }}" method="POST" class="mb-2">
              @csrf
              @method('PUT')
              <button type="submit" class="btn btn-success w-100" onclick="return confirm('Mark this lease as completed?')">
                <i class='bx bx-check-circle'></i> Complete Lease
              </button>
            </form>
          @endif
          
          <form action="{{ route('landlord.bookings.cancel', $tenant->booking_id) }}" method="POST">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')">
              <i class='bx bx-x-circle'></i> Cancel Booking
            </button>
          </form>
        </div>
      </div>
    @endif
  </div>

  <!-- Detailed Information -->
  <div class="col-md-8">
    <!-- Personal Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class='bx bx-user'></i> Personal Information</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label text-muted small">Full Name</label>
            <p class="mb-0 fw-semibold">
              @if($tenant->user->first_name && $tenant->user->last_name)
                {{ $tenant->user->first_name }} {{ $tenant->user->last_name }}
              @else
                {{ $tenant->user->name }}
              @endif
            </p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">Student Number</label>
            <p class="mb-0 fw-semibold">{{ $tenant->user->student_number ?? $tenant->user->studID ?? 'N/A' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">Email Address</label>
            <p class="mb-0 fw-semibold">{{ $tenant->user->email }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">Contact Number</label>
            <p class="mb-0 fw-semibold">{{ $tenant->user->contact_number ?? 'N/A' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">Gender</label>
            <p class="mb-0 fw-semibold">{{ $tenant->user->gender ?? 'N/A' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">Program</label>
            <p class="mb-0 fw-semibold">{{ $tenant->user->program ?? 'N/A' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">Year Level</label>
            <p class="mb-0 fw-semibold">{{ $tenant->user->year_level ?? 'N/A' }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Property Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class='bx bx-home'></i> Property Information</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-3">
            @if($tenant->property->image)
              <img src="{{ asset('storage/' . $tenant->property->image) }}" 
                   alt="{{ $tenant->property->title }}" 
                   class="img-fluid rounded">
            @else
              <div class="bg-label-secondary rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                <i class='bx bx-image' style="font-size: 48px;"></i>
              </div>
            @endif
          </div>
          <div class="col-md-8">
            <h5 class="mb-2">{{ $tenant->property->title }}</h5>
            <p class="text-muted mb-3">
              <i class='bx bx-map'></i> {{ $tenant->property->address }}
            </p>
            
            <div class="row g-2">
              <div class="col-6">
                <div class="d-flex align-items-center">
                  <i class='bx bx-money text-primary me-2'></i>
                  <div>
                    <small class="text-muted d-block">Monthly Rate</small>
                    <span class="fw-semibold">₱{{ number_format($tenant->property->price, 2) }}</span>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="d-flex align-items-center">
                  <i class='bx bx-door-open text-primary me-2'></i>
                  <div>
                    <small class="text-muted d-block">Available Rooms</small>
                    <span class="fw-semibold">{{ $tenant->property->available_slots ?? 'N/A' }} / {{ $tenant->property->capacity ?? 'N/A' }}</span>
                  </div>
                </div>
              </div>
            </div>

            @if($tenant->property->propertyAmenities && $tenant->property->propertyAmenities->count() > 0)
              <div class="mt-3">
                <small class="text-muted d-block mb-2">Amenities:</small>
                <div class="d-flex flex-wrap gap-1">
                  @foreach($tenant->property->propertyAmenities->take(5) as $amenity)
                    <span class="badge bg-label-info">{{ $amenity->amenity->amenity_name }}</span>
                  @endforeach
                  @if($tenant->property->propertyAmenities->count() > 5)
                    <span class="badge bg-label-secondary">+{{ $tenant->property->propertyAmenities->count() - 5 }} more</span>
                  @endif
                </div>
              </div>
            @endif
          </div>
        </div>

        <div class="mt-3">
          <a href="{{ route('landlord.properties.view', $tenant->property->id) }}" class="btn btn-sm btn-outline-primary">
            <i class='bx bx-show'></i> View Property Details
          </a>
        </div>
      </div>
    </div>

    <!-- Booking Details -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class='bx bx-calendar'></i> Booking Details</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label text-muted small">Booking ID</label>
            <p class="mb-0 fw-semibold">#{{ str_pad($tenant->booking_id, 6, '0', STR_PAD_LEFT) }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">Booking Status</label>
            <p class="mb-0">
              @if($tenant->status === 'Active')
                <span class="badge bg-success">Active</span>
              @elseif($tenant->status === 'Approved')
                <span class="badge bg-info">Approved</span>
              @elseif($tenant->status === 'Completed')
                <span class="badge bg-secondary">Completed</span>
              @elseif($tenant->status === 'Cancelled')
                <span class="badge bg-danger">Cancelled</span>
              @else
                <span class="badge bg-warning">{{ $tenant->status }}</span>
              @endif
            </p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">Booking Date</label>
            <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($tenant->created_at)->format('M d, Y h:i A') }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">Last Updated</label>
            <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($tenant->updated_at)->format('M d, Y h:i A') }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">Move-in Date</label>
            <p class="mb-0 fw-semibold">
              {{ $tenant->move_in_date ? \Carbon\Carbon::parse($tenant->move_in_date)->format('M d, Y') : 'Not set' }}
            </p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">Move-out Date</label>
            <p class="mb-0 fw-semibold">
              {{ $tenant->move_out_date ? \Carbon\Carbon::parse($tenant->move_out_date)->format('M d, Y') : 'Not set' }}
            </p>
          </div>
          @if($tenant->move_in_date && $tenant->move_out_date)
            <div class="col-12">
              <label class="form-label text-muted small">Lease Duration</label>
              <p class="mb-0 fw-semibold">
                {{ \Carbon\Carbon::parse($tenant->move_in_date)->diffInMonths(\Carbon\Carbon::parse($tenant->move_out_date)) }} months
                ({{ \Carbon\Carbon::parse($tenant->move_in_date)->diffInDays(\Carbon\Carbon::parse($tenant->move_out_date)) }} days)
              </p>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Timeline -->
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class='bx bx-time'></i> Activity Timeline</h5>
      </div>
      <div class="card-body">
        <ul class="timeline">
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-primary"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">Booking Created</h6>
                <small class="text-muted">{{ \Carbon\Carbon::parse($tenant->created_at)->format('M d, Y h:i A') }}</small>
              </div>
              <p class="mb-0">Tenant submitted booking request</p>
            </div>
          </li>

          @if($tenant->status !== 'Pending')
            <li class="timeline-item timeline-item-transparent">
              <span class="timeline-point timeline-point-success"></span>
              <div class="timeline-event">
                <div class="timeline-header mb-1">
                  <h6 class="mb-0">Booking Approved</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($tenant->updated_at)->format('M d, Y h:i A') }}</small>
                </div>
                <p class="mb-0">Booking was approved by landlord</p>
              </div>
            </li>
          @endif

          @if($tenant->status === 'Active' || $tenant->status === 'Completed')
            <li class="timeline-item timeline-item-transparent">
              <span class="timeline-point timeline-point-info"></span>
              <div class="timeline-event">
                <div class="timeline-header mb-1">
                  <h6 class="mb-0">Moved In</h6>
                  <small class="text-muted">{{ $tenant->move_in_date ? \Carbon\Carbon::parse($tenant->move_in_date)->format('M d, Y') : 'Not set' }}</small>
                </div>
                <p class="mb-0">Tenant moved into the property</p>
              </div>
            </li>
          @endif

          @if($tenant->status === 'Completed')
            <li class="timeline-item timeline-item-transparent">
              <span class="timeline-point timeline-point-secondary"></span>
              <div class="timeline-event">
                <div class="timeline-header mb-1">
                  <h6 class="mb-0">Lease Completed</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($tenant->updated_at)->format('M d, Y h:i A') }}</small>
                </div>
                <p class="mb-0">Lease period ended</p>
              </div>
            </li>
          @endif

          @if($tenant->status === 'Cancelled')
            <li class="timeline-item timeline-item-transparent">
              <span class="timeline-point timeline-point-danger"></span>
              <div class="timeline-event">
                <div class="timeline-header mb-1">
                  <h6 class="mb-0">Booking Cancelled</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($tenant->updated_at)->format('M d, Y h:i A') }}</small>
                </div>
                <p class="mb-0">Booking was cancelled</p>
              </div>
            </li>
          @endif
        </ul>
      </div>
    </div>
  </div>
</div>

<style>
.timeline {
  position: relative;
  padding-left: 30px;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 8px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #e7e7e7;
}

.timeline-item {
  position: relative;
  padding-bottom: 25px;
  list-style: none;
}

.timeline-item:last-child {
  padding-bottom: 0;
}

.timeline-point {
  position: absolute;
  left: -22px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  border: 3px solid #fff;
  box-shadow: 0 0 0 1px #e7e7e7;
}

.timeline-point-primary {
  background: #696cff;
}

.timeline-point-success {
  background: #71dd37;
}

.timeline-point-info {
  background: #03c3ec;
}

.timeline-point-warning {
  background: #ffab00;
}

.timeline-point-danger {
  background: #ff3e1d;
}

.timeline-point-secondary {
  background: #8592a3;
}

.timeline-event {
  padding-left: 15px;
}
</style>
@endsection