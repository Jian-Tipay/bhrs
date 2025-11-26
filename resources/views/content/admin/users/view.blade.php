@extends('layouts/contentNavbarLayout')

@section('title', 'View User - ' . ($user->first_name ?? $user->name))

@section('content')
<div class="row">
  
  <!-- Back Button -->
  <div class="col-12 mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
      <i class='bx bx-arrow-back me-1'></i> Back to Users
    </a>
  </div>

  <!-- User Profile Card -->
  <!-- User Profile Card -->
  <div class="col-lg-4 col-md-5 mb-4">
    <div class="card">
      <div class="card-body text-center">
        <div class="mb-4 mt-3">
          @if($user->profile_picture)
            <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('assets/img/avatars/1.png') }}" 
                 class="rounded-circle" 
                 width="120" 
                 height="120"
                 style="object-fit: cover;">
          @else
            <div class="d-flex justify-content-center align-items-center" style="height: 120px;">
              <span class="avatar-initial rounded-circle bg-label-{{ $user->role === 'landlord' ? 'info' : ($user->role === 'admin' ? 'danger' : 'primary') }}" 
                    style="width: 120px; height: 120px; font-size: 48px; display: flex; align-items: center; justify-content: center;">
                {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
              </span>
            </div>
          @endif
        </div>

        <h4 class="mb-1">
          {{ $user->first_name ?? 'N/A' }}
          @if($user->last_name)
            {{ $user->last_name }}
          @endif
        </h4>
        
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

        <div class="d-flex justify-content-center gap-2 mb-3">
          @if($user->approval_status === 'approved')
            <span class="badge bg-success">
              <i class='bx bx-check-circle'></i> Approved
            </span>
          @elseif($user->approval_status === 'pending')
            <span class="badge bg-warning">
              <i class='bx bx-time'></i> Pending
            </span>
          @elseif($user->approval_status === 'rejected')
            <span class="badge bg-danger">
              <i class='bx bx-x-circle'></i> Rejected
            </span>
          @endif
        </div>

        <div class="d-grid gap-2">
          <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
            <i class='bx bx-edit me-1'></i> Edit User
          </a>
          @if($user->id !== Auth::id())
            <button type="button" class="btn btn-outline-danger" onclick="deleteUser({{ $user->id }})">
              <i class='bx bx-trash me-1'></i> Delete User
            </button>
          @endif
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="card mt-4">
      <div class="card-body">
        <h5 class="card-title mb-3">Quick Statistics</h5>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <i class='bx bx-calendar-check text-primary'></i>
            <span class="ms-2">Bookings</span>
          </div>
          <span class="badge bg-label-primary">{{ $bookingsCount }}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <i class='bx bx-star text-warning'></i>
            <span class="ms-2">Reviews Given</span>
          </div>
          <span class="badge bg-label-warning">{{ $ratingsCount }}</span>
        </div>
        @if($user->role === 'landlord')
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <i class='bx bx-building-house text-info'></i>
              <span class="ms-2">Properties</span>
            </div>
            <span class="badge bg-label-info">{{ $properties->count() }}</span>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- User Details -->
  <div class="col-lg-8 col-md-7">
    
    <!-- Basic Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Basic Information</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label text-muted small">FIRST NAME</label>
            <p class="mb-0"><strong>{{ $user->first_name ?? 'N/A' }}</strong></p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">LAST NAME</label>
            <p class="mb-0">
              @if($user->last_name)
                <strong>{{ $user->last_name }}</strong>
              @else
                <span class="text-muted">N/A</span>
              @endif
            </p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">EMAIL ADDRESS</label>
            <p class="mb-0">
              <i class='bx bx-envelope me-1'></i>
              <strong>{{ $user->email ?? 'N/A' }}</strong>
            </p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">CONTACT NUMBER</label>
            <p class="mb-0">
              @if($user->contact_number)
                <i class='bx bx-phone me-1'></i>
                <strong>{{ $user->contact_number }}</strong>
              @else
                <span class="text-muted">N/A</span>
              @endif
            </p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">GUARDIAN NUMBER</label>
            <p class="mb-0">
              @if($user->guardian_number)
                <i class='bx bx-phone me-1'></i>
                <strong>{{ $user->guardian_number }}</strong>
              @else
                <span class="text-muted">N/A</span>
              @endif
            </p>
          </div>
          @if($user->student_number)
            <div class="col-md-6">
              <label class="form-label text-muted small">STUDENT NUMBER</label>
              <p class="mb-0">
                <i class='bx bx-id-card me-1'></i>
                <strong>{{ $user->student_number }}</strong>
              </p>
            </div>
          @endif
          @if($user->program || $user->year_level)
            @if($user->program)
              <div class="col-md-6">
                <label class="form-label text-muted small">PROGRAM</label>
                <p class="mb-0"><strong>{{ $user->program }}</strong></p>
              </div>
            @endif
            @if($user->year_level)
              <div class="col-md-6">
                <label class="form-label text-muted small">YEAR LEVEL</label>
                <p class="mb-0"><strong>{{ $user->year_level }}</strong></p>
              </div>
            @endif
          @endif
          @if($user->gender)
            <div class="col-md-6">
              <label class="form-label text-muted small">GENDER</label>
              <p class="mb-0"><strong>{{ $user->gender }}</strong></p>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Account Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Account Information</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label text-muted small">ACCOUNT TYPE</label>
            <p class="mb-0">
              <strong>{{ ucfirst($user->role) }}</strong>
            </p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">APPROVAL STATUS</label>
            <p class="mb-0">
              @if($user->approval_status === 'approved')
                <span class="badge bg-success">APPROVED</span>
              @elseif($user->approval_status === 'pending')
                <span class="badge bg-warning">Pending</span>
              @elseif($user->approval_status === 'rejected')
                <span class="badge bg-danger">Rejected</span>
              @endif
            </p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted small">JOINED DATE</label>
            <p class="mb-0">
              <i class='bx bx-calendar me-1'></i>
              <strong>{{ $user->created_at->format('F d, Y') }}</strong>
              <br>
              <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
            </p>
          </div>
          @if($user->approved_at)
            <div class="col-md-6">
              <label class="form-label text-muted small">APPROVED DATE</label>
              <p class="mb-0">
                <i class='bx bx-check-circle me-1'></i>
                <strong>{{ $user->approved_at->format('F d, Y') }}</strong>
                <br>
                <small class="text-muted">{{ $user->approved_at->diffForHumans() }}</small>
              </p>
            </div>
          @endif
          @if($user->approvedBy)
            <div class="col-md-6">
              <label class="form-label text-muted small">APPROVED BY</label>
              <p class="mb-0">
                <strong>{{ $user->approvedBy->first_name }} {{ $user->approvedBy->last_name }}</strong>
              </p>
            </div>
          @endif
          @if($user->rejection_reason)
            <div class="col-12">
              <label class="form-label text-muted small">REJECTION REASON</label>
              <div class="alert alert-danger mb-0">
                {{ $user->rejection_reason }}
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Landlord Properties -->
    @if($user->role === 'landlord' && $properties->count() > 0)
      <div class="card mb-4">
        <div class="card-header">
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
                </tr>
              </thead>
              <tbody>
                @foreach($properties as $property)
                  <tr>
                    <td>
                      <strong>{{ $property->title }}</strong>
                    </td>
                    <td>{{ Str::limit($property->address, 30) }}</td>
                    <td><strong>₱{{ number_format($property->price, 2) }}</strong></td>
                    <td>
                      <span class="badge bg-label-primary">{{ $property->bookings_count }}</span>
                    </td>
                    <td>
                      @if($property->is_active)
                        <span class="badge bg-success">Active</span>
                      @else
                        <span class="badge bg-secondary">Inactive</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif

    <!-- Recent Bookings -->
    @if($user->bookings && $user->bookings->count() > 0)
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Recent Bookings</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Property</th>
                  <th>Move-in Date</th>
                  <th>Status</th>
                  <th>Created</th>
                </tr>
              </thead>
              <tbody>
                @foreach($user->bookings->take(5) as $booking)
                  <tr>
                    <td>
                      <strong>{{ $booking->property->title ?? 'N/A' }}</strong>
                    </td>
                    <td>{{ $booking->move_in_date ? \Carbon\Carbon::parse($booking->move_in_date)->format('M d, Y') : 'N/A' }}</td>
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
    @endif

    <!-- Recent Reviews -->
    @if($user->ratings && $user->ratings->count() > 0)
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Recent Reviews Given</h5>
        </div>
        <div class="card-body">
          @foreach($user->ratings->take(5) as $rating)
            <div class="d-flex mb-3 pb-3 border-bottom">
              <div class="flex-grow-1">
                <h6 class="mb-1">{{ $rating->property->title ?? 'N/A' }}</h6>
                <div class="mb-2">
                  @for($i = 1; $i <= 5; $i++)
                    @if($i <= $rating->rating)
                      <i class='bx bxs-star text-warning'></i>
                    @else
                      <i class='bx bx-star text-muted'></i>
                    @endif
                  @endfor
                  <span class="ms-1 text-muted">({{ $rating->rating }}/5)</span>
                </div>
                <p class="mb-1 text-muted small">{{ $rating->comment }}</p>
                <small class="text-muted">{{ $rating->created_at->format('M d, Y') }}</small>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

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
@endsection