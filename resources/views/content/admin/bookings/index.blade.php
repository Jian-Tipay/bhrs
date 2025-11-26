@extends('layouts/contentNavbarLayout')

@section('title', 'Booking Management')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h4 class="mb-1">Booking Management</h4>
            <p class="text-muted mb-0">Monitor and manage all property bookings</p>
          </div>
          <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
              <i class='bx bx-arrow-back'></i> Back to Dashboard
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="col-lg-2 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Total</p>
            <h4 class="mb-0">{{ number_format($bookings->total()) }}</h4>
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

  <div class="col-lg-2 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Pending</p>
            <h4 class="mb-0 text-warning">{{ number_format($bookings->where('status', 'Pending')->count()) }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-warning rounded p-2">
              <i class='bx bx-time-five bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-2 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Approved</p>
            <h4 class="mb-0 text-info">{{ number_format($bookings->where('status', 'Approved')->count()) }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-2">
              <i class='bx bx-check-circle bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-2 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Active</p>
            <h4 class="mb-0 text-success">{{ number_format($bookings->where('status', 'Active')->count()) }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-success rounded p-2">
              <i class='bx bx-calendar-check bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-2 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Completed</p>
            <h4 class="mb-0 text-secondary">{{ number_format($bookings->where('status', 'Completed')->count()) }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-secondary rounded p-2">
              <i class='bx bx-check-double bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-2 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Cancelled</p>
            <h4 class="mb-0 text-danger">{{ number_format($bookings->where('status', 'Cancelled')->count()) }}</h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-danger rounded p-2">
              <i class='bx bx-x-circle bx-sm'></i>
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
        <form method="GET" action="{{ route('admin.bookings.index') }}">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Date From</label>
              <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Date To</label>
              <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">&nbsp;</label>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class='bx bx-search'></i> Filter
                </button>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                  <i class='bx bx-reset'></i> Reset
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bookings Table -->
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Bookings</h5>
        <span class="badge bg-label-primary">{{ $bookings->total() }} Total</span>
      </div>
      <div class="card-body">
        @if($bookings->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Tenant</th>
                  <th>Property</th>
                  <th>Landlord</th>
                  <th>Move-in Date</th>
                  <th>Move-out Date</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($bookings as $booking)
                  <tr>
                    <td>
                      <strong class="text-primary">#{{ $booking->booking_id }}</strong>
                    </td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                          @if($booking->user->profile_picture)
                            <img src="{{ asset($booking->user->profile_picture) }}" 
                                 alt="{{ $booking->user->first_name }}" 
                                 class="rounded-circle"
                                 style="width: 38px; height: 38px; object-fit: cover;">
                          @else
                            <span class="avatar-initial rounded-circle bg-label-primary">
                              {{ strtoupper(substr($booking->user->first_name, 0, 1)) }}
                            </span>
                          @endif
                        </div>
                        <div>
                          <strong>{{ $booking->user->first_name }} {{ $booking->user->last_name }}</strong><br>
                          <small class="text-muted">{{ $booking->user->email }}</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex align-items-center">
                        @if($booking->property->images && $booking->property->images->count() > 0)
                          <img src="{{ asset('storage/' . $booking->property->images->first()->image_path) }}" 
                               alt="{{ $booking->property->title }}" 
                               class="rounded me-2" 
                               style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                          <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-info">
                              <i class='bx bx-building-house'></i>
                            </span>
                          </div>
                        @endif
                        <div>
                          <strong>{{ Str::limit($booking->property->title, 25) }}</strong><br>
                          <small class="text-muted">₱{{ number_format($booking->property->price, 2) }}/mo</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      @if($booking->property->landlord && $booking->property->landlord->user)
                        <div>
                          <strong>{{ $booking->property->landlord->user->first_name }} {{ $booking->property->landlord->user->last_name }}</strong><br>
                          <small class="text-muted">{{ $booking->property->landlord->user->contact_number ?? 'N/A' }}</small>
                        </div>
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      @if($booking->move_in_date)
                        <strong>{{ \Carbon\Carbon::parse($booking->move_in_date)->format('M d, Y') }}</strong><br>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($booking->move_in_date)->diffForHumans() }}</small>
                      @else
                        <span class="text-muted">Not set</span>
                      @endif
                    </td>
                    <td>
                      @if($booking->move_out_date)
                        <strong>{{ \Carbon\Carbon::parse($booking->move_out_date)->format('M d, Y') }}</strong><br>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($booking->move_out_date)->diffForHumans() }}</small>
                      @else
                        <span class="text-muted">Not set</span>
                      @endif
                    </td>
                    <td>
                      @switch($booking->status)
                        @case('Pending')
                          <span class="badge bg-warning">
                            <i class='bx bx-time'></i> Pending
                          </span>
                          @break
                        @case('Approved')
                          <span class="badge bg-info">
                            <i class='bx bx-check'></i> Approved
                          </span>
                          @break
                        @case('Active')
                          <span class="badge bg-success">
                            <i class='bx bx-check-circle'></i> Active
                          </span>
                          @break
                        @case('Completed')
                          <span class="badge bg-secondary">
                            <i class='bx bx-check-double'></i> Completed
                          </span>
                          @break
                        @case('Cancelled')
                          <span class="badge bg-danger">
                            <i class='bx bx-x'></i> Cancelled
                          </span>
                          @break
                        @default
                          <span class="badge bg-secondary">{{ $booking->status }}</span>
                      @endswitch
                    </td>
                    <td>
                      <small>{{ $booking->created_at->format('M d, Y') }}</small><br>
                      <small class="text-muted">{{ $booking->created_at->format('h:i A') }}</small>
                    </td>
                    <td>
                      <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-icon btn-outline-primary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i class='bx bx-dots-vertical-rounded'></i>
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="{{ route('properties.view', $booking->property->id) }}" target="_blank">
                            <i class='bx bx-building-house me-1'></i> View Property
                          </a>
                          <a class="dropdown-item" href="{{ route('admin.users.view', $booking->user->id) }}">
                            <i class='bx bx-user me-1'></i> View Tenant
                          </a>
                          @if($booking->property->landlord && $booking->property->landlord->user)
                            <a class="dropdown-item" href="{{ route('admin.users.view', $booking->property->landlord->user->id) }}">
                              <i class='bx bx-user-circle me-1'></i> View Landlord
                            </a>
                          @endif
                          <div class="dropdown-divider"></div>
                          <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bookingDetailsModal{{ $booking->booking_id }}">
                            <i class='bx bx-info-circle me-1'></i> View Details
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>

                  <!-- Booking Details Modal -->
                  <div class="modal fade" id="bookingDetailsModal{{ $booking->booking_id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Booking Details #{{ $booking->booking_id }}</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="row mb-3">
                            <div class="col-6">
                              <small class="text-muted">Tenant</small>
                              <div class="d-flex align-items-center gap-2 mt-1">
                                <img src="{{ $booking->user->profile_picture ? asset($booking->user->profile_picture) : asset('assets/img/avatars/1.png') }}" 
                                    alt="Tenant Avatar" 
                                    class="rounded-circle" 
                                    style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                  <p class="mb-0"><strong>{{ $booking->user->first_name }} {{ $booking->user->last_name }}</strong></p>
                                  <small>{{ $booking->user->email }}</small>
                                </div>
                              </div>
                            </div>
                            <div class="col-6">
                              <small class="text-muted">Status</small>
                              <p class="mb-0">
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
                              </p>
                            </div>
                          </div>

                          <div class="row mb-3">
                            <div class="col-6">
                              <small class="text-muted">Contact Number</small>
                              <p class="mb-0">
                                @if($booking->user->contact_number)
                                  <i class='bx bx-phone me-1'></i>{{ $booking->user->contact_number }}
                                @else
                                  <span class="text-muted">Not provided</span>
                                @endif
                              </p>
                            </div>
                            <div class="col-6">
                              <small class="text-muted">Guardian Number</small>
                              <p class="mb-0">
                                @if($booking->user->guardian_number)
                                  <i class='bx bx-phone me-1'></i>{{ $booking->user->guardian_number }}
                                @else
                                  <span class="text-muted">Not provided</span>
                                @endif
                              </p>
                            </div>
                          </div>

                          <hr>

                          <div class="mb-3">
                            <small class="text-muted">Property</small>
                            <p class="mb-0"><strong>{{ $booking->property->title }}</strong></p>
                            <small>{{ $booking->property->address }}</small>
                          </div>

                          <div class="row mb-3">
                            <div class="col-6">
                              <small class="text-muted">Move-in Date</small>
                              <p class="mb-0">
                                @if($booking->move_in_date)
                                  {{ \Carbon\Carbon::parse($booking->move_in_date)->format('F d, Y') }}
                                @else
                                  <span class="text-muted">Not set</span>
                                @endif
                              </p>
                            </div>
                            <div class="col-6">
                              <small class="text-muted">Move-out Date</small>
                              <p class="mb-0">
                                @if($booking->move_out_date)
                                  {{ \Carbon\Carbon::parse($booking->move_out_date)->format('F d, Y') }}
                                @else
                                  <span class="text-muted">Not set</span>
                                @endif
                              </p>
                            </div>
                          </div>

                          <div class="row mb-3">
                            <div class="col-6">
                              <small class="text-muted">Monthly Rate</small>
                              <p class="mb-0"><strong class="text-primary">₱{{ number_format($booking->property->price, 2) }}</strong></p>
                            </div>
                            <div class="col-6">
                              <small class="text-muted">Created</small>
                              <p class="mb-0">{{ $booking->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                          </div>

                          @if($booking->property->landlord && $booking->property->landlord->user)
                            <hr>
                            <div class="mb-3">
                              <small class="text-muted">Landlord</small>
                              <div class="d-flex align-items-center gap-2 mt-1">
                                <img src="{{ $booking->property->landlord->user->profile_picture ? asset($booking->property->landlord->user->profile_picture) : asset('assets/img/avatars/1.png') }}" 
                                    alt="Landlord Avatar" 
                                    class="rounded-circle" 
                                    style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                  <p class="mb-0"><strong>{{ $booking->property->landlord->user->first_name }} {{ $booking->property->landlord->user->last_name }}</strong></p>
                                  <small>{{ $booking->property->landlord->user->email }}</small><br>
                                  <small>{{ $booking->property->landlord->user->contact_number ?? 'No contact number' }}</small>
                                </div>
                              </div>
                            </div>
                          @endif
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                          <a href="{{ route('properties.view', $booking->property->id) }}" class="btn btn-primary" target="_blank">
                            View Property
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-4 d-flex justify-content-center">
            <ul class="pagination pagination-sm mb-0 shadow-sm rounded">
              {{-- Previous Page Link --}}
              @if ($bookings->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
              @else
                <li class="page-item"><a class="page-link" href="{{ $bookings->previousPageUrl() }}" rel="prev">&laquo;</a></li>
              @endif

              {{-- Pagination Elements --}}
              @foreach ($bookings->links()->elements[0] ?? [] as $page => $url)
                @if ($page == $bookings->currentPage())
                  <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @else
                  <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                @endif
              @endforeach

              {{-- Next Page Link --}}
              @if ($bookings->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $bookings->nextPageUrl() }}" rel="next">&raquo;</a></li>
              @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
              @endif
            </ul>
          </div>

        @else
          <div class="text-center py-5">
            <i class='bx bx-calendar-x bx-lg text-muted'></i>
            <p class="text-muted mt-3">No bookings found</p>
            @if(request()->hasAny(['status', 'date_from', 'date_to']))
              <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">
                Clear Filters
              </a>
            @endif
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@if(session('success'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      alert('{{ session('success') }}');
    });
  </script>
@endif

@if(session('error'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      alert('{{ session('error') }}');
    });
  </script>
@endif
@endsection