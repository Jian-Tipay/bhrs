<!-- Preference Onboarding Modal -->
<div class="modal fade" id="preferenceModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="preferenceForm" action="{{ route('preferences.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">
            <i class='bx bx-home-heart me-2'></i>{{ isset($preferences) && $preferences ? 'Edit' : 'Set' }} Your Preferences
          </h5>
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
                <input type="number" class="form-control" name="budget_min" id="budget_min" 
                       value="{{ isset($preferences) ? $preferences->budget_min : '2000' }}" 
                       placeholder="e.g., 2000" required>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Maximum Budget</label>
              <div class="input-group">
                <span class="input-group-text">₱</span>
                <input type="number" class="form-control" name="budget_max" id="budget_max" 
                       value="{{ isset($preferences) ? $preferences->budget_max : '5000' }}" 
                       placeholder="e.g., 5000" required>
              </div>
            </div>

            <!-- Distance & Room Type -->
            <div class="col-12 mb-3 mt-2">
              <h6 class="mb-3">Location & Room Preferences</h6>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Preferred Distance (km)</label>
              <input type="number" step="0.1" class="form-control" name="preferred_distance" 
                     id="preferred_distance" 
                     value="{{ isset($preferences) ? $preferences->preferred_distance : '2.5' }}" 
                     placeholder="e.g., 2.5" required>
              <div class="form-text">Maximum distance from campus</div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Room Type</label>
              <select class="form-select" name="room_type" required>
                <option value="">Select room type</option>
                <option value="Single" {{ isset($preferences) && $preferences->room_type == 'Single' ? 'selected' : '' }}>Single</option>
                <option value="Shared" {{ isset($preferences) && $preferences->room_type == 'Shared' ? 'selected' : '' }}>Shared</option>
                <option value="Any" {{ isset($preferences) && $preferences->room_type == 'Any' ? 'selected' : '' }}>Any</option>
              </select>
            </div>

            <!-- Gender Preference -->
            <div class="col-md-12 mb-3">
              <label class="form-label">Gender Preference</label>
              <select class="form-select" name="gender_preference" required>
                <option value="">Select gender preference</option>
                <option value="Male Only" {{ isset($preferences) && $preferences->gender_preference == 'Male Only' ? 'selected' : '' }}>Male Only</option>
                <option value="Female Only" {{ isset($preferences) && $preferences->gender_preference == 'Female Only' ? 'selected' : '' }}>Female Only</option>
                <option value="Co-ed" {{ isset($preferences) && $preferences->gender_preference == 'Co-ed' ? 'selected' : '' }}>Co-ed</option>
                <option value="Any" {{ isset($preferences) && $preferences->gender_preference == 'Any' ? 'selected' : '' }}>Any</option>
              </select>
            </div>

            <!-- Amenities -->
            <div class="col-12 mb-3 mt-2">
              <h6 class="mb-3">Preferred Amenities</h6>
              <div id="amenitiesContainer" class="d-flex flex-wrap gap-2">
                @if(isset($amenities) && count($amenities) > 0)
                  @foreach($amenities as $amenity)
                    <div class="form-check me-3">
                      <input class="form-check-input" type="checkbox" name="amenities[]" 
                             value="{{ $amenity->amenity_id }}" 
                             id="amenity_{{ $amenity->amenity_id }}"
                             @if(isset($preferences) && $preferences->preferred_amenities && $preferences->preferred_amenities->contains('amenity_id', $amenity->amenity_id)) checked @endif>
                      <label class="form-check-label" for="amenity_{{ $amenity->amenity_id }}">
                        {{ $amenity->amenity_name }}
                      </label>
                    </div>
                  @endforeach
                @endif
              </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Show modal if user has no preferences
  @if(!isset($preferences) || !$preferences)
    setTimeout(function() {
      const modal = new bootstrap.Modal(document.getElementById('preferenceModal'));
      modal.show();
    }, 500);
  @endif

  // Edit Preferences Button Handler (if you have an edit button)
  document.getElementById('editPreferencesBtn')?.addEventListener('click', function () {
    const modal = new bootstrap.Modal(document.getElementById('preferenceModal'));
    modal.show();
  });

  // Handle form submission with validation
  document.getElementById('preferenceForm')?.addEventListener('submit', function(e) {
    const budgetMin = parseInt(document.getElementById('budget_min').value);
    const budgetMax = parseInt(document.getElementById('budget_max').value);
    
    if (budgetMin >= budgetMax) {
      e.preventDefault();
      alert('Maximum budget must be greater than minimum budget!');
      return false;
    }
  });
});
</script>