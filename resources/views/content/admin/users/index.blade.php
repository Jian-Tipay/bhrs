@extends('layouts/contentNavbarLayout')

@section('title', 'User Management')

@section('content')
<div class="row">
  
  <!-- Page Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h4 class="mb-1">User Management</h4>
            <p class="mb-0 text-muted">Manage all users, landlords, and tenants</p>
          </div>
          <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
              <i class='bx bx-plus me-1'></i> Add User
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="col-lg-4 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Total Users</p>
            <h4 class="mb-0">{{ $users->total() }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
              <i class='bx bx-user bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Landlords</p>
            <h4 class="mb-0">{{ App\Models\User::where('role', 'landlord')->count() }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-2">
              <i class='bx bx-building-house bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Tenants</p>
            <h4 class="mb-0">{{ App\Models\User::where('role', 'user')->count() }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-success rounded p-2">
              <i class='bx bx-group bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters and Search -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.users.index') }}" method="GET">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Search</label>
              <input type="text" 
                     class="form-control" 
                     name="search" 
                     placeholder="Search by name or email..." 
                     value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
              <label class="form-label">Filter by Role</label>
              <select class="form-select" name="role">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="landlord" {{ request('role') === 'landlord' ? 'selected' : '' }}>Landlord</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Tenant</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Approval Status</label>
              <select class="form-select" name="approval_status">
                <option value="">All Status</option>
                <option value="pending" {{ request('approval_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('approval_status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('approval_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Sort By</label>
              <select class="form-select" name="sort">
                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest First</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name (A-Z)</option>
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-100">
                <i class='bx bx-search me-1'></i> Filter
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Users Table -->
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Users</h5>
        <div>
          <span class="text-muted">Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users</span>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Contact</th>
                <th>Joined</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $user)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                        @if($user->profile_picture)
                          <img src="{{ asset($user->profile_picture) }}" 
                               alt="{{ $user->first_name ?? $user->name }}" 
                               class="rounded-circle"
                               style="width: 38px; height: 38px; object-fit: cover;">
                        @else
                          <span class="avatar-initial rounded-circle bg-label-{{ $user->role === 'landlord' ? 'info' : ($user->role === 'admin' ? 'danger' : 'primary') }}">
                            {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
                          </span>
                        @endif
                      </div>
                      <div>
                        <strong>{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}</strong>
                        @if($user->student_number)
                          <br><small class="text-muted">{{ $user->student_number }}</small>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>{{ $user->email }}</td>
                  <td>
                    @switch($user->role)
                      @case('admin')
                        <span class="badge bg-label-danger">Admin</span>
                        @break
                      @case('landlord')
                        <span class="badge bg-label-info">Landlord</span>
                        @break
                      @case('user')
                        <span class="badge bg-label-primary">Tenant</span>
                        @break
                      @default
                        <span class="badge bg-label-secondary">{{ ucfirst($user->role) }}</span>
                    @endswitch
                  </td>
                  <td>
                    @if($user->contact_number)
                      <i class='bx bx-phone'></i> {{ $user->contact_number }}
                    @else
                      <span class="text-muted">N/A</span>
                    @endif
                  </td>
                  <td>
                    {{ $user->created_at->format('M d, Y') }}<br>
                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                  </td>
                  <td>
                    @if($user->approval_status === 'approved')
                      <span class="badge bg-success">Approved</span>
                    @elseif($user->approval_status === 'pending')
                      <span class="badge bg-warning">Pending</span>
                    @elseif($user->approval_status === 'rejected')
                      <span class="badge bg-danger">Rejected</span>
                    @else
                      <span class="badge bg-secondary">Unknown</span>
                    @endif
                  </td>
                  <td>
                    <div class="dropdown">
                      <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class='bx bx-dots-vertical-rounded'></i>
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.users.view', $user->id) }}">
                          <i class='bx bx-show me-1'></i> View Details
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}">
                          <i class='bx bx-edit me-1'></i> Edit
                        </a>
                        
                        @if($user->approval_status === 'pending')
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item text-success" href="javascript:void(0);" onclick="approveUser({{ $user->id }})">
                            <i class='bx bx-check-circle me-1'></i> Approve
                          </a>
                          <a class="dropdown-item text-warning" href="javascript:void(0);" onclick="showRejectModal({{ $user->id }}, '{{ $user->first_name ?? $user->name }}')">
                            <i class='bx bx-x-circle me-1'></i> Reject
                          </a>
                        @endif
                        
                        @if($user->id !== Auth::id())
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteUser({{ $user->id }})">
                            <i class='bx bx-trash me-1'></i> Delete
                          </a>
                        @endif
                      </div>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-4">
                    <i class='bx bx-user-x bx-lg text-muted mb-2'></i>
                    <p class="text-muted mb-0">No users found</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 d-flex justify-content-center">
          <ul class="pagination pagination-sm mb-0 shadow-sm rounded">
            {{-- Previous Page Link --}}
            @if ($users->onFirstPage())
              <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
              <li class="page-item"><a class="page-link" href="{{ $users->previousPageUrl() }}" rel="prev">&laquo;</a></li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($users->links()->elements[0] ?? [] as $page => $url)
              @if ($page == $users->currentPage())
                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
              @else
                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
              @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($users->hasMorePages())
              <li class="page-item"><a class="page-link" href="{{ $users->nextPageUrl() }}" rel="next">&raquo;</a></li>
            @else
              <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
          </ul>
        </div>

      </div>
    </div>
  </div>

</div>

<!-- Reject User Modal -->
<div class="modal fade" id="rejectUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reject User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="rejectUserForm" method="POST">
        @csrf
        <div class="modal-body">
          <p>You are about to reject the user: <strong id="rejectUserName"></strong></p>
          <div class="mb-3">
            <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
            <textarea class="form-control" name="rejection_reason" rows="4" required placeholder="Please provide a reason for rejection..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Reject User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="first_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="last_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control" name="email" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Role <span class="text-danger">*</span></label>
              <select class="form-select" name="role" required>
                <option value="">Select Role</option>
                <option value="user">Tenant</option>
                <option value="landlord">Landlord</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Number</label>
              <input type="text" class="form-control" name="contact_number">
            </div>
            <div class="col-md-6">
              <label class="form-label">Student Number</label>
              <input type="text" class="form-control" name="student_number">
            </div>
            <div class="col-md-6">
              <label class="form-label">Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control" name="password_confirmation" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create User</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
function approveUser(userId) {
  if (confirm('Are you sure you want to approve this user?')) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/users/${userId}/approve`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    document.body.appendChild(form);
    form.submit();
  }
}

function showRejectModal(userId, userName) {
  document.getElementById('rejectUserName').textContent = userName;
  document.getElementById('rejectUserForm').action = `/admin/users/${userId}/reject`;
  
  const modal = new bootstrap.Modal(document.getElementById('rejectUserModal'));
  modal.show();
}

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