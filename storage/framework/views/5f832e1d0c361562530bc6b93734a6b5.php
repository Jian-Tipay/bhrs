

<?php $__env->startSection('title', 'Edit Property - ' . $property->title); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <a href="<?php echo e(route('landlord.properties.view', $property->id)); ?>" class="btn btn-sm btn-outline-secondary me-3">
              <i class='bx bx-arrow-back'></i> Back
            </a>
            <div>
              <h4 class="mb-1">Edit Property</h4>
              <p class="mb-0 text-muted">Update property information and details</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Form -->
  <div class="col-12">
    <form action="<?php echo e(route('landlord.properties.update', $property->id)); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      <div class="row">
        <!-- Main Information -->
        <div class="col-md-8 mb-4">
          <!-- Basic Information Card -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">📋 Basic Information</h5>
            </div>
            <div class="card-body">
              <!-- Title -->
              <div class="mb-3">
                <label for="title" class="form-label">Property Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       id="title" name="title" 
                       value="<?php echo e(old('title', $property->title)); ?>" 
                       required placeholder="e.g., Cozy 2-Bedroom Apartment">
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <!-- Description -->
              <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                          id="description" name="description" 
                          rows="5" required 
                          placeholder="Describe your property..."><?php echo e(old('description', $property->description)); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <!-- Address -->
              <div class="mb-3">
                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       id="address" name="address" 
                       value="<?php echo e(old('address', $property->address)); ?>" 
                       required placeholder="Complete address">
                <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <!-- Price and Rooms -->
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="price" class="form-label">Monthly Rate (₱) <span class="text-danger">*</span></label>
                  <input type="number" class="form-control <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                         id="price" name="price" 
                         value="<?php echo e(old('price', $property->price)); ?>" 
                         required min="0" step="0.01" placeholder="5000">
                  <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                  <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="rooms" class="form-label">Number of Rooms <span class="text-danger">*</span></label>
                  <input type="number" class="form-control <?php $__errorArgs = ['rooms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                         id="rooms" name="rooms" 
                         value="<?php echo e(old('rooms', $property->rooms)); ?>" 
                         required min="1" placeholder="2">
                  <?php $__errorArgs = ['rooms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                  <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
              </div>

              <!-- Capacity and Available Slots -->
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="capacity" class="form-label">Total Capacity</label>
                  <input type="number" class="form-control <?php $__errorArgs = ['capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                         id="capacity" name="capacity" 
                         value="<?php echo e(old('capacity', $property->capacity)); ?>" 
                         min="1" placeholder="4">
                  <small class="text-muted">Maximum number of tenants</small>
                  <?php $__errorArgs = ['capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                  <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="available_slots" class="form-label">Available Slots</label>
                  <input type="number" class="form-control <?php $__errorArgs = ['available_slots'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                         id="available_slots" name="available_slots" 
                         value="<?php echo e(old('available_slots', $property->available_slots)); ?>" 
                         min="0" placeholder="2">
                  <small class="text-muted">Current available slots</small>
                  <?php $__errorArgs = ['available_slots'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                  <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
              </div>

              <!-- Distance from Campus -->
              <div class="mb-3">
                <label for="distance_from_campus" class="form-label">Distance from Campus (km)</label>
                <input type="number" class="form-control <?php $__errorArgs = ['distance_from_campus'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       id="distance_from_campus" name="distance_from_campus" 
                       value="<?php echo e(old('distance_from_campus', $property->distance_from_campus)); ?>" 
                       min="0" step="0.1" placeholder="1.5">
                <?php $__errorArgs = ['distance_from_campus'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <!-- House Rules -->
              <div class="mb-3">
                <label for="house_rules" class="form-label">House Rules</label>
                <textarea class="form-control <?php $__errorArgs = ['house_rules'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                          id="house_rules" name="house_rules" 
                          rows="4" 
                          placeholder="e.g., No smoking, No pets, Quiet hours 10PM-6AM"><?php echo e(old('house_rules', $property->house_rules)); ?></textarea>
                <?php $__errorArgs = ['house_rules'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <!-- Location Coordinates -->
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="latitude" class="form-label">Latitude</label>
                  <input type="number" class="form-control <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                         id="latitude" name="latitude" 
                         value="<?php echo e(old('latitude', $property->latitude)); ?>" 
                         step="any" placeholder="14.5995">
                  <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                  <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="longitude" class="form-label">Longitude</label>
                  <input type="number" class="form-control <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                         id="longitude" name="longitude" 
                         value="<?php echo e(old('longitude', $property->longitude)); ?>" 
                         step="any" placeholder="120.9842">
                  <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                  <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Amenities Card -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">🏆 Amenities</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <?php $__empty_1 = true; $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <div class="col-md-4 mb-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" 
                             name="amenities[]" 
                             value="<?php echo e($amenity->amenity_id); ?>" 
                             id="amenity_<?php echo e($amenity->amenity_id); ?>"
                             <?php echo e(in_array($amenity->amenity_id, $propertyAmenities) ? 'checked' : ''); ?>>
                      <label class="form-check-label" for="amenity_<?php echo e($amenity->amenity_id); ?>">
                        <?php echo e($amenity->amenity_name); ?>

                      </label>
                    </div>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <div class="col-12">
                    <p class="text-muted mb-0">No amenities available</p>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
          <!-- Property Image Card -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">📷 Property Image</h5>
            </div>
            <div class="card-body">
              <!-- Current Image Preview -->
              <div class="mb-3 text-center">
                <?php if($property->image && file_exists(public_path('assets/img/boarding/' . $property->image))): ?>
                  <img src="<?php echo e(asset('assets/img/boarding/' . $property->image)); ?>" 
                       id="imagePreview"
                       class="img-fluid rounded" 
                       alt="Current Image"
                       style="max-height: 300px; object-fit: cover;">
                <?php else: ?>
                  <img src="<?php echo e(asset('assets/img/boarding/default.jpg')); ?>" 
                       id="imagePreview"
                       class="img-fluid rounded" 
                       alt="Default Image"
                       style="max-height: 300px; object-fit: cover;">
                <?php endif; ?>
              </div>

              <!-- Upload New Image -->
              <div class="mb-3">
                <label for="image" class="form-label">Upload New Image</label>
                <input type="file" class="form-control <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       id="image" name="image" 
                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF, WEBP</small>
                <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
            </div>
          </div>

          <!-- Owner Information Card -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">👤 Owner Information</h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label for="owner_name" class="form-label">Owner Name</label>
                <input type="text" class="form-control <?php $__errorArgs = ['owner_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       id="owner_name" name="owner_name" 
                       value="<?php echo e(old('owner_name', $property->owner_name)); ?>" 
                       placeholder="John Doe">
                <?php $__errorArgs = ['owner_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="mb-3">
                <label for="owner_contact" class="form-label">Contact Number</label>
                <input type="text" class="form-control <?php $__errorArgs = ['owner_contact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       id="owner_contact" name="owner_contact" 
                       value="<?php echo e(old('owner_contact', $property->owner_contact)); ?>" 
                       placeholder="09XX XXX XXXX">
                <?php $__errorArgs = ['owner_contact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
            </div>
          </div>

          <!-- Property Status Card -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">⚙️ Property Status</h5>
            </div>
            <div class="card-body">
              <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" 
                       id="available" name="available" 
                       <?php echo e(old('available', $property->available) ? 'checked' : ''); ?>>
                <label class="form-check-label" for="available">
                  Mark as Available
                </label>
              </div>
              <small class="text-muted">Uncheck if property is fully booked or unavailable</small>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="card">
            <div class="card-body">
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class='bx bx-save'></i> Update Property
                </button>
                <a href="<?php echo e(route('landlord.properties.view', $property->id)); ?>" class="btn btn-outline-secondary">
                  <i class='bx bx-x'></i> Cancel
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script>
  // Image preview functionality
  document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('imagePreview').src = e.target.result;
      }
      reader.readAsDataURL(file);
    }
  });

  // Auto-update available_slots when capacity changes
  document.getElementById('capacity').addEventListener('input', function() {
    const currentAvailable = parseInt(document.getElementById('available_slots').value) || 0;
    const oldCapacity = <?php echo e($property->capacity ?? 0); ?>;
    const newCapacity = parseInt(this.value) || 0;
    
    // Adjust available slots proportionally
    if (oldCapacity > 0) {
      const ratio = currentAvailable / oldCapacity;
      document.getElementById('available_slots').value = Math.floor(newCapacity * ratio);
    }
  });

  // Form validation
  document.querySelector('form').addEventListener('submit', function(e) {
    const capacity = parseInt(document.getElementById('capacity').value) || 0;
    const availableSlots = parseInt(document.getElementById('available_slots').value) || 0;
    
    if (availableSlots > capacity) {
      e.preventDefault();
      alert('Available slots cannot exceed total capacity!');
      return false;
    }
  });

  console.log('Edit property page loaded');
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/contentNavbarLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\renzl\Downloads\myprojects\bhrs\resources\views/content/landlord/properties/edit.blade.php ENDPATH**/ ?>