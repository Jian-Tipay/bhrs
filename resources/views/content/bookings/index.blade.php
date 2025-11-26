@extends('layouts/contentNavbarLayout')

@section('title', 'My Bookings')

@section('content')
<div class="container-fluid">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class='bx bx-calendar-check'></i> My Bookings</h4>
    <a href="{{ route('dashboard.user') }}" class="btn btn-outline-secondary">
      <i class='bx bx-home'></i> Back to Dashboard
    </a>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class='bx bx-check-circle me-2'></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class='bx bx-error-circle me-2'></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <!-- Status Filter -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="btn-group" role="group">
        <a href="{{ route('bookings.index') }}" 
           class="btn btn-outline-primary {{ !request('status') ? 'active' : '' }}">
          All Bookings
        </a>
        <a href="{{ route('bookings.index', ['status' => 'Pending']) }}" 
           class="btn btn-outline-warning {{ request('status') == 'Pending' ? 'active' : '' }}">
          Pending
        </a>
        <a href="{{ route('bookings.index', ['status' => 'Approved']) }}" 
           class="btn btn-outline-info {{ request('status') == 'Approved' ? 'active' : '' }}">
          Approved
        </a>
        <a href="{{ route('bookings.index', ['status' => 'Active']) }}" 
           class="btn btn-outline-success {{ request('status') == 'Active' ? 'active' : '' }}">
          Active
        </a>
      </div>
    </div>
  </div>

  @if($bookings->count() > 0)
    @foreach($bookings as $booking)
    <div class="card mb-3">
      <div class="card-body">
        <div class="row">
          <!-- Property Image -->
          <div class="col-md-2 col-12 mb-3 mb-md-0">
            <img src="{{ $booking->property->image ? asset('storage/' . $booking->property->image) : asset('assets/img/boarding/default.jpg') }}" 
                 class="img-fluid rounded" 
                 style="width: 100%; height: 120px; object-fit: cover;"
                 alt="{{ $booking->property->title }}">
          </div>

          <!-- Booking Details -->
          <div class="col-md-7 col-12">
            <h5 class="mb-2">
              <a href="{{ route('properties.view', $booking->property->id) }}" class="text-decoration-none">
                {{ $booking->property->title }}
              </a>
            </h5>
            
            <p class="text-muted mb-2">
              <i class='bx bx-map'></i> {{ $booking->property->address }}
            </p>

            <div class="row g-2 mb-2">
              <div class="col-sm-6">
                <small class="text-muted">
                  <i class='bx bx-calendar'></i> <strong>Move-in:</strong> 
                  {{ $booking->move_in_date->format('M d, Y') }}
                </small>
              </div>
              @if($booking->move_out_date)
              <div class="col-sm-6">
                <small class="text-muted">
                  <i class='bx bx-calendar-x'></i> <strong>Move-out:</strong> 
                  {{ $booking->move_out_date->format('M d, Y') }}
                </small>
              </div>
              @endif
            </div>

            <div class="mb-2">
              <small class="text-muted">
                <i class='bx bx-time'></i> Booked on: {{ $booking->created_at->format('M d, Y h:i A') }}
              </small>
            </div>

            <span class="badge bg-{{ $booking->statusBadge }}">
              <i class='bx {{ $booking->statusIcon }}'></i> {{ $booking->status }}
            </span>
          </div>

          <!-- Actions -->
          <div class="col-md-3 col-12 text-md-end mt-3 mt-md-0">
            <div class="d-flex flex-column gap-2">
              <a href="{{ route('bookings.show', $booking->booking_id) }}" 
                 class="btn btn-sm btn-outline-primary">
                <i class='bx bx-show'></i> View Details
              </a>

              @if(in_array($booking->status, ['Pending', 'Approved']))
              <button type="button" 
                      class="btn btn-sm btn-outline-danger"
                      onclick="cancelBooking({{ $booking->booking_id }})">
                <i class='bx bx-x'></i> Cancel
              </button>
              @endif

              <a href="{{ route('properties.view', $booking->property->id) }}" 
                 class="btn btn-sm btn-outline-secondary">
                <i class='bx bx-building'></i> View Property
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endforeach

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
      {{ $bookings->links() }}
    </div>
  @else
    <div class="card">
      <div class="card-body text-center py-5">
        <i class='bx bx-calendar-x display-1 text-muted mb-3'></i>
        <h5 class="text-muted">No Bookings Found</h5>
        <p class="text-muted mb-4">You haven't made any booking requests yet.</p>
        <a href="{{ route('dashboard.user') }}" class="btn btn-primary">
          <i class='bx bx-search'></i> Browse Properties
        </a>
      </div>
    </div>
  @endif
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
          <p class="text-muted mb-0">This action cannot be undone.</p>
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
.btn-group .btn.active {
  font-weight: 600;
}

.card {
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endsection