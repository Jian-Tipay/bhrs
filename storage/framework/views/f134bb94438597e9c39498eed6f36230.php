

<?php $__env->startSection('title', 'Activity Logs'); ?>

<?php $__env->startSection('vendor-script'); ?>
<script src="<?php echo e(asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/flatpickr/flatpickr.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <div class="col-12">
    <!-- Activity Statistics -->
    <div class="row mb-4">
      <div class="col-xl-3 col-sm-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
              <div class="content-left">
                <span>Total Activities</span>
                <div class="d-flex align-items-center my-2">
                  <h3 class="mb-0 me-2"><?php echo e(number_format($logs->total())); ?></h3>
                </div>
                <p class="mb-0">All time</p>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-primary">
                  <i class="bx bx-trending-up bx-sm"></i>
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
                <span>Today</span>
                <div class="d-flex align-items-center my-2">
                  <h3 class="mb-0 me-2" id="todayCount">-</h3>
                </div>
                <p class="mb-0">Last 24 hours</p>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-success">
                  <i class="bx bx-time bx-sm"></i>
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
                <span>This Week</span>
                <div class="d-flex align-items-center my-2">
                  <h3 class="mb-0 me-2" id="weekCount">-</h3>
                </div>
                <p class="mb-0">Last 7 days</p>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-info">
                  <i class="bx bx-calendar bx-sm"></i>
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
                <span>This Month</span>
                <div class="d-flex align-items-center my-2">
                  <h3 class="mb-0 me-2" id="monthCount">-</h3>
                </div>
                <p class="mb-0">Last 30 days</p>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-warning">
                  <i class="bx bx-bar-chart bx-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">Filter Activity Logs</h5>
      </div>
      <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.activity-logs')); ?>" id="filterForm">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">User</label>
              <select name="user_id" class="form-select">
                <option value="">All Users</option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($user->user_id); ?>" <?php echo e(request('user_id') == $user->user_id ? 'selected' : ''); ?>>
                    <?php echo e($user->user_name); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="col-md-2">
              <label class="form-label">Role</label>
              <select name="role" class="form-select">
                <option value="">All Roles</option>
                <option value="admin" <?php echo e(request('role') == 'admin' ? 'selected' : ''); ?>>Admin</option>
                <option value="landlord" <?php echo e(request('role') == 'landlord' ? 'selected' : ''); ?>>Landlord</option>
                <option value="user" <?php echo e(request('role') == 'user' ? 'selected' : ''); ?>>User</option>
              </select>
            </div>

            <div class="col-md-2">
              <label class="form-label">Action</label>
              <select name="action" class="form-select">
                <option value="">All Actions</option>
                <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($action); ?>" <?php echo e(request('action') == $action ? 'selected' : ''); ?>>
                    <?php echo e(ucfirst($action)); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="col-md-2">
              <label class="form-label">Subject Type</label>
              <select name="subject_type" class="form-select">
                <option value="">All Types</option>
                <?php $__currentLoopData = $subjectTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($type); ?>" <?php echo e(request('subject_type') == $type ? 'selected' : ''); ?>>
                    <?php echo e($type); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Search</label>
              <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo e(request('search')); ?>">
            </div>

            <div class="col-md-3">
              <label class="form-label">Date From</label>
              <input type="text" name="date_from" class="form-control flatpickr-date" placeholder="Select date" value="<?php echo e(request('date_from')); ?>">
            </div>

            <div class="col-md-3">
              <label class="form-label">Date To</label>
              <input type="text" name="date_to" class="form-control flatpickr-date" placeholder="Select date" value="<?php echo e(request('date_to')); ?>">
            </div>

            <div class="col-md-6 d-flex align-items-end gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-search me-1"></i> Filter
              </button>
              <a href="<?php echo e(route('admin.activity-logs')); ?>" class="btn btn-label-secondary">
                <i class="bx bx-reset me-1"></i> Reset
              </a>
              <a href="<?php echo e(route('admin.activity-logs.export', request()->all())); ?>" class="btn btn-success">
                <i class="bx bx-download me-1"></i> Export CSV
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Activity Logs</h5>
        <button type="button" class="btn btn-sm btn-danger" onclick="clearOldLogs()">
          <i class="bx bx-trash me-1"></i> Clear Old Logs
        </button>
      </div>
      <div class="card-datatable table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Date & Time</th>
              <th>User</th>
              <th>Role</th>
              <th>Action</th>
              <th>Subject</th>
              <th>Description</th>
              <th>IP Address</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr class="align-middle">

                
                <td class="text-nowrap">
                  <small class="text-muted">
                    <?php echo e(\Carbon\Carbon::parse($log->created_at)->format('M d, Y h:i A')); ?>

                  </small>
                </td>

                
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                      <span class="avatar-initial rounded-circle bg-label-<?php echo e($log->role === 'admin' ? 'danger' : 
                          ($log->role === 'landlord' ? 'info' : 'primary')); ?>">
                        <?php echo e(strtoupper(substr($log->user_name ?? 'S', 0, 1))); ?>

                      </span>
                    </div>
                    <span class="fw-medium"><?php echo e($log->user_name ?? 'System'); ?></span>
                  </div>
                </td>

                
                <td>
                  <?php if($log->role): ?>
                    <span class="badge bg-label-<?php echo e($log->role === 'admin' ? 'danger' : 
                        ($log->role === 'landlord' ? 'info' : 'primary')); ?>">
                      <?php echo e(strtoupper($log->role)); ?>

                    </span>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>

                
                <td>
                  <span class="badge bg-label-<?php echo e($log->action === 'created' ? 'success' :
                      ($log->action === 'updated' ? 'info' :
                      ($log->action === 'deleted' ? 'danger' :
                      ($log->action === 'approved' ? 'primary' : 'secondary')))); ?>">
                    <?php echo e(strtoupper($log->action)); ?>

                  </span>
                </td>

                
                <td class="text-nowrap">
                  <?php
                      $isViewingList = str_contains(strtolower($log->action), 'viewed')
                                      && str_contains(strtolower($log->action), 'list');
                  ?>

                  
                  <?php if($isViewingList): ?>
                      <span class="text-muted">-</span>

                  
                  <?php elseif($log->subject_type): ?>
                      <span class="text-muted small"><?php echo e($log->subject_type); ?></span><br>
                      <span class="fw-medium">
                          <?php echo e($log->subject_name ?? 'ID '.$log->subject_id); ?>

                      </span>

                  
                  <?php else: ?>
                      <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>


                
                <td style="max-width: 280px; white-space: normal;">
                  <span class="text-wrap">
                    <?php echo e($log->description); ?>

                  </span>

                  <?php if($log->properties): ?>
                    <button type="button" class="btn btn-xs btn-link p-0" onclick="showProperties(<?php echo e($log->id); ?>)">
                      View Details
                    </button>
                  <?php endif; ?>
                </td>

                
                <td class="text-nowrap">
                  <small class="text-muted"><?php echo e($log->ip_address ?? 'N/A'); ?></small>
                </td>

              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr>
                <td colspan="7" class="text-center py-4">
                  <i class="bx bx-info-circle bx-lg text-muted mb-2"></i>
                  <p class="text-muted mb-0">No activity logs found</p>
                </td>
              </tr>
            <?php endif; ?>
            </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="card-footer">
        <?php echo e($logs->links()); ?>

      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script>
// Initialize Flatpickr
document.addEventListener('DOMContentLoaded', function() {
  const flatpickrElements = document.querySelectorAll('.flatpickr-date');
  flatpickrElements.forEach(el => {
    flatpickr(el, {
      dateFormat: 'Y-m-d'
    });
  });

  // Load statistics
  loadStatistics();
});

// Load statistics
function loadStatistics() {
  fetch('/admin/activity-logs/statistics')
    .then(response => response.json())
    .then(data => {
      // You can populate the stats here
      console.log('Statistics loaded:', data);
    })
    .catch(error => console.error('Error loading statistics:', error));
}

// Clear old logs
function clearOldLogs() {
  Swal.fire({
    title: 'Clear Old Logs?',
    text: 'This will delete activity logs older than 90 days.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, clear them',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch('/admin/activity-logs/clear', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ days: 90 })
      })
      .then(response => response.json())
      .then(data => {
        Swal.fire('Cleared!', data.message, 'success');
        location.reload();
      })
      .catch(error => {
        Swal.fire('Error!', 'Failed to clear logs', 'error');
      });
    }
  });
}

// Show properties details
function showProperties(logId) {
  // Implement modal to show detailed properties
  Swal.fire({
    title: 'Activity Details',
    text: 'Detailed view coming soon...',
    icon: 'info'
  });
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/contentNavbarLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\renzl\Downloads\myprojects\bhrs\resources\views/content/admin/activity/index.blade.php ENDPATH**/ ?>