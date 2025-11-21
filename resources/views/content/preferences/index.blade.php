@extends('layouts/contentNavbarLayout')

@section('title', 'My Preferences')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">My Preferences</h4>

    @if($preferences)
    <div class="card p-3 mb-3">
        <p><strong>Budget:</strong> ₱{{ $preferences->budget_min }} - ₱{{ $preferences->budget_max }}</p>
        <p><strong>Preferred Distance:</strong> {{ $preferences->preferred_distance }} km</p>
        <p><strong>Room Type:</strong> {{ $preferences->room_type }}</p>
        <p><strong>Gender Preference:</strong> {{ $preferences->gender_preference }}</p>
        <p><strong>Amenities:</strong>
            @forelse($amenities as $a)
                <span class="badge bg-primary">{{ $a->amenity_name }}</span>
            @empty
                None
            @endforelse
        </p>

        <button class="btn btn-primary mt-2" id="editPreferencesBtn">Edit Preferences</button>
    </div>
    @else
    <div class="alert alert-info">You haven’t set any preferences yet.</div>
    @endif
</div>

<!-- 🧩 Modal -->
<div class="modal fade" id="editPreferencesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Preferences</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editPreferencesForm">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Budget Min</label>
              <input type="number" class="form-control" name="budget_min" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Budget Max</label>
              <input type="number" class="form-control" name="budget_max" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Preferred Distance (km)</label>
              <input type="number" step="0.1" class="form-control" name="preferred_distance" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Room Type</label>
              <select class="form-select" name="room_type" required>
                <option value="Single">Single</option>
                <option value="Shared">Shared</option>
                <option value="Any">Any</option>
              </select>
            </div>
            <div class="col-md-12 mb-3">
              <label>Gender Preference</label>
              <select class="form-select" name="gender_preference" required>
                <option value="Male Only">Male Only</option>
                <option value="Female Only">Female Only</option>
                <option value="Co-ed">Co-ed</option>
                <option value="Any">Any</option>
              </select>
            </div>

            <div class="col-md-12">
              <label>Amenities</label>
              <div id="amenitiesContainer" class="d-flex flex-wrap gap-2 mt-2"></div>
            </div>
          </div>

          <div class="mt-3 text-end">
            <button type="submit" class="btn btn-success">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('page-script')
<script>
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
