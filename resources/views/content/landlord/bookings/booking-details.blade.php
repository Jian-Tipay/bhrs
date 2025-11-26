@extends('layouts/contentNavbarLayout')

@section('title', 'Booking Details')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center">
            <a href="{{ route('landlord.bookings.index') }}" class="btn btn-sm btn-outline-secondary me-3">
              <i class='bx bx-arrow-back'></i>
            </a>
            <div>
              <h4 class="mb-1">Booking Details #{{ $booking->booking_id }}</h4>
              <p class="mb-0 text-muted">Complete information about this booking request</p>
            </div>
          </div>
          <div>
            @if($booking->status == 'Pending')
              <span class="badge bg-warning fs-6 px-3 py-2">
                <i class='bx bx-time'></i> Pending
              </span>
            @elseif($booking->status == 'Approved')
              <span class="badge bg-success fs-6 px-3 py-2">
                <i class='bx bx-check'></i> Approved
              </span>
            @elseif($booking->status == 'Active')
              <span class="badge bg-info fs-6 px-3 py-2">
                <i class='bx bx-user-check'></i> Active
              </span>
            @elseif($booking->status == 'Completed')
              <span class="badge bg-secondary fs-6 px-3 py-2">
                <i class='bx bx-check-circle'></i> Completed
              </span>
            @elseif($booking->status == 'Cancelled')
              <span class="badge bg-danger fs-6 px-3 py-2">
                <i class='bx bx-x'></i> Cancelled
              </span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  @if($booking->status == 'Pending')
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">⚡ Quick Actions</h5>
        <div class="d-flex gap-2">
          <form action="{{ route('landlord.bookings.approve', $booking->booking_id) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success" onclick="return confirm('Approve this booking?')">
              <i class='bx bx-check me-1'></i> Approve Booking
            </button>
          </form>
          <form action="{{ route('landlord.bookings.reject', $booking->booking_id) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this booking?')">
              <i class='bx bx-x me-1'></i> Reject Booking
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
  @endif

  @if($booking->status == 'Approved' || $booking->status == 'Active')
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">⚡ Quick Actions</h5>
        <div class="d-flex gap-2">
          @if($booking->status == 'Active')
          <form action="{{ route('landlord.bookings.complete', $booking->booking_id) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-info" onclick="return confirm('Mark this booking as completed?')">
              <i class='bx bx-check-circle me-1'></i> Complete Booking
            </button>
          </form>
          @endif
          <form action="{{ route('landlord.bookings.cancel', $booking->booking_id) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-warning" onclick="return confirm('Cancel this booking?')">
              <i class='bx bx-x-circle me-1'></i> Cancel Booking
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Tenant Information -->
  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">👤 Tenant Information</h5>
      </div>
      <div class="card-body">
        <div class="text-center mb-3">
          @if($booking->user->profile_picture)
            <img src="{{ asset($booking->user->profile_picture) }}" 
                 alt="{{ $booking->user->first_name ?? $booking->user->name }}" 
                 class="rounded-circle mb-2" 
                 style="width: 80px; height: 80px; object-fit: cover;">
          @else
            <div class="d-flex justify-content-center mb-2">
              <span class="avatar-initial rounded-circle bg-label-primary" 
                    style="width: 80px; height: 80px; font-size: 32px; display: flex; align-items: center; justify-content: center;">
                {{ strtoupper(substr($booking->user->first_name ?? $booking->user->name, 0, 1)) }}
              </span>
            </div>
          @endif
          <h5 class="mb-0">{{ $booking->user->first_name ?? $booking->user->name }} {{ $booking->user->last_name ?? '' }}</h5>
          <p class="text-muted mb-0">{{ $booking->user->email }}</p>
        </div>
        
        <hr class="my-3">
        
        <div class="mb-2">
          <strong>Contact Number:</strong>
          <p class="mb-0">
            @if($booking->user->contact_number)
              <i class='bx bx-phone me-1'></i>{{ $booking->user->contact_number }}
            @else
              <span class="text-muted">N/A</span>
            @endif
          </p>
        </div>
        
        <div class="mb-2">
          <strong>Guardian Number:</strong>
          <p class="mb-0">
            @if($booking->user->guardian_number)
              <i class='bx bx-phone me-1'></i>{{ $booking->user->guardian_number }}
            @else
              <span class="text-muted">N/A</span>
            @endif
          </p>
        </div>

        @if($booking->user->student_number)
        <div class="mb-2">
          <strong>Student Number:</strong>
          <p class="mb-0">{{ $booking->user->student_number }}</p>
        </div>
        @endif

        @if($booking->user->program)
        <div class="mb-2">
          <strong>Program:</strong>
          <p class="mb-0">{{ $booking->user->program }}</p>
        </div>
        @endif

        @if($booking->user->year_level)
        <div class="mb-2">
          <strong>Year Level:</strong>
          <p class="mb-0">{{ $booking->user->year_level }}</p>
        </div>
        @endif

        @if($booking->user->gender)
        <div class="mb-2">
          <strong>Gender:</strong>
          <p class="mb-0">{{ $booking->user->gender }}</p>
        </div>
        @endif
        
        <div class="mb-2">
          <strong>Member Since:</strong>
          <p class="mb-0">{{ $booking->user->created_at->format('M d, Y') }}</p>
        </div>

        <hr class="my-3">

        <div class="d-grid gap-2">
          <a href="mailto:{{ $booking->user->email }}" class="btn btn-outline-primary">
            <i class='bx bx-envelope me-1'></i> Send Email
          </a>
          @if($booking->user->contact_number)
          <a href="tel:{{ $booking->user->contact_number }}" class="btn btn-outline-success">
            <i class='bx bx-phone me-1'></i> Call Tenant
          </a>
          @endif
          @if($booking->user->guardian_number)
          <a href="tel:{{ $booking->user->guardian_number }}" class="btn btn-outline-info">
            <i class='bx bx-phone me-1'></i> Call Guardian
          </a>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Booking & Property Information -->
  <div class="col-lg-8">
    <!-- Property Details -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">🏠 Property Details</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            @if($booking->property->images && $booking->property->images->count() > 0)
              <img src="{{ asset('storage/' . $booking->property->images->first()->image_path) }}" 
                   alt="{{ $booking->property->title }}" 
                   class="rounded w-100"
                   style="height: 200px; object-fit: cover;">
            @else
              <img src="{{ asset('assets/img/boarding/default.jpg') }}" 
                   alt="Default" 
                   class="rounded w-100"
                   style="height: 200px; object-fit: cover;">
            @endif
          </div>
          <div class="col-md-8">
            <h5 class="mb-2">{{ $booking->property->title }}</h5>
            <p class="text-muted mb-3">
              <i class='bx bx-map'></i> {{ $booking->property->address }}
            </p>
            
            <div class="row g-3">
              <div class="col-6">
                <small class="text-muted">Monthly Rent</small>
                <h6 class="mb-0">₱{{ number_format($booking->property->price, 2) }}</h6>
              </div>
              <div class="col-6">
                <small class="text-muted">Available Rooms</small>
                <h6 class="mb-0">{{ $booking->property->available_slots ?? 0 }} / {{ $booking->property->capacity ?? 0 }}</h6>
              </div>
              <div class="col-6">
                <small class="text-muted">Property Type</small>
                <h6 class="mb-0">{{ $booking->property->property_type ?? 'N/A' }}</h6>
              </div>
              <div class="col-6">
                <small class="text-muted">Gender Preference</small>
                <h6 class="mb-0">{{ $booking->property->gender_preference ?? 'N/A' }}</h6>
              </div>
            </div>

            <div class="mt-3">
              <a href="{{ route('properties.view', $booking->property->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                <i class='bx bx-link-external'></i> View Property
              </a>
            </div>
          </div>
        </div>

        @if($booking->property->propertyAmenities && $booking->property->propertyAmenities->count() > 0)
        <hr class="my-3">
        <h6 class="mb-2">✨ Amenities</h6>
        <div class="d-flex flex-wrap gap-2">
          @foreach($booking->property->propertyAmenities as $propertyAmenity)
            <span class="badge bg-label-info">
              <i class='bx bx-check'></i> {{ $propertyAmenity->amenity->amenity_name }}
            </span>
          @endforeach
        </div>
        @endif
      </div>
    </div>

    <!-- Booking Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">📋 Booking Information</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="border rounded p-3">
              <small class="text-muted">Booking ID</small>
              <h6 class="mb-0">#{{ $booking->booking_id }}</h6>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3">
              <small class="text-muted">Date Requested</small>
              <h6 class="mb-0">{{ $booking->created_at->format('M d, Y h:i A') }}</h6>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3">
              <small class="text-muted">Move-in Date</small>
              <h6 class="mb-0">
                @if($booking->move_in_date)
                  {{ \Carbon\Carbon::parse($booking->move_in_date)->format('M d, Y') }}
                @else
                  <span class="text-muted">N/A</span>
                @endif
              </h6>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3">
              <small class="text-muted">Move-out Date</small>
              <h6 class="mb-0">
                @if($booking->move_out_date)
                  {{ \Carbon\Carbon::parse($booking->move_out_date)->format('M d, Y') }}
                @else
                  <span class="text-muted">N/A</span>
                @endif
              </h6>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3">
              <small class="text-muted">Monthly Rent</small>
              <h6 class="mb-0 text-success">₱{{ number_format($booking->property->price, 2) }}</h6>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3">
              <small class="text-muted">Status</small>
              <h6 class="mb-0">
                @if($booking->status == 'Pending')
                  <span class="badge bg-warning">Pending</span>
                @elseif($booking->status == 'Approved')
                  <span class="badge bg-success">Approved</span>
                @elseif($booking->status == 'Active')
                  <span class="badge bg-info">Active</span>
                @elseif($booking->status == 'Completed')
                  <span class="badge bg-secondary">Completed</span>
                @elseif($booking->status == 'Cancelled')
                  <span class="badge bg-danger">Cancelled</span>
                @endif
              </h6>
            </div>
          </div>
        </div>

        @if($booking->notes)
        <hr class="my-3">
        <h6 class="mb-2">📝 Tenant Notes</h6>
        <p class="mb-0">{{ $booking->notes }}</p>
        @endif
      </div>
    </div>

    <!-- Timeline -->
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">📅 Booking Timeline</h5>
      </div>
      <div class="card-body">
        <ul class="timeline mb-0">
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-primary"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">Booking Created</h6>
                <small class="text-muted">{{ $booking->created_at->format('M d, Y h:i A') }}</small>
              </div>
              <p class="mb-0">Booking request submitted by tenant</p>
            </div>
          </li>

          @if($booking->status == 'Approved' || $booking->status == 'Active' || $booking->status == 'Completed')
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-success"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">Booking Approved</h6>
                <small class="text-muted">{{ $booking->updated_at->format('M d, Y h:i A') }}</small>
              </div>
              <p class="mb-0">Booking has been approved by landlord</p>
            </div>
          </li>
          @endif

          @if($booking->status == 'Cancelled')
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-danger"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">Booking Cancelled</h6>
                <small class="text-muted">{{ $booking->updated_at->format('M d, Y h:i A') }}</small>
              </div>
              <p class="mb-0">Booking has been cancelled</p>
            </div>
          </li>
          @endif

          @if($booking->status == 'Completed')
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-info"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">Booking Completed</h6>
                <small class="text-muted">{{ $booking->updated_at->format('M d, Y h:i A') }}</small>
              </div>
              <p class="mb-0">Tenant has completed their stay</p>
            </div>
          </li>
          @endif
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection