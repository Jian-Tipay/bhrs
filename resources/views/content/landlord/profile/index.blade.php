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
              @if(Auth::user()->landlord && Auth::user()->landlord->company_name)
                <p class="text-white mb-0 opacity-90">
                  <i class='bx bx-building me-1'></i>{{ Auth::user()->landlord->company_name }}
                </p>
              @endif
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
            <span class="badge bg-label-info px-3 py-2">
              <i class='bx bx-building-house me-1'></i> Landlord
            </span>
          </div>
          
          <div class="text-start mt-4">
            <div class="d-flex align-items-center mb-3 p-3 bg-label-info rounded">
              <i class='bx bx-envelope fs-4 me-3 text-info'></i>
              <div class="text-truncate">
                <small class="text-muted d-block">Email Address</small>
                <strong class="text-truncate d-block">{{ Auth::user()->email }}</strong>
              </div>
            </div>

            @if(Auth::user()->contact_number)
            <div class="d-flex align-items-center mb-3 p-3 bg-label-primary rounded">
              <i class='bx bx-phone fs-4 me-3 text-primary'></i>
              <div>
                <small class="text-muted d-block">Contact Number</small>
                <strong>{{ Auth::user()->contact_number }}</strong>
              </div>
            </div>
            @endif

            @if(Auth::user()->landlord && Auth::user()->landlord->phone)
            <div class="d-flex align-items-center mb-3 p-3 bg-label-success rounded">
              <i class='bx bx-phone-call fs-4 me-3 text-success'></i>
              <div>
                <small class="text-muted d-block">Business Phone</small>
                <strong>{{ Auth::user()->landlord->phone }}</strong>
              </div>
            </div>
            @endif

            @if(Auth::user()->gender)
            <div class="d-flex align-items-center p-3 bg-label-warning rounded">
              <i class='bx bx-male-female fs-4 me-3 text-warning'></i>
              <div>
                <small class="text-muted d-block">Gender</small>
                <strong>{{ Auth::user()->gender }}</strong>
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
            <a href="{{ route('landlord.properties.index') }}" class="btn btn-outline-primary">
              <i class='bx bx-building-house me-2'></i> My Properties
            </a>
            <a href="{{ route('landlord.bookings.index') }}" class="btn btn-outline-success">
              <i class='bx bx-calendar-check me-2'></i> Bookings
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

          <form method="POST" action="{{ route('landlord.profile.update') }}" enctype="multipart/form-data" id="profileForm">
            @csrf
            @method('PUT')

            <!-- Hidden file input -->
            <input type="file" class="d-none" id="profile_picture" name="profile_picture" accept="image/*" disabled>

            <div class="row">
              <!-- Full Name -->
              <div class="col-md-6 mb-4">
                <label for="name" class="form-label fw-semibold">
                  <i class='bx bx-user me-1'></i>Display Name <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control form-control-lg" id="name" name="name" 
                       value="{{ Auth::user()->name }}" required disabled 
                       placeholder="Enter your display name">
              </div>

              <!-- Email -->
              <div class="col-md-6 mb-4">
                <label for="email" class="form-label fw-semibold">
                  <i class='bx bx-envelope me-1'></i>Email Address <span class="text-danger">*</span>
                </label>
                <input type="email" class="form-control form-control-lg" id="email" name="email" 
                       value="{{ Auth::user()->email }}" required disabled 
                       placeholder="your.email@example.com">
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

              <!-- Contact Number -->
              <div class="col-md-6 mb-4">
                <label for="contact_number" class="form-label fw-semibold">
                  <i class='bx bx-phone me-1'></i>Personal Contact Number
                </label>
                <input type="text" class="form-control form-control-lg" id="contact_number" name="contact_number" 
                       value="{{ Auth::user()->contact_number }}" disabled 
                       placeholder="09XX XXX XXXX">
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

              <!-- Divider -->
              <div class="col-12 mb-3">
                <hr class="my-2">
                <h6 class="text-primary"><i class='bx bx-briefcase me-2'></i>Business Information</h6>
              </div>

              <!-- Business Phone -->
              <div class="col-md-6 mb-4">
                <label for="phone" class="form-label fw-semibold">
                  <i class='bx bx-phone-call me-1'></i>Business Phone
                </label>
                <input type="text" class="form-control form-control-lg" id="phone" name="phone" 
                       value="{{ Auth::user()->landlord ? Auth::user()->landlord->phone : '' }}" disabled 
                       placeholder="09XX XXX XXXX">
                <small class="text-muted">Contact number for tenants</small>
              </div>

              <!-- Company Name -->
              <div class="col-md-6 mb-4">
                <label for="company_name" class="form-label fw-semibold">
                  <i class='bx bx-building me-1'></i>Company/Business Name
                </label>
                <input type="text" class="form-control form-control-lg" id="company_name" name="company_name" 
                       value="{{ Auth::user()->landlord ? Auth::user()->landlord->company_name : '' }}" disabled 
                       placeholder="e.g., ABC Property Management">
              </div>

              <!-- Business Address -->
              <div class="col-12 mb-4">
                <label for="business_address" class="form-label fw-semibold">
                  <i class='bx bx-map me-1'></i>Business Address
                </label>
                <textarea class="form-control form-control-lg" id="business_address" name="business_address" 
                          rows="3" disabled 
                          placeholder="Complete business address">{{ Auth::user()->landlord ? Auth::user()->landlord->business_address : '' }}</textarea>
              </div>

              <!-- Divider -->
              <div class="col-12 mb-3">
                <hr class="my-2">
                <h6 class="text-primary"><i class='bx bx-lock-alt me-2'></i>Security</h6>
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
                <i class='bx bx-building-house text-primary fs-2 mb-2'></i>
                <h4 class="mb-0">{{ Auth::user()->landlord ? Auth::user()->landlord->properties()->count() : 0 }}</h4>
                <small class="text-muted">Properties</small>
              </div>
            </div>
            <div class="col-4">
              <div class="p-3">
                <i class='bx bx-calendar-check text-success fs-2 mb-2'></i>
                <h4 class="mb-0">
                  @php
                    $bookingsCount = 0;
                    if(Auth::user()->landlord) {
                      foreach(Auth::user()->landlord->properties as $property) {
                        $bookingsCount += $property->bookings()->count();
                      }
                    }
                  @endphp
                  {{ $bookingsCount }}
                </h4>
                <small class="text-muted">Bookings</small>
              </div>
            </div>
            <div class="col-4">
              <div class="p-3">
                <i class='bx bx-star text-warning fs-2 mb-2'></i>
                <h4 class="mb-0">
                  @php
                    $ratingsCount = 0;
                    if(Auth::user()->landlord) {
                      foreach(Auth::user()->landlord->properties as $property) {
                        $ratingsCount += $property->ratings()->count();
                      }
                    }
                  @endphp
                  {{ $ratingsCount }}
                </h4>
                <small class="text-muted">Reviews</small>
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
.bg-label-warning,
.bg-label-secondary {
  transition: all 0.3s;
}

.bg-label-primary:hover,
.bg-label-info:hover,
.bg-label-success:hover,
.bg-label-warning:hover,
.bg-label-secondary:hover {
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
  const inputs = profileForm.querySelectorAll('input:not([type="file"]), select, textarea');
  const fileInput = document.getElementById('profile_picture');
  const loadingOverlay = document.getElementById('loading-overlay');
  const uploadLabel = document.getElementById('uploadLabel');

  let originalValues = {};

  // Store original values
  inputs.forEach(input => {
    originalValues[input.id] = input.value;
  });

  // Enable editing
  editBtn.addEventListener('click', () => {
    inputs.forEach(input => {
      input.removeAttribute('disabled');
    });
    
    // Enable file input
    fileInput.removeAttribute('disabled');
    
    saveBtn.classList.remove('d-none');
    cancelBtn.classList.remove('d-none');
    editBtn.classList.add('d-none');
    uploadLabel.style.display = 'block';
  });

  // Cancel editing
  cancelBtn.addEventListener('click', () => {
    // Restore original values
    inputs.forEach(input => {
      if(originalValues[input.id] !== undefined) {
        input.value = originalValues[input.id];
        input.setAttribute('disabled', 'disabled');
      }
    });

    // Disable file input and clear it
    fileInput.setAttribute('disabled', 'disabled');
    fileInput.value = '';

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
  fileInput.addEventListener('change', function(e){
    const [file] = this.files;
    if(file){
      console.log('File selected:', file.name, file.size, file.type);
      const objectUrl = URL.createObjectURL(file);
      document.getElementById('profilePreview').src = objectUrl;
      document.getElementById('profilePreviewHeader').src = objectUrl;
    }
  });

  // Show full-page loading overlay on form submit
  profileForm.addEventListener('submit', (e) => {
    console.log('Form submitting...');
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