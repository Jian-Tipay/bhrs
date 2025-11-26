<?php $__env->startSection('title', 'Landlord Dashboard'); ?>

<?php $__env->startSection('vendor-style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/apex-charts/apex-charts.css')); ?>">
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
<script src="<?php echo e(asset('assets/vendor/libs/apex-charts/apexcharts.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    // Revenue chart
    <?php if(isset($revenueChartData) && is_array($revenueChartData)): ?>
    const revenueOptions = {
      series: [{
        name: 'Revenue',
        data: <?php echo json_encode(array_values($revenueChartData), 15, 512) ?>
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
        categories: <?php echo json_encode(array_keys($revenueChartData), 15, 512) ?>
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
    <?php endif; ?>

    // Bookings chart
    <?php if(isset($bookingChartData) && is_array($bookingChartData)): ?>
    const bookingsOptions = {
      series: <?php echo json_encode(array_values($bookingChartData), 15, 512) ?>,
      chart: {
        type: 'donut',
        height: 250
      },
      labels: <?php echo json_encode(array_keys($bookingChartData), 15, 512) ?>,
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
    <?php endif; ?>
  });
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">

  <!-- Welcome Banner -->
  <div class="col-12 mb-4">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <h4 class="text-white mb-1">Welcome back, <?php echo e(Auth::user()->first_name ?? Auth::user()->name); ?>! 👋</h4>
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
              <h4 class="mb-0 me-2"><?php echo e($totalProperties ?? 0); ?></h4>
              <small class="text-success">
                <i class='bx bx-up-arrow-alt'></i>
                <?php echo e($activeProperties ?? 0); ?> active
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
              <h4 class="mb-0 me-2"><?php echo e($totalBookings ?? 0); ?></h4>
              <small class="text-warning">
                <i class='bx bx-time'></i>
                <?php echo e($pendingBookings ?? 0); ?> pending
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
              <h4 class="mb-0 me-2">₱<?php echo e(number_format($monthlyRevenue ?? 0)); ?></h4>
              <small class="text-success">
                <i class='bx bx-trending-up'></i>
                +<?php echo e($revenueGrowth ?? 0); ?>%
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
              <h4 class="mb-0 me-2"><?php echo e(number_format($averageRating ?? 0, 1)); ?></h4>
              <small class="text-muted">
                <i class='bx bx-star'></i>
                <?php echo e($totalReviews ?? 0); ?> reviews
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
            <a href="<?php echo e(route('landlord.properties.create')); ?>" class="btn btn-primary w-100 quick-action-btn">
              <i class='bx bx-plus-circle me-2'></i>
              Add New Property
            </a>
          </div>
          <div class="col-md-3">
            <a href="<?php echo e(route('landlord.bookings.index')); ?>" class="btn btn-warning w-100 quick-action-btn">
              <i class='bx bx-time-five me-2'></i>
              View Bookings
              <?php if(($pendingBookings ?? 0) > 0): ?>
                <span class="badge bg-white text-warning ms-1"><?php echo e($pendingBookings); ?></span>
              <?php endif; ?>
            </a>
          </div>
          <div class="col-md-3">
            <a href="<?php echo e(route('landlord.properties.index')); ?>" class="btn btn-info w-100 quick-action-btn">
              <i class='bx bx-list-ul me-2'></i>
              Manage Properties
            </a>
          </div>
          <div class="col-md-3">
            <a href="<?php echo e(route('landlord.reports.index')); ?>" class="btn btn-success w-100 quick-action-btn">
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
        <?php if(isset($revenueChartData) && is_array($revenueChartData) && count($revenueChartData) > 0): ?>
          <div id="revenueChart"></div>
        <?php else: ?>
          <div class="text-center py-5">
            <i class='bx bx-line-chart display-1 text-muted mb-3'></i>
            <p class="text-muted">No revenue data available yet</p>
          </div>
        <?php endif; ?>
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
        <?php if(isset($bookingChartData) && is_array($bookingChartData) && count($bookingChartData) > 0): ?>
          <div id="bookingsChart"></div>
        <?php else: ?>
          <div class="text-center py-5">
            <i class='bx bx-pie-chart display-1 text-muted mb-3'></i>
            <p class="text-muted">No booking data available yet</p>
          </div>
        <?php endif; ?>
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
              <?php $__empty_1 = true; $__currentLoopData = $recentBookings ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <img src="<?php echo e($booking->property->images->first()->image_path ?? asset('assets/img/boarding/default.jpg')); ?>" 
                         alt="<?php echo e($booking->property->title); ?>" 
                         class="rounded me-2" 
                         style="width: 40px; height: 40px; object-fit: cover;">
                    <span><?php echo e($booking->property->title); ?></span>
                  </div>
                </td>
                <td><?php echo e($booking->user->first_name ?? $booking->user->name); ?></td>
                <td><?php echo e($booking->created_at->format('M d, Y')); ?></td>
                <td>
                  <?php if($booking->status == 'Pending'): ?>
                    <span class="badge bg-warning">Pending</span>
                  <?php elseif($booking->status == 'Approved'): ?>
                    <span class="badge bg-success">Approved</span>
                  <?php elseif($booking->status == 'Active'): ?>
                    <span class="badge bg-info">Active</span>
                  <?php elseif($booking->status == 'Completed'): ?>
                    <span class="badge bg-secondary">Completed</span>
                  <?php elseif($booking->status == 'Cancelled'): ?>
                    <span class="badge bg-danger">Cancelled</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      Actions
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="<?php echo e(route('landlord.bookings.index', $booking->booking_id)); ?>">
                        <i class='bx bx-show me-1'></i> View Details
                      </a></li>
                      <?php if($booking->status == 'Pending'): ?>
                      <li>
                        <form action="<?php echo e(route('booking.approve', $booking->booking_id)); ?>" method="POST">
                          <?php echo csrf_field(); ?>
                          <?php echo method_field('PATCH'); ?>
                          <button type="submit" class="dropdown-item text-success">
                            <i class='bx bx-check me-1'></i> Approve
                          </button>
                        </form>
                      </li>
                      <li>
                        <form action="<?php echo e(route('booking.reject', $booking->booking_id)); ?>" method="POST">
                          <?php echo csrf_field(); ?>
                          <?php echo method_field('PATCH'); ?>
                          <button type="submit" class="dropdown-item text-danger">
                            <i class='bx bx-x me-1'></i> Reject
                          </button>
                        </form>
                      </li>
                      <?php endif; ?>
                    </ul>
                  </div>
                </td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                  No recent bookings found
                </td>
              </tr>
              <?php endif; ?>
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
        <?php $__empty_1 = true; $__currentLoopData = $topProperties ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
          <img src="<?php echo e($property->images->first()->image_path ?? asset('assets/img/boarding/default.jpg')); ?>" 
               alt="<?php echo e($property->title); ?>" 
               class="rounded me-3" 
               style="width: 60px; height: 60px; object-fit: cover;">
          <div class="flex-grow-1">
            <h6 class="mb-1"><?php echo e($property->title); ?></h6>
            <small class="text-muted">
              <i class='bx bx-star text-warning'></i> 
              <?php echo e(number_format($property->ratings_avg_rating ?? 0, 1)); ?> 
              • <?php echo e($property->bookings_count ?? 0); ?> bookings
            </small>
          </div>
          <div class="text-end">
            <h6 class="mb-0 text-success">₱<?php echo e(number_format($property->monthly_revenue ?? 0)); ?></h6>
            <small class="text-muted">this month</small>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-muted text-center py-4">No data available yet</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- My Properties -->
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-1">🏘️ My Properties</h5>
          <small class="text-muted"><?php echo e(count($myProperties ?? [])); ?> properties listed</small>
        </div>
        <a href="" class="btn btn-primary">
          <i class='bx bx-plus'></i> Add Property
        </a>
      </div>
      <div class="card-body">
        <div class="row">
          <?php $__empty_1 = true; $__currentLoopData = $myProperties ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-4 mb-3">
              <div class="card property-card">
                <div class="position-relative">
                  <?php if($property->images && $property->images->count() > 0): ?>
                    <img src="<?php echo e(asset('storage/' . $property->images->first()->image_path)); ?>" 
                         class="property-img" alt="<?php echo e($property->title); ?>">
                  <?php else: ?>
                    <img src="<?php echo e(asset('assets/img/boarding/default.jpg')); ?>" 
                         class="property-img" alt="Default">
                  <?php endif; ?>
                  <?php if($property->is_active): ?>
                    <span class="status-badge bg-success">Active</span>
                  <?php else: ?>
                    <span class="status-badge bg-secondary">Inactive</span>
                  <?php endif; ?>
                </div>
                <div class="card-body">
                  <h5 class="card-title"><?php echo e($property->title); ?></h5>
                  <p class="card-text">
                    <i class='bx bx-map'></i> <?php echo e($property->address); ?><br>
                    <i class='bx bx-money'></i> ₱<?php echo e(number_format($property->price, 2)); ?> / month<br>
                    <i class='bx bx-bed'></i> <?php echo e($property->available_slots ?? 0); ?> / <?php echo e($property->capacity ?? 0); ?> slots available
                  </p>
                  <?php if($property->ratings_avg_rating > 0): ?>
                  <div class="mb-2">
                    <span class="badge bg-warning">⭐ <?php echo e(number_format($property->ratings_avg_rating, 1)); ?></span>
                    <small class="text-muted">(<?php echo e($property->ratings_count ?? 0); ?> reviews)</small>
                  </div>
                  <?php endif; ?>
                  <div class="d-flex gap-2">
                   <a href="<?php echo e(route('landlord.properties.edit', $property->id)); ?>" class="btn btn-sm btn-primary">Edit</a>
                      <i class='bx bx-edit'></i> Edit
                    </a>
                    <a href="<?php echo e(route('landlord.properties.index', $property->id)); ?>" class="btn btn-sm btn-outline-primary">
                      <i class='bx bx-show'></i> View
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
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
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/contentNavbarLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\renzl\Downloads\myprojects\bhrs\resources\views/content/landlord/landlord_dashboard.blade.php ENDPATH**/ ?>