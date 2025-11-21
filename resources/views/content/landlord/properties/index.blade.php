@extends('layouts/contentNavbarLayout')

@section('title', 'My Properties')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">🏘️ My Properties</h4>
          <p class="mb-0 text-muted">Manage and view all your listed properties</p>
        </div>
        <a href="{{ route('landlord.properties.create') }}" class="btn btn-primary">
          <i class='bx bx-plus'></i> Add New Property
        </a>
      </div>
    </div>
  </div>

  <!-- Success/Error Messages -->
  @if(session('success'))
    <div class="col-12 mb-3">
      <div class="alert alert-success alert-dismissible" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  @endif

  @if(session('error'))
    <div class="col-12 mb-3">
      <div class="alert alert-danger alert-dismissible" role="alert">
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  @endif

  <!-- Properties Grid -->
  <div class="col-12">
    <div class="row">
      @forelse($properties as $property)
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <div class="position-relative">
              @if($property->image && file_exists(public_path('assets/img/boarding/' . $property->image)))
                <img src="{{ asset('assets/img/boarding/' . $property->image) }}" 
                     class="card-img-top" 
                     alt="{{ $property->title }}"
                     style="height: 200px; object-fit: cover;">
              @else
                <img src="{{ asset('assets/img/boarding/default.jpg') }}" 
                     class="card-img-top" 
                     alt="Default"
                     style="height: 200px; object-fit: cover;"
                     onerror="this.src='{{ asset('assets/img/default-placeholder.png') }}'">
              @endif
              
              <!-- Status Badge -->
              <span class="position-absolute top-0 end-0 m-2">
                @if($property->is_active && $property->available)
                  <span class="badge bg-success">Active</span>
                @elseif($property->is_active && !$property->available)
                  <span class="badge bg-warning">Fully Booked</span>
                @else
                  <span class="badge bg-secondary">Inactive</span>
                @endif
              </span>
            </div>
            
            <div class="card-body">
              <h5 class="card-title">{{ $property->title }}</h5>
              
              <p class="card-text text-muted mb-2">
                <i class='bx bx-map'></i> {{ Str::limit($property->address, 40) }}
              </p>
              
              <div class="mb-3">
                <span class="badge bg-label-primary me-1">
                  <i class='bx bx-money'></i> ₱{{ number_format($property->price, 2) }}/month
                </span>
                <span class="badge bg-label-info">
                  <i class='bx bx-bed'></i> {{ $property->available_slots ?? 0 }}/{{ $property->capacity ?? 0 }} slots
                </span>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  @php
                    $avgRating = $property->ratings()->avg('rating');
                    $ratingsCount = $property->ratings()->count();
                  @endphp
                  @if($avgRating > 0)
                    <span class="text-warning">
                      <i class='bx bxs-star'></i> {{ number_format($avgRating, 1) }}
                    </span>
                    <small class="text-muted">({{ $ratingsCount }})</small>
                  @else
                    <small class="text-muted">No ratings yet</small>
                  @endif
                </div>
                <div>
                  <span class="badge bg-label-secondary">
                    {{ $property->bookings()->count() }} bookings
                  </span>
                </div>
              </div>

              <!-- Amenities Preview -->
              @if($property->propertyAmenities && $property->propertyAmenities->count() > 0)
              <div class="mb-3">
                <small class="text-muted">Amenities:</small>
                <div class="d-flex flex-wrap gap-1 mt-1">
                  @foreach($property->propertyAmenities->take(3) as $propertyAmenity)
                    @if($propertyAmenity->amenity)
                      <span class="badge bg-label-info" style="font-size: 10px;">
                        {{ $propertyAmenity->amenity->amenity_name }}
                      </span>
                    @endif
                  @endforeach
                  @if($property->propertyAmenities->count() > 3)
                    <span class="badge bg-label-secondary" style="font-size: 10px;">
                      +{{ $property->propertyAmenities->count() - 3 }} more
                    </span>
                  @endif
                </div>
              </div>
              @endif

              <!-- Action Buttons -->
              <div class="d-flex gap-2">
                <a href="{{ route('landlord.properties.view', $property->id) }}" class="btn btn-sm btn-info flex-grow-1" title="View property details">
                  <i class='bx bx-show'></i> View
                </a>
                <a href="{{ route('landlord.properties.edit', $property->id) }}" class="btn btn-sm btn-primary" title="Edit property">
                  <i class='bx bx-edit'></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteProperty({{ $property->id }})" title="Delete property">
                  <i class='bx bx-trash'></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="card">
            <div class="card-body text-center py-5">
              <i class='bx bx-home display-1 text-muted mb-3'></i>
              <h5 class="text-muted mb-2">No Properties Yet</h5>
              <p class="text-muted mb-4">Start by adding your first property to attract tenants</p>
              <a href="{{ route('landlord.properties.create') }}" class="btn btn-primary">
                <i class='bx bx-plus'></i> Add Your First Property
              </a>
            </div>
          </div>
        </div>
      @endforelse
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Property</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this property? This action cannot be undone.</p>
        <p class="text-danger mb-0"><strong>Note:</strong> All property data including images will be permanently deleted.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteForm" method="POST" class="d-inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Delete Property</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
  function deleteProperty(propertyId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/landlord/properties/${propertyId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
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