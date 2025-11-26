@extends('layouts/contentNavbarLayout')

@section('title', 'Booking Details #' . $booking->booking_id)

@section('content')
<div class="container-fluid">
  <!-- Back Button -->
  <div class="mb-3">
    <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-secondary">
      <i class='bx bx-arrow-back'></i> Back to My Bookings
    </a>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class='bx bx-check-circle me-2'></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="row">
    <!-- Main Booking Details -->
    <div class="col-lg-8">
      <!-- Status Card -->
      <div class="card mb-4 border-{{ $booking->statusBadge }}">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-1">Booking #{{ $booking->booking_id }}</h5>
              <small class="text-muted">
                Created on {{ $booking->created_at->format('F d, Y h:i A') }}
              </small>
            </div>
            <div>
              <span class="badge bg-{{ $booking->statusBadge }} fs-6">
                <i class='bx {{ $booking->statusIcon }}'></i> {{ $booking->status }}
              </span>
            </div>
          </div>

          <!-- Status Timeline -->
          <div class="mt-4">
            <div class="timeline-container">
              <div class="timeline-item {{ in_array($booking->status, ['Pending', 'Approved', 'Active', 'Completed']) ? 'active' : '' }}">
                <div class="timeline-badge">
                  <i class='bx bx-send'></i>
                </div>
                <div class="timeline-content">
                  <h6>Request Submitted</h6>
                  <small>{{ $booking->created_at->format('M d, Y h:i A') }}</small>
                </div>
              </div>

              <div class="timeline-item {{ in_array($booking->status, ['Approved', 'Active', 'Completed']) ? 'active' : '' }}">
                <div class="timeline-badge">
                  <i class='bx bx-check-circle'></i>
                </div>
                <div class="timeline-content">
                  <h6>Approved by Landlord</h6>
                  <small>{{ $booking->status === 'Pending' ? 'Waiting for approval' : ($booking->updated_at->format('M d, Y h:i A')) }}</small>
                </div>
              </div>

              <div class="timeline-item {{ in_array($booking->status, ['Active', 'Completed']) ? 'active' : '' }}">
                <div class="timeline-badge">
                  <i class='bx bx-home-circle'></i>
                </div>
                <div class="timeline-content">
                  <h6>Move-in / Active</h6>
                  <small>{{ $booking->status === 'Active' || $booking->status === 'Completed' ? $booking->move_in_date->format('M d, Y') : 'Pending move-in' }}</small>
                </div>
              </div>

              <div class="timeline-item {{ $booking->status === 'Completed' ? 'active' : '' }}">
                <div class="timeline-badge">
                  <i class='bx bx-check-double'></i>
                </div>
                <div class="timeline-content">
                  <h6>Completed</h6>
                  <small>{{ $booking->status === 'Completed' ? ($booking->move_out_date ? $booking->move_out_date->format('M d, Y') : 'Completed') : 'Future' }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Property Details -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Property Information</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 mb-3">
              <img src="{{ $booking->property->image ? asset('storage/' . $booking->property->image) : asset('assets/img/boarding/default.jpg') }}" 
                   class="img-fluid rounded" 
                   alt="{{ $booking->property->title }}">
            </div>
            <div class="col-md-8">
              <h5 class="mb-3">{{ $booking->property->title }}</h5>
              
              <p class="mb-2">
                <i class='bx bx-map text-primary'></i>
                <strong>Address:</strong> {{ $booking->property->address }}
              </p>

              <p class="mb-2">
                <i class='bx bx-money text-success'></i>
                <strong>Monthly Rent:</strong> ₱{{ number_format($booking->property->price, 2) }}
              </p>

              @if($booking->property->distance_from_campus)
              <p class="mb-2">
                <i class='bx bx-map-pin text-danger'></i>
                <strong>Distance from Campus:</strong> {{ $booking->property->distance_from_campus }} km
              </p>
              @endif

              <a href="{{ route('properties.view', $booking->property->id) }}" 
                 class="btn btn-sm btn-outline-primary mt-2">
                <i class='bx bx-show'></i> View Full Property Details
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Booking Dates -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Booking Schedule</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="d-flex align-items-center mb-3">
                <div class="avatar flex-shrink-0 me-3">
                  <span class="avatar-initial rounded bg-label-success">
                    <i class='bx bx-calendar-check'></i>
                  </span>
                </div>
                <div>
                  <small class="text-muted d-block">Move-in Date</small>
                  <h6 class="mb-0">{{ $booking->move_in_date->format('F d, Y') }}</h6>
                  <small class="text-muted">{{ $booking->move_in_date->diffForHumans() }}</small>
                </div>
              </div>
            </div>

            @if($booking->move_out_date)
            <div class="col-md-6">
              <div class="d-flex align-items-center mb-3">
                <div class="avatar flex-shrink-0 me-3">
                  <span class="avatar-initial rounded bg-label-danger">
                    <i class='bx bx-calendar-x'></i>
                  </span>
                </div>
                <div>
                  <small class="text-muted d-block">Move-out Date</small>
                  <h6 class="mb-0">{{ $booking->move_out_date->format('F d, Y') }}</h6>
                  <small class="text-muted">{{ $booking->move_out_date->diffForHumans() }}</small>
                </div>
              </div>
            </div>
            @endif
          </div>

          @if($booking->move_in_date && $booking->move_out_date)
          <div class="alert alert-info mb-0 mt-3">
            <i class='bx bx-info-circle'></i>
            <strong>Duration:</strong> {{ $booking->move_in_date->diffInDays($booking->move_out_date) }} days
            ({{ number_format($booking->move_in_date->diffInMonths($booking->move_out_date), 1) }} months)
          </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <!-- Landlord Contact -->
      <div class="card mb-4">
        <div class="card-header bg-primary">
          <h5 class="mb-0 text-white">Landlord Information</h5>
        </div>
        <div class="card-body">
          <div class="text-center mb-3">
            <div class="avatar avatar-xl mb-3">
              <span class="avatar-initial rounded-circle bg-label-primary">
                <i class='bx bx-user' style="font-size: 2rem;"></i>
              </span>
            </div>
            <h6 class="mb-1">{{ $booking->property->owner_name ?? $booking->property->landlord->user->name }}</h6>
            <small class="text-muted">Property Owner</small>
          </div>

          @if($booking->property->owner_contact)
          <div class="d-grid gap-2">
            <a href="tel:{{ $booking->property->owner_contact }}" class="btn btn-outline-primary btn-sm">
              <i class='bx bx-phone'></i> {{ $booking->property->owner_contact }}
            </a>
          </div>
          @endif

          @if($booking->status !== 'Cancelled')
          <div class="alert alert-info mt-3 mb-0">
            <small>
              <i class='bx bx-info-circle'></i>
              Contact the landlord if you have any questions about your booking.
            </small>
          </div>
          @endif
        </div>
      </div>

      <!-- Actions Card -->
      @if(in_array($booking->status, ['Pending', 'Approved']))
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Actions</h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <button type="button" 
                    class="btn btn-danger"
                    onclick="cancelBooking({{ $booking->booking_id }})">
              <i class='bx bx-x-circle'></i> Cancel Booking
            </button>

            <a href="{{ route('properties.view', $booking->property->id) }}" 
               class="btn btn-outline-secondary">
              <i class='bx bx-building'></i> View Property
            </a>
          </div>

          <div class="alert alert-warning mt-3 mb-0">
            <small>
              <i class='bx bx-info-circle'></i>
              @if($booking->status === 'Pending')
                Your booking is pending landlord approval.
              @else
                Your booking is approved! The landlord will contact you soon.
              @endif
            </small>
          </div>
        </div>
      </div>
      @endif

      @if($booking->status === 'Active')
      <div class="card mb-4 border-success">
        <div class="card-body text-center">
          <i class='bx bx-home-heart text-success' style="font-size: 3rem;"></i>
          <h6 class="mt-3">You're all set!</h6>
          <p class="mb-0 small text-muted">
            Enjoy your stay at {{ $booking->property->title }}
          </p>
        </div>
      </div>
      @endif

      @if($booking->status === 'Completed')
      <div class="card mb-4 border-secondary">
        <div class="card-body">
          <h6 class="mb-3">Leave a Review</h6>
          <p class="small text-muted">Help other students by sharing your experience!</p>
          <a href="{{ route('properties.view', $booking->property->id) }}#reviews" 
             class="btn btn-warning btn-sm w-100">
            <i class='bx bx-star'></i> Rate Property
          </a>
        </div>
      </div>
      @endif

      <!-- Help Card -->
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0"><i class='bx bx-help-circle'></i> Need Help?</h6>
        </div>
        <div class="card-body">
          <p class="small mb-2">Having issues with your booking?</p>
          <a href="#" class="btn btn-sm btn-outline-primary w-100">
            <i class='bx bx-support'></i> Contact Support
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Cancel Booking Modal -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="cancelBookingForm" method="POST">
        @csrf
        @method('PUT')
        
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-white">
            <i class='bx bx-error-circle'></i> Cancel Booking
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        
        <div class="modal-body text-center py-4">
          <i class='bx bx-error-circle text-danger' style="font-size: 60px;"></i>
          <h6 class="mt-3">Are you sure you want to cancel this booking?</h6>
          <p class="text-muted">This action cannot be undone. The landlord will be notified.</p>
          
          @if($booking->status === 'Approved')
          <div class="alert alert-warning">
            <small>
              <i class='bx bx-info-circle'></i>
              This booking is already approved. Please contact the landlord before cancelling.
            </small>
          </div>
          @endif
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            No, Keep It
          </button>
          <button type="submit" class="btn btn-danger">
            Yes, Cancel Booking
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function cancelBooking(bookingId) {
  const modal = new bootstrap.Modal(document.getElementById('cancelBookingModal'));
  const form = document.getElementById('cancelBookingForm');
  form.action = `/bookings/${bookingId}/cancel`;
  modal.show();
}
</script>

<style>
/* Timeline Styles */
.timeline-container {
  position: relative;
  padding: 20px 0;
}

.timeline-item {
  position: relative;
  padding-left: 60px;
  margin-bottom: 30px;
  opacity: 0.5;
}

.timeline-item.active {
  opacity: 1;
}

.timeline-item:not(:last-child)::before {
  content: '';
  position: absolute;
  left: 19px;
  top: 40px;
  height: calc(100% + 10px);
  width: 2px;
  background: #ddd;
}

.timeline-item.active:not(:last-child)::before {
  background: #696cff;
}

.timeline-badge {
  position: absolute;
  left: 0;
  top: 0;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f5f5f5;
  border: 2px solid #ddd;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: #999;
}

.timeline-item.active .timeline-badge {
  background: #696cff;
  border-color: #696cff;
  color: white;
}

.timeline-content h6 {
  margin-bottom: 5px;
  font-size: 14px;
}

.timeline-content small {
  color: #999;
}

.card-header.bg-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}
</style>
@endsection