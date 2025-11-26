@extends('layouts/contentNavbarLayout')

@section('title', 'Edit User - ' . ($user->first_name ?? $user->name))

@section('content')
<div class="row">
  
  <!-- Back Button -->
  <div class="col-12 mb-4">
    <a href="{{ route('admin.users.view', $user->id) }}" class="btn btn-outline-secondary">
      <i class='bx bx-arrow-back me-1'></i> Back to User Details
    </a>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Edit User Information</h5>
      </div>
      <div class="card-body">
        
        @if($errors->any())
          <div class="alert alert-danger alert-dismissible" role="alert">
            <h6 class="alert-heading mb-1">Validation Errors</h6>
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <!-- Personal Information Section -->
          <div class="mb-4">
            <h6 class="text-primary mb-3">
              <i class='bx bx-user me-1'></i> Personal Information
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control @error('first_name') is-invalid @enderror" 
                       name="first_name" 
                       value="{{ old('first_name', $user->first_name) }}" 
                       required>
                @error('first_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control @error('last_name') is-invalid @enderror" 
                       name="last_name" 
                       value="{{ old('last_name', $user->last_name) }}" 
                       required>
                @error('last_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       name="email" 
                       value="{{ old('email', $user->email) }}" 
                       required>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Contact Number</label>
                <input type="text" 
                       class="form-control @error('contact_number') is-invalid @enderror" 
                       name="contact_number" 
                       value="{{ old('contact_number', $user->contact_number) }}" 
                       placeholder="+63 912 345 6789">
                @error('contact_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Gender</label>
                <select class="form-select @error('gender') is-invalid @enderror" name="gender">
                  <option value="">Select Gender</option>
                  <option value="Male" {{ old('gender', $user->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ old('gender', $user->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                  <option value="Other" {{ old('gender', $user->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <input type="date" 
                       class="form-control @error('date_of_birth') is-invalid @enderror" 
                       name="date_of_birth" 
                       value="{{ old('date_of_birth', $user->date_of_birth) }}">
                @error('date_of_birth')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <hr class="my-4">

          <!-- Academic Information Section (for students/tenants) -->
          @if($user->role === 'user' || old('role') === 'user')
          <div class="mb-4" id="academicSection">
            <h6 class="text-primary mb-3">
              <i class='bx bx-book me-1'></i> Academic Information
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Student Number</label>
                <input type="text" 
                       class="form-control @error('student_number') is-invalid @enderror" 
                       name="student_number" 
                       value="{{ old('student_number', $user->student_number) }}" 
                       placeholder="e.g., 2021-00001">
                @error('student_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Program</label>
                <input type="text" 
                       class="form-control @error('program') is-invalid @enderror" 
                       name="program" 
                       value="{{ old('program', $user->program) }}" 
                       placeholder="e.g., BS Computer Science">
                @error('program')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Year Level</label>
                <select class="form-select @error('year_level') is-invalid @enderror" name="year_level">
                  <option value="">Select Year Level</option>
                  <option value="1st Year" {{ old('year_level', $user->year_level) === '1st Year' ? 'selected' : '' }}>1st Year</option>
                  <option value="2nd Year" {{ old('year_level', $user->year_level) === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                  <option value="3rd Year" {{ old('year_level', $user->year_level) === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                  <option value="4th Year" {{ old('year_level', $user->year_level) === '4th Year' ? 'selected' : '' }}>4th Year</option>
                  <option value="Graduate" {{ old('year_level', $user->year_level) === 'Graduate' ? 'selected' : '' }}>Graduate</option>
                </select>
                @error('year_level')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <hr class="my-4">
          @endif

          <!-- Account Settings Section -->
          <div class="mb-4">
            <h6 class="text-primary mb-3">
              <i class='bx bx-cog me-1'></i> Account Settings
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Account Role <span class="text-danger">*</span></label>
                <select class="form-select @error('role') is-invalid @enderror" name="role" id="roleSelect" required>
                  <option value="">Select Role</option>
                  <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Tenant</option>
                  <option value="landlord" {{ old('role', $user->role) === 'landlord' ? 'selected' : '' }}>Landlord</option>
                  <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Approval Status</label>
                <select class="form-select @error('approval_status') is-invalid @enderror" name="approval_status">
                  <option value="pending" {{ old('approval_status', $user->approval_status) === 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="approved" {{ old('approval_status', $user->approval_status) === 'approved' ? 'selected' : '' }}>Approved</option>
                  <option value="rejected" {{ old('approval_status', $user->approval_status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                @error('approval_status')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <hr class="my-4">

          <!-- Password Section (Optional) -->
          <div class="mb-4">
            <h6 class="text-primary mb-3">
              <i class='bx bx-lock me-1'></i> Change Password (Optional)
            </h6>
            <div class="alert alert-info" role="alert">
              <i class='bx bx-info-circle me-1'></i>
              Leave these fields empty if you don't want to change the password.
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">New Password</label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       name="password" 
                       placeholder="Enter new password">
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Minimum 8 characters</small>
              </div>

              <div class="col-md-6">
                <label class="form-label">Confirm New Password</label>
                <input type="password" 
                       class="form-control" 
                       name="password_confirmation" 
                       placeholder="Confirm new password">
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="d-flex justify-content-between pt-3">
            <div>
              @if($user->id !== Auth::id())
                <button type="button" class="btn btn-outline-danger" onclick="deleteUser({{ $user->id }})">
                  <i class='bx bx-trash me-1'></i> Delete User
                </button>
              @endif
            </div>
            <div>
              <a href="{{ route('admin.users.view', $user->id) }}" class="btn btn-outline-secondary me-2">
                <i class='bx bx-x me-1'></i> Cancel
              </a>
              <button type="submit" class="btn btn-primary">
                <i class='bx bx-save me-1'></i> Save Changes
              </button>
            </div>
          </div>

        </form>

      </div>
    </div>
  </div>

</div>

@endsection

@section('page-script')
<script>
// Toggle academic section based on role
document.getElementById('roleSelect').addEventListener('change', function() {
  const academicSection = document.getElementById('academicSection');
  if (this.value === 'user') {
    academicSection.style.display = 'block';
  } else {
    academicSection.style.display = 'none';
  }
});

// Delete user function
function deleteUser(userId) {
  if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/users/${userId}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    form.submit();
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  const roleSelect = document.getElementById('roleSelect');
  const academicSection = document.getElementById('academicSection');
  
  if (roleSelect.value !== 'user') {
    academicSection.style.display = 'none';
  }
});
</script>
@endsection