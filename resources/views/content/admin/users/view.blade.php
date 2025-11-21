@extends('layouts/contentNavbarLayout')

@section('title', 'User Details')

@section('content')
<div class="row">
  
  <!-- Back Button -->
  <div class="col-12 mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
      <i class='bx bx-arrow-back me-1'></i> Back to Users
    </a>
  </div>

  <!-- User Profile Card -->
  <div class="col-lg-4 mb-4">
    <div class="card h-100">
      <div class="card-body text-center">
        <div class="mb-3">
          @if($user->profile_picture)
            <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                 alt="{{ $user->first_name }}" 
                 class="rounded-circle" 
                 style="width: 120px; height: 120px; object-fit: cover;">
          @else
            <div class="avatar avatar-xl mx-auto">
              <span class="avatar-initial rounded-circle bg-label-{{ $user->role === 'landlord' ? 'info' : ($user->role === 'admin' ? 'danger' : 'primary') }}" 
                    style="font-size: 3rem;">
                {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
              </span>
            </div>
          @endif
        </div>
        <h4 class="mb-1">{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}</h4>
        <p class="text-muted mb-3">{{ $user->email }}</p>
        
        @switch($user->role)
          @case('admin')
            <span class="badge bg-label-danger mb-3">Admin</span>
            @break
          @case('landlord')
            <span class="badge bg-label-info mb-3">Landlord</span>
            @break
          @case('user')
            <span class="badge bg-label-primary mb-3">Tenant</span>
            @break
        @endswitch

        <div class="d-flex justify-content-around my-4">
          <div>
            <h5 class="mb-0">{{ $bookingsCount }}</h5>
            <small class="text-muted">Bookings</small>
          </div>
          <div>
            <h5 class="mb-0">{{ $ratingsCount }}</h5>
            <small class="text-muted">Reviews</small>
          </div>
          @if($user->role === 'landlord' && $properties->count() > 0)
            <div>
              <h5 class="mb-0">{{ $properties->count() }}</h5>
              <small class="text-muted">Properties</small>
            </div>
          @endif
        </div>

        <div class="d-flex gap-2 justify-content-center">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal">
            <i class='bx bx-edit me-1'></i> Edit
          </button>
          @if($user->id !== Auth::id())
            <button type="button" class="btn btn-danger" onclick="deleteUser({{ $user->id }})">
              <i class='bx bx-trash me-1'></i> Delete
            </button>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- User Information -->
  <div class="col-lg-8 mb-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">User Information</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label text-muted">Full Name</label>
            <p class="mb-0"><strong>{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}</strong></p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted">Email Address</label>
            <p class="mb-0"><strong>{{ $user->email }}</strong></p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted">Student Number</label>
            <p class="mb-0"><strong>{{ $user->student_number ?? 'N/A' }}</strong></p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted">Contact Number</label>
            <p class="mb-0"><strong>{{ $user->contact_number ?? 'N/A' }}</strong></p>
          </div>
          @if($user->program)
            <div class="col-md-6">
              <label class="form-label">Program</label>
              <input type="text" class="form-control" name="program" value="{{ $user->program }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Year Level</label>
              <select class="form-select" name="year_level">
                <option value="">Select Year Level</option>
                <option value="1st Year" {{ $user->year_level === '1st Year' ? 'selected' : '' }}>1st Year</option>
                <option value="2nd Year" {{ $user->year_level === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                <option value="3rd Year" {{ $user->year_level === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                <option value="4th Year" {{ $user->year_level === '4th Year' ? 'selected' : '' }}>4th Year</option>
                <option value="Graduate" {{ $user->year_level === 'Graduate' ? 'selected' : '' }}>Graduate</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Gender</label>
              <select class="form-select" name="gender">
                <option value="">Select Gender</option>
                <option value="Male" {{ $user->gender === 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ $user->gender === 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ $user->gender === 'Other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
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
</script>
@endsectionlabel text-muted">Program</label>
              <p class="mb-0"><strong>{{ $user->program }}</strong></p>
            </div>
          @endif
          @if($user->year_level)
            <div class="col-md-6">
              <label class="form-label text-muted">Year Level</label>
              <p class="mb-0"><strong>{{ $user->year_level }}</strong></p>
            </div>
          @endif
          @if($user->gender)
            <div class="col-md-6">
              <label class="form-label text-muted">Gender</label>
              <p class="mb-0"><strong>{{ $user->gender }}</strong></p>
            </div>
          @endif
          <div class="col-md-6">
            <label class="form-label text-muted">Account Created</label>
            <p class="mb-0"><strong>{{ $user->created_at->format('M d, Y') }}</strong></p>
            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Landlord Properties -->
  @if($user->role === 'landlord' && $properties->count() > 0)
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Properties ({{ $properties->count() }})</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Property</th>
                  <th>Address</th>
                  <th>Price</th>
                  <th>Bookings</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($properties as $property)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        @if($property->images && $property->images->count() > 0)
                          <img src="{{ asset('storage/' . $property->images->first()->image_path) }}" 
                               alt="{{ $property->title }}" 
                               class="rounded me-2" 
                               style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                          <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-info">
                              <i class='bx bx-building-house'></i>
                            </span>
                          </div>
                        @endif
                        <strong>{{ $property->title }}</strong>
                      </div>
                    </td>
                    <td>{{ Str::limit($property->address, 30) }}</td>
                    <td>₱{{ number_format($property->price, 2) }}</td>
                    <td>
                      <span class="badge bg-label-primary">{{ $property->bookings_count ?? 0 }} Bookings</span>
                    </td>
                    <td>
                      @if($property->is_active)
                        <span class="badge bg-success">Active</span>
                      @else
                        <span class="badge bg-warning">Pending</span>
                      @endif
                    </td>
                    <td>
                      <a href="{{ route('properties.show', $property->id) }}" class="btn btn-sm btn-icon btn-outline-primary">
                        <i class='bx bx-show'></i>
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- User Bookings -->
  @if($user->bookings && $user->bookings->count() > 0)
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Booking History ({{ $user->bookings->count() }})</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Property</th>
                  <th>Move-in Date</th>
                  <th>Move-out Date</th>
                  <th>Status</th>
                  <th>Created</th>
                </tr>
              </thead>
              <tbody>
                @foreach($user->bookings->take(10) as $booking)
                  <tr>
                    <td><strong>#{{ $booking->booking_id }}</strong></td>
                    <td>{{ $booking->property->title ?? 'N/A' }}</td>
                    <td>{{ $booking->move_in_date ? \Carbon\Carbon::parse($booking->move_in_date)->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ $booking->move_out_date ? \Carbon\Carbon::parse($booking->move_out_date)->format('M d, Y') : 'N/A' }}</td>
                    <td>
                      @switch($booking->status)
                        @case('Pending')
                          <span class="badge bg-warning">Pending</span>
                          @break
                        @case('Approved')
                          <span class="badge bg-info">Approved</span>
                          @break
                        @case('Active')
                          <span class="badge bg-success">Active</span>
                          @break
                        @case('Completed')
                          <span class="badge bg-secondary">Completed</span>
                          @break
                        @case('Cancelled')
                          <span class="badge bg-danger">Cancelled</span>
                          @break
                      @endswitch
                    </td>
                    <td>{{ $booking->created_at->format('M d, Y') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- User Reviews -->
  @if($user->ratings && $user->ratings->count() > 0)
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Reviews Given ({{ $user->ratings->count() }})</h5>
        </div>
        <div class="card-body">
          <div class="row">
            @foreach($user->ratings->take(6) as $rating)
              <div class="col-md-6 mb-3">
                <div class="card border">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <h6 class="mb-0">{{ $rating->property->title ?? 'N/A' }}</h6>
                      <span class="badge bg-warning">⭐ {{ $rating->rating }}</span>
                    </div>
                    <p class="text-muted mb-2">{{ Str::limit($rating->review_text, 100) }}</p>
                    <small class="text-muted">{{ $rating->created_at->format('M d, Y') }}</small>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  @endif

</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="first_name" value="{{ $user->first_name ?? $user->name }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="last_name" value="{{ $user->last_name }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Role <span class="text-danger">*</span></label>
              <select class="form-select" name="role" required>
                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Tenant</option>
                <option value="landlord" {{ $user->role === 'landlord' ? 'selected' : '' }}>Landlord</option>
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Number</label>
              <input type="text" class="form-control" name="contact_number" value="{{ $user->contact_number }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Student Number</label>
              <input type="text" class="form-control" name="student_number" value="{{ $user->student_number }}">
            </div>
            <div class="col-md-6">
              <label class="form-