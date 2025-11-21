@extends('layouts/blankLayout')

@section('title', 'Register - SLSU Staysmart')

@section('page-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
<style>
  body {
    background: linear-gradient(135deg, #003366 0%, #0099cc 100%);
    font-family: 'Public Sans', sans-serif;
    min-height: 100vh;
  }

  .card {
    animation: popIn 0.9s ease-in-out;
    transition: all 0.3s ease;
  }

  .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
  }

  .card-body {
    padding: 2rem 2.5rem;
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .form-group-full {
    grid-column: 1 / -1;
  }

  .section-divider {
    border-top: 2px solid #f0f0f0;
    margin: 2rem 0;
  }

  .section-title {
    font-size: 15px;
    font-weight: 700;
    color: #003366;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .form-label {
    font-weight: 600;
    color: #333;
    font-size: 13px;
    margin-bottom: 0.5rem;
    display: block;
  }

  .form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 11px 14px;
    font-size: 14px;
    transition: all 0.3s;
    width: 100%;
  }

  @media (max-width: 768px) {
    .form-row {
      grid-template-columns: 1fr;
      gap: 1rem;
    }
  }

  .form-control:focus, .form-select:focus {
    border-color: #0099cc;
    box-shadow: 0 0 0 3px rgba(0,153,204,0.15);
  }

  .btn-primary {
    background: linear-gradient(135deg, #003366, #0099cc);
    border: none;
    color: #fff;
    font-weight: 600;
    font-size: 15px;
    border-radius: 8px;
    padding: 12px;
    transition: all 0.3s;
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,153,204,0.3);
  }

  .btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
  }

  .conditional-fields {
    background: transparent;
    padding: 0;
    border-radius: 0;
    margin-bottom: 0;
    border: none;
    box-shadow: none;
  }

  .conditional-fields .section-title {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 2px solid #f0f0f0;
  }

  .alert-info-custom {
    background: #e7f3ff;
    border-left: 4px solid #2196f3;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 13px;
    color: #0c5491;
  }

  .approval-notice {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 13px;
    color: #856404;
    margin-bottom: 1.5rem;
  }

  .approval-notice.success {
    background: #d4edda;
    border-left: 4px solid #28a745;
    color: #155724;
  }

  .approval-notice i {
    font-size: 16px;
    margin-right: 8px;
  }

  @keyframes popIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }

  .spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
  }
</style>
@endsection

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-xl-7 col-lg-8 col-md-10">
      <div class="card shadow-lg rounded-4 border-0 my-5">
        <div class="card-body">

          <div class="text-center mb-4">
            <img src="{{ asset('assets/img/slsu_logo.png') }}" alt="SLSU Logo" style="width: 80px; height: 80px; margin-bottom: 1rem;">
            <h4 class="fw-bold text-primary mb-1">Create Your Account</h4>
            <p class="text-muted small mb-0">Join SLSU StaySmart and find your ideal boarding house 🏠</p>
          </div>

          <!-- Conditional Approval Notice -->
          <div id="approvalNoticeStudent" class="approval-notice success" style="display: none;">
            <i class="bx bx-check-circle"></i>
            <strong>Students:</strong> Your account will be activated immediately after registration!
          </div>

          <div id="approvalNoticeLandlord" class="approval-notice" style="display: none;">
            <i class="bx bx-info-circle"></i>
            <strong>Landlords:</strong> Your account requires admin approval. You'll receive an email notification once approved.
          </div>

          <!-- Validation Errors -->
          @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>Oops!</strong> Please fix the following errors:
              <ul class="mt-2 mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <!-- Form -->
          <form id="formAuthentication" action="{{ route('register.store') }}" method="POST" onsubmit="handleSubmit(event)">
            @csrf

            <!-- Account Type -->
            <div class="section-title">
              <i class="bx bx-user-check"></i> Account Type
            </div>

            <div class="form-row">
              <div class="form-group-full">
                <label for="role" class="form-label">Register as <span class="text-danger">*</span></label>
                <select class="form-select" id="role" name="role" required onchange="toggleRoleFields()">
                  <option value="">Select Role</option>
                  <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Student</option>
                  <option value="landlord" {{ old('role') === 'landlord' ? 'selected' : '' }}>Landlord</option>
                </select>
              </div>
            </div>

            <!-- Basic Information -->
            <div class="section-divider"></div>
            <div class="section-title">
              <i class="bx bx-id-card"></i> Basic Information
            </div>

            <div class="form-row">
              <div>
                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Juan Dela Cruz" value="{{ old('name') }}" required>
              </div>

              <div>
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="student@slsu.edu.ph" value="{{ old('email') }}" required>
              </div>
            </div>

            <div class="form-row">
              <div>
                <label id="studIdLabel" for="studId" class="form-label">Student Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="studId" name="studId" placeholder="e.g., 2021-00001" value="{{ old('studId') }}" required>
              </div>

              <div>
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="09XX XXX XXXX" value="{{ old('contact_number') }}">
              </div>
            </div>

            <!-- Student-specific fields -->
            <div id="studentFields" class="conditional-fields" style="display: none;">
              <div class="section-title" style="color: #003366;">
                <i class="bx bx-user-circle"></i> Student Information
              </div>

              <div class="form-row">
                <div>
                  <label for="program" class="form-label">Program <span class="text-danger">*</span></label>
                  <select class="form-select" id="program" name="program">
                    <option value="">Select your program</option>
                    <option value="BS Computer Science" {{ old('program') === 'BS Computer Science' ? 'selected' : '' }}>BS Computer Science</option>
                    <option value="BS Information Technology" {{ old('program') === 'BS Information Technology' ? 'selected' : '' }}>BS Information Technology</option>
                    <option value="BS Education" {{ old('program') === 'BS Education' ? 'selected' : '' }}>BS Education</option>
                    <option value="BS Nursing" {{ old('program') === 'BS Nursing' ? 'selected' : '' }}>BS Nursing</option>
                    <option value="BS Engineering" {{ old('program') === 'BS Engineering' ? 'selected' : '' }}>BS Engineering</option>
                    <option value="BS Business Administration" {{ old('program') === 'BS Business Administration' ? 'selected' : '' }}>BS Business Administration</option>
                    <option value="Other" {{ old('program') === 'Other' ? 'selected' : '' }}>Other</option>
                  </select>
                </div>

                <div>
                  <label for="year_level" class="form-label">Year Level <span class="text-danger">*</span></label>
                  <select class="form-select" id="year_level" name="year_level">
                    <option value="">Select year level</option>
                    <option {{ old('year_level') === '1st Year' ? 'selected' : '' }}>1st Year</option>
                    <option {{ old('year_level') === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                    <option {{ old('year_level') === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                    <option {{ old('year_level') === '4th Year' ? 'selected' : '' }}>4th Year</option>
                    <option {{ old('year_level') === 'Graduate' ? 'selected' : '' }}>Graduate</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Landlord-specific fields -->
            <div id="landlordFields" class="conditional-fields" style="display: none;">
              <div class="section-title" style="color: #f57c00;">
                <i class="bx bx-building-house"></i> Landlord Information
              </div>

              <div class="alert-info-custom mb-3">
                <i class="bx bx-info-circle me-2"></i>As a landlord, you can list and manage your boarding houses after admin approval.
              </div>

              <div class="form-row">
                <div>
                  <label for="phone" class="form-label">Business Phone <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="phone" name="phone" placeholder="09XX XXX XXXX" value="{{ old('phone') }}">
                </div>

                <div>
                  <label for="company_name" class="form-label">Company Name (Optional)</label>
                  <input type="text" class="form-control" id="company_name" name="company_name" placeholder="e.g., Santos Rentals" value="{{ old('company_name') }}">
                </div>
              </div>
            </div>

            <!-- Password Section -->
            <div class="section-divider"></div>
            <div class="section-title">
              <i class="bx bx-lock-alt"></i> Security
            </div>

            <div class="form-row">
              <div>
                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="password" class="form-control" id="password" name="password" placeholder="Enter secure password" required>
                  <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="bx bx-show"></i></button>
                </div>
                <small class="text-muted">Minimum 8 characters</small>
              </div>

              <div>
                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-enter your password" required>
              </div>
            </div>

            <!-- Terms -->
            <div class="section-divider"></div>
            <div class="form-check mb-4">
              <input type="checkbox" class="form-check-input" id="terms" required>
              <label class="form-check-label small" for="terms">
                I agree to the <a href="#" class="text-primary">Terms and Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a>
              </label>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn">
              <span id="btnText">
                <i class="bx bx-user-plus me-2"></i>Create Account
              </span>
              <span id="btnLoading" style="display: none;">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Creating Account...
              </span>
            </button>
          </form>

          <!-- Footer -->
          <p class="text-center mb-2">
            <span class="small">Already have an account?</span>
            <a href="{{ url('/') }}">
              <span class="fw-semibold text-primary small">Sign in here</span>
            </a>
          </p>

          <p class="text-center text-muted small mb-0">
            © {{ date('Y') }} SLSU StaySmart
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function toggleRoleFields() {
  const role = document.getElementById('role').value;
  const studentFields = document.getElementById('studentFields');
  const landlordFields = document.getElementById('landlordFields');
  const studIdLabel = document.getElementById('studIdLabel');
  const programField = document.getElementById('program');
  const yearLevelField = document.getElementById('year_level');
  const phoneField = document.getElementById('phone');
  
  // Approval notices
  const approvalNoticeStudent = document.getElementById('approvalNoticeStudent');
  const approvalNoticeLandlord = document.getElementById('approvalNoticeLandlord');

  studentFields.style.display = (role === 'user') ? 'block' : 'none';
  landlordFields.style.display = (role === 'landlord') ? 'block' : 'none';
  studIdLabel.innerHTML = (role === 'landlord') ? 'User ID / Business ID <span class="text-danger">*</span>' : 'Student Number <span class="text-danger">*</span>';
  
  // Show appropriate approval notice
  approvalNoticeStudent.style.display = (role === 'user') ? 'block' : 'none';
  approvalNoticeLandlord.style.display = (role === 'landlord') ? 'block' : 'none';
  
  // Set required attributes based on role
  if (role === 'user') {
    programField.setAttribute('required', 'required');
    yearLevelField.setAttribute('required', 'required');
    phoneField.removeAttribute('required');
  } else if (role === 'landlord') {
    programField.removeAttribute('required');
    yearLevelField.removeAttribute('required');
    phoneField.setAttribute('required', 'required');
  } else {
    programField.removeAttribute('required');
    yearLevelField.removeAttribute('required');
    phoneField.removeAttribute('required');
    approvalNoticeStudent.style.display = 'none';
    approvalNoticeLandlord.style.display = 'none';
  }
}

// Toggle Password Visibility
document.getElementById('togglePassword')?.addEventListener('click', function() {
  const password = document.getElementById('password');
  const icon = this.querySelector('i');
  if (password.type === 'password') {
    password.type = 'text';
    icon.classList.replace('bx-show', 'bx-hide');
  } else {
    password.type = 'password';
    icon.classList.replace('bx-hide', 'bx-show');
  }
});

// Handle form submission with loading state
function handleSubmit(event) {
  const submitBtn = document.getElementById('submitBtn');
  const btnText = document.getElementById('btnText');
  const btnLoading = document.getElementById('btnLoading');
  
  // Show loading state
  submitBtn.disabled = true;
  btnText.style.display = 'none';
  btnLoading.style.display = 'inline-block';
}

document.addEventListener('DOMContentLoaded', function() {
  toggleRoleFields();
  
  // Re-enable button if validation fails
  const form = document.getElementById('formAuthentication');
  form.addEventListener('invalid', function() {
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    
    submitBtn.disabled = false;
    btnText.style.display = 'inline-block';
    btnLoading.style.display = 'none';
  }, true);
});
</script>
@endsection