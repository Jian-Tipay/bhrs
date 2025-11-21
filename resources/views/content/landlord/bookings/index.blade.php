@extends('layouts/contentNavbarLayout')

@section('title', 'Booking Requests')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-1">📅 Booking Requests</h4>
        <p class="mb-0 text-muted">Manage booking requests from potential tenants</p>
      </div>
    </div>
  </div>

  <!-- Stats Overview -->
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <p class="card-text">Total Bookings</p>
            <h4 class="mb-0">{{ $bookings->total() ?? 0 }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
              <i class='bx bx-calendar bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <p class="card-text">Pending</p>
            <h4 class="mb-0">{{ $bookings->where('status', 'Pending')->count() ?? 0 }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-warning rounded p-2">
              <i class='bx bx-time bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <p class="card-text">Approved</p>
            <h4 class="mb-0">{{ $bookings->where('status', 'Approved')->count() ?? 0 }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-success rounded p-2">
              <i class='bx bx-check-circle bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <p class="card-text">Active</p>
            <h4 class="mb-0">{{ $bookings->where('status', 'Active')->count() ?? 0 }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-2">
              <i class='bx bx-user-check bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <form method="GET" action="{{ route('landlord.bookings.index') }}">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Property</label>
              <select class="form-select" name="property_id">
                <option value="">All Properties</option>
                @foreach($properties as $property)
                  <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                    {{ $property->title }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select class="form-select" name="status">
                <option value="">All Status</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Sort By</label>
              <select class="form-select" name="sort">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">&nbsp;</label>
              <button type="submit" class="btn btn-primary w-100">
                <i class='bx bx-filter'></i> Apply Filters
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bookings List -->
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Property</th>
                <th>Tenant</th>
                <th>Date Requested</th>
                <th>Move-in Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($bookings as $booking)
              <tr>
                <td>
                  <span class="badge bg-label-secondary">#{{ $booking->booking_id }}</span>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    @if($booking->property->image)
                      <img src="{{ asset('storage/' . $booking->property->image) }}" 
                           alt="{{ $booking->property->title }}" 
                           class="rounded me-2" 
                           style="width: 40px; height: 40px; object-fit: cover;">
                    @else
                      <img src="{{ asset('assets/img/boarding/default.jpg') }}" 
                           alt="Default" 
                           class="rounded me-2" 
                           style="width: 40px; height: 40px; object-fit: cover;">
                    @endif
                    <div>
                      <span class="fw-bold">{{ Str::limit($booking->property->title, 30) }}</span><br>
                      <small class="text-muted">₱{{ number_format($booking->property->price, 2) }}/month</small>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                      <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                    </div>
                    <div>
                      <span class="fw-bold">{{ $booking->user->first_name ?? $booking->user->name }}</span><br>
                      <small class="text-muted">{{ $booking->user->email }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <span>{{ $booking->created_at->format('M d, Y') }}</span><br>
                  <small class="text-muted">{{ $booking->created_at->diffForHumans() }}</small>
                </td>
                <td>
                  <span>{{ \Carbon\Carbon::parse($booking->move_in_date)->format('M d, Y') ?? 'N/A' }}</span>
                </td>
                <td>
                  @if($booking->status == 'Pending')
                    <span class="badge bg-warning">
                      <i class='bx bx-time'></i> Pending
                    </span>
                  @elseif($booking->status == 'Approved')
                    <span class="badge bg-success">
                      <i class='bx bx-check'></i> Approved
                    </span>
                  @elseif($booking->status == 'Active')
                    <span class="badge bg-info">
                      <i class='bx bx-user-check'></i> Active
                    </span>
                  @elseif($booking->status == 'Completed')
                    <span class="badge bg-secondary">
                      <i class='bx bx-check-circle'></i> Completed
                    </span>
                  @elseif($booking->status == 'Cancelled')
                    <span class="badge bg-danger">
                      <i class='bx bx-x'></i> Cancelled
                    </span>
                  @endif
                </td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      <i class='bx bx-dots-vertical-rounded'></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item" href="{{ route('landlord.bookings.show', $booking->booking_id) }}">
                          <i class='bx bx-show me-1'></i> View Details
                        </a>
                      </li>
                      
                      @if($booking->status == 'Pending')
                        <li>
                          <form action="{{ route('landlord.bookings.approve', $booking->booking_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="dropdown-item text-success" onclick="return confirm('Approve this booking?')">
                              <i class='bx bx-check me-1'></i> Approve
                            </button>
                          </form>
                        </li>
                        <li>
                          <form action="{{ route('landlord.bookings.reject', $booking->booking_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Reject this booking?')">
                              <i class='bx bx-x me-1'></i> Reject
                            </button>
                          </form>
                        </li>
                      @endif

                      @if($booking->status == 'Approved' || $booking->status == 'Active')
                        <li>
                          <form action="{{ route('landlord.bookings.cancel', $booking->booking_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="dropdown-item text-warning" onclick="return confirm('Cancel this booking?')">
                              <i class='bx bx-x-circle me-1'></i> Cancel
                            </button>
                          </form>
                        </li>
                      @endif

                      @if($booking->status == 'Active')
                        <li>
                          <form action="{{ route('landlord.bookings.complete', $booking->booking_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="dropdown-item text-info" onclick="return confirm('Mark this booking as completed?')">
                              <i class='bx bx-check-circle me-1'></i> Complete
                            </button>
                          </form>
                        </li>
                      @endif
                    </ul>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center py-5">
                  <i class='bx bx-calendar-x display-1 text-muted mb-3'></i>
                  <h5 class="text-muted">No Bookings Found</h5>
                  <p class="text-muted">Booking requests will appear here</p>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
        <div class="mt-3">
          {{ $bookings->links() }}
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
  // Auto-dismiss success/error messages after 5 seconds
  setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    });
  }, 5000);
</script>
@endsection