@if ($bookings->isEmpty())
  <div class="alert alert-info text-center">No bookings found for this category.</div>
@else
  <div class="row">
    @foreach ($bookings as $booking)
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card booking-card shadow-sm">
          <div class="card-body">
            <h5 class="fw-bold mb-2">{{ $booking->property->title ?? 'Unnamed Property' }}</h5>
            <p class="booking-status status-{{ $booking->status }}">Status: {{ $booking->status }}</p>
            <hr>
            <p class="mb-1"><strong>Check-in:</strong> {{ $booking->move_in_date ?? '—' }}</p>
            <p class="mb-1"><strong>Check-out:</strong> {{ $booking->move_out_date ?? '—' }}</p>
            <p class="mb-0"><strong>Total:</strong> ₱{{ number_format($booking->property->price ?? 0, 2) }}</p>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif
