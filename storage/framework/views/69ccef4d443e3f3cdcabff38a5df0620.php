

<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('vendor-style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/apex-charts/apex-charts.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
<script src="<?php echo e(asset('assets/vendor/libs/apex-charts/apexcharts.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // User Growth Chart
  const userGrowthData = <?php echo json_encode(array_values($userGrowthData), 15, 512) ?>;
  const userGrowthLabels = <?php echo json_encode(array_keys($userGrowthData), 15, 512) ?>;
  
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
  const bookingStatusData = <?php echo json_encode(array_values($bookingStatusData), 15, 512) ?>;
  const bookingStatusLabels = <?php echo json_encode(array_keys($bookingStatusData), 15, 512) ?>;
  
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
  const revenueData = <?php echo json_encode(array_values($revenueData), 15, 512) ?>;
  const revenueLabels = <?php echo json_encode(array_keys($revenueData), 15, 512) ?>;
  
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  
  <!-- Welcome Banner -->
  <div class="col-12 mb-4">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <h4 class="text-white mb-1">Welcome back, Admin <?php echo e(Auth::user()->first_name); ?>! 👋</h4>
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
              <h4 class="mb-0 me-2"><?php echo e(number_format($totalUsers)); ?></h4>
              <small class="text-success">+<?php echo e($newUsersThisMonth); ?> this month</small>
            </div>
            <small class="text-muted"><?php echo e($totalLandlords); ?> Landlords, <?php echo e($totalTenants); ?> Tenants</small>
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
              <h4 class="mb-0 me-2"><?php echo e(number_format($totalProperties)); ?></h4>
              <?php if($pendingProperties > 0): ?>
                <small class="text-warning"><?php echo e($pendingProperties); ?> pending</small>
              <?php endif; ?>
            </div>
            <small class="text-muted"><?php echo e($activeProperties); ?> Active</small>
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
              <h4 class="mb-0 me-2"><?php echo e(number_format($totalBookings)); ?></h4>
              <?php if($pendingBookings > 0): ?>
                <small class="text-warning"><?php echo e($pendingBookings); ?> pending</small>
              <?php endif; ?>
            </div>
            <small class="text-muted"><?php echo e($activeBookings); ?> Active</small>
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
              <h4 class="mb-0 me-2">₱<?php echo e(number_format($monthlyRevenue, 2)); ?></h4>
            </div>
            <small class="text-muted"><?php echo e($totalReviews); ?> Reviews (<?php echo e(number_format($averageRating, 1)); ?>★)</small>
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
        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-sm btn-outline-primary">View All</a>
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
              <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recentUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded-circle bg-label-primary">
                          <?php echo e(strtoupper(substr($recentUser->first_name, 0, 1))); ?>

                        </span>
                      </div>
                      <div>
                        <strong><?php echo e($recentUser->first_name); ?> <?php echo e($recentUser->last_name); ?></strong><br>
                        <small class="text-muted"><?php echo e($recentUser->email); ?></small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-label-<?php echo e($recentUser->role === 'landlord' ? 'info' : 'primary'); ?>">
                      <?php echo e(ucfirst($recentUser->role)); ?>

                    </span>
                  </td>
                  <td><?php echo e($recentUser->created_at->diffForHumans()); ?></td>
                  <td>
                    <a href="<?php echo e(route('admin.users.view', $recentUser->id)); ?>" class="btn btn-sm btn-icon btn-outline-primary">
                      <i class='bx bx-show'></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="4" class="text-center text-muted">No recent users</td>
                </tr>
              <?php endif; ?>
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
        <a href="<?php echo e(route('admin.properties.index')); ?>" class="btn btn-sm btn-outline-primary">View All</a>
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
              <?php $__empty_1 = true; $__currentLoopData = $recentProperties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <?php if($property->images && $property->images->count() > 0): ?>
                        <img src="<?php echo e(asset('storage/' . $property->images->first()->image_path)); ?>" 
                             alt="<?php echo e($property->title); ?>" 
                             class="rounded me-2" 
                             style="width: 40px; height: 40px; object-fit: cover;">
                      <?php else: ?>
                        <div class="avatar avatar-sm me-2">
                          <span class="avatar-initial rounded bg-label-info">
                            <i class='bx bx-building-house'></i>
                          </span>
                        </div>
                      <?php endif; ?>
                      <div>
                        <strong><?php echo e(Str::limit($property->title, 20)); ?></strong><br>
                        <small class="text-muted">₱<?php echo e(number_format($property->price, 2)); ?></small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <?php if($property->landlord && $property->landlord->user): ?>
                      <?php echo e($property->landlord->user->first_name); ?> <?php echo e($property->landlord->user->last_name); ?>

                    <?php else: ?>
                      N/A
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($property->is_active): ?>
                      <span class="badge bg-success">Active</span>
                    <?php else: ?>
                      <span class="badge bg-warning">Pending</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a href="<?php echo e(route('admin.properties.index', $property->id)); ?>" class="btn btn-sm btn-icon btn-outline-primary">
                      <i class='bx bx-show'></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="4" class="text-center text-muted">No recent properties</td>
                </tr>
              <?php endif; ?>
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
        <a href="<?php echo e(route('admin.bookings.index')); ?>" class="btn btn-sm btn-outline-primary">View All</a>
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
              <?php $__empty_1 = true; $__currentLoopData = $recentBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><strong>#<?php echo e($booking->id); ?></strong></td>
                  <td><?php echo e($booking->user->first_name); ?> <?php echo e($booking->user->last_name); ?></td>
                  <td><?php echo e(Str::limit($booking->property->title, 30)); ?></td>
                  <td><?php echo e($booking->move_in_date ? \Carbon\Carbon::parse($booking->move_in_date)->format('M d, Y') : 'N/A'); ?></td>
                  <td>
                    <?php switch($booking->status):
                      case ('Pending'): ?>
                        <span class="badge bg-warning">Pending</span>
                        <?php break; ?>
                      <?php case ('Approved'): ?>
                        <span class="badge bg-info">Approved</span>
                        <?php break; ?>
                      <?php case ('Active'): ?>
                        <span class="badge bg-success">Active</span>
                        <?php break; ?>
                      <?php case ('Completed'): ?>
                        <span class="badge bg-secondary">Completed</span>
                        <?php break; ?>
                      <?php case ('Cancelled'): ?>
                        <span class="badge bg-danger">Cancelled</span>
                        <?php break; ?>
                      <?php default: ?>
                        <span class="badge bg-secondary"><?php echo e($booking->status); ?></span>
                    <?php endswitch; ?>
                  </td>
                  <td><?php echo e($booking->created_at->diffForHumans()); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="6" class="text-center text-muted">No recent bookings</td>
                </tr>
              <?php endif; ?>
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
          <?php $__empty_1 = true; $__currentLoopData = $topProperties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topProperty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-4 mb-3">
              <div class="card border">
                <?php if($topProperty->images && $topProperty->images->count() > 0): ?>
                  <img src="<?php echo e(asset('storage/' . $topProperty->images->first()->image_path)); ?>" 
                       class="card-img-top" 
                       style="height: 200px; object-fit: cover;"
                       alt="<?php echo e($topProperty->title); ?>">
                <?php else: ?>
                  <div class="card-img-top bg-label-secondary d-flex align-items-center justify-content-center" 
                       style="height: 200px;">
                    <i class='bx bx-building-house bx-lg'></i>
                  </div>
                <?php endif; ?>
                <div class="card-body">
                  <h6 class="card-title"><?php echo e($topProperty->title); ?></h6>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-warning">
                      ⭐ <?php echo e(number_format($topProperty->ratings_avg_rating ?? 0, 1)); ?>

                    </span>
                    <small class="text-muted"><?php echo e($topProperty->ratings_count); ?> reviews</small>
                  </div>
                  <p class="card-text">
                    <i class='bx bx-money'></i> ₱<?php echo e(number_format($topProperty->price, 2)); ?>/mo
                  </p>
                  <a href="<?php echo e(route('properties.view', $topProperty->id)); ?>" class="btn btn-sm btn-outline-primary w-100">
                    View Details
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
              <p class="text-center text-muted">No properties available</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/contentNavbarLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\renzl\Downloads\myprojects\bhrs\resources\views/content/admin/admin_dashboard.blade.php ENDPATH**/ ?>