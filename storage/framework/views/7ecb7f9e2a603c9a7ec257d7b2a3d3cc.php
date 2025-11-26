

<?php $__env->startSection('title', 'Amenity Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h4 class="mb-1">Amenity Management</h4>
            <p class="text-muted mb-0">Manage property amenities and features</p>
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAmenityModal">
              <i class='bx bx-plus'></i> Add Amenity
            </button>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline-secondary">
              <i class='bx bx-arrow-back'></i> Back to Dashboard
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Card -->
  <div class="col-lg-4 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Total Amenities</p>
            <h4 class="mb-0"><?php echo e(number_format($amenities->total())); ?></h4>
            <small class="text-muted">Available options for properties</small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
              <i class='bx bx-list-check bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Amenities Grid -->
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Amenities</h5>
        <span class="badge bg-label-primary"><?php echo e($amenities->total()); ?> Total</span>
      </div>
      <div class="card-body">
        <?php if($amenities->count() > 0): ?>
          <div class="row">
            <?php $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="card border h-100">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                          <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded bg-label-success">
                              <i class='bx bx-check'></i>
                            </span>
                          </div>
                          <h6 class="mb-0"><?php echo e($amenity->amenity_name); ?></h6>
                        </div>
                        <small class="text-muted">
                          <i class='bx bx-time-five'></i> Added <?php echo e($amenity->created_at->diffForHumans()); ?>

                        </small>
                      </div>
                      <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i class='bx bx-dots-vertical-rounded'></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                          <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editAmenityModal<?php echo e($amenity->amenity_id); ?>">
                            <i class='bx bx-edit me-1'></i> Edit
                          </button>
                          <form action="<?php echo e(route('admin.amenities.delete', $amenity->amenity_id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this amenity? This will remove it from all properties.')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="dropdown-item text-danger">
                              <i class='bx bx-trash me-1'></i> Delete
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Edit Amenity Modal -->
              <div class="modal fade" id="editAmenityModal<?php echo e($amenity->amenity_id); ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <form action="<?php echo e(route('admin.amenities.update', $amenity->amenity_id)); ?>" method="POST">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('PUT'); ?>
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Amenity</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label for="amenity_name_<?php echo e($amenity->amenity_id); ?>" class="form-label">Amenity Name</label>
                          <input type="text" class="form-control" id="amenity_name_<?php echo e($amenity->amenity_id); ?>" name="amenity_name" value="<?php echo e($amenity->amenity_name); ?>" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                          <i class='bx bx-save'></i> Update Amenity
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>

          <!-- Pagination -->
          <div class="mt-4">
            <?php echo e($amenities->links()); ?>

          </div>
        <?php else: ?>
          <div class="text-center py-5">
            <i class='bx bx-list-check bx-lg text-muted'></i>
            <p class="text-muted mt-3">No amenities found</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAmenityModal">
              <i class='bx bx-plus'></i> Add Your First Amenity
            </button>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Create Amenity Modal -->
<div class="modal fade" id="createAmenityModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="<?php echo e(route('admin.amenities.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="modal-header">
          <h5 class="modal-title">Add New Amenity</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="amenity_name" class="form-label">Amenity Name</label>
            <input type="text" class="form-control" id="amenity_name" name="amenity_name" placeholder="e.g., Wi-Fi, Air Conditioning, Parking" required>
            <div class="form-text">Enter a descriptive name for the amenity</div>
          </div>

          <!-- Common Amenities Suggestions -->
          <div class="mb-3">
            <label class="form-label text-muted">Common Amenities:</label>
            <div class="d-flex flex-wrap gap-2">
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('amenity_name').value='Wi-Fi'">Wi-Fi</button>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('amenity_name').value='Air Conditioning'">Air Conditioning</button>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('amenity_name').value='Parking'">Parking</button>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('amenity_name').value='Kitchen'">Kitchen</button>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('amenity_name').value='Laundry'">Laundry</button>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('amenity_name').value='Security'">Security</button>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('amenity_name').value='Water Supply'">Water Supply</button>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('amenity_name').value='Study Area'">Study Area</button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class='bx bx-plus'></i> Add Amenity
          </button>
        </div>
      </form>
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

<?php if($errors->any()): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      alert('<?php echo e($errors->first()); ?>');
    });
  </script>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/contentNavbarLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\renzl\Downloads\myprojects\bhrs\resources\views/content/admin/amenities/index.blade.php ENDPATH**/ ?>