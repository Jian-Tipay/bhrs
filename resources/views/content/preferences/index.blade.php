@extends('layouts/contentNavbarLayout')

@section('title', 'My Preferences')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h4 class="mb-1">My Preferences</h4>
            <p class="text-muted mb-0">Manage your accommodation preferences and requirements</p>
          </div>
          <div class="d-flex gap-2">
            @if(isset($preferences) && $preferences)
            <button type="button" class="btn btn-primary" id="editPreferencesBtn">
              <i class='bx bx-edit'></i> Edit Preferences
            </button>
            @endif
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
              <i class='bx bx-arrow-back'></i> Back
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if(isset($preferences) && $preferences)
    <!-- Budget Card -->
    <div class="col-lg-4 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="card-text">Budget Range</p>
              <h4 class="mb-0">₱{{ number_format($preferences->budget_min) }} - ₱{{ number_format($preferences->budget_max) }}</h4>
              <small class="text-muted">Monthly rent budget</small>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-primary rounded p-2">
                <i class='bx bx-money bx-sm'></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Distance Card -->
    <div class="col-lg-4 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="card-text">Preferred Distance</p>
              <h4 class="mb-0">{{ $preferences->preferred_distance }} km</h4>
              <small class="text-muted">Maximum distance from campus</small>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-success rounded p-2">
                <i class='bx bx-map bx-sm'></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Amenities Count Card -->
    <div class="col-lg-4 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="card-text">Selected Amenities</p>
              <h4 class="mb-0">{{ isset($amenities) ? count($amenities) : 0 }}</h4>
              <small class="text-muted">Preferred features</small>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-info rounded p-2">
                <i class='bx bx-list-check bx-sm'></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Preferences Details -->
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Preference Details</h5>
          <span class="badge bg-label-success">Active</span>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- Room Type & Gender Preference -->
            <div class="col-md-6 mb-4">
              <div class="border rounded p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                  <div class="avatar avatar-sm me-2">
                    <span class="avatar-initial rounded bg-label-primary">
                      <i class='bx bx-home'></i>
                    </span>
                  </div>
                  <h6 class="mb-0">Room Preferences</h6>
                </div>
                <div class="mb-2">
                  <small class="text-muted d-block">Room Type</small>
                  <span class="badge bg-primary">{{ $preferences->room_type }}</span>
                </div>
                <div>
                  <small class="text-muted d-block">Gender Preference</small>
                  <span class="badge bg-info">{{ $preferences->gender_preference }}</span>
                </div>
              </div>
            </div>

            <!-- Amenities -->
            <div class="col-md-6 mb-4">
              <div class="border rounded p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                  <div class="avatar avatar-sm me-2">
                    <span class="avatar-initial rounded bg-label-success">
                      <i class='bx bx-check-circle'></i>
                    </span>
                  </div>
                  <h6 class="mb-0">Required Amenities</h6>
                </div>
                @if(isset($amenities) && count($amenities) > 0)
                  <div class="d-flex flex-wrap gap-2">
                    @foreach($amenities as $a)
                      <span class="badge bg-label-success">
                        <i class='bx bx-check'></i> {{ $a->amenity_name }}
                      </span>
                    @endforeach
                  </div>
                @else
                  <p class="text-muted mb-0">No specific amenities required</p>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @else
    <!-- No Preferences Set -->
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="text-center py-5">
            <i class='bx bx-slider-alt bx-lg text-muted'></i>
            <p class="text-muted mt-3 mb-3">You haven't set any preferences yet</p>
            <button type="button" class="btn btn-primary" id="editPreferencesBtn">
              <i class='bx bx-plus'></i> Set Your Preferences
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>

<!-- Edit Preferences Modal -->
<div class="modal fade" id="editPreferencesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="editPreferencesForm">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">{{ isset($preferences) && $preferences ? 'Edit' : 'Set' }} Preferences</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Budget Range -->
            <div class="col-12 mb-3">
              <h6 class="mb-3">Budget Range</h6>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Minimum Budget</label>
              <div class="input-group">
                <span class="input-group-text">₱</span>
                <input type="number" class="form-control" name="budget_min" placeholder="e.g., 2000" required>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Maximum Budget</label>
              <div class="input-group">
                <span class="input-group-text">₱</span>
                <input type="number" class="form-control" name="budget_max" placeholder="e.g., 5000" required>
              </div>
            </div>

            <!-- Distance & Room Type -->
            <div class="col-12 mb-3 mt-2">
              <h6 class="mb-3">Location & Room Preferences</h6>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Preferred Distance (km)</label>
              <input type="number" step="0.1" class="form-control" name="preferred_distance" placeholder="e.g., 2.5" required>
              <div class="form-text">Maximum distance from campus</div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Room Type</label>
              <select class="form-select" name="room_type" required>
                <option value="">Select room type</option>
                <option value="Single">Single</option>
                <option value="Shared">Shared</option>
                <option value="Any">Any</option>
              </select>
            </div>

            <!-- Gender Preference -->
            <div class="col-md-12 mb-3">
              <label class="form-label">Gender Preference</label>
              <select class="form-select" name="gender_preference" required>
                <option value="">Select gender preference</option>
                <option value="Male Only">Male Only</option>
                <option value="Female Only">Female Only</option>
                <option value="Co-ed">Co-ed</option>
                <option value="Any">Any</option>
              </select>
            </div>

            <!-- Amenities -->
            <div class="col-12 mb-3 mt-2">
              <h6 class="mb-3">Preferred Amenities</h6>
              <div id="amenitiesContainer" class="d-flex flex-wrap gap-2"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class='bx bx-save'></i> Save Preferences
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
@if(session('success'))
  alert('{{ session('success') }}');
@endif

@if(session('error'))
  alert('{{ session('error') }}');
@endif

@if($errors->any())
  alert('{{ $errors->first() }}');
@endif

document.getElementById('editPreferencesBtn')?.addEventListener('click', function () {
    fetch('/preferences/edit')
        .then(response => response.json())
        .then(data => {
            if (!data.success) return alert(data.message);

            const pref = data.preference;
            const amenities = data.amenities;

            // Fill inputs
            document.querySelector('[name="budget_min"]').value = pref.budget_min;
            document.querySelector('[name="budget_max"]').value = pref.budget_max;
            document.querySelector('[name="preferred_distance"]').value = pref.preferred_distance;
            document.querySelector('[name="room_type"]').value = pref.room_type;
            document.querySelector('[name="gender_preference"]').value = pref.gender_preference;

            // Fill amenities
            const container = document.getElementById('amenitiesContainer');
            container.innerHTML = '';
            amenities.forEach(a => {
                const checked = pref.preferred_amenities.some(p => p.amenity_id === a.amenity_id);
                container.innerHTML += `
                    <div class="form-check me-3">
                      <input class="form-check-input" type="checkbox" name="amenities[]" value="${a.amenity_id}" id="amenity_${a.amenity_id}" ${checked ? 'checked' : ''}>
                      <label class="form-check-label" for="amenity_${a.amenity_id}">${a.amenity_name}</label>
                    </div>`;
            });

            new bootstrap.Modal(document.getElementById('editPreferencesModal')).show();
        })
        .catch(() => alert('Failed to load preferences.'));
});

document.getElementById('editPreferencesForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('/preferences', {
        method: 'POST',
        headers: { 'X-HTTP-Method-Override': 'PUT' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(() => alert('Error updating preferences.'));
});
</script>
@endsection