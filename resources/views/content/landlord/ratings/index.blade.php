@extends('layouts/contentNavbarLayout')

@section('title', 'Property Reviews')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-1">⭐ Property Reviews</h4>
        <p class="mb-0 text-muted">View and manage reviews from your tenants</p>
      </div>
    </div>
  </div>

  <!-- Rating Overview -->
  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-body text-center">
        <h2 class="display-3 mb-2">{{ number_format($averageRating, 1) }}</h2>
        <div class="mb-2">
          @for($i = 1; $i <= 5; $i++)
            @if($i <= floor($averageRating))
              <i class='bx bxs-star text-warning'></i>
            @elseif($i - 0.5 <= $averageRating)
              <i class='bx bxs-star-half text-warning'></i>
            @else
              <i class='bx bx-star text-warning'></i>
            @endif
          @endfor
        </div>
        <p class="text-muted mb-0">Based on {{ $totalReviews }} {{ Str::plural('review', $totalReviews) }}</p>
      </div>
    </div>
  </div>

  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-3">Rating Distribution</h6>
        
        @foreach([5, 4, 3, 2, 1] as $star)
          @php
            $count = $ratingDistribution[$star] ?? 0;
            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
            $colorClass = match($star) {
              5 => 'bg-success',
              4 => 'bg-info',
              3 => 'bg-warning',
              2 => 'bg-orange',
              1 => 'bg-danger',
            };
          @endphp
          <div class="d-flex align-items-center mb-2">
            <span class="me-2" style="min-width: 40px;">{{ $star }} ⭐</span>
            <div class="progress flex-grow-1 me-2" style="height: 8px;">
              <div class="progress-bar {{ $colorClass }}" style="width: {{ $percentage }}%"></div>
            </div>
            <span class="text-muted" style="min-width: 30px;">{{ $count }}</span>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <form method="GET" action="{{ route('landlord.reviews.index') }}">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Property</label>
              <select class="form-select" name="property_id">
                <option value="">All Properties</option>
                @foreach($properties as $property)
                  <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                    {{ $property->title }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Rating</label>
              <select class="form-select" name="rating">
                <option value="">All Ratings</option>
                @for($i = 5; $i >= 1; $i--)
                  <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                    {{ $i }} {{ Str::plural('Star', $i) }}
                  </option>
                @endfor
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Sort By</label>
              <select class="form-select" name="sort_by">
                <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Newest First</option>
                <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                <option value="highest" {{ request('sort_by') == 'highest' ? 'selected' : '' }}>Highest Rating</option>
                <option value="lowest" {{ request('sort_by') == 'lowest' ? 'selected' : '' }}>Lowest Rating</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">&nbsp;</label>
              <button type="submit" class="btn btn-primary w-100">
                <i class='bx bx-filter'></i> Apply Filter
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Reviews List -->
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        @forelse($reviews as $review)
          <div class="border-bottom pb-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div class="d-flex align-items-center">
                <div class="avatar avatar-sm me-2">
                  @if($review->user->profile_picture)
                    <img src="{{ asset('storage/' . $review->user->profile_picture) }}" alt="Avatar" class="rounded-circle">
                  @else
                    <span class="avatar-initial rounded-circle bg-label-primary">
                      {{ substr($review->user->name ?? 'U', 0, 1) }}
                    </span>
                  @endif
                </div>
                <div>
                  <h6 class="mb-0">{{ $review->user->name ?? 'Anonymous' }}</h6>
                  <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                </div>
              </div>
              <div class="text-end">
                <div class="mb-1">
                  @for($i = 1; $i <= 5; $i++)
                    @if($i <= $review->rating)
                      <i class='bx bxs-star text-warning'></i>
                    @else
                      <i class='bx bx-star text-warning'></i>
                    @endif
                  @endfor
                </div>
                <small class="text-muted">{{ $review->property->title }}</small>
              </div>
            </div>
            
            @if($review->review_text)
              <p class="mb-2">{{ $review->review_text }}</p>
            @else
              <p class="mb-2 text-muted fst-italic">No review text provided</p>
            @endif

            @if($review->landlord_reply)
              <div class="ms-4 mt-2 p-2 bg-light rounded">
                <small class="text-muted d-block mb-1">
                  <i class='bx bx-reply'></i> Your Reply • {{ $review->replied_at ? $review->replied_at->diffForHumans() : '' }}
                </small>
                <p class="mb-0">{{ $review->landlord_reply }}</p>
              </div>
            @else
              <button class="btn btn-sm btn-outline-primary" 
                      onclick="showReplyModal({{ $review->rating_id }})">
                <i class='bx bx-reply'></i> Reply
              </button>
            @endif
          </div>
        @empty
          <!-- Empty State -->
          <div class="text-center py-5">
            <i class='bx bx-star display-1 text-muted mb-3'></i>
            <h5 class="text-muted">No Reviews Yet</h5>
            <p class="text-muted">Reviews from your tenants will appear here</p>
          </div>
        @endforelse

        <!-- Pagination -->
        @if($reviews->hasPages())
          <div class="mt-4 d-flex justify-content-center">
            {{ $reviews->links('pagination::bootstrap-5') }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="replyForm" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Reply to Review</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Your Reply</label>
            <textarea class="form-control" name="reply" rows="4" 
                      placeholder="Write your response to this review..." 
                      required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class='bx bx-send'></i> Send Reply
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ✅ Custom Pagination Styling -->
<style>
.pagination {
  justify-content: center;
  margin-top: 1rem;
}

.page-item .page-link {
  padding: 0.35rem 0.75rem;
  font-size: 0.875rem;
  border-radius: 0.375rem;
  color: #5a5a5a;
  border: 1px solid #ddd;
  transition: all 0.2s ease;
}

.page-item .page-link:hover {
  background-color: #7367f0;
  color: white;
  border-color: #7367f0;
}

.page-item.active .page-link {
  background-color: #7367f0;
  border-color: #7367f0;
  color: white;
}
</style>
@endsection

@section('page-script')
<script>
  function showReplyModal(reviewId) {
    const modal = new bootstrap.Modal(document.getElementById('replyModal'));
    const form = document.getElementById('replyForm');
    form.action = `/landlord/reviews/${reviewId}/reply`;
    modal.show();
  }

  // Auto-dismiss alerts
  setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    });
  }, 5000);
</script>
@endsection
