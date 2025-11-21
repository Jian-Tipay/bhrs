<!-- Preference Onboarding Modal -->
<div class="modal fade" id="preferenceModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class='bx bx-home-heart me-2'></i>Welcome! Let's Find Your Perfect Boarding House
        </h5>
      </div>
      <div class="modal-body p-4">
        <form id="preferenceForm" action="{{ route('preferences.store') }}" method="POST">
          @csrf
          
          <!-- Step Indicator -->
          <div class="mb-4">
            <div class="d-flex justify-content-between mb-2">
              <span class="badge bg-label-primary step-badge active" data-step="1">1. Budget</span>
              <span class="badge bg-label-secondary step-badge" data-step="2">2. Location</span>
              <span class="badge bg-label-secondary step-badge" data-step="3">3. Preferences</span>
              <span class="badge bg-label-secondary step-badge" data-step="4">4. Amenities</span>
            </div>
            <div class="progress" style="height: 4px;">
              <div class="progress-bar" id="progressBar" style="width: 25%"></div>
            </div>
          </div>

          <!-- Step 1: Budget -->
          <div class="preference-step active" id="step1">
            <h6 class="mb-3">💰 What's your monthly budget?</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Minimum Budget</label>
                <div class="input-group">
                  <span class="input-group-text">₱</span>
                  <input type="number" class="form-control" name="budget_min" id="budget_min" 
                         value="2000" min="1000" max="10000" step="100" required>
                </div>
                <small class="text-muted">Min: ₱1,000</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Maximum Budget</label>
                <div class="input-group">
                  <span class="input-group-text">₱</span>
                  <input type="number" class="form-control" name="budget_max" id="budget_max" 
                         value="4000" min="1000" max="10000" step="100" required>
                </div>
                <small class="text-muted">Max: ₱10,000</small>
              </div>
            </div>
            <div class="mt-3">
              <div class="alert alert-info d-flex align-items-center">
                <i class='bx bx-info-circle me-2'></i>
                <div>
                  <strong>Budget Range:</strong> 
                  <span id="budgetDisplay">₱2,000 - ₱4,000</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Location -->
          <div class="preference-step" id="step2">
            <h6 class="mb-3">📍 How far from campus?</h6>
            <div class="mb-3">
              <label class="form-label">Maximum Distance (km)</label>
              <input type="range" class="form-range" name="preferred_distance" id="preferred_distance" 
                     min="0.1" max="5" step="0.1" value="1.0">
              <div class="d-flex justify-content-between mt-2">
                <small class="text-muted">0.1 km</small>
                <strong class="text-primary" id="distanceDisplay">1.0 km</strong>
                <small class="text-muted">5.0 km</small>
              </div>
            </div>
            <div class="alert alert-warning">
              <i class='bx bx-walk me-2'></i>
              <strong>Walking time:</strong> <span id="walkingTime">~12 minutes</span>
            </div>
          </div>

          <!-- Step 3: Room & Gender Preferences -->
          <div class="preference-step" id="step3">
            <h6 class="mb-3">🛏️ Room & Gender Preferences</h6>
            
            <div class="mb-4">
              <label class="form-label">Room Type</label>
              <div class="row g-2">
                <div class="col-4">
                  <input type="radio" class="btn-check" name="room_type" id="room_single" value="Single" required>
                  <label class="btn btn-outline-primary w-100 py-3" for="room_single">
                    <i class='bx bx-user d-block mb-2' style="font-size: 24px;"></i>
                    Single
                  </label>
                </div>
                <div class="col-4">
                  <input type="radio" class="btn-check" name="room_type" id="room_shared" value="Shared" checked>
                  <label class="btn btn-outline-primary w-100 py-3" for="room_shared">
                    <i class='bx bx-group d-block mb-2' style="font-size: 24px;"></i>
                    Shared
                  </label>
                </div>
                <div class="col-4">
                  <input type="radio" class="btn-check" name="room_type" id="room_any" value="Any">
                  <label class="btn btn-outline-primary w-100 py-3" for="room_any">
                    <i class='bx bx-radio-circle-marked d-block mb-2' style="font-size: 24px;"></i>
                    Any
                  </label>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Gender Preference</label>
              <div class="row g-2">
                <div class="col-3">
                  <input type="radio" class="btn-check" name="gender_preference" id="gender_male" value="Male Only">
                  <label class="btn btn-outline-success w-100 py-2" for="gender_male">
                    <i class='bx bx-male-sign me-1'></i>Male Only
                  </label>
                </div>
                <div class="col-3">
                  <input type="radio" class="btn-check" name="gender_preference" id="gender_female" value="Female Only">
                  <label class="btn btn-outline-danger w-100 py-2" for="gender_female">
                    <i class='bx bx-female-sign me-1'></i>Female Only
                  </label>
                </div>
                <div class="col-3">
                  <input type="radio" class="btn-check" name="gender_preference" id="gender_coed" value="Co-ed">
                  <label class="btn btn-outline-info w-100 py-2" for="gender_coed">
                    <i class='bx bx-group me-1'></i>Co-ed
                  </label>
                </div>
                <div class="col-3">
                  <input type="radio" class="btn-check" name="gender_preference" id="gender_any" value="Any" checked>
                  <label class="btn btn-outline-secondary w-100 py-2" for="gender_any">
                    <i class='bx bx-street-view me-1'></i>Any
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 4: Amenities -->
          <div class="preference-step" id="step4">
            <h6 class="mb-3">⚡ What amenities do you need?</h6>
            <small class="text-muted mb-3 d-block">Select all that apply</small>
            
            <div class="row g-2">
              @if(isset($amenities) && count($amenities) > 0)
                @foreach($amenities as $amenity)
                  <div class="col-6 col-md-4">
                    <input type="checkbox" class="btn-check" name="amenities[]" 
                           id="amenity_{{ $amenity->amenity_id }}" 
                           value="{{ $amenity->amenity_id }}"
                           @if(in_array($amenity->amenity_id, [1, 5, 8])) checked @endif>
                    <label class="btn btn-outline-info w-100" for="amenity_{{ $amenity->amenity_id }}">
                      <i class='bx bx-check-circle me-1'></i>{{ $amenity->amenity_name }}
                    </label>
                  </div>
                @endforeach
              @else
                <p class="text-muted">No amenities available</p>
              @endif
            </div>
          </div>

        </form>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
          <i class='bx bx-chevron-left'></i> Previous
        </button>
        <button type="button" class="btn btn-primary" id="nextBtn">
          Next <i class='bx bx-chevron-right'></i>
        </button>
        <button type="submit" form="preferenceForm" class="btn btn-success" id="submitBtn" style="display: none;">
          <i class='bx bx-check'></i> Save Preferences
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  .preference-step {
    display: none;
    animation: fadeIn 0.3s;
  }
  .preference-step.active {
    display: block;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .step-badge.active {
    background-color: var(--bs-primary) !important;
    color: white !important;
  }
  .btn-check:checked + .btn-outline-primary {
    background-color: var(--bs-primary);
    color: white;
  }
  .btn-check:checked + .btn-outline-info {
    background-color: #17a2b8;
    color: white;
  }
  .btn-check:checked + .btn-outline-success {
    background-color: #28a745;
    color: white;
  }
  .btn-check:checked + .btn-outline-danger {
    background-color: #dc3545;
    color: white;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let currentStep = 1;
  const totalSteps = 4;
  
  // Budget display
  const budgetMin = document.getElementById('budget_min');
  const budgetMax = document.getElementById('budget_max');
  const budgetDisplay = document.getElementById('budgetDisplay');
  
  function updateBudgetDisplay() {
    const min = parseInt(budgetMin.value).toLocaleString();
    const max = parseInt(budgetMax.value).toLocaleString();
    budgetDisplay.textContent = `₱${min} - ₱${max}`;
  }
  
  if (budgetMin && budgetMax) {
    budgetMin.addEventListener('input', updateBudgetDisplay);
    budgetMax.addEventListener('input', updateBudgetDisplay);
  }
  
  // Distance display
  const distanceSlider = document.getElementById('preferred_distance');
  const distanceDisplay = document.getElementById('distanceDisplay');
  const walkingTime = document.getElementById('walkingTime');
  
  if (distanceSlider) {
    distanceSlider.addEventListener('input', function() {
      const dist = parseFloat(this.value);
      distanceDisplay.textContent = dist.toFixed(1) + ' km';
      const minutes = Math.round(dist * 12);
      walkingTime.textContent = `~${minutes} minutes`;
    });
  }
  
  // Step navigation
  const nextBtn = document.getElementById('nextBtn');
  const prevBtn = document.getElementById('prevBtn');
  
  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
      if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
          currentStep++;
          showStep(currentStep);
        }
      }
    });
  }
  
  if (prevBtn) {
    prevBtn.addEventListener('click', function() {
      if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
      }
    });
  }
  
  function showStep(step) {
    // Hide all steps
    document.querySelectorAll('.preference-step').forEach(s => s.classList.remove('active'));
    const stepElement = document.getElementById(`step${step}`);
    if (stepElement) {
      stepElement.classList.add('active');
    }
    
    // Update badges
    document.querySelectorAll('.step-badge').forEach((badge, index) => {
      if (index + 1 <= step) {
        badge.classList.remove('bg-label-secondary');
        badge.classList.add('bg-label-primary', 'active');
      } else {
        badge.classList.remove('bg-label-primary', 'active');
        badge.classList.add('bg-label-secondary');
      }
    });
    
    // Update progress bar
    const progress = (step / totalSteps) * 100;
    const progressBar = document.getElementById('progressBar');
    if (progressBar) {
      progressBar.style.width = progress + '%';
    }
    
    // Show/hide buttons
    if (prevBtn) prevBtn.style.display = step === 1 ? 'none' : 'inline-block';
    if (nextBtn) nextBtn.style.display = step === totalSteps ? 'none' : 'inline-block';
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) submitBtn.style.display = step === totalSteps ? 'inline-block' : 'none';
  }
  
  function validateStep(step) {
    if (step === 1) {
      const min = parseInt(budgetMin.value);
      const max = parseInt(budgetMax.value);
      if (min >= max) {
        alert('Maximum budget must be greater than minimum budget!');
        return false;
      }
    }
    return true;
  }
  
  // Show modal if user has no preferences
  @if(!isset($preference) || !$preference)
    setTimeout(function() {
      const modal = new bootstrap.Modal(document.getElementById('preferenceModal'));
      modal.show();
    }, 500);
  @endif

  // Open edit preferences modal when triggered
  window.openEditPreferences = function() {
    const modal = new bootstrap.Modal(document.getElementById('editPreferencesModal'));
    modal.show();
  };
});
</script>