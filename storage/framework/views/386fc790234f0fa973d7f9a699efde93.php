

<?php $__env->startSection('title', $property->title . ' - Property Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
  <div class="row">
   <!-- Back Button -->
<div class="col-12 mb-3">
  <a href="<?php echo e(url()->previous()); ?>" class="btn btn-sm btn-outline-secondary">
    <i class='bx bx-arrow-back'></i> Back
  </a>
</div>

    <!-- Property Image -->
    <div class="col-lg-8 mb-4">
      <div class="card">
        <div class="card-body p-0">
          <?php if($property->image && file_exists(public_path('assets/img/boarding/' . $property->image))): ?>
            <img src="<?php echo e(asset('assets/img/boarding/' . $property->image)); ?>" 
                 class="img-fluid w-100" 
                 style="max-height: 500px; object-fit: cover;"
                 alt="<?php echo e($property->title); ?>">
          <?php else: ?>
            <img src="<?php echo e(asset('assets/img/boarding/default.jpg')); ?>" 
                 class="img-fluid w-100" 
                 style="max-height: 500px; object-fit: cover;"
                 alt="Default"
                 onerror="this.src='<?php echo e(asset('assets/img/default-placeholder.png')); ?>'">
          <?php endif; ?>
        </div>
      </div>

      <!-- Property Details -->
      <div class="card mt-4">
        <div class="card-header">
          <h4 class="mb-0"><?php echo e($property->title); ?></h4>
          <?php if($averageRating): ?>
            <div class="mt-2">
              <span class="badge bg-warning">⭐ <?php echo e(number_format($averageRating, 1)); ?></span>
              <small class="text-muted">(<?php echo e($totalReviews); ?> reviews)</small>
            </div>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <p><i class='bx bx-map text-primary'></i> <strong>Address:</strong> <?php echo e($property->address); ?></p>
              <p><i class='bx bx-money text-success'></i> <strong>Price:</strong> ₱<?php echo e(number_format($property->price, 2)); ?> / month</p>
              <p><i class='bx bx-door-open text-info'></i> <strong>Rooms:</strong> <?php echo e($property->rooms); ?></p>
            </div>
            <div class="col-md-6">
              <?php if($property->distance_from_campus): ?>
                <p><i class='bx bx-map-pin text-danger'></i> <strong>Distance from Campus:</strong> <?php echo e($property->distance_from_campus); ?> km</p>
              <?php endif; ?>
              <?php if($property->capacity): ?>
                <p><i class='bx bx-group text-warning'></i> <strong>Capacity:</strong> <?php echo e($property->capacity); ?> persons</p>
              <?php endif; ?>
              <?php if($property->available_slots): ?>
                <p><i class='bx bx-check-circle text-success'></i> <strong>Available Slots:</strong> <?php echo e($property->available_slots); ?></p>
              <?php endif; ?>
            </div>
          </div>

          <hr>

          <h6 class="mb-2">Description</h6>
          <p><?php echo e($property->description); ?></p>

          <?php if($property->propertyAmenities->count() > 0): ?>
            <hr>
            <h6 class="mb-2">Amenities</h6>
            <div class="d-flex flex-wrap gap-2">
              <?php $__currentLoopData = $property->propertyAmenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="badge bg-label-primary">
                  <i class='bx bx-check'></i> <?php echo e($pa->amenity->amenity_name); ?>

                </span>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          <?php endif; ?>

          <?php if($property->house_rules): ?>
            <hr>
            <h6 class="mb-2">House Rules</h6>
            <p><?php echo e($property->house_rules); ?></p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Reviews Section -->
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="mb-0">Reviews & Ratings</h5>
        </div>
        <div class="card-body">
          <?php if($ratings->count() > 0): ?>
            <?php $__currentLoopData = $ratings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="mb-3 pb-3 border-bottom">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <strong><?php echo e($rating->user->name ?? 'Anonymous'); ?></strong>
                    <div class="text-warning">
                      <?php for($i = 1; $i <= 5; $i++): ?>
                        <?php if($i <= $rating->rating): ?>
                          ⭐
                        <?php else: ?>
                          ☆
                        <?php endif; ?>
                      <?php endfor; ?>
                    </div>
                  </div>
                  <small class="text-muted"><?php echo e($rating->created_at->diffForHumans()); ?></small>
                </div>
                <?php if($rating->review_text): ?>
                  <p class="mt-2 mb-0"><?php echo e($rating->review_text); ?></p>
                <?php endif; ?>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php else: ?>
            <p class="text-muted text-center py-4">No reviews yet. Be the first to review!</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <!-- Contact Card -->
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0 text-white">Contact Information</h5>
        </div>
        <div class="card-body">
          <?php if($property->owner_name): ?>
            <p><i class='bx bx-user'></i> <strong>Owner:</strong> <?php echo e($property->owner_name); ?></p>
          <?php endif; ?>
          <?php if($property->owner_contact): ?>
            <p><i class='bx bx-phone'></i> <strong>Contact:</strong> <?php echo e($property->owner_contact); ?></p>
          <?php endif; ?>
          
          <a href="<?php echo e(route('bookings.create', ['property' => $property->id])); ?>" class="btn btn-primary w-100 mt-2">
            <i class='bx bx-calendar'></i> Book Now
          </a>
          
          <?php if(auth()->guard()->check()): ?>
            <?php if(!$userRating): ?>
              <!-- Show Rate Button if user hasn't rated -->
              <button class="btn btn-outline-warning w-100 mt-2" data-bs-toggle="modal" data-bs-target="#rateModal">
                <i class='bx bx-star'></i> Rate This Property
              </button>
              
              <?php
                $userRatingsCount = \App\Models\Rating::where('user_id', Auth::id())->count();
              ?>
              
              <?php if($userRatingsCount < 2): ?>
                <div class="alert alert-info mt-3 mb-0 p-2">
                  <small>
                    <i class='bx bx-info-circle'></i>
                    <?php if($userRatingsCount == 0): ?>
                      Rate this to start getting AI recommendations!
                    <?php else: ?>
                      Rate this to unlock AI-powered recommendations!
                    <?php endif; ?>
                  </small>
                </div>
              <?php endif; ?>
            <?php else: ?>
              <!-- Show current rating and edit button -->
              <div class="alert alert-success mt-3 mb-2 p-3">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <strong>Your Rating:</strong>
                    <div class="text-warning fs-5">
                      <?php for($i = 1; $i <= 5; $i++): ?>
                        <?php if($i <= $userRating->rating): ?>
                          ⭐
                        <?php else: ?>
                          ☆
                        <?php endif; ?>
                      <?php endfor; ?>
                    </div>
                    <?php if($userRating->review_text): ?>
                      <small class="text-muted d-block mt-1">
                        "<?php echo e(Str::limit($userRating->review_text, 50)); ?>"
                      </small>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#updateRateModal">
                <i class='bx bx-edit'></i> Update Your Rating
              </button>
            <?php endif; ?>
          <?php else: ?>
            <div class="alert alert-info mt-3 mb-0">
              <small><i class='bx bx-info-circle'></i> Login to rate this property</small>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Map Card -->
      <?php if($property->latitude && $property->longitude): ?>
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Location</h5>
        </div>
        <div class="card-body p-0">
          <div id="propertyMap" style="height: 300px;"></div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Similar Properties -->
      <?php if($similarProperties->count() > 0): ?>
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="mb-0">Similar Properties</h5>
        </div>
        <div class="card-body">
          <?php $__currentLoopData = $similarProperties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $similar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="d-flex mb-3">
              <img src="<?php echo e($similar->image ? asset('storage/' . $similar->image) : asset('assets/img/boarding/default.jpg')); ?>" 
                   style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;" 
                   alt="<?php echo e($similar->title); ?>">
              <div class="ms-3">
                <h6 class="mb-1"><?php echo e($similar->title); ?></h6>
                <p class="mb-0 text-success">₱<?php echo e(number_format($similar->price, 2)); ?></p>
                <a href="<?php echo e(route('properties.view', $similar->id)); ?>" class="btn btn-sm btn-outline-primary mt-1">View</a>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ========================================= -->
<!-- RATING MODALS START HERE -->
<!-- ========================================= -->

<!-- CREATE RATING MODAL -->
<?php if(auth()->guard()->check()): ?>
<div class="modal fade" id="rateModal" tabindex="-1" aria-labelledby="rateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="<?php echo e(route('ratings.store')); ?>" method="POST" id="ratingForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="property_id" value="<?php echo e($property->id); ?>">
        
        <div class="modal-header bg-gradient-primary">
          <h5 class="modal-title text-white" id="rateModalLabel">
            <i class='bx bx-star'></i> Rate This Property
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body">
          <!-- Property Info -->
          <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
            <img src="<?php echo e($property->image ? asset('storage/' . $property->image) : asset('assets/img/boarding/default.jpg')); ?>" 
                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" 
                 alt="<?php echo e($property->title); ?>">
            <div class="ms-3">
              <h6 class="mb-0"><?php echo e($property->title); ?></h6>
              <small class="text-muted"><?php echo e($property->address); ?></small>
            </div>
          </div>

          <!-- Star Rating -->
          <div class="mb-4">
            <label class="form-label fw-bold">How would you rate this property?</label>
            <div class="star-rating-input d-flex justify-content-center gap-2 my-3">
              <?php for($i = 5; $i >= 1; $i--): ?>
                <input type="radio" name="rating" id="star<?php echo e($i); ?>" value="<?php echo e($i); ?>" required>
                <label for="star<?php echo e($i); ?>" class="star-label" data-rating="<?php echo e($i); ?>">
                  <i class='bx bxs-star'></i>
                </label>
              <?php endfor; ?>
            </div>
            <div class="text-center">
              <small class="text-muted rating-text">Select a rating</small>
            </div>
          </div>

          <!-- Review Text -->
          <div class="mb-3">
            <label class="form-label fw-bold">
              Share your experience (Optional)
              <small class="text-muted fw-normal">- Help other students make better decisions</small>
            </label>
            <textarea name="review_text" 
                      class="form-control" 
                      rows="4" 
                      placeholder="Tell us about your experience with this property..."
                      maxlength="1000"></textarea>
            <div class="form-text">
              <span id="charCount">0</span>/1000 characters
            </div>
          </div>

          <!-- Rating Benefits -->
          <?php
            $userRatingsCount = \App\Models\Rating::where('user_id', Auth::id())->count();
          ?>
          
          <?php if($userRatingsCount < 2): ?>
          <div class="alert alert-info mb-0">
            <div class="d-flex align-items-start">
              <i class='bx bx-info-circle fs-4 me-2'></i>
              <div>
                <strong>Unlock AI Recommendations!</strong><br>
                <small>
                  <?php if($userRatingsCount == 0): ?>
                    This will be your first rating. Rate one more property to unlock personalized AI-powered recommendations!
                  <?php else: ?>
                    You have <?php echo e($userRatingsCount); ?> rating. Just one more to unlock AI-powered recommendations!
                  <?php endif; ?>
                </small>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class='bx bx-x'></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary" id="submitRating">
            <i class='bx bx-check'></i> Submit Rating
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- UPDATE RATING MODAL (if user already rated) -->
<?php if($userRating): ?>
<div class="modal fade" id="updateRateModal" tabindex="-1" aria-labelledby="updateRateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="<?php echo e(route('ratings.update', $userRating->rating_id)); ?>" method="POST" id="updateRatingForm">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="modal-header bg-gradient-warning">
          <h5 class="modal-title text-white" id="updateRateModalLabel">
            <i class='bx bx-edit'></i> Update Your Rating
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body">
          <!-- Property Info -->
          <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
            <img src="<?php echo e($property->image ? asset('storage/' . $property->image) : asset('assets/img/boarding/default.jpg')); ?>" 
                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" 
                 alt="<?php echo e($property->title); ?>">
            <div class="ms-3">
              <h6 class="mb-0"><?php echo e($property->title); ?></h6>
              <small class="text-muted">Rated on <?php echo e($userRating->created_at->format('M d, Y')); ?></small>
            </div>
          </div>

          <!-- Star Rating -->
          <div class="mb-4">
            <label class="form-label fw-bold">Update your rating</label>
            <div class="star-rating-input-update d-flex justify-content-center gap-2 my-3">
              <?php for($i = 5; $i >= 1; $i--): ?>
                <input type="radio" name="rating" id="update_star<?php echo e($i); ?>" value="<?php echo e($i); ?>" 
                       <?php echo e($userRating->rating == $i ? 'checked' : ''); ?> required>
                <label for="update_star<?php echo e($i); ?>" class="star-label" data-rating="<?php echo e($i); ?>">
                  <i class='bx bxs-star'></i>
                </label>
              <?php endfor; ?>
            </div>
            <div class="text-center">
              <small class="text-muted rating-text-update">
                <?php echo e($userRating->rating); ?> <?php echo e($userRating->rating == 1 ? 'star' : 'stars'); ?>

              </small>
            </div>
          </div>

          <!-- Review Text -->
          <div class="mb-3">
            <label class="form-label fw-bold">Update your review (Optional)</label>
            <textarea name="review_text" 
                      class="form-control" 
                      rows="4" 
                      placeholder="Update your experience..."
                      maxlength="1000"><?php echo e($userRating->review_text); ?></textarea>
            <div class="form-text">
              <span id="charCountUpdate"><?php echo e(strlen($userRating->review_text ?? '')); ?></span>/1000 characters
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class='bx bx-x'></i> Cancel
          </button>
          <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteRateModal" data-bs-dismiss="modal">
            <i class='bx bx-trash'></i> Delete
          </button>
          <button type="submit" class="btn btn-warning">
            <i class='bx bx-save'></i> Update Rating
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="deleteRateModal" tabindex="-1" aria-labelledby="deleteRateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <form action="<?php echo e(route('ratings.destroy', $userRating->rating_id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-white" id="deleteRateModalLabel">
            <i class='bx bx-trash'></i> Delete Rating
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body text-center">
          <i class='bx bx-error-circle text-danger' style="font-size: 60px;"></i>
          <p class="mt-3 mb-0">Are you sure you want to delete your rating?</p>
          <small class="text-muted">This action cannot be undone.</small>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger btn-sm">Delete Rating</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- Success/Error Toast Notifications -->
<?php if(session('success') || session('error')): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
  <?php if(session('success')): ?>
  <div class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <i class='bx bx-check-circle me-2'></i>
        <?php echo e(session('success')); ?>

      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
  <?php endif; ?>

  <?php if(session('error')): ?>
  <div class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <i class='bx bx-error-circle me-2'></i>
        <?php echo e(session('error')); ?>

      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<style>
/* Star Rating Input Styles */
.star-rating-input,
.star-rating-input-update {
  display: flex;
  flex-direction: row-reverse;
  justify-content: center;
}

.star-rating-input input,
.star-rating-input-update input {
  display: none;
}

.star-label {
  cursor: pointer;
  font-size: 45px;
  color: #ddd;
  transition: all 0.2s ease;
}

.star-label:hover {
  transform: scale(1.2);
}

/* Checked star and stars before it */
.star-rating-input input:checked ~ label,
.star-rating-input label:hover,
.star-rating-input label:hover ~ label {
  color: #ffc107;
  text-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
}

.star-rating-input-update input:checked ~ label,
.star-rating-input-update label:hover,
.star-rating-input-update label:hover ~ label {
  color: #ffc107;
  text-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
}

/* Modal Gradient Headers */
.bg-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-warning {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

/* Character counter */
#charCount, #charCountUpdate {
  font-weight: bold;
  color: #696cff;
}

/* Toast styles */
.toast {
  min-width: 300px;
}
</style>

<?php if($property->latitude && $property->longitude): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('propertyMap').setView([<?php echo e($property->latitude); ?>, <?php echo e($property->longitude); ?>], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    
    L.marker([<?php echo e($property->latitude); ?>, <?php echo e($property->longitude); ?>])
      .addTo(map)
      .bindPopup('<?php echo e($property->title); ?>')
      .openPopup();
  });
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Rating text descriptions
  const ratingDescriptions = {
    1: '⭐ Poor',
    2: '⭐⭐ Fair',
    3: '⭐⭐⭐ Good',
    4: '⭐⭐⭐⭐ Very Good',
    5: '⭐⭐⭐⭐⭐ Excellent'
  };

  // Handle rating selection (Create modal)
  const starInputs = document.querySelectorAll('.star-rating-input input[name="rating"]');
  const ratingText = document.querySelector('.rating-text');
  
  starInputs.forEach(input => {
    input.addEventListener('change', function() {
      const rating = this.value;
      if (ratingText) {
        ratingText.textContent = ratingDescriptions[rating];
        ratingText.style.color = '#ffc107';
        ratingText.style.fontWeight = 'bold';
      }
    });
  });

  // Handle rating selection (Update modal)
  const updateStarInputs = document.querySelectorAll('.star-rating-input-update input[name="rating"]');
  const updateRatingText = document.querySelector('.rating-text-update');
  
  updateStarInputs.forEach(input => {
    input.addEventListener('change', function() {
      const rating = this.value;
      if (updateRatingText) {
        updateRatingText.textContent = ratingDescriptions[rating];
        updateRatingText.style.color = '#ffc107';
        updateRatingText.style.fontWeight = 'bold';
      }
    });
  });

  // Character counter for review text (Create modal)
  const reviewTextarea = document.querySelector('#ratingForm textarea[name="review_text"]');
  const charCount = document.getElementById('charCount');
  
  if (reviewTextarea && charCount) {
    reviewTextarea.addEventListener('input', function() {
      charCount.textContent = this.value.length;
      
      if (this.value.length >= 1000) {
        charCount.style.color = '#ff3e1d';
      } else if (this.value.length >= 800) {
        charCount.style.color = '#ff9f43';
      } else {
        charCount.style.color = '#696cff';
      }
    });
  }

  // Character counter for review text (Update modal)
  const updateReviewTextarea = document.querySelector('#updateRatingForm textarea[name="review_text"]');
  const charCountUpdate = document.getElementById('charCountUpdate');
  
  if (updateReviewTextarea && charCountUpdate) {
    updateReviewTextarea.addEventListener('input', function() {
      charCountUpdate.textContent = this.value.length;
      
      if (this.value.length >= 1000) {
        charCountUpdate.style.color = '#ff3e1d';
      } else if (this.value.length >= 800) {
        charCountUpdate.style.color = '#ff9f43';
      } else {
        charCountUpdate.style.color = '#696cff';
      }
    });
  }

  // Form validation
  const ratingForm = document.getElementById('ratingForm');
  if (ratingForm) {
    ratingForm.addEventListener('submit', function(e) {
      const ratingChecked = document.querySelector('.star-rating-input input[name="rating"]:checked');
      
      if (!ratingChecked) {
        e.preventDefault();
        alert('Please select a rating before submitting');
        return false;
      }
    });
  }

  // Auto hide toasts after delay
  const toasts = document.querySelectorAll('.toast');
  toasts.forEach(toast => {
    setTimeout(() => {
      toast.classList.remove('show');
    }, 5000);
  });

  // Special celebration for CF unlock
  <?php if(session('success') && str_contains(session('success'), '🎉')): ?>
    setTimeout(() => {
      // You can add confetti or animation here
      console.log('AI Recommendations Unlocked! 🎉');
    }, 500);
  <?php endif; ?>
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/contentNavbarLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\renzl\Downloads\myprojects\bhrs\resources\views/content/properties/show.blade.php ENDPATH**/ ?>