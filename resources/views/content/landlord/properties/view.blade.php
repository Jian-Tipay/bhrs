@extends('layouts/contentNavbarLayout')

@section('title', $property->title . ' - Property Details')

@section('content')
<div class="row">
  <!-- Back Button & Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <a href="{{ route('landlord.properties.index') }}" class="btn btn-sm btn-outline-secondary me-3">
              <i class='bx bx-arrow-back'></i> Back
            </a>
            <div>
              <h4 class="mb-1">{{ $property->title }}</h4>
              <p class="mb-0 text-muted">Property Details & Statistics</p>
            </div>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('landlord.properties.edit', $property->id) }}" class="btn btn-primary">
              <i class='bx bx-edit'></i> Edit Property
            </a>
            <a href="{{ route('landlord.properties.view', $property->id) }}" class="btn btn-outline-primary" target="_blank">
              <i class='bx bx-link-external'></i> Public View
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="col-12 mb-4">
    <div class="row">
      <div class="col-md-3 mb-3">
        <div class="card">
          <div class="card-body text-center">
            <div class="avatar mx-auto mb-2">
              <span class="avatar-initial rounded-circle bg-label-primary">
                <i class='bx bx-calendar-check bx-lg'></i>
              </span>
            </div>
            <h3 class="mb-1">{{ $totalBookings }}</h3>
            <p class="mb-0 text-muted">Total Bookings</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card">
          <div class="card-body text-center">
            <div class="avatar mx-auto mb-2">
              <span class="avatar-initial rounded-circle bg-label-success">
                <i class='bx bx-check-circle bx-lg'></i>
              </span>
            </div>
            <h3 class="mb-1">{{ $activeBookings }}</h3>
            <p class="mb-0 text-muted">Active Tenants</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card">
          <div class="card-body text-center">
            <div class="avatar mx-auto mb-2">
              <span class="avatar-initial rounded-circle bg-label-warning">
                <i class='bx bx-time bx-lg'></i>
              </span>
            </div>
            <h3 class="mb-1">{{ $pendingBookings }}</h3>
            <p class="mb-0 text-muted">Pending Bookings</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card">
          <div class="card-body text-center">
            <div class="avatar mx-auto mb-2">
              <span class="avatar-initial rounded-circle bg-label-info">
                <i class='bx bx-star bx-lg'></i>
              </span>
            </div>
            <h3 class="mb-1">{{ $averageRating ? number_format($averageRating, 1) : 'N/A' }}</h3>
            <p class="mb-0 text-muted">Average Rating</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Property Views Statistics -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">👁️ Property Views</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-3 mb-md-0">
            <div class="d-flex align-items-center">
              <div class="avatar me-3">
                <span class="avatar-initial rounded-circle bg-label-primary">
                  <i class='bx bx-show bx-md'></i>
                </span>
              </div>
              <div>
                <p class="mb-0 text-muted small">Total Views</p>
                <h4 class="mb-0">{{ number_format($totalViews) }}</h4>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3 mb-md-0">
            <div class="d-flex align-items-center">
              <div class="avatar me-3">
                <span class="avatar-initial rounded-circle bg-label-success">
                  <i class='bx bx-user bx-md'></i>
                </span>
              </div>
              <div>
                <p class="mb-0 text-muted small">Unique Viewers</p>
                <h4 class="mb-0">{{ number_format($uniqueViewers) }}</h4>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="d-flex align-items-center">
              <div class="avatar me-3">
                <span class="avatar-initial rounded-circle bg-label-info">
                  <i class='bx bx-trending-up bx-md'></i>
                </span>
              </div>
              <div>
                <p class="mb-0 text-muted small">Views (Last 30 Days)</p>
                <h4 class="mb-0">{{ number_format($recentViews) }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Property Information -->
  <div class="col-md-8 mb-4">
    <!-- Property Image -->
    <div class="card mb-4">
      <div class="card-body p-0">
        @if($property->image && file_exists(public_path('assets/img/boarding/' . $property->image)))
          <img src="{{ asset('assets/img/boarding/' . $property->image) }}" 
              class="img-fluid w-100" 
              alt="{{ $property->title }}"
              style="max-height: 400px; object-fit: cover;">
        @else
          <img src="{{ asset('assets/img/boarding/default.jpg') }}" 
              class="img-fluid w-100" 
              alt="Default"
              style="max-height: 400px; object-fit: cover;">
        @endif
      </div>
    </div>

    <!-- Property Details -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">📋 Property Information</h5>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <p class="mb-2"><strong>Status:</strong> 
              @if($property->is_active && $property->available)
                <span class="badge bg-success">Active & Available</span>
              @elseif($property->is_active && !$property->available)
                <span class="badge bg-warning">Active - Fully Booked</span>
              @else
                <span class="badge bg-secondary">Inactive</span>
              @endif
            </p>
            <p class="mb-2"><strong>Monthly Rate:</strong> ₱{{ number_format($property->price, 2) }}</p>
            <p class="mb-2"><strong>Rooms:</strong> {{ $property->rooms }}</p>
          </div>
          <div class="col-md-6">
            <p class="mb-2"><strong>Capacity:</strong> {{ $property->capacity ?? 'N/A' }} persons</p>
            <p class="mb-2"><strong>Available Slots:</strong> {{ $property->available_slots ?? 0 }}/{{ $property->capacity ?? 0 }}</p>
            <p class="mb-2"><strong>Distance from Campus:</strong> {{ $property->distance_from_campus ? $property->distance_from_campus . ' km' : 'N/A' }}</p>
          </div>
        </div>

        <hr>

        <div class="mb-3">
          <p class="mb-2"><strong><i class='bx bx-map'></i> Address:</strong></p>
          <p class="text-muted">{{ $property->address }}</p>
        </div>

        <div class="mb-3">
          <p class="mb-2"><strong><i class='bx bx-detail'></i> Description:</strong></p>
          <p class="text-muted">{{ $property->description }}</p>
        </div>

        @if($property->house_rules)
        <div class="mb-3">
          <p class="mb-2"><strong><i class='bx bx-list-check'></i> House Rules:</strong></p>
          <p class="text-muted">{{ $property->house_rules }}</p>
        </div>
        @endif

        @if($property->propertyAmenities && $property->propertyAmenities->count() > 0)
        <div>
          <p class="mb-2"><strong><i class='bx bx-trophy'></i> Amenities:</strong></p>
          <div class="d-flex flex-wrap gap-2">
            @foreach($property->propertyAmenities as $propertyAmenity)
              @if($propertyAmenity->amenity)
                <span class="badge bg-label-primary">{{ $propertyAmenity->amenity->amenity_name }}</span>
              @endif
            @endforeach
          </div>
        </div>
        @endif
      </div>
    </div>

    <!-- Reviews Section -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">⭐ Reviews ({{ $totalReviews }})</h5>
        @if($averageRating)
          <div>
            <span class="text-warning">
              @for($i = 1; $i <= 5; $i++)
                @if($i <= floor($averageRating))
                  <i class='bx bxs-star'></i>
                @elseif($i - 0.5 <= $averageRating)
                  <i class='bx bxs-star-half'></i>
                @else
                  <i class='bx bx-star'></i>
                @endif
              @endfor
            </span>
            <span class="ms-2"><strong>{{ number_format($averageRating, 1) }}</strong></span>
          </div>
        @endif
      </div>
      <div class="card-body">
        @forelse($ratings as $rating)
          <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div class="flex-shrink-0">
              <div class="avatar">
                <span class="avatar-initial rounded-circle bg-label-primary">
                  {{ substr($rating->user->name ?? 'U', 0, 1) }}
                </span>
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="mb-1">{{ $rating->user->name ?? 'Anonymous' }}</h6>
              <div class="mb-2">
                <span class="text-warning">
                  @for($i = 1; $i <= 5; $i++)
                    @if($i <= $rating->rating)
                      <i class='bx bxs-star'></i>
                    @else
                      <i class='bx bx-star'></i>
                    @endif
                  @endfor
                </span>
                <small class="text-muted ms-2">{{ $rating->created_at->diffForHumans() }}</small>
              </div>
              @if($rating->review_text)
                <p class="mb-0 text-muted">{{ $rating->review_text }}</p>
              @endif
            </div>
          </div>
        @empty
          <div class="text-center py-4">
            <i class='bx bx-star display-3 text-muted'></i>
            <p class="text-muted mb-0">No reviews yet</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Sidebar -->
  <div class="col-md-4">
    <!-- Owner Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">👤 Owner Information</h5>
      </div>
      <div class="card-body">
        <p class="mb-2"><strong>Name:</strong> {{ $property->owner_name ?? 'N/A' }}</p>
        <p class="mb-2"><strong>Contact:</strong> {{ $property->owner_contact ?? 'N/A' }}</p>
        <p class="mb-0"><strong>Email:</strong> {{ $property->landlord->user->email ?? 'N/A' }}</p>
      </div>
    </div>

    <!-- Recent Bookings -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📅 Recent Bookings</h5>
        <a href="{{ route('landlord.bookings.index') }}" class="btn btn-sm btn-text-primary">View All</a>
      </div>
      <div class="card-body">
        @forelse($recentBookings as $booking)
          <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div class="flex-shrink-0">
              <div class="avatar">
                <span class="avatar-initial rounded-circle bg-label-info">
                  {{ substr($booking->user->name ?? 'U', 0, 1) }}
                </span>
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="mb-1">{{ $booking->user->name ?? 'Unknown' }}</h6>
              <small class="text-muted d-block mb-1">{{ $booking->created_at->format('M d, Y') }}</small>
              <span class="badge 
                @if($booking->status == 'Active') bg-success
                @elseif($booking->status == 'Pending') bg-warning
                @elseif($booking->status == 'Approved') bg-info
                @elseif($booking->status == 'Completed') bg-secondary
                @else bg-danger
                @endif">
                {{ $booking->status }}
              </span>
            </div>
          </div>
        @empty
          <div class="text-center py-3">
            <i class='bx bx-calendar-x display-4 text-muted'></i>
            <p class="text-muted mb-0 mt-2">No bookings yet</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
  // Add any additional scripts here
  console.log('Property view loaded');
</script>
@endsection