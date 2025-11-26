

<?php $__env->startSection('title', 'Review Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h4 class="mb-1">Review Management</h4>
            <p class="text-muted mb-0">Monitor and manage property reviews and ratings</p>
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
            <p class="card-text">Total Reviews</p>
            <h4 class="mb-0"><?php echo e(number_format($reviews->total())); ?></h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
              <i class='bx bx-message-square-dots bx-sm'></i>
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
            <p class="card-text">Average Rating</p>
            <h4 class="mb-0"><?php echo e(number_format($reviews->avg('rating') ?? 0, 1)); ?> <small class="text-warning">★</small></h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-warning rounded p-2">
              <i class='bx bx-star bx-sm'></i>
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
            <p class="card-text">5 Star Reviews</p>
            <h4 class="mb-0 text-success"><?php echo e(number_format($reviews->where('rating', 5.0)->count())); ?></h4>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-success rounded p-2">
              <i class='bx bx-like bx-sm'></i>
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
            <h4 class="mb-0 text-info"><?php echo e(number_format($reviews->filter(function($r) { return $r->created_at->isCurrentMonth(); })->count())); ?></h4>
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

  <!-- Filters -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.reviews.index')); ?>">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Rating</label>
              <select name="rating" class="form-select">
                <option value="">All Ratings</option>
                <option value="5" <?php echo e(request('rating') == '5' ? 'selected' : ''); ?>>5 Stars</option>
                <option value="4" <?php echo e(request('rating') == '4' ? 'selected' : ''); ?>>4 Stars</option>
                <option value="3" <?php echo e(request('rating') == '3' ? 'selected' : ''); ?>>3 Stars</option>
                <option value="2" <?php echo e(request('rating') == '2' ? 'selected' : ''); ?>>2 Stars</option>
                <option value="1" <?php echo e(request('rating') == '1' ? 'selected' : ''); ?>>1 Star</option>
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
                <a href="<?php echo e(route('admin.reviews.index')); ?>" class="btn btn-outline-secondary">
                  <i class='bx bx-reset'></i> Reset
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Reviews List -->
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Reviews</h5>
        <span class="badge bg-label-primary"><?php echo e($reviews->total()); ?> Total</span>
      </div>
      <div class="card-body">
        <?php if($reviews->count() > 0): ?>
          <div class="row">
            <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="col-12 mb-3">
                <div class="card border">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-8">
                        <!-- User Info -->
                        <div class="d-flex align-items-start mb-3">
                          <div class="avatar avatar-md me-3">
                            <?php if($review->user->profile_picture): ?>
                              <img src="<?php echo e(asset('storage/' . $review->user->profile_picture)); ?>" alt="<?php echo e($review->user->first_name); ?>" class="rounded-circle">
                            <?php else: ?>
                              <span class="avatar-initial rounded-circle bg-label-primary">
                                <?php echo e(strtoupper(substr($review->user->first_name, 0, 1))); ?>

                              </span>
                            <?php endif; ?>
                          </div>
                          <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                              <div>
                                <h6 class="mb-0"><?php echo e($review->user->first_name); ?> <?php echo e($review->user->last_name); ?></h6>
                                <small class="text-muted"><?php echo e($review->user->email); ?></small>
                              </div>
                              <div class="text-end">
                                <!-- Rating Stars -->
                                <div class="mb-1">
                                  <?php for($i = 1; $i <= 5; $i++): ?>
                                    <?php if($i <= $review->rating): ?>
                                      <i class='bx bxs-star text-warning'></i>
                                    <?php else: ?>
                                      <i class='bx bx-star text-muted'></i>
                                    <?php endif; ?>
                                  <?php endfor; ?>
                                  <strong class="ms-1"><?php echo e(number_format($review->rating, 1)); ?></strong>
                                </div>
                                <small class="text-muted"><?php echo e($review->created_at->diffForHumans()); ?></small>
                              </div>
                            </div>
                            
                            <!-- Review Text -->
                            <?php if($review->review_text): ?>
                              <p class="mt-3 mb-2"><?php echo e($review->review_text); ?></p>
                            <?php else: ?>
                              <p class="mt-3 mb-2 text-muted fst-italic">No written review</p>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <!-- Property Info -->
                        <div class="border-start ps-3">
                          <small class="text-muted d-block mb-2">PROPERTY REVIEWED</small>
                          <div class="d-flex align-items-center mb-2">
                            <?php if($review->property->images && $review->property->images->count() > 0): ?>
                              <img src="<?php echo e(asset('storage/' . $review->property->images->first()->image_path)); ?>" 
                                   alt="<?php echo e($review->property->title); ?>" 
                                   class="rounded me-2" 
                                   style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                              <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-info">
                                  <i class='bx bx-building-house'></i>
                                </span>
                              </div>
                            <?php endif; ?>
                            <div>
                              <strong class="d-block"><?php echo e(Str::limit($review->property->title, 30)); ?></strong>
                              <small class="text-muted">₱<?php echo e(number_format($review->property->price, 2)); ?>/mo</small>
                            </div>
                          </div>
                          
                          <!-- Action Buttons -->
                          <div class="d-flex gap-2 mt-3">
                            <a href="<?php echo e(route('properties.view', $review->property->id)); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                              <i class='bx bx-show'></i> View Property
                            </a>
                            <form action="<?php echo e(route('admin.reviews.delete', $review->rating_id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?')">
                              <?php echo csrf_field(); ?>
                              <?php echo method_field('DELETE'); ?>
                              <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class='bx bx-trash'></i> Delete
                              </button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>

          <!-- Pagination -->
        <!-- ✅ Fixed Pagination Styling -->
<div class="mt-4 d-flex justify-content-center">
  <ul class="pagination pagination-sm mb-0 shadow-sm rounded">
    
    <?php if($reviews->onFirstPage()): ?>
      <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
    <?php else: ?>
      <li class="page-item"><a class="page-link" href="<?php echo e($reviews->previousPageUrl()); ?>" rel="prev">&laquo;</a></li>
    <?php endif; ?>

    
    <?php $__currentLoopData = $reviews->links()->elements[0] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php if($page == $reviews->currentPage()): ?>
        <li class="page-item active"><span class="page-link"><?php echo e($page); ?></span></li>
      <?php else: ?>
        <li class="page-item"><a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a></li>
      <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <?php if($reviews->hasMorePages()): ?>
      <li class="page-item"><a class="page-link" href="<?php echo e($reviews->nextPageUrl()); ?>" rel="next">&raquo;</a></li>
    <?php else: ?>
      <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
    <?php endif; ?>
  </ul>
</div>

        <?php else: ?>
          <div class="text-center py-5">
            <i class='bx bx-message-square-x bx-lg text-muted'></i>
            <p class="text-muted mt-3">No reviews found</p>
            <?php if(request()->hasAny(['rating', 'date_from', 'date_to'])): ?>
              <a href="<?php echo e(route('admin.reviews.index')); ?>" class="btn btn-sm btn-outline-primary">
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
<?php echo $__env->make('layouts/contentNavbarLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\renzl\Downloads\myprojects\bhrs\resources\views/content/admin/reviews/index.blade.php ENDPATH**/ ?>