

<?php $__env->startSection('title', 'Property Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h4 class="mb-1">Property Management</h4>
            <p class="text-muted mb-0">Manage and review all property listings</p>
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
  <div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Total Properties</p>
            <h4 class="mb-0"><?php echo e(number_format($properties->total())); ?></h4>
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

  <div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Active</p>
            <h4 class="mb-0 text-success"><?php echo e(number_format($properties->where('is_active', 1)->count())); ?></h4>
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

  <div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Pending</p>
            <h4 class="mb-0 text-warning"><?php echo e(number_format($properties->where('is_active', 0)->count())); ?></h4>
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

  <div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">This Month</p>
            <h4 class="mb-0 text-info"><?php echo e(number_format($properties->filter(function($p) { return $p->created_at->isCurrentMonth(); })->count())); ?></h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-2">
              <i class='bx bx-calendar bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters and Search -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.properties.index')); ?>">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Search</label>
              <input type="text" name="search" class="form-control" placeholder="Search by title or address..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Active</option>
                <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">&nbsp;</label>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class='bx bx-search'></i> Search
                </button>
                <a href="<?php echo e(route('admin.properties.index')); ?>" class="btn btn-outline-secondary">
                  <i class='bx bx-reset'></i> Reset
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Properties Table -->
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Properties</h5>
        <span class="badge bg-label-primary"><?php echo e($properties->total()); ?> Total</span>
      </div>
      <div class="card-body">
        <?php if($properties->count() > 0): ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Property</th>
                  <th>Landlord</th>
                  <th>Price</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <?php if($property->images && $property->images->count() > 0): ?>
                          <img src="<?php echo e(asset('storage/' . $property->images->first()->image_path)); ?>" 
                               alt="<?php echo e($property->title); ?>" 
                               class="rounded me-3" 
                               style="width: 60px; height: 60px; object-fit: cover;">
                        <?php else: ?>
                          <div class="avatar avatar-md me-3">
                            <span class="avatar-initial rounded bg-label-info">
                              <i class='bx bx-building-house'></i>
                            </span>
                          </div>
                        <?php endif; ?>
                        <div>
                          <strong><?php echo e(Str::limit($property->title, 30)); ?></strong><br>
                          <small class="text-muted">
                            <i class='bx bx-map'></i> <?php echo e(Str::limit($property->address, 40)); ?>

                          </small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <?php if($property->landlord && $property->landlord->user): ?>
                        <div>
                          <strong><?php echo e($property->landlord->user->first_name); ?> <?php echo e($property->landlord->user->last_name); ?></strong><br>
                          <small class="text-muted"><?php echo e($property->landlord->user->email); ?></small>
                        </div>
                      <?php else: ?>
                        <span class="text-muted">N/A</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <strong class="text-primary">₱<?php echo e(number_format($property->price, 2)); ?></strong><br>
                      <small class="text-muted">per month</small>
                    </td>
                    <td>
                      <?php if($property->property_type): ?>
                        <span class="badge bg-label-secondary"><?php echo e($property->property_type); ?></span>
                      <?php else: ?>
                        <span class="text-muted">N/A</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if($property->is_active): ?>
                        <span class="badge bg-success">
                          <i class='bx bx-check'></i> Active
                        </span>
                      <?php else: ?>
                        <span class="badge bg-warning">
                          <i class='bx bx-time'></i> Pending
                        </span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <small><?php echo e($property->created_at->format('M d, Y')); ?></small><br>
                      <small class="text-muted"><?php echo e($property->created_at->diffForHumans()); ?></small>
                    </td>
                    <td>
                      <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-icon btn-outline-primary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i class='bx bx-dots-vertical-rounded'></i>
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="<?php echo e(route('properties.view', $property->id)); ?>" target="_blank">
                            <i class='bx bx-show me-1'></i> View Property
                          </a>
                          <?php if(!$property->is_active): ?>
                            <form action="<?php echo e(route('admin.properties.approve', $property->id)); ?>" method="POST" class="d-inline">
                              <?php echo csrf_field(); ?>
                              <button type="submit" class="dropdown-item text-success">
                                <i class='bx bx-check me-1'></i> Approve
                              </button>
                            </form>
                          <?php else: ?>
                            <form action="<?php echo e(route('admin.properties.reject', $property->id)); ?>" method="POST" class="d-inline">
                              <?php echo csrf_field(); ?>
                              <button type="submit" class="dropdown-item text-warning">
                                <i class='bx bx-x me-1'></i> Deactivate
                              </button>
                            </form>
                          <?php endif; ?>
                          <div class="dropdown-divider"></div>
                          <form action="<?php echo e(route('admin.properties.delete', $property->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this property? This action cannot be undone.')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="dropdown-item text-danger">
                              <i class='bx bx-trash me-1'></i> Delete
                            </button>
                          </form>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>

<div class="mt-4 d-flex justify-content-center">
  <ul class="pagination pagination-sm mb-0 shadow-sm rounded">
    
    <?php if($properties->onFirstPage()): ?>
      <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
    <?php else: ?>
      <li class="page-item"><a class="page-link" href="<?php echo e($properties->previousPageUrl()); ?>" rel="prev">&laquo;</a></li>
    <?php endif; ?>

    
    <?php $__currentLoopData = $properties->links()->elements[0] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php if($page == $properties->currentPage()): ?>
        <li class="page-item active"><span class="page-link"><?php echo e($page); ?></span></li>
      <?php else: ?>
        <li class="page-item"><a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a></li>
      <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <?php if($properties->hasMorePages()): ?>
      <li class="page-item"><a class="page-link" href="<?php echo e($properties->nextPageUrl()); ?>" rel="next">&raquo;</a></li>
    <?php else: ?>
      <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
    <?php endif; ?>
  </ul>
</div>

        <?php else: ?>
          <div class="text-center py-5">
            <i class='bx bx-building-house bx-lg text-muted'></i>
            <p class="text-muted mt-3">No properties found</p>
            <?php if(request()->hasAny(['search', 'status'])): ?>
              <a href="<?php echo e(route('admin.properties.index')); ?>" class="btn btn-sm btn-outline-primary">
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
      // You can add toast notification here if you have one
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
<?php echo $__env->make('layouts/contentNavbarLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\renzl\Downloads\myprojects\bhrs\resources\views/content/admin/properties/index.blade.php ENDPATH**/ ?>