@extends('layouts/contentNavbarLayout')

@section('title', 'Landlord Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
<style>
  .property-card {
    transition: transform 0.3s, box-shadow 0.3s;
    height: 100%;
  }
  .property-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  }
  .status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    z-index: 10;
  }
  .property-img {
    height: 200px;
    object-fit: cover;
    width: 100%;
  }
  .stat-card {
    transition: all 0.3s ease;
  }
  .stat-card:hover {
    transform: translateY(-5px);
  }
  .quick-action-btn {
    transition: all 0.3s ease;
  }
  .quick-action-btn:hover {
    transform: scale(1.05);
  }
</style>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
<script>
  document.addEventListener("DOMContentLoaded", () => {
    // Revenue chart
    @if(isset($revenueChartData) && is_array($revenueChartData))
    const revenueOptions = {
      series: [{
        name: 'Revenue',
        data: @json(array_values($revenueChartData))
      }],
      chart: {
        height: 250,
        type: 'area',
        toolbar: { show: false }
      },
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 3 },
      colors: ['#696cff'],
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.7,
          opacityTo: 0.3,
        }
      },
      xaxis: {
        categories: @json(array_keys($revenueChartData))
      },
      yaxis: {
        labels: {
          formatter: function (value) {
            return '₱' + value.toLocaleString();
          }
        }
      }
    };
    const revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
    revenueChart.render();
    @endif

    // Bookings chart
    @if(isset($bookingChartData) && is_array($bookingChartData))
    const bookingsOptions = {
      series: @json(array_values($bookingChartData)),
      chart: {
        type: 'donut',
        height: 250
      },
      labels: @json(array_keys($bookingChartData)),
      colors: ['#696cff', '#71dd37', '#ffab00', '#ff3e1d'],
      legend: {
        position: 'bottom'
      },
      plotOptions: {
        pie: {
          donut: {
            size: '70%',
            labels: {
              show: true,
              total: {
                show: true,
                label: 'Total Bookings',
                formatter: function (w) {
                  return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                }
              }
            }
          }
        }
      }
    };
    const bookingsChart = new ApexCharts(document.querySelector("#bookingsChart"), bookingsOptions);
    bookingsChart.render();
    @endif
  });
</script>
@endsection

@section('content')
<div class="row">

  <!-- Welcome Banner -->
  <div class="col-12 mb-4">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <h4 class="text-white mb-1">Welcome back, {{ Auth::user()->first_name ?? Auth::user()->name }}! 👋</h4>
        <p class="mb-0">Manage your properties and track your performance</p>
      </div>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card stat-card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Total Properties</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="mb-0 me-2">{{ $totalProperties ?? 0 }}</h4>
              <small class="text-success">
                <i class='bx bx-up-arrow-alt'></i>
                {{ $activeProperties ?? 0 }} active
              </small>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
              <i class='bx bx-building-house bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card stat-card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Total Bookings</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="mb-0 me-2">{{ $totalBookings ?? 0 }}</h4>
              <small class="text-warning">
                <i class='bx bx-time'></i>
                {{ $pendingBookings ?? 0 }} pending
              </small>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-warning rounded p-2">
              <i class='bx bx-calendar bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card stat-card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Monthly Revenue</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="mb-0 me-2">₱{{ number_format($monthlyRevenue ?? 0) }}</h4>
              <small class="text-success">
                <i class='bx bx-trending-up'></i>
                +{{ $revenueGrowth ?? 0 }}%
              </small>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-success rounded p-2">
              <i class='bx bx-wallet bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card stat-card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Avg Rating</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="mb-0 me-2">{{ number_format($averageRating ?? 0, 1) }}</h4>
              <small class="text-muted">
                <i class='bx bx-star'></i>
                {{ $totalReviews ?? 0 }} reviews
              </small>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-danger rounded p-2">
              <i class='bx bx-star bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">⚡ Quick Actions</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <a href="{{ route('landlord.properties.create') }}" class="btn btn-primary w-100 quick-action-btn">
              <i class='bx bx-plus-circle me-2'></i>
              Add New Property
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('landlord.bookings.index') }}" class="btn btn-warning w-100 quick-action-btn">
              <i class='bx bx-time-five me-2'></i>
              View Bookings
              @if(($pendingBookings ?? 0) > 0)
                <span class="badge bg-white text-warning ms-1">{{ $pendingBookings }}</span>
              @endif
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('landlord.properties.show') }}" class="btn btn-info w-100 quick-action-btn">
              <i class='bx bx-list-ul me-2'></i>
              Manage Properties
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('landlord.reports.index') }}" class="btn btn-success w-100 quick-action-btn">
              <i class='bx bx-bar-chart me-2'></i>
              View Reports
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <div>
          <h5 class="mb-1">📈 Revenue Overview</h5>
          <small class="text-muted">Last 6 months</small>
        </div>
      </div>
      <div class="card-body">
        @if(isset($revenueChartData) && is_array($revenueChartData) && count($revenueChartData) > 0)
          <div id="revenueChart"></div>
        @else
          <div class="text-center py-5">
            <i class='bx bx-line-chart display-1 text-muted mb-3'></i>
            <p class="text-muted">No revenue data available yet</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-1">📊 Booking Status</h5>
        <small class="text-muted">Current distribution</small>
      </div>
      <div class="card-body">
        @if(isset($bookingChartData) && is_array($bookingChartData) && count($bookingChartData) > 0)
          <div id="bookingsChart"></div>
        @else
          <div class="text-center py-5">
            <i class='bx bx-pie-chart display-1 text-muted mb-3'></i>
            <p class="text-muted">No booking data available yet</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Recent Bookings -->
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">🔔 Recent Bookings</h5>
        <a href="" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Property</th>
                <th>Tenant</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentBookings ?? [] as $booking)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <img src="{{ $booking->property->images->first()->image_path ?? asset('assets/img/boarding/default.jpg') }}" 
                         alt="{{ $booking->property->title }}" 
                         class="rounded me-2" 
                         style="width: 40px; height: 40px; object-fit: cover;">
                    <span>{{ $booking->property->title }}</span>
                  </div>
                </td>
                <td>{{ $booking->user->first_name ?? $booking->user->name }}</td>
                <td>{{ $booking->created_at->format('M d, Y') }}</td>
                <td>
                  @if($booking->status == 'Pending')
                    <span class="badge bg-warning">Pending</span>
                  @elseif($booking->status == 'Approved')
                    <span class="badge bg-success">Approved</span>
                  @elseif($booking->status == 'Active')
                    <span class="badge bg-info">Active</span>
                  @elseif($booking->status == 'Completed')
                    <span class="badge bg-secondary">Completed</span>
                  @elseif($booking->status == 'Cancelled')
                    <span class="badge bg-danger">Cancelled</span>
                  @endif
                </td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      Actions
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="{{ route('landlord.bookings.show', $booking->booking_id) }}">
                        <i class='bx bx-show me-1'></i> View Details
                      </a></li>
                      @if($booking->status == 'Pending')
                      <li>
                        <form action="{{ route('booking.approve', $booking->booking_id) }}" method="POST">
                          @csrf
                          @method('PATCH')
                          <button type="submit" class="dropdown-item text-success">
                            <i class='bx bx-check me-1'></i> Approve
                          </button>
                        </form>
                      </li>
                      <li>
                        <form action="{{ route('booking.reject', $booking->booking_id) }}" method="POST">
                          @csrf
                          @method('PATCH')
                          <button type="submit" class="dropdown-item text-danger">
                            <i class='bx bx-x me-1'></i> Reject
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
                <td colspan="5" class="text-center py-4 text-muted">
                  No recent bookings found
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Property Performance -->
  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">🏆 Top Performing Properties</h5>
      </div>
      <div class="card-body">
        @forelse($topProperties ?? [] as $property)
        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
          <img src="{{ $property->images->first()->image_path ?? asset('assets/img/boarding/default.jpg') }}" 
               alt="{{ $property->title }}" 
               class="rounded me-3" 
               style="width: 60px; height: 60px; object-fit: cover;">
          <div class="flex-grow-1">
            <h6 class="mb-1">{{ $property->title }}</h6>
            <small class="text-muted">
              <i class='bx bx-star text-warning'></i> 
              {{ number_format($property->ratings_avg_rating ?? 0, 1) }} 
              • {{ $property->bookings_count ?? 0 }} bookings
            </small>
          </div>
          <div class="text-end">
            <h6 class="mb-0 text-success">₱{{ number_format($property->monthly_revenue ?? 0) }}</h6>
            <small class="text-muted">this month</small>
          </div>
        </div>
        @empty
        <p class="text-muted text-center py-4">No data available yet</p>
        @endforelse
      </div>
    </div>
  </div>

  <!-- My Properties -->
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-1">🏘️ My Properties</h5>
          <small class="text-muted">{{ count($myProperties ?? []) }} properties listed</small>
        </div>
        <a href="" class="btn btn-primary">
          <i class='bx bx-plus'></i> Add Property
        </a>
      </div>
      <div class="card-body">
        <div class="row">
          @forelse($myProperties ?? [] as $property)
            <div class="col-md-4 mb-3">
              <div class="card property-card">
                <div class="position-relative">
                  @if($property->images && $property->images->count() > 0)
                    <img src="{{ asset('storage/' . $property->images->first()->image_path) }}" 
                         class="property-img" alt="{{ $property->title }}">
                  @else
                    <img src="{{ asset('assets/img/boarding/default.jpg') }}" 
                         class="property-img" alt="Default">
                  @endif
                  @if($property->is_active)
                    <span class="status-badge bg-success">Active</span>
                  @else
                    <span class="status-badge bg-secondary">Inactive</span>
                  @endif
                </div>
                <div class="card-body">
                  <h5 class="card-title">{{ $property->title }}</h5>
                  <p class="card-text">
                    <i class='bx bx-map'></i> {{ $property->address }}<br>
                    <i class='bx bx-money'></i> ₱{{ number_format($property->price, 2) }} / month<br>
                    <i class='bx bx-bed'></i> {{ $property->available_slots ?? 0 }} / {{ $property->capacity ?? 0 }} slots available
                  </p>
                  @if($property->ratings_avg_rating > 0)
                  <div class="mb-2">
                    <span class="badge bg-warning">⭐ {{ number_format($property->ratings_avg_rating, 1) }}</span>
                    <small class="text-muted">({{ $property->ratings_count ?? 0 }} reviews)</small>
                  </div>
                  @endif
                  <div class="d-flex gap-2">
                   <a href="{{ route('landlord.properties.edit', $property->id) }}" class="btn btn-sm btn-primary">Edit</a>
                      <i class='bx bx-edit'></i> Edit
                    </a>
                    <a href="{{ route('landlord.properties.show', $property->id) }}" class="btn btn-sm btn-outline-primary">
                      <i class='bx bx-show'></i> View
                    </a>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12">
              <div class="text-center py-5">
                <i class='bx bx-home display-1 text-muted mb-3'></i>
                <h5 class="text-muted mb-2">No Properties Yet</h5>
                <p class="text-muted mb-4">Start by adding your first property</p>
                <a href="" class="btn btn-primary">
                  <i class='bx bx-plus'></i> Add Your First Property
                </a>
              </div>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

</div>
@endsection