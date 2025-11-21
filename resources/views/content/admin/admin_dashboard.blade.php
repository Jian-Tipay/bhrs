@extends('layouts/contentNavbarLayout')

@section('title', 'Admin Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // User Growth Chart
  const userGrowthData = @json(array_values($userGrowthData));
  const userGrowthLabels = @json(array_keys($userGrowthData));
  
  const userGrowthChart = new ApexCharts(document.querySelector("#userGrowthChart"), {
    series: [{
      name: 'New Users',
      data: userGrowthData
    }],
    chart: {
      height: 300,
      type: 'area',
      toolbar: { show: false }
    },
    colors: ['#696cff'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 3 },
    xaxis: {
      categories: userGrowthLabels
    },
    yaxis: {
      labels: {
        formatter: function(val) {
          return Math.floor(val);
        }
      }
    },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.7,
        opacityTo: 0.3
      }
    }
  });
  userGrowthChart.render();

  // Booking Status Chart
  const bookingStatusData = @json(array_values($bookingStatusData));
  const bookingStatusLabels = @json(array_keys($bookingStatusData));
  
  const bookingStatusChart = new ApexCharts(document.querySelector("#bookingStatusChart"), {
    series: bookingStatusData,
    chart: {
      height: 300,
      type: 'donut'
    },
    labels: bookingStatusLabels,
    colors: ['#ffc107', '#28a745', '#17a2b8', '#6c757d', '#dc3545'],
    legend: {
      position: 'bottom'
    }
  });
  bookingStatusChart.render();

  // Revenue Chart
  const revenueData = @json(array_values($revenueData));
  const revenueLabels = @json(array_keys($revenueData));
  
  const revenueChart = new ApexCharts(document.querySelector("#revenueChart"), {
    series: [{
      name: 'Revenue',
      data: revenueData
    }],
    chart: {
      height: 300,
      type: 'bar',
      toolbar: { show: false }
    },
    colors: ['#28a745'],
    plotOptions: {
      bar: {
        borderRadius: 8,
        columnWidth: '45%'
      }
    },
    dataLabels: { enabled: false },
    xaxis: {
      categories: revenueLabels
    },
    yaxis: {
      labels: {
        formatter: function(val) {
          return '₱' + val.toLocaleString();
        }
      }
    }
  });
  revenueChart.render();
});
</script>
@endsection

@section('content')
<div class="row">
  
  <!-- Welcome Banner -->
  <div class="col-12 mb-4">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <h4 class="text-white mb-1">Welcome back, Admin {{ Auth::user()->first_name }}! 👋</h4>
        <p class="mb-0">Here's what's happening with your platform today</p>
      </div>
    </div>
  </div>

  <!-- Statistics Cards Row 1 -->
  <div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Total Users</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="mb-0 me-2">{{ number_format($totalUsers) }}</h4>
              <small class="text-success">+{{ $newUsersThisMonth }} this month</small>
            </div>
            <small class="text-muted">{{ $totalLandlords }} Landlords, {{ $totalTenants }} Tenants</small>
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

  <div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Properties</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="mb-0 me-2">{{ number_format($totalProperties) }}</h4>
              @if($pendingProperties > 0)
                <small class="text-warning">{{ $pendingProperties }} pending</small>
              @endif
            </div>
            <small class="text-muted">{{ $activeProperties }} Active</small>
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

  <div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Total Bookings</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="mb-0 me-2">{{ number_format($totalBookings) }}</h4>
              @if($pendingBookings > 0)
                <small class="text-warning">{{ $pendingBookings }} pending</small>
              @endif
            </div>
            <small class="text-muted">{{ $activeBookings }} Active</small>
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

  <div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Monthly Revenue</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="mb-0 me-2">₱{{ number_format($monthlyRevenue, 2) }}</h4>
            </div>
            <small class="text-muted">{{ $totalReviews }} Reviews ({{ number_format($averageRating, 1) }}★)</small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-warning rounded p-2">
              <i class='bx bx-wallet bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="col-lg-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">User Growth</h5>
        <small class="text-muted">Last 6 months</small>
      </div>
      <div class="card-body">
        <div id="userGrowthChart"></div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Booking Status</h5>
        <small class="text-muted">Distribution</small>
      </div>
      <div class="card-body">
        <div id="bookingStatusChart"></div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Revenue Trend</h5>
        <small class="text-muted">Last 6 months</small>
      </div>
      <div class="card-body">
        <div id="revenueChart"></div>
      </div>
    </div>
  </div>

  <!-- Recent Activities -->
  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Recent Users</h5>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Joined</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentUsers as $recentUser)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded-circle bg-label-primary">
                          {{ strtoupper(substr($recentUser->first_name, 0, 1)) }}
                        </span>
                      </div>
                      <div>
                        <strong>{{ $recentUser->first_name }} {{ $recentUser->last_name }}</strong><br>
                        <small class="text-muted">{{ $recentUser->email }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-label-{{ $recentUser->role === 'landlord' ? 'info' : 'primary' }}">
                      {{ ucfirst($recentUser->role) }}
                    </span>
                  </td>
                  <td>{{ $recentUser->created_at->diffForHumans() }}</td>
                  <td>
                    <a href="{{ route('admin.users.view', $recentUser->id) }}" class="btn btn-sm btn-icon btn-outline-primary">
                      <i class='bx bx-show'></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">No recent users</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Recent Properties</h5>
        <a href="{{ route('admin.properties.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Property</th>
                <th>Landlord</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentProperties as $property)
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
                      <div>
                        <strong>{{ Str::limit($property->title, 20) }}</strong><br>
                        <small class="text-muted">₱{{ number_format($property->price, 2) }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($property->landlord && $property->landlord->user)
                      {{ $property->landlord->user->first_name }} {{ $property->landlord->user->last_name }}
                    @else
                      N/A
                    @endif
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
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">No recent properties</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Bookings -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Recent Bookings</h5>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Tenant</th>
                <th>Property</th>
                <th>Move-in Date</th>
                <th>Status</th>
                <th>Created</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentBookings as $booking)
                <tr>
                  <td><strong>#{{ $booking->id }}</strong></td>
                  <td>{{ $booking->user->first_name }} {{ $booking->user->last_name }}</td>
                  <td>{{ Str::limit($booking->property->title, 30) }}</td>
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
                      @default
                        <span class="badge bg-secondary">{{ $booking->status }}</span>
                    @endswitch
                  </td>
                  <td>{{ $booking->created_at->diffForHumans() }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted">No recent bookings</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Top Properties -->
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Top Rated Properties</h5>
      </div>
      <div class="card-body">
        <div class="row">
          @forelse($topProperties as $topProperty)
            <div class="col-md-4 mb-3">
              <div class="card border">
                @if($topProperty->images && $topProperty->images->count() > 0)
                  <img src="{{ asset('storage/' . $topProperty->images->first()->image_path) }}" 
                       class="card-img-top" 
                       style="height: 200px; object-fit: cover;"
                       alt="{{ $topProperty->title }}">
                @else
                  <div class="card-img-top bg-label-secondary d-flex align-items-center justify-content-center" 
                       style="height: 200px;">
                    <i class='bx bx-building-house bx-lg'></i>
                  </div>
                @endif
                <div class="card-body">
                  <h6 class="card-title">{{ $topProperty->title }}</h6>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-warning">
                      ⭐ {{ number_format($topProperty->ratings_avg_rating ?? 0, 1) }}
                    </span>
                    <small class="text-muted">{{ $topProperty->ratings_count }} reviews</small>
                  </div>
                  <p class="card-text">
                    <i class='bx bx-money'></i> ₱{{ number_format($topProperty->price, 2) }}/mo
                  </p>
                  <a href="{{ route('properties.show', $topProperty->id) }}" class="btn btn-sm btn-outline-primary w-100">
                    View Details
                  </a>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12">
              <p class="text-center text-muted">No properties available</p>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection