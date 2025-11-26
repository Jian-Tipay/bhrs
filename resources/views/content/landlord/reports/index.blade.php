@extends('layouts/contentNavbarLayout')

@section('title', 'Reports & Analytics')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card bg-primary text-white">
      <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h4 class="mb-1 text-white">📊 Reports & Analytics</h4>
            <p class="mb-0 text-white opacity-75">Track your business performance and insights</p>
          </div>
          <div class="d-flex gap-2">
            <div class="btn-group">
              <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
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
            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="dropdown">
              <i class='bx bx-download'></i> Export
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="{{ route('landlord.reports.export-pdf', ['period' => request('period', 30)]) }}"><i class='bx bxs-file-pdf me-2'></i>Export as PDF</a></li>
              <li><a class="dropdown-item" href="{{ route('landlord.reports.export-excel', ['period' => request('period', 30)]) }}"><i class='bx bxs-file me-2'></i>Export as Excel</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Key Metrics Row -->
  <div class="col-xl-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-muted d-block mb-1">Total Revenue</span>
            <div class="d-flex align-items-center mb-1">
              <h3 class="mb-0 me-2">₱{{ number_format($totalRevenue ?? 0, 0) }}</h3>
            </div>
            <small class="{{ ($revenueChange ?? 0) >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
              <i class='bx bx-{{ ($revenueChange ?? 0) >= 0 ? "up" : "down" }}-arrow-alt'></i> 
              {{ number_format(abs($revenueChange ?? 0), 1) }}%
            </small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class='bx bx-wallet bx-lg'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-muted d-block mb-1">Total Views</span>
            <div class="d-flex align-items-center mb-1">
              <h3 class="mb-0 me-2">{{ number_format($totalViews ?? 0) }}</h3>
            </div>
            <small class="{{ ($viewsChange ?? 0) >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
              <i class='bx bx-{{ ($viewsChange ?? 0) >= 0 ? "up" : "down" }}-arrow-alt'></i> 
              {{ number_format(abs($viewsChange ?? 0), 1) }}%
            </small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-info">
              <i class='bx bx-show bx-lg'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-muted d-block mb-1">Total Bookings</span>
            <div class="d-flex align-items-center mb-1">
              <h3 class="mb-0 me-2">{{ $totalBookings ?? 0 }}</h3>
            </div>
            <small class="{{ ($bookingsChange ?? 0) >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
              <i class='bx bx-{{ ($bookingsChange ?? 0) >= 0 ? "up" : "down" }}-arrow-alt'></i> 
              {{ number_format(abs($bookingsChange ?? 0), 1) }}%
            </small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class='bx bx-calendar-check bx-lg'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-muted d-block mb-1">Occupancy Rate</span>
            <div class="d-flex align-items-center mb-1">
              <h3 class="mb-0 me-2">{{ number_format($occupancyRate ?? 0, 1) }}%</h3>
            </div>
            <small class="text-muted">
              {{ $occupiedSlots ?? 0 }}/{{ $totalCapacity ?? 0 }} slots
            </small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class='bx bx-home bx-lg'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart and Stats Row -->
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Revenue & Views Trend</h5>
        <div class="dropdown">
          <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Last 12 Months
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="javascript:void(0);">Last 6 Months</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">Last 12 Months</a></li>
          </ul>
        </div>
      </div>
      <div class="card-body pb-0">
        <canvas id="combinedChart" style="height: 300px;"></canvas>
      </div>
    </div>
  </div>

  <div class="col-lg-4 mb-4">
    <div class="card mb-4">
      <div class="card-body text-center">
        <div class="avatar avatar-xl mx-auto mb-3">
          <span class="avatar-initial rounded-circle bg-label-success">
            <i class='bx bx-trending-up bx-lg'></i>
          </span>
        </div>
        <h4 class="mb-1">{{ number_format($conversionRate ?? 0, 1) }}%</h4>
        <p class="mb-0 text-muted">Conversion Rate</p>
        <small class="text-muted">{{ $totalBookings ?? 0 }} bookings from {{ $totalViews ?? 0 }} views</small>
      </div>
    </div>

    <div class="card">
      <div class="card-body text-center">
        <div class="avatar avatar-xl mx-auto mb-3">
          <span class="avatar-initial rounded-circle bg-label-warning">
            <i class='bx bxs-star bx-lg'></i>
          </span>
        </div>
        <h4 class="mb-1">{{ number_format($averageRating ?? 0, 1) }}</h4>
        <p class="mb-0 text-muted">Average Rating</p>
        <div class="mt-2">
          @for($i = 1; $i <= 5; $i++)
            @if($i <= floor($averageRating ?? 0))
              <i class='bx bxs-star text-warning'></i>
            @elseif($i - 0.5 <= ($averageRating ?? 0))
              <i class='bx bxs-star-half text-warning'></i>
            @else
              <i class='bx bx-star text-muted'></i>
            @endif
          @endfor
        </div>
        <small class="text-muted">Based on {{ $totalReviews ?? 0 }} reviews</small>
      </div>
    </div>
  </div>

  <!-- Property Performance -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">🏆 Top Performing Properties</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Property</th>
                <th class="text-center">Views</th>
                <th class="text-center">Bookings</th>
                <th class="text-center">Conversion</th>
                <th class="text-center">Revenue</th>
                <th class="text-center">Rating</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($propertyPerformance ?? [] as $index => $property)
                <tr>
                  <td class="text-center">
                    <span class="badge badge-center rounded-pill {{ $index < 3 ? 'bg-label-primary' : 'bg-label-secondary' }}" style="width: 30px; height: 30px;">
                      {{ $index + 1 }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      @if($property->image && file_exists(public_path('assets/img/boarding/' . $property->image)))
                        <img src="{{ asset('assets/img/boarding/' . $property->image) }}" 
                             alt="{{ $property->title }}" 
                             class="rounded me-3" 
                             style="width: 45px; height: 45px; object-fit: cover;">
                      @else
                        <div class="avatar avatar-sm me-3 bg-label-primary">
                          <span class="avatar-initial rounded">{{ substr($property->title, 0, 1) }}</span>
                        </div>
                      @endif
                      <div>
                        <a href="{{ route('landlord.properties.view', $property->id) }}" class="fw-semibold text-primary text-decoration-none">
                          {{ $property->title }}
                        </a>
                        <small class="text-muted d-block">{{ Str::limit($property->address, 35) }}</small>
                      </div>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-label-info rounded-pill">{{ $property->views_count ?? 0 }}</span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-label-primary rounded-pill">{{ $property->bookings_count ?? 0 }}</span>
                  </td>
                  <td class="text-center">
                    @php
                      $propConversion = ($property->views_count ?? 0) > 0 ? (($property->bookings_count ?? 0) / $property->views_count) * 100 : 0;
                    @endphp
                    <span class="fw-semibold {{ $propConversion >= 10 ? 'text-success' : ($propConversion >= 5 ? 'text-warning' : 'text-muted') }}">
                      {{ number_format($propConversion, 1) }}%
                    </span>
                  </td>
                  <td class="text-center fw-semibold">
                    ₱{{ number_format($property->price * ($property->bookings_count ?? 0), 0) }}
                  </td>
                  <td class="text-center">
                    @if($property->ratings_avg_rating)
                      <div class="d-flex align-items-center justify-content-center">
                        <i class='bx bxs-star text-warning me-1'></i>
                        <span class="fw-semibold">{{ number_format($property->ratings_avg_rating, 1) }}</span>
                      </div>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td class="text-center">
                    @if($property->is_active && $property->available)
                      <span class="badge bg-success">Active</span>
                    @elseif($property->is_active)
                      <span class="badge bg-warning">Full</span>
                    @else
                      <span class="badge bg-secondary">Inactive</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-5 text-muted">
                    <i class='bx bx-building-house display-4 d-block mb-2'></i>
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

  <!-- Bottom Row: Booking Status & Activity -->
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">📋 Booking Status Overview</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-6">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-success">
                  <i class='bx bx-check-circle bx-sm'></i>
                </span>
              </div>
              <div>
                <h5 class="mb-0">{{ $activeBookings ?? 0 }}</h5>
                <small class="text-muted">Active</small>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-warning">
                  <i class='bx bx-time bx-sm'></i>
                </span>
              </div>
              <div>
                <h5 class="mb-0">{{ $pendingBookings ?? 0 }}</h5>
                <small class="text-muted">Pending</small>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-info">
                  <i class='bx bx-check-double bx-sm'></i>
                </span>
              </div>
              <div>
                <h5 class="mb-0">{{ $approvedBookings ?? 0 }}</h5>
                <small class="text-muted">Approved</small>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-secondary">
                  <i class='bx bx-archive bx-sm'></i>
                </span>
              </div>
              <div>
                <h5 class="mb-0">{{ $completedBookings ?? 0 }}</h5>
                <small class="text-muted">Completed</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">🔔 Recent Activity</h5>
      </div>
      <div class="card-body">
        <ul class="timeline mb-0 pb-0">
          @forelse($recentActivity ?? [] as $activity)
            <li class="timeline-item timeline-item-transparent {{ $loop->last ? '' : 'pb-3' }}">
              <span class="timeline-point timeline-point-{{ $activity['color'] }}"></span>
              <div class="timeline-event">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <h6 class="mb-0">{{ $activity['title'] }}</h6>
                  <small class="text-muted">{{ $activity['time'] }}</small>
                </div>
                <p class="mb-0 text-muted small">{{ $activity['message'] }}</p>
              </div>
            </li>
          @empty
            <li class="text-center text-muted py-3">
              <i class='bx bx-info-circle display-4 d-block mb-2'></i>
              No recent activity
            </li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const combinedCtx = document.getElementById('combinedChart');
  if (combinedCtx) {
    const labels = @json($revenueChartLabels ?? []);
    const revenueData = @json($revenueChartData ?? []);
    const viewsData = @json($viewsChartData ?? []);
    
    new Chart(combinedCtx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Revenue (₱)',
            data: revenueData,
            borderColor: '#71dd37',
            backgroundColor: 'rgba(113, 221, 55, 0.1)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y'
          },
          {
            label: 'Views',
            data: viewsData,
            borderColor: '#03c3ec',
            backgroundColor: 'rgba(3, 195, 236, 0.1)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y1'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false
        },
        plugins: {
          legend: {
            display: true,
            position: 'top',
            align: 'end'
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                if (context.dataset.yAxisID === 'y') {
                  label += '₱' + context.parsed.y.toLocaleString();
                } else {
                  label += context.parsed.y;
                }
                return label;
              }
            }
          }
        },
        scales: {
          y: {
            type: 'linear',
            display: true,
            position: 'left',
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '₱' + value.toLocaleString();
              }
            }
          },
          y1: {
            type: 'linear',
            display: true,
            position: 'right',
            beginAtZero: true,
            grid: {
              drawOnChartArea: false
            }
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
  list-style: none;
  margin: 0;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 9px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #e7e7e7;
}

.timeline-item {
  position: relative;
}

.timeline-point {
  position: absolute;
  left: -21px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 3px solid #fff;
  box-shadow: 0 0 0 1px #e7e7e7;
}

.timeline-point-success {
  background: #71dd37;
}

.timeline-point-warning {
  background: #ffab00;
}

.timeline-point-info {
  background: #03c3ec;
}

.timeline-point-primary {
  background: #696cff;
}

.timeline-event {
  padding-left: 15px;
}

.table > :not(caption) > * > * {
  padding: 0.875rem 1rem;
}
</style>
@endsection