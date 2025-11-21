@extends('layouts/contentNavbarLayout')

@section('title', 'My Ratings & Reviews')

@section('content')
<div class="container-fluid">
  <!-- Header Section -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card bg-gradient-primary text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="text-white mb-1">
                <i class='bx bx-star'></i> My Ratings & Reviews
              </h4>
              <p class="mb-0 opacity-75">Manage your property ratings and reviews</p>
            </div>
            <div class="text-end">
              <h2 class="text-white mb-0">{{ $ratings->count() }}</h2>
              <small class="opacity-75">Total Ratings</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Rating Statistics -->
  @if($ratings->count() > 0)
  <div class="row mb-4">
    <div class="col-md-3 col-6 mb-3">
      <div class="card">
        <div class="card-body text-center">
          <i class='bx bx-star text-warning' style="font-size: 2rem;"></i>
          <h4 class="mb-0 mt-2">{{ number_format($ratings->avg('rating'), 1) }}</h4>
          <small class="text-muted">Average Rating</small>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
      <div class="card">
        <div class="card-body text-center">
          <i class='bx bx-message-square-detail text-info' style="font-size: 2rem;"></i>
          <h4 class="mb-0 mt-2">{{ $ratings->whereNotNull('review_text')->count() }}</h4>
          <small class="text-muted">With Reviews</small>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
      <div class="card">
        <div class="card-body text-center">
          <i class='bx bx-trending-up text-success' style="font-size: 2rem;"></i>
          <h4 class="mb-0 mt-2">{{ $ratings->where('rating', '>=', 4)->count() }}</h4>
          <small class="text-muted">Positive (4-5★)</small>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
      <div class="card">
        <div class="card-body text-center">
          <i class='bx bx-calendar text-primary' style="font-size: 2rem;"></i>
          <h4 class="mb-0 mt-2">{{ $ratings->first()->created_at->format('M Y') }}</h4>
          <small class="text-muted">First Rating</small>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- AI Recommendation Status -->
  @php
    $userRatingsCount = $ratings->count();
  @endphp
  
  @if($userRatingsCount < 2)
  <div class="row mb-4">
    <div class="col-12">
      <div class="alert alert-info d-flex align-items-center" role="alert">
        <i class='bx bx-info-circle fs-4 me-3'></i>
        <div class="flex-grow-1">
          <h6 class="alert-heading mb-1">Unlock AI-Powered Recommendations!</h6>
          <p class="mb-0">
            @if($userRatingsCount == 0)
              Rate at least 2 properties to receive personalized AI recommendations based on your preferences.
            @else
              You have {{ $userRatingsCount }} rating. Just rate one more property to unlock personalized AI recommendations!
            @endif
          </p>
        </div>
        <a href="{{ route('recommendations.index') }}" class="btn btn-sm btn-primary">
          <i class='bx bx-search'></i> Browse Properties
        </a>
      </div>
    </div>
  </div>
  @else
  <div class="row mb-4">
    <div class="col-12">
      <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class='bx bx-check-circle fs-4 me-3'></i>
        <div class="flex-grow-1">
          <h6 class="alert-heading mb-1">🎉 AI Recommendations Active!</h6>
          <p class="mb-0">You've unlocked personalized property recommendations. Check your dashboard for suggestions!</p>
        </div>
        <a href="{{ route('dashboard.user') }}" class="btn btn-sm btn-success">
          <i class='bx bx-home'></i> View Dashboard
        </a>
      </div>
    </div>
  </div>
  @endif

  <!-- Ratings List -->
  <div class="row">
    <div class="col-12">
      @if($ratings->count() > 0)
        @foreach($ratings as $rating)
        <div class="card mb-3">
          <div class="card-body">
            <div class="row">
              <!-- Property Image -->
              <div class="col-md-2 col-sm-3 mb-3 mb-md-0">
                <img src="{{ $rating->property->image ? asset('storage/' . $rating->property->image) : asset('assets/img/boarding/default.jpg') }}" 
                     class="img-fluid rounded" 
                     style="width: 100%; height: 120px; object-fit: cover;"
                     alt="{{ $rating->property->title }}">
              </div>

              <!-- Rating Content -->
              <div class="col-md-7 col-sm-9">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <h5 class="mb-1">
                      <a href="{{ route('properties.show', $rating->property->id) }}" class="text-decoration-none">
                        {{ $rating->property->title }}
                      </a>
                    </h5>
                    <p class="text-muted mb-2">
                      <i class='bx bx-map'></i> {{ $rating->property->address }}
                    </p>
                  </div>
                </div>

                <!-- Star Rating -->
                <div class="mb-2">
                  <span class="fw-bold me-2">Your Rating:</span>
                  <span class="text-warning fs-5">
                    @for($i = 1; $i <= 5; $i++)
                      @if($i <= $rating->rating)
                        ⭐
                      @else
                        ☆
                      @endif
                    @endfor
                  </span>
                  <span class="badge bg-label-warning ms-2">{{ $rating->rating }}/5</span>
                </div>

                <!-- Review Text -->
                @if($rating->review_text)
                <div class="mb-2">
                  <div class="bg-light p-3 rounded">
                    <small class="text-muted d-block mb-1">
                      <i class='bx bx-message-square-detail'></i> Your Review:
                    </small>
                    <p class="mb-0">"{{ $rating->review_text }}"</p>
                  </div>
                </div>
                @else
                <div class="mb-2">
                  <small class="text-muted">
                    <i class='bx bx-info-circle'></i> No review text provided
                  </small>
                </div>
                @endif

                <!-- Rating Date -->
                <small class="text-muted">
                  <i class='bx bx-time'></i> Rated {{ $rating->created_at->diffForHumans() }}
                  @if($rating->updated_at != $rating->created_at)
                    <span class="badge bg-label-info ms-2">Updated</span>
                  @endif
                </small>
              </div>

              <!-- Action Buttons -->
              <div class="col-md-3 col-12 text-md-end mt-3 mt-md-0">
                <div class="d-flex flex-md-column gap-2">
                  <a href="{{ route('properties.show', $rating->property->id) }}" 
                     class="btn btn-sm btn-outline-primary w-100">
                    <i class='bx bx-show'></i> View Property
                  </a>
                  
                  <button class="btn btn-sm btn-outline-warning w-100" 
                          data-bs-toggle="modal" 
                          data-bs-target="#editRatingModal{{ $rating->rating_id }}">
                    <i class='bx bx-edit'></i> Edit Rating
                  </button>
                  
                  <button class="btn btn-sm btn-outline-danger w-100" 
                          data-bs-toggle="modal" 
                          data-bs-target="#deleteRatingModal{{ $rating->rating_id }}">
                    <i class='bx bx-trash'></i> Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Rating Modal -->
        <div class="modal fade" id="editRatingModal{{ $rating->rating_id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <form action="{{ route('ratings.update', $rating->rating_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-header bg-gradient-warning">
                  <h5 class="modal-title text-white">
                    <i class='bx bx-edit'></i> Edit Rating
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                  <!-- Property Info -->
                  <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                    <img src="{{ $rating->property->image ? asset('storage/' . $rating->property->image) : asset('assets/img/boarding/default.jpg') }}" 
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" 
                         alt="{{ $rating->property->title }}">
                    <div class="ms-3">
                      <h6 class="mb-0">{{ $rating->property->title }}</h6>
                      <small class="text-muted">Originally rated {{ $rating->created_at->format('M d, Y') }}</small>
                    </div>
                  </div>

                  <!-- Star Rating -->
                  <div class="mb-4">
                    <label class="form-label fw-bold">Update your rating</label>
                    <div class="star-rating-edit d-flex justify-content-center gap-2 my-3" data-rating-id="{{ $rating->rating_id }}">
                      @for($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating" id="edit_star{{ $rating->rating_id }}_{{ $i }}" value="{{ $i }}" 
                               {{ $rating->rating == $i ? 'checked' : '' }} required>
                        <label for="edit_star{{ $rating->rating_id }}_{{ $i }}" class="star-label">
                          <i class='bx bxs-star'></i>
                        </label>
                      @endfor
                    </div>
                    <div class="text-center">
                      <small class="text-muted rating-text-{{ $rating->rating_id }}">{{ $rating->rating }} stars</small>
                    </div>
                  </div>

                  <!-- Review Text -->
                  <div class="mb-3">
                    <label class="form-label fw-bold">Update your review (Optional)</label>
                    <textarea name="review_text" 
                              class="form-control" 
                              rows="4" 
                              maxlength="1000">{{ $rating->review_text }}</textarea>
                  </div>
                </div>
                
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-warning">
                    <i class='bx bx-save'></i> Update Rating
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Delete Rating Modal -->
        <div class="modal fade" id="deleteRatingModal{{ $rating->rating_id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
              <form action="{{ route('ratings.destroy', $rating->rating_id) }}" method="POST">
                @csrf
                @method('DELETE')
                
                <div class="modal-header bg-danger">
                  <h5 class="modal-title text-white">
                    <i class='bx bx-trash'></i> Delete Rating
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body text-center py-4">
                  <i class='bx bx-error-circle text-danger' style="font-size: 60px;"></i>
                  <h6 class="mt-3 mb-2">Delete rating for:</h6>
                  <p class="fw-bold mb-2">{{ $rating->property->title }}</p>
                  <p class="text-muted mb-0">This action cannot be undone.</p>
                </div>
                
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class='bx bx-trash'></i> Delete Rating
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        @endforeach

      @else
        <!-- Empty State -->
        <div class="card">
          <div class="card-body text-center py-5">
            <i class='bx bx-star' style="font-size: 80px; color: #ddd;"></i>
            <h4 class="mt-3 mb-2">No Ratings Yet</h4>
            <p class="text-muted mb-4">You haven't rated any properties yet. Start rating to get personalized recommendations!</p>
            <a href="{{ route('recommendations.index') }}" class="btn btn-primary">
              <i class='bx bx-search'></i> Browse Properties
            </a>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- Success/Error Toast -->
@if(session('success') || session('error'))
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
  @if(session('success'))
  <div class="toast align-items-center text-white bg-success border-0 show" role="alert">
    <div class="d-flex">
      <div class="toast-body">
        <i class='bx bx-check-circle me-2'></i>
        {{ session('success') }}
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
  @endif

  @if(session('error'))
  <div class="toast align-items-center text-white bg-danger border-0 show" role="alert">
    <div class="d-flex">
      <div class="toast-body">
        <i class='bx bx-error-circle me-2'></i>
        {{ session('error') }}
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
  @endif
</div>
@endif

<style>
/* Gradient Header */
.bg-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-warning {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

/* Star Rating Styles */
.star-rating-edit {
  display: flex;
  flex-direction: row-reverse;
  justify-content: center;
}

.star-rating-edit input {
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

.star-rating-edit input:checked ~ label,
.star-rating-edit label:hover,
.star-rating-edit label:hover ~ label {
  color: #ffc107;
  text-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
}

/* Card Hover Effect */
.card {
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Button Styles */
.btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
}

/* Toast Styles */
.toast {
  min-width: 300px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .star-label {
    font-size: 35px;
  }
}
</style>

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

  // Handle all star rating inputs
  document.querySelectorAll('.star-rating-edit').forEach(container => {
    const ratingId = container.dataset.ratingId;
    const inputs = container.querySelectorAll('input[name="rating"]');
    const ratingText = document.querySelector(`.rating-text-${ratingId}`);
    
    inputs.forEach(input => {
      input.addEventListener('change', function() {
        const rating = this.value;
        if (ratingText) {
          ratingText.textContent = ratingDescriptions[rating];
          ratingText.style.color = '#ffc107';
          ratingText.style.fontWeight = 'bold';
        }
      });
    });
  });

  // Auto hide toasts
  const toasts = document.querySelectorAll('.toast');
  toasts.forEach(toast => {
    setTimeout(() => {
      toast.classList.remove('show');
    }, 5000);
  });

  // Confirmation for delete
  document.querySelectorAll('form[method="POST"]').forEach(form => {
    if (form.querySelector('input[name="_method"][value="DELETE"]')) {
      form.addEventListener('submit', function(e) {
        // The modal already handles confirmation, so we just prevent double-submission
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Deleting...';
        }
      });
    }
  });
});
</script>
@endsection