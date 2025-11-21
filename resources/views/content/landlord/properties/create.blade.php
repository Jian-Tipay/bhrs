@extends('layouts/contentNavbarLayout')

@section('title', 'Add New Property')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <a href="{{ route('landlord.properties.show') }}" class="btn btn-sm btn-outline-secondary me-3">
            <i class='bx bx-arrow-back'></i>
          </a>
          <div>
            <h4 class="mb-1">➕ Add New Property</h4>
            <p class="mb-0 text-muted">Fill in the details to list your property</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Display Success/Error Messages -->
  @if (session('success'))
    <div class="col-12 mb-3">
      <div class="alert alert-success alert-dismissible" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  @endif

  @if (session('error'))
    <div class="col-12 mb-3">
      <div class="alert alert-danger alert-dismissible" role="alert">
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  @endif

  <!-- Display Validation Errors -->
  @if ($errors->any())
    <div class="col-12 mb-3">
      <div class="alert alert-danger alert-dismissible" role="alert">
        <h6 class="alert-heading mb-2"><i class='bx bx-error'></i> Please fix the following errors:</h6>
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  @endif

  <!-- Form -->
  <div class="col-12">
    <form action="{{ route('landlord.properties.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      
      <!-- Basic Information -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">📝 Basic Information</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label">Property Title *</label>
              <input type="text" class="form-control @error('title') is-invalid @enderror" 
                     name="title" 
                     value="{{ old('title') }}"
                     placeholder="e.g., Cozy Boarding House near University" 
                     required>
              @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-12">
              <label class="form-label">Description *</label>
              <textarea class="form-control @error('description') is-invalid @enderror" 
                        name="description" 
                        rows="4" 
                        placeholder="Describe your property..." 
                        required>{{ old('description') }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-12">
              <label class="form-label">Complete Address *</label>
              <input type="text" class="form-control @error('address') is-invalid @enderror" 
                     name="address" 
                     value="{{ old('address') }}"
                     placeholder="Street, Barangay, City" 
                     required>
              @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      <!-- Location Details (Optional) -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">📍 Location Coordinates (Optional)</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Latitude</label>
              <input type="text" class="form-control @error('latitude') is-invalid @enderror" 
                     name="latitude" 
                     value="{{ old('latitude') }}"
                     placeholder="e.g., 10.3157">
              @error('latitude')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">You can find this on Google Maps</small>
            </div>

            <div class="col-md-4">
              <label class="form-label">Longitude</label>
              <input type="text" class="form-control @error('longitude') is-invalid @enderror" 
                     name="longitude" 
                     value="{{ old('longitude') }}"
                     placeholder="e.g., 123.8854">
              @error('longitude')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label">Distance from Campus (km)</label>
              <input type="number" step="0.01" class="form-control @error('distance_from_campus') is-invalid @enderror" 
                     name="distance_from_campus" 
                     value="{{ old('distance_from_campus') }}"
                     placeholder="e.g., 1.5">
              @error('distance_from_campus')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      <!-- Pricing & Capacity -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">💰 Pricing & Capacity</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Monthly Price (₱) *</label>
              <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                     name="price" 
                     value="{{ old('price') }}"
                     placeholder="e.g., 3500" 
                     required>
              @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label">Number of Rooms *</label>
              <input type="number" class="form-control @error('rooms') is-invalid @enderror" 
                     name="rooms" 
                     value="{{ old('rooms') }}"
                     placeholder="e.g., 5" 
                     required>
              @error('rooms')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label">Total Capacity</label>
              <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                     name="capacity" 
                     value="{{ old('capacity') }}"
                     placeholder="e.g., 10">
              @error('capacity')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Total number of tenants</small>
            </div>

            <div class="col-md-4">
              <label class="form-label">Available Slots</label>
              <input type="number" class="form-control @error('available_slots') is-invalid @enderror" 
                     name="available_slots" 
                     value="{{ old('available_slots') }}"
                     placeholder="e.g., 10">
              @error('available_slots')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="available" id="available" 
                       {{ old('available', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="available">
                  Property is currently available for booking
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Amenities -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">✨ Amenities</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            @foreach($amenities as $amenity)
              <div class="col-md-3">
                <div class="form-check">
                  <input class="form-check-input" 
                         type="checkbox" 
                         name="amenities[]" 
                         value="{{ $amenity->amenity_id }}" 
                         id="amenity_{{ $amenity->amenity_id }}"
                         {{ in_array($amenity->amenity_id, old('amenities', [])) ? 'checked' : '' }}>
                  <label class="form-check-label" for="amenity_{{ $amenity->amenity_id }}">
                    {{ $amenity->amenity_name }}
                  </label>
                </div>
              </div>
            @endforeach
          </div>
          @if($amenities->isEmpty())
            <p class="text-muted mb-0">No amenities available in the system.</p>
          @endif
        </div>
      </div>

      <!-- Owner Information (Optional) -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">👤 Owner Information (Optional)</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Owner Name</label>
              <input type="text" class="form-control @error('owner_name') is-invalid @enderror" 
                     name="owner_name" 
                     value="{{ old('owner_name', Auth::user()->name) }}"
                     placeholder="Property owner name">
              @error('owner_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Contact Number</label>
              <input type="text" class="form-control @error('owner_contact') is-invalid @enderror" 
                     name="owner_contact" 
                     value="{{ old('owner_contact') }}"
                     placeholder="e.g., 09123456789">
              @error('owner_contact')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-12">
              <label class="form-label">House Rules</label>
              <textarea class="form-control @error('house_rules') is-invalid @enderror" 
                        name="house_rules" 
                        rows="4" 
                        placeholder="Enter house rules (e.g., curfew time, visitor policy, etc.)">{{ old('house_rules') }}</textarea>
              @error('house_rules')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      <!-- Property Image -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">📷 Property Image</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Upload Property Image</label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                   name="image" 
                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
            @error('image')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF, WEBP. Max size: 2MB</small>
          </div>
        </div>
      </div>

      <!-- Submit Buttons -->
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('landlord.properties.show') }}" class="btn btn-outline-secondary">
              <i class='bx bx-x'></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
              <i class='bx bx-save'></i> Save Property
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('page-script')
<script>
  // Auto-calculate available slots if capacity changes
  document.querySelector('input[name="capacity"]')?.addEventListener('input', function(e) {
    const availableSlotsInput = document.querySelector('input[name="available_slots"]');
    if (availableSlotsInput && !availableSlotsInput.value) {
      availableSlotsInput.value = e.target.value;
    }
  });

  // Auto-dismiss alerts after 5 seconds
  setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    });
  }, 5000);
</script>
@endsection