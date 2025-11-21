@extends('layouts/contentNavbarLayout')

@section('title', 'Edit My Preferences')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
<style>
  .preference-card {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  .form-label {
    font-weight: 600;
    margin-bottom: 8px;
  }
  .icon-badge {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }
  .amenity-checkbox {
    display: none;
  }
  .amenity-label {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    margin-bottom: 10px;
  }
  .amenity-checkbox:checked + .amenity-label {
    border-color: #696cff;
    background-color: #f0f0ff;
  }
  .amenity-label:hover {
    border-color: #696cff;
  }
  .budget-display {
    font-size: 18px;
    font-weight: 600;
    color: #696cff;
  }
  .section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f0f0f0;
  }
</style>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('page-script')
<script>
  document.addEventListener("DOMContentLoaded", () => {
    // Update budget display
    const budgetMin = document.getElementById('budget_min');
    const budgetMax = document.getElementById('budget_max');
    const budgetDisplay = document.getElementById('budgetDisplay');

    function updateBudgetDisplay() {
      const min = parseInt(budgetMin.value) || 0;
      const max = parseInt(budgetMax.value) || 0;
      budgetDisplay.textContent = `₱${min.toLocaleString()} - ₱${max.toLocaleString()}`;
    }

    budgetMin.addEventListener('input', updateBudgetDisplay);
    budgetMax.addEventListener('input', updateBudgetDisplay);
    updateBudgetDisplay();

    // Update distance display
    const distanceInput = document.getElementById('preferred_distance');
    const distanceDisplay = document.getElementById('distanceDisplay');

    function updateDistanceDisplay() {
      const distance = parseFloat(distanceInput.value) || 0;
      distanceDisplay.textContent = `${distance} km`;
    }

    distanceInput.addEventListener('input', updateDistanceDisplay);
    updateDistanceDisplay();
  });
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{ route('dashboard.user') }}">Dashboard</a>
      </li>
      <li class="breadcrumb-item active">Edit Preferences</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8 mx-auto">
      
      <!-- Header Card -->
      <div class="card mb-4 preference-card">
        <div class="card-body text-center py-4">
          <div class="mb-3">
            <span class="badge bg-label-primary rounded p-3">
              <i class='bx bx-cog bx-lg'></i>
            </span>
          </div>
          <h4 class="mb-1">Update Your Preferences</h4>
          <p class="text-muted mb-0">Help us find the perfect boarding house for you</p>
        </div>
      </div>

      <!-- Preference Form -->
      <form action="{{ route('preferences.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Budget Section -->
        <div class="card mb-4 preference-card">
          <div class="card-body">
            <div class="section-header">
              <div class="icon-badge bg-label-success">
                <i class='bx bx-wallet'></i>
              </div>
              <div>
                <h5 class="mb-0">Budget Range</h5>
                <small class="text-muted">Set your monthly budget</small>
              </div>
            </div>

            <div class="text-center mb-3">
              <div class="budget-display" id="budgetDisplay">
                ₱{{ number_format($preference->budget_min) }} - ₱{{ number_format($preference->budget_max) }}
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="budget_min" class="form-label">Minimum Budget</label>
                <input type="number" class="form-control @error('budget_min') is-invalid @enderror" 
                       id="budget_min" name="budget_min" 
                       value="{{ old('budget_min', $preference->budget_min) }}" 
                       min="1000" max="10000" step="100" required>
                @error('budget_min')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="budget_max" class="form-label">Maximum Budget</label>
                <input type="number" class="form-control @error('budget_max') is-invalid @enderror" 
                       id="budget_max" name="budget_max" 
                       value="{{ old('budget_max', $preference->budget_max) }}" 
                       min="1000" max="10000" step="100" required>
                @error('budget_max')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>

        <!-- Distance Section -->
        <div class="card mb-4 preference-card">
          <div class="card-body">
            <div class="section-header">
              <div class="icon-badge bg-label-info">
                <i class='bx bx-map'></i>
              </div>
              <div>
                <h5 class="mb-0">Distance from Campus</h5>
                <small class="text-muted">Maximum walking distance</small>
              </div>
            </div>

            <div class="text-center mb-3">
              <div class="budget-display" id="distanceDisplay">
                {{ $preference->preferred_distance }} km
              </div>
            </div>

            <div class="mb-3">
              <label for="preferred_distance" class="form-label">Preferred Distance (km)</label>
              <input type="range" class="form-range" 
                     id="preferred_distance" name="preferred_distance" 
                     value="{{ old('preferred_distance', $preference->preferred_distance) }}" 
                     min="0.1" max="5" step="0.1" required>
              @error('preferred_distance')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        <!-- Room Type & Gender Section -->
        <div class="card mb-4 preference-card">
          <div class="card-body">
            <div class="section-header">
              <div class="icon-badge bg-label-warning">
                <i class='bx bx-home'></i>
              </div>
              <div>
                <h5 class="mb-0">Room Preferences</h5>
                <small class="text-muted">Choose your room type and gender preference</small>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="room_type" class="form-label">Room Type</label>
                <select class="form-select @error('room_type') is-invalid @enderror" 
                        id="room_type" name="room_type" required>
                  <option value="">Select room type</option>
                  <option value="Single" {{ old('room_type', $preference->room_type) == 'Single' ? 'selected' : '' }}>Single Room</option>
                  <option value="Shared" {{ old('room_type', $preference->room_type) == 'Shared' ? 'selected' : '' }}>Shared Room</option>
                  <option value="Any" {{ old('room_type', $preference->room_type) == 'Any' ? 'selected' : '' }}>Any</option>
                </select>
                @error('room_type')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="gender_preference" class="form-label">Gender Preference</label>
                <select class="form-select @error('gender_preference') is-invalid @enderror" 
                        id="gender_preference" name="gender_preference" required>
                  <option value="">Select gender preference</option>
                  <option value="Male Only" {{ old('gender_preference', $preference->gender_preference) == 'Male Only' ? 'selected' : '' }}>Male Only</option>
                  <option value="Female Only" {{ old('gender_preference', $preference->gender_preference) == 'Female Only' ? 'selected' : '' }}>Female Only</option>
                  <option value="Co-ed" {{ old('gender_preference', $preference->gender_preference) == 'Co-ed' ? 'selected' : '' }}>Co-ed</option>
                  <option value="Any" {{ old('gender_preference', $preference->gender_preference) == 'Any' ? 'selected' : '' }}>Any</option>
                </select>
                @error('gender_preference')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>

        <!-- Amenities Section -->
        <div class="card mb-4 preference-card">
          <div class="card-body">
            <div class="section-header">
              <div class="icon-badge bg-label-primary">
                <i class='bx bx-star'></i>
              </div>
              <div>
                <h5 class="mb-0">Preferred Amenities</h5>
                <small class="text-muted">Select amenities that matter to you</small>
              </div>
            </div>

            <div class="row">
              @foreach($amenities as $amenity)
                <div class="col-md-6">
                  <input type="checkbox" 
                         class="amenity-checkbox" 
                         id="amenity_{{ $amenity->amenity_id }}" 
                         name="amenities[]" 
                         value="{{ $amenity->amenity_id }}"
                         {{ in_array($amenity->amenity_id, old('amenities', $preference->preferredAmenities->pluck('amenity_id')->toArray())) ? 'checked' : '' }}>
                  <label for="amenity_{{ $amenity->amenity_id }}" class="amenity-label">
                    <i class='bx bx-check-circle me-2' style="font-size: 20px;"></i>
                    <span>{{ $amenity->amenity_name }}</span>
                  </label>
                </div>
              @endforeach
            </div>
            @error('amenities')
              <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="card preference-card">
          <div class="card-body">
            <div class="d-flex gap-2 justify-content-between">
              <a href="{{ route('dashboard.user') }}" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back'></i> Cancel
              </a>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                  <i class='bx bx-trash'></i> Delete Preferences
                </button>
                <button type="submit" class="btn btn-primary">
                  <i class='bx bx-save'></i> Save Changes
                </button>
              </div>
            </div>
          </div>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Preferences?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete your preferences? This action cannot be undone.</p>
        <p class="text-muted small mb-0">You'll need to set up your preferences again to get personalized recommendations.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form action="{{ route('preferences.destroy') }}" method="POST" class="d-inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection