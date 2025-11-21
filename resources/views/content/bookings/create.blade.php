@extends('layouts/contentNavbarLayout')

@section('title', 'Book Property - ' . $property->title)

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12 mb-3">
      <a href="{{ route('properties.show', $property->id) }}" class="btn btn-sm btn-outline-secondary">
        <i class='bx bx-arrow-back'></i> Back to Property
      </a>
    </div>

    <div class="col-lg-8">
      <!-- Booking Form -->
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0"><i class='bx bx-calendar-check'></i> Book This Property</h4>
        </div>
        <div class="card-body">
          <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
            @csrf
            <input type="hidden" name="property_id" value="{{ $property->id }}">

            <!-- Move-in Date -->
            <div class="mb-4">
              <label class="form-label fw-bold">
                <i class='bx bx-calendar'></i> Move-in Date
                <span class="text-danger">*</span>
              </label>
              <input type="date" 
                     class="form-control @error('move_in_date') is-invalid @enderror" 
                     name="move_in_date" 
                     min="{{ date('Y-m-d') }}"
                     value="{{ old('move_in_date') }}"
                     required>
              @error('move_in_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Select when you plan to move in</small>
            </div>

            <!-- Move-out Date (Optional) -->
            <div class="mb-4">
              <label class="form-label fw-bold">
                <i class='bx bx-calendar-x'></i> Move-out Date (Optional)
              </label>
              <input type="date" 
                     class="form-control @error('move_out_date') is-invalid @enderror" 
                     name="move_out_date" 
                     value="{{ old('move_out_date') }}"
                     id="moveOutDate">
              @error('move_out_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Leave empty if you don't have a specific move-out date</small>
            </div>

            <!-- Additional Message (Optional) -->
            <div class="mb-4">
              <label class="form-label fw-bold">
                <i class='bx bx-message-dots'></i> Message to Landlord (Optional)
              </label>
              <textarea class="form-control @error('message') is-invalid @enderror" 
                        name="message" 
                        rows="4" 
                        maxlength="1000"
                        placeholder="Any questions or special requests for the landlord...">{{ old('message') }}</textarea>
              @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted"><span id="msgCharCount">0</span>/1000 characters</small>
            </div>

            <!-- Important Notice -->
            <div class="alert alert-info">
              <h6 class="alert-heading"><i class='bx bx-info-circle'></i> Important Information</h6>
              <ul class="mb-0 ps-3">
                <li>Your booking request will be sent to the landlord for approval</li>
                <li>You will be notified once the landlord responds</li>
                <li>Monthly rent: <strong>₱{{ number_format($property->price, 2) }}</strong></li>
                <li>Available slots: <strong>{{ $property->available_slots }}</strong></li>
              </ul>
            </div>

            <!-- Terms & Conditions -->
            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" id="agreeTerms" required>
              <label class="form-check-label" for="agreeTerms">
                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
                <span class="text-danger">*</span>
              </label>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class='bx bx-send'></i> Submit Booking Request
              </button>
              <a href="{{ route('properties.show', $property->id) }}" class="btn btn-outline-secondary">
                <i class='bx bx-x'></i> Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Property Summary Sidebar -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header bg-primary">
          <h5 class="mb-0 text-white">Property Summary</h5>
        </div>
        <div class="card-body">
          @if($property->image)
            <img src="{{ asset('storage/' . $property->image) }}" 
                 class="img-fluid rounded mb-3" 
                 alt="{{ $property->title }}">
          @endif

          <h5 class="mb-3">{{ $property->title }}</h5>
          
          <div class="mb-2">
            <i class='bx bx-map text-primary'></i>
            <small>{{ $property->address }}</small>
          </div>

          <div class="mb-2">
            <i class='bx bx-money text-success'></i>
            <strong>₱{{ number_format($property->price, 2) }}</strong> / month
          </div>

          @if($property->distance_from_campus)
          <div class="mb-2">
            <i class='bx bx-map-pin text-danger'></i>
            {{ $property->distance_from_campus }} km from campus
          </div>
          @endif

          <div class="mb-2">
            <i class='bx bx-check-circle text-success'></i>
            {{ $property->available_slots }} slots available
          </div>

          <hr>

          <h6 class="mb-2">Contact Owner</h6>
          @if($property->owner_name)
            <p class="mb-1"><i class='bx bx-user'></i> {{ $property->owner_name }}</p>
          @endif
          @if($property->owner_contact)
            <p class="mb-0"><i class='bx bx-phone'></i> {{ $property->owner_contact }}</p>
          @endif
        </div>
      </div>

      <!-- Booking Tips -->
      <div class="card mt-3">
        <div class="card-header">
          <h6 class="mb-0"><i class='bx bx-bulb'></i> Booking Tips</h6>
        </div>
        <div class="card-body">
          <ul class="mb-0 ps-3 small">
            <li>Plan your move-in date in advance</li>
            <li>Be courteous in your message to the landlord</li>
            <li>Ask any questions you have about the property</li>
            <li>Check the property rules before booking</li>
            <li>Visit the property in person if possible</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Terms and Conditions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <h6>Booking Agreement</h6>
        <p>By submitting a booking request, you agree to the following terms:</p>
        
        <ol>
          <li><strong>Booking Process:</strong> Your booking is subject to landlord approval. A confirmed booking does not guarantee occupancy until approved.</li>
          
          <li><strong>Payment:</strong> Payment terms will be discussed directly with the landlord upon approval.</li>
          
          <li><strong>Cancellation:</strong> You may cancel pending or approved bookings. Active bookings require coordination with the landlord.</li>
          
          <li><strong>Property Rules:</strong> You must comply with all house rules set by the landlord.</li>
          
          <li><strong>Damages:</strong> You are responsible for any damages to the property during your stay.</li>
          
          <li><strong>Personal Information:</strong> Your contact information will be shared with the landlord.</li>
          
          <li><strong>Verification:</strong> The landlord may require identity verification before approval.</li>
          
          <li><strong>Disputes:</strong> Any disputes should be resolved directly with the landlord or through proper channels.</li>
        </ol>

        <p class="mb-0"><small class="text-muted">Last updated: {{ date('F d, Y') }}</small></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Move-in date validation
  const moveInDate = document.querySelector('input[name="move_in_date"]');
  const moveOutDate = document.getElementById('moveOutDate');

  moveInDate.addEventListener('change', function() {
    // Set min date for move-out to be after move-in
    if (this.value) {
      const minMoveOut = new Date(this.value);
      minMoveOut.setDate(minMoveOut.getDate() + 1);
      moveOutDate.min = minMoveOut.toISOString().split('T')[0];
      
      // Clear move-out if it's before move-in
      if (moveOutDate.value && new Date(moveOutDate.value) <= new Date(this.value)) {
        moveOutDate.value = '';
      }
    }
  });

  // Character counter for message
  const messageTextarea = document.querySelector('textarea[name="message"]');
  const charCount = document.getElementById('msgCharCount');

  if (messageTextarea && charCount) {
    // Initial count
    charCount.textContent = messageTextarea.value.length;

    messageTextarea.addEventListener('input', function() {
      const length = this.value.length;
      charCount.textContent = length;
      
      if (length >= 1000) {
        charCount.style.color = '#ff3e1d';
      } else if (length >= 800) {
        charCount.style.color = '#ff9f43';
      } else {
        charCount.style.color = '#696cff';
      }
    });
  }

  // Form submission
  const bookingForm = document.getElementById('bookingForm');
  bookingForm.addEventListener('submit', function(e) {
    const agreeTerms = document.getElementById('agreeTerms');
    
    if (!agreeTerms.checked) {
      e.preventDefault();
      alert('Please agree to the Terms and Conditions to continue.');
      return false;
    }

    // Confirm submission
    if (!confirm('Are you sure you want to submit this booking request?')) {
      e.preventDefault();
      return false;
    }
  });
});
</script>

<style>
.card-header.bg-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.form-label.fw-bold i {
  color: #696cff;
}

.alert-info {
  border-left: 4px solid #03c3ec;
}
</style>
@endsection