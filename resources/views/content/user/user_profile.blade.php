@extends('layouts/contentNavbarLayout')

@section('title', 'My Profile')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Modern Hero Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
        <div class="card-body p-4">
          <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-xl border border-3 border-white shadow">
              <img id="profilePreviewHeader" 
                   src="{{ Auth::user()->profile_picture ? asset(Auth::user()->profile_picture) : asset('assets/img/avatars/1.png') }}" 
                   alt="avatar"
                   class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
            </div>
            <div class="flex-grow-1">
              <h3 class="text-white mb-1">{{ Auth::user()->name }}</h3>
              <p class="text-white mb-0 opacity-90">
                <i class='bx bx-envelope me-1'></i>{{ Auth::user()->email }}
              </p>
            </div>
            <div class="text-end">
              <button type="button" class="btn btn-light btn-sm" id="editProfileBtn">
                <i class='bx bx-edit-alt me-1'></i> Edit Profile
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Profile Card with Avatar -->
    <div class="col-lg-4 mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center p-4">
          <div class="position-relative d-inline-block mb-3">
            <img id="profilePreview" 
                 src="{{ Auth::user()->profile_picture ? asset(Auth::user()->profile_picture) : asset('assets/img/avatars/1.png') }}" 
                 alt="avatar"
                 class="rounded-circle border border-5 border-light shadow-lg" 
                 style="width: 150px; height: 150px; object-fit: cover;">
            <div class="position-absolute bottom-0 end-0 mb-2 me-2">
              <label for="profile_picture" class="btn btn-sm btn-primary rounded-circle p-2" id="uploadLabel" style="cursor: pointer; display: none;">
                <i class='bx bx-camera fs-5'></i>
              </label>
            </div>
          </div>
          
          <h4 class="mb-2">{{ Auth::user()->name }}</h4>
          <div class="mb-3">
            <span class="badge bg-label-primary px-3 py-2">
              <i class='bx bx-user me-1'></i> Student
            </span>
          </div>
          
          <div class="text-start mt-4">
            <div class="d-flex align-items-center mb-3 p-3 bg-label-primary rounded">
              <i class='bx bx-id-card fs-4 me-3 text-primary'></i>
              <div>
                <small class="text-muted d-block">Student ID</small>
                <strong>{{ Auth::user()->studID }}</strong>
              </div>
            </div>
            
            <div class="d-flex align-items-center mb-3 p-3 bg-label-info rounded">
              <i class='bx bx-envelope fs-4 me-3 text-info'></i>
              <div class="text-truncate">
                <small class="text-muted d-block">Email Address</small>
                <strong class="text-truncate d-block">{{ Auth::user()->email }}</strong>
              </div>
            </div>

            @if(Auth::user()->program)
            <div class="d-flex align-items-center mb-3 p-3 bg-label-success rounded">
              <i class='bx bx-book fs-4 me-3 text-success'></i>
              <div>
                <small class="text-muted d-block">Program</small>
                <strong>{{ Auth::user()->program }}</strong>
              </div>
            </div>
            @endif

            @if(Auth::user()->year_level)
            <div class="d-flex align-items-center p-3 bg-label-warning rounded">
              <i class='bx bx-graduation fs-4 me-3 text-warning'></i>
              <div>
                <small class="text-muted d-block">Year Level</small>
                <strong>{{ Auth::user()->year_level }}</strong>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Quick Actions Card -->
      <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">
          <h6 class="mb-3">Quick Actions</h6>
          <div class="d-grid gap-2">
            <a href="{{ route('recommendations.index') }}" class="btn btn-outline-primary">
              <i class='bx bx-search-alt me-2'></i> Browse Properties
            </a>
            <a href="{{ route('ratings.index') }}" class="btn btn-outline-warning">
              <i class='bx bx-star me-2'></i> My Ratings
            </a>
            <a href="{{ route('bookings.index') }}" class="btn btn-outline-success">
              <i class='bx bx-calendar-check me-2'></i> My Bookings
            </a>
             <a href="{{ route('preferences.index') }}" class="btn btn-outline-info">
              <i class='bx bx-slider-alt me-2'></i> My Preferences
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Profile Details Form -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
          <h5 class="mb-0">
            <i class='bx bx-user-circle me-2'></i>Personal Information
          </h5>
        </div>
        <div class="card-body p-4">
          <!-- Loading Overlay -->
          <div id="loading-overlay" style="
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.9);
            z-index: 9999;
            justify-content: center;
            align-items: center;
          ">
            <div class="text-center">
              <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="text-muted">Updating your profile...</p>
            </div>
          </div>

          <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
            @csrf
            @method('PUT')

            <!-- Hidden file input -->
            <input type="file" class="d-none" id="profile_picture" name="profile_picture" accept="image/*" disabled>

            <div class="row">
              <!-- Full Name -->
              <div class="col-md-6 mb-4">
                <label for="name" class="form-label fw-semibold">
                  <i class='bx bx-user me-1'></i>Full Name
                </label>
                <input type="text" class="form-control form-control-lg" id="name" name="name" 
                       value="{{ Auth::user()->name }}" required disabled 
                       placeholder="Enter your full name">
              </div>

              <!-- Student ID (Read-only) -->
              <div class="col-md-6 mb-4">
                <label for="studID" class="form-label fw-semibold">
                  <i class='bx bx-id-card me-1'></i>Student ID
                </label>
                <input type="text" class="form-control form-control-lg bg-light" id="studID" name="studID" 
                       value="{{ Auth::user()->studID }}" readonly>
                <small class="text-muted">Student ID cannot be changed</small>
              </div>

              <!-- Email -->
              <div class="col-md-6 mb-4">
                <label for="email" class="form-label fw-semibold">
                  <i class='bx bx-envelope me-1'></i>Email Address
                </label>
                <input type="email" class="form-control form-control-lg" id="email" name="email" 
                       value="{{ Auth::user()->email }}" required disabled 
                       placeholder="your.email@example.com">
              </div>

              <!-- Contact Number -->
              <div class="col-md-6 mb-4">
                <label for="contact_number" class="form-label fw-semibold">
                  <i class='bx bx-phone me-1'></i>Contact Number
                </label>
                <input type="text" class="form-control form-control-lg" id="contact_number" name="contact_number" 
                       value="{{ Auth::user()->contact_number }}" disabled 
                       placeholder="09XX XXX XXXX">
              </div>

              <!-- First Name -->
              <div class="col-md-6 mb-4">
                <label for="first_name" class="form-label fw-semibold">
                  <i class='bx bx-user-pin me-1'></i>First Name
                </label>
                <input type="text" class="form-control form-control-lg" id="first_name" name="first_name" 
                       value="{{ Auth::user()->first_name }}" disabled 
                       placeholder="First name">
              </div>

              <!-- Last Name -->
              <div class="col-md-6 mb-4">
                <label for="last_name" class="form-label fw-semibold">
                  <i class='bx bx-user-pin me-1'></i>Last Name
                </label>
                <input type="text" class="form-control form-control-lg" id="last_name" name="last_name" 
                       value="{{ Auth::user()->last_name }}" disabled 
                       placeholder="Last name">
              </div>

              <!-- Program -->
              <div class="col-md-6 mb-4">
                <label for="program" class="form-label fw-semibold">
                  <i class='bx bx-book me-1'></i>Program/Course
                </label>
                <input type="text" class="form-control form-control-lg" id="program" name="program" 
                       value="{{ Auth::user()->program }}" disabled 
                       placeholder="e.g., BS Computer Science">
              </div>

              <!-- Year Level -->
              <div class="col-md-6 mb-4">
                <label for="year_level" class="form-label fw-semibold">
                  <i class='bx bx-graduation me-1'></i>Year Level
                </label>
                <select class="form-select form-select-lg" id="year_level" name="year_level" disabled>
                  <option value="">Select Year Level</option>
                  <option value="1st Year" {{ Auth::user()->year_level == '1st Year' ? 'selected' : '' }}>1st Year</option>
                  <option value="2nd Year" {{ Auth::user()->year_level == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                  <option value="3rd Year" {{ Auth::user()->year_level == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                  <option value="4th Year" {{ Auth::user()->year_level == '4th Year' ? 'selected' : '' }}>4th Year</option>
                  <option value="Graduate" {{ Auth::user()->year_level == 'Graduate' ? 'selected' : '' }}>Graduate</option>
                </select>
              </div>

              <!-- Gender -->
              <div class="col-md-6 mb-4">
                <label for="gender" class="form-label fw-semibold">
                  <i class='bx bx-male-female me-1'></i>Gender
                </label>
                <select class="form-select form-select-lg" id="gender" name="gender" disabled>
                  <option value="">Select Gender</option>
                  <option value="Male" {{ Auth::user()->gender == 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ Auth::user()->gender == 'Female' ? 'selected' : '' }}>Female</option>
                  <option value="Other" {{ Auth::user()->gender == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>

              <!-- Password -->
              <div class="col-12 mb-4">
                <label for="password" class="form-label fw-semibold">
                  <i class='bx bx-lock-alt me-1'></i>New Password 
                  <small class="text-muted fw-normal">(Leave blank to keep current password)</small>
                </label>
                <input type="password" class="form-control form-control-lg" id="password" name="password" 
                       disabled placeholder="Enter new password">
                <small class="text-muted">Minimum 8 characters</small>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 justify-content-end mt-4">
              <button type="button" class="btn btn-outline-secondary d-none" id="cancelBtn">
                <i class='bx bx-x me-1'></i> Cancel
              </button>
              <button type="submit" class="btn btn-success d-none" id="saveBtn">
                <i class='bx bx-save me-1'></i> Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Account Stats Card -->
      <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">
          <h6 class="mb-3">
            <i class='bx bx-chart me-2'></i>Your Activity
          </h6>
          <div class="row text-center">
            <div class="col-4">
              <div class="p-3">
                <i class='bx bx-star text-warning fs-2 mb-2'></i>
                <h4 class="mb-0">{{ Auth::user()->ratings()->count() }}</h4>
                <small class="text-muted">Ratings</small>
              </div>
            </div>
            <div class="col-4">
              <div class="p-3">
                <i class='bx bx-calendar-check text-success fs-2 mb-2'></i>
                <h4 class="mb-0">{{ Auth::user()->bookings()->count() }}</h4>
                <small class="text-muted">Bookings</small>
              </div>
            </div>
            <div class="col-4">
              <div class="p-3">
                <i class='bx bx-show text-info fs-2 mb-2'></i>
                <h4 class="mb-0">{{ Auth::user()->views()->count() }}</h4>
                <small class="text-muted">Views</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Modern Card Styles */
.card {
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-2px);
}

/* Avatar Styles */
.avatar-xl {
  width: 80px;
  height: 80px;
}

/* Form Control Modern Look */
.form-control-lg, .form-select-lg {
  border-radius: 10px;
  border: 2px solid #e7e7e7;
  padding: 12px 18px;
  transition: all 0.3s;
}

.form-control-lg:focus, .form-select-lg:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
}

.form-control-lg:disabled, .form-select-lg:disabled {
  background-color: #f8f9fa;
  cursor: not-allowed;
}

/* Button Styles */
.btn {
  border-radius: 8px;
  padding: 10px 24px;
  font-weight: 500;
  transition: all 0.3s;
}

.btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Badge Styles */
.badge {
  font-weight: 500;
  font-size: 0.875rem;
}

/* Info Box Styles */
.bg-label-primary,
.bg-label-info,
.bg-label-success,
.bg-label-warning {
  transition: all 0.3s;
}

.bg-label-primary:hover,
.bg-label-info:hover,
.bg-label-success:hover,
.bg-label-warning:hover {
  transform: translateX(5px);
}

/* Upload Button Hover */
#uploadLabel {
  transition: all 0.3s;
}

#uploadLabel:hover {
  transform: scale(1.1);
}
</style>
@endsection

@section('page-script')
<script>
  const editBtn = document.getElementById('editProfileBtn');
  const saveBtn = document.getElementById('saveBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const profileForm = document.getElementById('profileForm');
  const inputs = profileForm.querySelectorAll('input, select');
  const loadingOverlay = document.getElementById('loading-overlay');
  const uploadLabel = document.getElementById('uploadLabel');

  let originalValues = {};

  // Store original values
  inputs.forEach(input => {
    if(input.id !== 'studID') {
      originalValues[input.id] = input.value;
    }
  });

  // Enable editing
  editBtn.addEventListener('click', () => {
    inputs.forEach(input => {
      if(input.id !== 'studID') {
        input.removeAttribute('disabled');
      }
    });
    
    saveBtn.classList.remove('d-none');
    cancelBtn.classList.remove('d-none');
    editBtn.classList.add('d-none');
    uploadLabel.style.display = 'block';
  });

  // Cancel editing
  cancelBtn.addEventListener('click', () => {
    // Restore original values
    inputs.forEach(input => {
      if(input.id !== 'studID' && originalValues[input.id] !== undefined) {
        input.value = originalValues[input.id];
        input.setAttribute('disabled', 'disabled');
      }
    });

    // Reset profile picture preview
    const currentPic = "{{ Auth::user()->profile_picture ? asset(Auth::user()->profile_picture) : asset('assets/img/avatars/1.png') }}";
    document.getElementById('profilePreview').src = currentPic;
    document.getElementById('profilePreviewHeader').src = currentPic;
    
    saveBtn.classList.add('d-none');
    cancelBtn.classList.add('d-none');
    editBtn.classList.remove('d-none');
    uploadLabel.style.display = 'none';
  });

  // Preview selected profile picture
  document.getElementById('profile_picture').addEventListener('change', function(e){
    const [file] = this.files;
    if(file){
      const objectUrl = URL.createObjectURL(file);
      document.getElementById('profilePreview').src = objectUrl;
      document.getElementById('profilePreviewHeader').src = objectUrl;
    }
  });

  // Show full-page loading overlay on form submit
  profileForm.addEventListener('submit', () => {
    loadingOverlay.style.display = 'flex';
  });

  // Validate password
  const passwordInput = document.getElementById('password');
  passwordInput.addEventListener('input', function() {
    if(this.value.length > 0 && this.value.length < 8) {
      this.setCustomValidity('Password must be at least 8 characters');
    } else {
      this.setCustomValidity('');
    }
  });
</script>
@endsection