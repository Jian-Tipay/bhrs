@extends('layouts/contentNavbarLayout')

@section('title', 'Reports & Analytics')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h4 class="mb-1">📊 Reports & Analytics</h4>
            <p class="mb-0 text-muted">Track your business performance and insights</p>
          </div>
          <div class="d-flex gap-2">
            <div class="btn-group">
              <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class='bx bx-filter'></i> 
                @if(request('period') == 7)
                  Last 7 Days
                @elseif(request('period') == 30)
                  Last 30 Days
                @elseif(request('period') == 90)
                  Last 90 Days
                @elseif(request('period') == 365)
                  Last Year
                @else
                  Last 30 Days
                @endif
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('landlord.reports.index', ['period' => 7]) }}">Last 7 Days</a></li>
                <li><a class="dropdown-item" href="{{ route('landlord.reports.index', ['period' => 30]) }}">Last 30 Days</a></li>
                <li><a class="dropdown-item" href="{{ route('landlord.reports.index', ['period' => 90]) }}">Last 90 Days</a></li>
                <li><a class="dropdown-item" href="{{ route('landlord.reports.index', ['period' => 365]) }}">Last Year</a></li>
              </ul>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="dropdown">
              <i class='bx bx-download'></i> Export
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="{{ route('landlord.reports.export-pdf') }}"><i class='bx bxs-file-pdf me-2'></i>Export as PDF</a></li>
              <li><a class="dropdown-item" href="{{ route('landlord.reports.export-excel') }}"><i class='bx bxs-file me-2'></i>Export as Excel</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Key Metrics -->
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <p class="card-text text-muted mb-1">Total Revenue</p>
            <h3 class="mb-0">₱{{ number_format($totalRevenue ?? 0, 2) }}</h3>
            <small class="text-success">
              <i class='bx bx-up-arrow-alt'></i> 
              <span class="fw-semibold">12.5%</span> from last period
            </small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-success rounded p-3">
              <i class='bx bx-money bx-lg'></i>
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
            <p class="card-text text-muted mb-1">Occupancy Rate</p>
            <h3 class="mb-0">{{ number_format($occupancyRate ?? 0, 1) }}%</h3>
            <small class="text-info">
              <i class='bx bx-up-arrow-alt'></i> 
              <span class="fw-semibold">5.2%</span> from last period
            </small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-3">
              <i class='bx bx-home bx-lg'></i>
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
            <p class="card-text text-muted mb-1">Total Bookings</p>
            <h3 class="mb-0">{{ $totalBookings ?? 0 }}</h3>
            <small class="text-primary">
              <i class='bx bx-up-arrow-alt'></i> 
              <span class="fw-semibold">8.1%</span> from last period
            </small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-3">
              <i class='bx bx-calendar-check bx-lg'></i>
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
            <p class="card-text text-muted mb-1">Average Rating</p>
            <h3 class="mb-0">{{ number_format($averageRating ?? 0, 1) }}</h3>
            <small class="text-warning">
              <i class='bx bx-star'></i> 
              <span class="fw-semibold">{{ $totalReviews ?? 0 }}</span> reviews
            </small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-warning rounded p-3">
              <i class='bx bx-star bx-lg'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Revenue Chart -->
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Revenue Overview</h5>
        <div class="btn-group btn-group-sm" role="group">
          <input type="radio" class="btn-check" name="revenue-period" id="revenue-week" checked>
          <label class="btn btn-outline-primary" for="revenue-week">Week</label>
          
          <input type="radio" class="btn-check" name="revenue-period" id="revenue-month">
          <label class="btn btn-outline-primary" for="revenue-month">Month</label>
          
          <input type="radio" class="btn-check" name="revenue-period" id="revenue-year">
          <label class="btn btn-outline-primary" for="revenue-year">Year</label>
        </div>
      </div>
      <div class="card-body">
        <canvas id="revenueChart" height="100"></canvas>
      </div>
    </div>
  </div>

  <!-- Occupancy Trend -->
  <div class="col-lg-4 mb-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Occupancy Trend</h5>
      </div>
      <div class="card-body">
        <div style="position: relative; height: 200px;">
          <canvas id="occupancyChart"></canvas>
        </div>
        <div class="mt-3">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Total Capacity</span>
            <span class="fw-semibold">{{ $totalCapacity ?? 0 }} slots</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Occupied</span>
            <span class="fw-semibold text-success">{{ $occupiedSlots ?? 0 }} slots</span>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Available</span>
            <span class="fw-semibold text-primary">{{ ($totalCapacity ?? 0) - ($occupiedSlots ?? 0) }} slots</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Property Performance Table -->
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Property Performance</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Property</th>
                <th>Bookings</th>
                <th>Occupancy</th>
                <th>Revenue</th>
                <th>Rating</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($propertyPerformance ?? [] as $property)
                <tr>
                  <td>
                    <div class="d-flex flex-column">
                      <span class="fw-semibold">{{ $property->title }}</span>
                      <small class="text-muted">{{ Str::limit($property->address, 30) }}</small>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-label-primary">{{ $property->bookings_count ?? 0 }}</span>
                  </td>
                  <td>
                    @php
                      $occupancy = $property->capacity > 0 ? (($property->capacity - $property->available_slots) / $property->capacity) * 100 : 0;
                    @endphp
                    <div class="d-flex align-items-center">
                      <div class="progress flex-grow-1 me-2" style="height: 6px;">
                        <div class="progress-bar bg-{{ $occupancy >= 80 ? 'success' : ($occupancy >= 50 ? 'warning' : 'danger') }}" 
                             style="width: {{ $occupancy }}%"></div>
                      </div>
                      <small class="fw-semibold">{{ number_format($occupancy, 0) }}%</small>
                    </div>
                  </td>
                  <td class="fw-semibold">₱{{ number_format($property->price * $property->bookings_count, 2) }}</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <i class='bx bxs-star text-warning me-1'></i>
                      <span class="fw-semibold">{{ number_format($property->ratings_avg_rating ?? 0, 1) }}</span>
                    </div>
                  </td>
                  <td>
                    @if($property->is_active)
                      <span class="badge bg-label-success">Active</span>
                    @else
                      <span class="badge bg-label-secondary">Inactive</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">
                    No property data available
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Booking Status Distribution -->
  <div class="col-lg-4 mb-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Booking Status</h5>
      </div>
      <div class="card-body">
        <div style="position: relative; height: 200px;">
          <canvas id="bookingStatusChart"></canvas>
        </div>
        <div class="mt-3">
          <div class="d-flex justify-content-between mb-2">
            <div class="d-flex align-items-center">
              <span class="badge badge-dot bg-success me-2"></span>
              <span class="text-muted">Active</span>
            </div>
            <span class="fw-semibold">{{ $activeBookings ?? 0 }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <div class="d-flex align-items-center">
              <span class="badge badge-dot bg-warning me-2"></span>
              <span class="text-muted">Pending</span>
            </div>
            <span class="fw-semibold">{{ $pendingBookings ?? 0 }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <div class="d-flex align-items-center">
              <span class="badge badge-dot bg-info me-2"></span>
              <span class="text-muted">Approved</span>
            </div>
            <span class="fw-semibold">{{ $approvedBookings ?? 0 }}</span>
          </div>
          <div class="d-flex justify-content-between">
            <div class="d-flex align-items-center">
              <span class="badge badge-dot bg-secondary me-2"></span>
              <span class="text-muted">Completed</span>
            </div>
            <span class="fw-semibold">{{ $completedBookings ?? 0 }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Recent Activity</h5>
      </div>
      <div class="card-body">
        <ul class="timeline mb-0">
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-success"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">New Booking</h6>
                <small class="text-muted">2 hours ago</small>
              </div>
              <p class="mb-2">John Doe booked Sunshine Apartment</p>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-info"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">Payment Received</h6>
                <small class="text-muted">5 hours ago</small>
              </div>
              <p class="mb-2">₱5,000 payment from Maria Santos</p>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-warning"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">New Review</h6>
                <small class="text-muted">1 day ago</small>
              </div>
              <p class="mb-2">5-star review on Garden View House</p>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-primary"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">Lease Completed</h6>
                <small class="text-muted">2 days ago</small>
              </div>
              <p class="mb-2">Juan Cruz completed 6-month lease</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Top Performing Properties -->
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Top Performing Properties</h5>
      </div>
      <div class="card-body">
        @forelse($propertyPerformance->take(5) ?? [] as $index => $property)
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center flex-grow-1">
              <div class="badge badge-center rounded-pill bg-label-primary me-3" style="width: 32px; height: 32px;">
                {{ $index + 1 }}
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-0">{{ $property->title }}</h6>
                <small class="text-muted">{{ $property->bookings_count ?? 0 }} bookings</small>
              </div>
            </div>
            <div class="text-end">
              <span class="fw-semibold">₱{{ number_format($property->price * ($property->bookings_count ?? 0), 0) }}</span>
              <div class="d-flex align-items-center">
                <i class='bx bxs-star text-warning me-1' style="font-size: 14px;"></i>
                <small>{{ number_format($property->ratings_avg_rating ?? 0, 1) }}</small>
              </div>
            </div>
          </div>
        @empty
          <p class="text-center text-muted">No property data available</p>
        @endforelse
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Revenue Chart
  const revenueCtx = document.getElementById('revenueChart');
  if (revenueCtx) {
    new Chart(revenueCtx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Revenue',
          data: [12000, 15000, 13000, 17000, 16000, 19000, 22000, 20000, 23000, 25000, 24000, 28000],
          borderColor: '#696cff',
          backgroundColor: 'rgba(105, 108, 255, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '₱' + value.toLocaleString();
              }
            }
          }
        }
      }
    });
  }

  // Occupancy Chart (Doughnut)
  const occupancyCtx = document.getElementById('occupancyChart');
  if (occupancyCtx) {
    const occupancyRate = {{ $occupancyRate ?? 0 }};
    new Chart(occupancyCtx, {
      type: 'doughnut',
      data: {
        labels: ['Occupied', 'Available'],
        datasets: [{
          data: [occupancyRate, 100 - occupancyRate],
          backgroundColor: ['#71dd37', '#e7e7e7'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '75%',
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  }

  // Booking Status Chart (Doughnut)
  const bookingStatusCtx = document.getElementById('bookingStatusChart');
  if (bookingStatusCtx) {
    new Chart(bookingStatusCtx, {
      type: 'doughnut',
      data: {
        labels: ['Active', 'Pending', 'Approved', 'Completed'],
        datasets: [{
          data: [
            {{ $activeBookings ?? 0 }},
            {{ $pendingBookings ?? 0 }},
            {{ $approvedBookings ?? 0 }},
            {{ $completedBookings ?? 0 }}
          ],
          backgroundColor: ['#71dd37', '#ffab00', '#03c3ec', '#8592a3'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '70%',
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  }
});
</script>

<style>
.timeline {
  position: relative;
  padding-left: 30px;
  margin: 0;
  list-style: none;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 8px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #e7e7e7;
}

.timeline-item {
  position: relative;
  padding-bottom: 20px;
}

.timeline-item:last-child {
  padding-bottom: 0;
}

.timeline-point {
  position: absolute;
  left: -22px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  border: 3px solid #fff;
  box-shadow: 0 0 0 1px #e7e7e7;
}

.timeline-point-success {
  background: #71dd37;
}

.timeline-point-info {
  background: #03c3ec;
}

.timeline-point-warning {
  background: #ffab00;
}

.timeline-point-primary {
  background: #696cff;
}

.timeline-event {
  padding-left: 15px;
}

.badge-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  padding: 0;
}
</style>
@endsection