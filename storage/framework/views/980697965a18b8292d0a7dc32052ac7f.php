

<?php $__env->startSection('title', 'Booking Management'); ?>

<?php $__env->startSection('content'); ?>
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
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline-secondary">
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
            <h4 class="mb-0"><?php echo e(number_format($bookings->total())); ?></h4>
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
            <h4 class="mb-0 text-warning"><?php echo e(number_format($bookings->where('status', 'Pending')->count())); ?></h4>
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
            <h4 class="mb-0 text-info"><?php echo e(number_format($bookings->where('status', 'Approved')->count())); ?></h4>
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
            <h4 class="mb-0 text-success"><?php echo e(number_format($bookings->where('status', 'Active')->count())); ?></h4>
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
            <h4 class="mb-0 text-secondary"><?php echo e(number_format($bookings->where('status', 'Completed')->count())); ?></h4>
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
            <h4 class="mb-0 text-danger"><?php echo e(number_format($bookings->where('status', 'Cancelled')->count())); ?></h4>
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
        <form method="GET" action="<?php echo e(route('admin.bookings.index')); ?>">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="Pending" <?php echo e(request('status') == 'Pending' ? 'selected' : ''); ?>>Pending</option>
                <option value="Approved" <?php echo e(request('status') == 'Approved' ? 'selected' : ''); ?>>Approved</option>
                <option value="Active" <?php echo e(request('status') == 'Active' ? 'selected' : ''); ?>>Active</option>
                <option value="Completed" <?php echo e(request('status') == 'Completed' ? 'selected' : ''); ?>>Completed</option>
                <option value="Cancelled" <?php echo e(request('status') == 'Cancelled' ? 'selected' : ''); ?>>Cancelled</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Date From</label>
              <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Date To</label>
              <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">&nbsp;</label>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class='bx bx-search'></i> Filter
                </button>
                <a href="<?php echo e(route('admin.bookings.index')); ?>" class="btn btn-outline-secondary">
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
        <span class="badge bg-label-primary"><?php echo e($bookings->total()); ?> Total</span>
      </div>
      <div class="card-body">
        <?php if($bookings->count() > 0): ?>
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
                <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td>
                      <strong class="text-primary">#<?php echo e($booking->booking_id); ?></strong>
                    </td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                          <?php if($booking->user->profile_picture): ?>
                            <img src="<?php echo e(asset($booking->user->profile_picture)); ?>" 
                                 alt="<?php echo e($booking->user->first_name); ?>" 
                                 class="rounded-circle"
                                 style="width: 38px; height: 38px; object-fit: cover;">
                          <?php else: ?>
                            <span class="avatar-initial rounded-circle bg-label-primary">
                              <?php echo e(strtoupper(substr($booking->user->first_name, 0, 1))); ?>

                            </span>
                          <?php endif; ?>
                        </div>
                        <div>
                          <strong><?php echo e($booking->user->first_name); ?> <?php echo e($booking->user->last_name); ?></strong><br>
                          <small class="text-muted"><?php echo e($booking->user->email); ?></small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex align-items-center">
                        <?php if($booking->property->images && $booking->property->images->count() > 0): ?>
                          <img src="<?php echo e(asset('storage/' . $booking->property->images->first()->image_path)); ?>" 
                               alt="<?php echo e($booking->property->title); ?>" 
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
                          <strong><?php echo e(Str::limit($booking->property->title, 25)); ?></strong><br>
                          <small class="text-muted">₱<?php echo e(number_format($booking->property->price, 2)); ?>/mo</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <?php if($booking->property->landlord && $booking->property->landlord->user): ?>
                        <div>
                          <strong><?php echo e($booking->property->landlord->user->first_name); ?> <?php echo e($booking->property->landlord->user->last_name); ?></strong><br>
                          <small class="text-muted"><?php echo e($booking->property->landlord->user->contact_number ?? 'N/A'); ?></small>
                        </div>
                      <?php else: ?>
                        <span class="text-muted">N/A</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if($booking->move_in_date): ?>
                        <strong><?php echo e(\Carbon\Carbon::parse($booking->move_in_date)->format('M d, Y')); ?></strong><br>
                        <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($booking->move_in_date)->diffForHumans()); ?></small>
                      <?php else: ?>
                        <span class="text-muted">Not set</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if($booking->move_out_date): ?>
                        <strong><?php echo e(\Carbon\Carbon::parse($booking->move_out_date)->format('M d, Y')); ?></strong><br>
                        <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($booking->move_out_date)->diffForHumans()); ?></small>
                      <?php else: ?>
                        <span class="text-muted">Not set</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php switch($booking->status):
                        case ('Pending'): ?>
                          <span class="badge bg-warning">
                            <i class='bx bx-time'></i> Pending
                          </span>
                          <?php break; ?>
                        <?php case ('Approved'): ?>
                          <span class="badge bg-info">
                            <i class='bx bx-check'></i> Approved
                          </span>
                          <?php break; ?>
                        <?php case ('Active'): ?>
                          <span class="badge bg-success">
                            <i class='bx bx-check-circle'></i> Active
                          </span>
                          <?php break; ?>
                        <?php case ('Completed'): ?>
                          <span class="badge bg-secondary">
                            <i class='bx bx-check-double'></i> Completed
                          </span>
                          <?php break; ?>
                        <?php case ('Cancelled'): ?>
                          <span class="badge bg-danger">
                            <i class='bx bx-x'></i> Cancelled
                          </span>
                          <?php break; ?>
                        <?php default: ?>
                          <span class="badge bg-secondary"><?php echo e($booking->status); ?></span>
                      <?php endswitch; ?>
                    </td>
                    <td>
                      <small><?php echo e($booking->created_at->format('M d, Y')); ?></small><br>
                      <small class="text-muted"><?php echo e($booking->created_at->format('h:i A')); ?></small>
                    </td>
                    <td>
                      <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-icon btn-outline-primary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i class='bx bx-dots-vertical-rounded'></i>
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="<?php echo e(route('properties.view', $booking->property->id)); ?>" target="_blank">
                            <i class='bx bx-building-house me-1'></i> View Property
                          </a>
                          <a class="dropdown-item" href="<?php echo e(route('admin.users.view', $booking->user->id)); ?>">
                            <i class='bx bx-user me-1'></i> View Tenant
                          </a>
                          <?php if($booking->property->landlord && $booking->property->landlord->user): ?>
                            <a class="dropdown-item" href="<?php echo e(route('admin.users.view', $booking->property->landlord->user->id)); ?>">
                              <i class='bx bx-user-circle me-1'></i> View Landlord
                            </a>
                          <?php endif; ?>
                          <div class="dropdown-divider"></div>
                          <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bookingDetailsModal<?php echo e($booking->booking_id); ?>">
                            <i class='bx bx-info-circle me-1'></i> View Details
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>

                  <!-- Booking Details Modal -->
                  <div class="modal fade" id="bookingDetailsModal<?php echo e($booking->booking_id); ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Booking Details #<?php echo e($booking->booking_id); ?></h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="row mb-3">
                            <div class="col-6">
                              <small class="text-muted">Tenant</small>
                              <div class="d-flex align-items-center gap-2 mt-1">
                                <img src="<?php echo e($booking->user->profile_picture ? asset($booking->user->profile_picture) : asset('assets/img/avatars/1.png')); ?>" 
                                    alt="Tenant Avatar" 
                                    class="rounded-circle" 
                                    style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                  <p class="mb-0"><strong><?php echo e($booking->user->first_name); ?> <?php echo e($booking->user->last_name); ?></strong></p>
                                  <small><?php echo e($booking->user->email); ?></small>
                                </div>
                              </div>
                            </div>
                            <div class="col-6">
                              <small class="text-muted">Status</small>
                              <p class="mb-0">
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
                                <?php endswitch; ?>
                              </p>
                            </div>
                          </div>

                          <div class="row mb-3">
                            <div class="col-6">
                              <small class="text-muted">Contact Number</small>
                              <p class="mb-0">
                                <?php if($booking->user->contact_number): ?>
                                  <i class='bx bx-phone me-1'></i><?php echo e($booking->user->contact_number); ?>

                                <?php else: ?>
                                  <span class="text-muted">Not provided</span>
                                <?php endif; ?>
                              </p>
                            </div>
                            <div class="col-6">
                              <small class="text-muted">Guardian Number</small>
                              <p class="mb-0">
                                <?php if($booking->user->guardian_number): ?>
                                  <i class='bx bx-phone me-1'></i><?php echo e($booking->user->guardian_number); ?>

                                <?php else: ?>
                                  <span class="text-muted">Not provided</span>
                                <?php endif; ?>
                              </p>
                            </div>
                          </div>

                          <hr>

                          <div class="mb-3">
                            <small class="text-muted">Property</small>
                            <p class="mb-0"><strong><?php echo e($booking->property->title); ?></strong></p>
                            <small><?php echo e($booking->property->address); ?></small>
                          </div>

                          <div class="row mb-3">
                            <div class="col-6">
                              <small class="text-muted">Move-in Date</small>
                              <p class="mb-0">
                                <?php if($booking->move_in_date): ?>
                                  <?php echo e(\Carbon\Carbon::parse($booking->move_in_date)->format('F d, Y')); ?>

                                <?php else: ?>
                                  <span class="text-muted">Not set</span>
                                <?php endif; ?>
                              </p>
                            </div>
                            <div class="col-6">
                              <small class="text-muted">Move-out Date</small>
                              <p class="mb-0">
                                <?php if($booking->move_out_date): ?>
                                  <?php echo e(\Carbon\Carbon::parse($booking->move_out_date)->format('F d, Y')); ?>

                                <?php else: ?>
                                  <span class="text-muted">Not set</span>
                                <?php endif; ?>
                              </p>
                            </div>
                          </div>

                          <div class="row mb-3">
                            <div class="col-6">
                              <small class="text-muted">Monthly Rate</small>
                              <p class="mb-0"><strong class="text-primary">₱<?php echo e(number_format($booking->property->price, 2)); ?></strong></p>
                            </div>
                            <div class="col-6">
                              <small class="text-muted">Created</small>
                              <p class="mb-0"><?php echo e($booking->created_at->format('M d, Y h:i A')); ?></p>
                            </div>
                          </div>

                          <?php if($booking->property->landlord && $booking->property->landlord->user): ?>
                            <hr>
                            <div class="mb-3">
                              <small class="text-muted">Landlord</small>
                              <div class="d-flex align-items-center gap-2 mt-1">
                                <img src="<?php echo e($booking->property->landlord->user->profile_picture ? asset($booking->property->landlord->user->profile_picture) : asset('assets/img/avatars/1.png')); ?>" 
                                    alt="Landlord Avatar" 
                                    class="rounded-circle" 
                                    style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                  <p class="mb-0"><strong><?php echo e($booking->property->landlord->user->first_name); ?> <?php echo e($booking->property->landlord->user->last_name); ?></strong></p>
                                  <small><?php echo e($booking->property->landlord->user->email); ?></small><br>
                                  <small><?php echo e($booking->property->landlord->user->contact_number ?? 'No contact number'); ?></small>
                                </div>
                              </div>
                            </div>
                          <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                          <a href="<?php echo e(route('properties.view', $booking->property->id)); ?>" class="btn btn-primary" target="_blank">
                            View Property
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-4 d-flex justify-content-center">
            <ul class="pagination pagination-sm mb-0 shadow-sm rounded">
              
              <?php if($bookings->onFirstPage()): ?>
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
              <?php else: ?>
                <li class="page-item"><a class="page-link" href="<?php echo e($bookings->previousPageUrl()); ?>" rel="prev">&laquo;</a></li>
              <?php endif; ?>

              
              <?php $__currentLoopData = $bookings->links()->elements[0] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($page == $bookings->currentPage()): ?>
                  <li class="page-item active"><span class="page-link"><?php echo e($page); ?></span></li>
                <?php else: ?>
                  <li class="page-item"><a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a></li>
                <?php endif; ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

              
              <?php if($bookings->hasMorePages()): ?>
                <li class="page-item"><a class="page-link" href="<?php echo e($bookings->nextPageUrl()); ?>" rel="next">&raquo;</a></li>
              <?php else: ?>
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
              <?php endif; ?>
            </ul>
          </div>

        <?php else: ?>
          <div class="text-center py-5">
            <i class='bx bx-calendar-x bx-lg text-muted'></i>
            <p class="text-muted mt-3">No bookings found</p>
            <?php if(request()->hasAny(['status', 'date_from', 'date_to'])): ?>
              <a href="<?php echo e(route('admin.bookings.index')); ?>" class="btn btn-sm btn-outline-primary">
                Clear Filters
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php if(session('success')): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      alert('<?php echo e(session('success')); ?>');
    });
  </script>
<?php endif; ?>

<?php if(session('error')): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      alert('<?php echo e(session('error')); ?>');
    });
  </script>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/contentNavbarLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\renzl\Downloads\myprojects\bhrs\resources\views/content/admin/bookings/index.blade.php ENDPATH**/ ?>