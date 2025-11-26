@extends('layouts/contentNavbarLayout')

@section('title', 'Review Management')

@section('content')
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
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
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
            <h4 class="mb-0">{{ number_format($reviews->total()) }}</h4>
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
            <h4 class="mb-0">{{ number_format($reviews->avg('rating') ?? 0, 1) }} <small class="text-warning">★</small></h4>
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
            <h4 class="mb-0 text-success">{{ number_format($reviews->where('rating', 5.0)->count()) }}</h4>
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
            <h4 class="mb-0 text-info">{{ number_format($reviews->filter(function($r) { return $r->created_at->isCurrentMonth(); })->count()) }}</h4>
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
        <form method="GET" action="{{ route('admin.reviews.index') }}">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Rating</label>
              <select name="rating" class="form-select">
                <option value="">All Ratings</option>
                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Date From</label>
              <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Date To</label>
              <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">&nbsp;</label>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class='bx bx-search'></i> Filter
                </button>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">
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
        <span class="badge bg-label-primary">{{ $reviews->total() }} Total</span>
      </div>
      <div class="card-body">
        @if($reviews->count() > 0)
          <div class="row">
            @foreach($reviews as $review)
              <div class="col-12 mb-3">
                <div class="card border">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-8">
                        <!-- User Info -->
                        <div class="d-flex align-items-start mb-3">
                          <div class="avatar avatar-md me-3">
                            @if($review->user->profile_picture)
                              <img src="{{ asset('storage/' . $review->user->profile_picture) }}" alt="{{ $review->user->first_name }}" class="rounded-circle">
                            @else
                              <span class="avatar-initial rounded-circle bg-label-primary">
                                {{ strtoupper(substr($review->user->first_name, 0, 1)) }}
                              </span>
                            @endif
                          </div>
                          <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                              <div>
                                <h6 class="mb-0">{{ $review->user->first_name }} {{ $review->user->last_name }}</h6>
                                <small class="text-muted">{{ $review->user->email }}</small>
                              </div>
                              <div class="text-end">
                                <!-- Rating Stars -->
                                <div class="mb-1">
                                  @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                      <i class='bx bxs-star text-warning'></i>
                                    @else
                                      <i class='bx bx-star text-muted'></i>
                                    @endif
                                  @endfor
                                  <strong class="ms-1">{{ number_format($review->rating, 1) }}</strong>
                                </div>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                              </div>
                            </div>
                            
                            <!-- Review Text -->
                            @if($review->review_text)
                              <p class="mt-3 mb-2">{{ $review->review_text }}</p>
                            @else
                              <p class="mt-3 mb-2 text-muted fst-italic">No written review</p>
                            @endif
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <!-- Property Info -->
                        <div class="border-start ps-3">
                          <small class="text-muted d-block mb-2">PROPERTY REVIEWED</small>
                          <div class="d-flex align-items-center mb-2">
                            @if($review->property->images && $review->property->images->count() > 0)
                              <img src="{{ asset('storage/' . $review->property->images->first()->image_path) }}" 
                                   alt="{{ $review->property->title }}" 
                                   class="rounded me-2" 
                                   style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                              <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-info">
                                  <i class='bx bx-building-house'></i>
                                </span>
                              </div>
                            @endif
                            <div>
                              <strong class="d-block">{{ Str::limit($review->property->title, 30) }}</strong>
                              <small class="text-muted">₱{{ number_format($review->property->price, 2) }}/mo</small>
                            </div>
                          </div>
                          
                          <!-- Action Buttons -->
                          <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('properties.view', $review->property->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                              <i class='bx bx-show'></i> View Property
                            </a>
                            <form action="{{ route('admin.reviews.delete', $review->rating_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?')">
                              @csrf
                              @method('DELETE')
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
            @endforeach
          </div>

          <!-- Pagination -->
        <!-- ✅ Fixed Pagination Styling -->
<div class="mt-4 d-flex justify-content-center">
  <ul class="pagination pagination-sm mb-0 shadow-sm rounded">
    {{-- Previous Page Link --}}
    @if ($reviews->onFirstPage())
      <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
    @else
      <li class="page-item"><a class="page-link" href="{{ $reviews->previousPageUrl() }}" rel="prev">&laquo;</a></li>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($reviews->links()->elements[0] ?? [] as $page => $url)
      @if ($page == $reviews->currentPage())
        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
      @else
        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
      @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($reviews->hasMorePages())
      <li class="page-item"><a class="page-link" href="{{ $reviews->nextPageUrl() }}" rel="next">&raquo;</a></li>
    @else
      <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
    @endif
  </ul>
</div>

        @else
          <div class="text-center py-5">
            <i class='bx bx-message-square-x bx-lg text-muted'></i>
            <p class="text-muted mt-3">No reviews found</p>
            @if(request()->hasAny(['rating', 'date_from', 'date_to']))
              <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-outline-primary">
                Clear Filters
              </a>
            @endif
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@if(session('success'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      alert('{{ session('success') }}');
    });
  </script>
@endif

@if(session('error'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      alert('{{ session('error') }}');
    });
  </script>
@endif
@endsection