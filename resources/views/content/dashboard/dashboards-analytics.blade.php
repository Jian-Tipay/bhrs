@extends('layouts/contentNavbarLayout')

@section('title', 'SLSU Student Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
  .alert-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .alert-warning {
    border-left: 4px solid #ff9f43;
  }
  .alert-success {
    border-left: 4px solid #28c76f;
  }
  .alert-info {
    border-left: 4px solid #00cfe8;
  }
  .search-container {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 40px;
    padding: 8px 20px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: 0.3s ease;
  }
  .search-container input, .search-container select {
    border: none;
    outline: none;
    font-size: 15px;
    padding: 10px;
    background: transparent;
    flex: 1;
    min-width: 150px;
  }
  .search-container button {
    background: var(--bs-primary);
    border: none;
    border-radius: 50%;
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: 0.3s;
  }
  .search-container button:hover {
    background: #004b9a;
  }
  .search-container button i {
    color: #fff;
    font-size: 18px;
  }
  
  .property-card {
    transition: transform 0.3s, box-shadow 0.3s;
    height: 100%;
  }
  .property-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  }
  .match-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    z-index: 10;
  }
  .recommendation-section {
    margin-bottom: 30px;
  }
  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  #map {
    height: 400px;
    width: 100%;
  }
  .property-img {
    height: 200px;
    object-fit: cover;
    width: 100%;
  }
  .budget-display-edit {
    font-size: 24px;
    font-weight: 600;
    color: #696cff;
  }
  .loading-overlay {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.9);
    z-index: 999;
    align-items: center;
    justify-content: center;
  }
  .loading-overlay.active {
    display: flex;
  }
  
</style>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection

@section('page-script')
<script>
  document.addEventListener("DOMContentLoaded", () => {
    // Map modal logic
    const mapModal = new bootstrap.Modal(document.getElementById('mapModal'));
    let mapInstance = null;

    document.querySelectorAll(".view-map").forEach(button => {
      button.addEventListener("click", function () {
        let lat = this.dataset.lat;
        let lng = this.dataset.lng;
        let title = this.dataset.title;

        document.getElementById("mapTitle").textContent = title;
        mapModal.show();

        setTimeout(() => {
          if (mapInstance !== null) {
            mapInstance.remove();
          }

          mapInstance = L.map('map').setView([lat, lng], 17);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
          }).addTo(mapInstance);
          L.marker([lat, lng]).addTo(mapInstance).bindPopup(title).openPopup();
        }, 400);
      });
    });

    // JavaScript Search Functionality
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.querySelector('input[name="query"]');
    const priceRangeSelect = document.querySelector('select[name="price_range"]');
    const distanceSelect = document.querySelector('select[name="distance"]');
    
    function filterProperties() {
      const query = searchInput.value.toLowerCase().trim();
      const priceRange = priceRangeSelect.value;
      const distance = distanceSelect.value;
      
      // Get all property cards
      const allPropertyCards = document.querySelectorAll('.all-properties-section .property-card');
      let visibleCount = 0;
      
      allPropertyCards.forEach(card => {
        const title = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
        const address = card.querySelector('.card-text')?.textContent.toLowerCase() || '';
        const priceText = card.querySelector('.card-text')?.textContent || '';
        const distanceText = card.querySelector('.card-text')?.textContent || '';
        
        // Extract price
        const priceMatch = priceText.match(/₱([\d,]+)/);
        const price = priceMatch ? parseInt(priceMatch[1].replace(/,/g, '')) : 0;
        
        // Extract distance
        const distanceMatch = distanceText.match(/([\d.]+)\s*km from campus/);
        const propertyDistance = distanceMatch ? parseFloat(distanceMatch[1]) * 1000 : 0; // Convert to meters
        
        // Check query match
        const matchesQuery = !query || title.includes(query) || address.includes(query);
        
        // Check price range
        let matchesPrice = true;
        if (priceRange) {
          if (priceRange === '0-2000') {
            matchesPrice = price >= 0 && price <= 2000;
          } else if (priceRange === '2001-4000') {
            matchesPrice = price >= 2001 && price <= 4000;
          } else if (priceRange === '4001-6000') {
            matchesPrice = price >= 4001 && price <= 6000;
          } else if (priceRange === '6001+') {
            matchesPrice = price >= 6001;
          }
        }
        
        // Check distance
        let matchesDistance = true;
        if (distance) {
          if (distance === '0-500') {
            matchesDistance = propertyDistance >= 0 && propertyDistance <= 500;
          } else if (distance === '501-1000') {
            matchesDistance = propertyDistance >= 501 && propertyDistance <= 1000;
          } else if (distance === '1001-2000') {
            matchesDistance = propertyDistance >= 1001 && propertyDistance <= 2000;
          } else if (distance === '2001+') {
            matchesDistance = propertyDistance >= 2001;
          }
        }
        
        // Show or hide card
        const parentCol = card.closest('.col-md-4');
        if (matchesQuery && matchesPrice && matchesDistance) {
          parentCol.style.display = '';
          visibleCount++;
        } else {
          parentCol.style.display = 'none';
        }
      });
      
      // Update property count
      const propertyCount = document.querySelector('.property-count');
      if (propertyCount) {
        propertyCount.textContent = `${visibleCount} properties available`;
      }
      
      // Show no results message
      const noResultsMsg = document.querySelector('.no-results-message');
      if (visibleCount === 0) {
        if (!noResultsMsg) {
          const msg = document.createElement('div');
          msg.className = 'col-12 no-results-message';
          msg.innerHTML = '<p class="text-muted text-center py-4">No properties match your search criteria.</p>';
          document.querySelector('.all-properties-section .row').appendChild(msg);
        }
      } else {
        if (noResultsMsg) {
          noResultsMsg.remove();
        }
      }
    }
    
    // Prevent form submission and use JS filtering instead
    searchForm.addEventListener('submit', function(e) {
      e.preventDefault();
      filterProperties();
    });
    
    // Real-time filtering
    searchInput.addEventListener('input', filterProperties);
    priceRangeSelect.addEventListener('change', filterProperties);
    distanceSelect.addEventListener('change', filterProperties);

    // Track clicks
    window.trackClick = function(propertyId) {
      fetch(`/recommendations/track-click/${propertyId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      });
    };

    // Update budget display in edit modal
    const budgetMinEdit = document.getElementById('edit_budget_min');
    const budgetMaxEdit = document.getElementById('edit_budget_max');
    const budgetDisplayEdit = document.getElementById('budgetDisplayEdit');

    if (budgetMinEdit && budgetMaxEdit && budgetDisplayEdit) {
      function updateBudgetDisplayEdit() {
        const min = parseInt(budgetMinEdit.value) || 0;
        const max = parseInt(budgetMaxEdit.value) || 0;
        budgetDisplayEdit.textContent = `₱${min.toLocaleString()} - ₱${max.toLocaleString()}`;
      }

      budgetMinEdit.addEventListener('input', updateBudgetDisplayEdit);
      budgetMaxEdit.addEventListener('input', updateBudgetDisplayEdit);
      updateBudgetDisplayEdit();
    }

    // Update distance display in edit modal
    const distanceInputEdit = document.getElementById('edit_preferred_distance');
    const distanceDisplayEdit = document.getElementById('distanceDisplayEdit');

    if (distanceInputEdit && distanceDisplayEdit) {
      function updateDistanceDisplayEdit() {
        const distance = parseFloat(distanceInputEdit.value) || 0;
        distanceDisplayEdit.textContent = `${distance} km`;
      }

      distanceInputEdit.addEventListener('input', updateDistanceDisplayEdit);
      updateDistanceDisplayEdit();
    }

    // Auto-open edit modal if redirected from edit route
    @if(session('show_edit_modal'))
      const editModal = new bootstrap.Modal(document.getElementById('editPreferencesModal'));
      editModal.show();
    @endif
  });
</script>
@endsection

@section('content')
<div class="row">
   @if(!auth()->user()->hasVerifiedEmail())
  <div class="col-12 mb-4">
    <div class="alert alert-warning alert-dismissible d-flex align-items-center" role="alert">
      <span class="alert-icon rounded-circle bg-warning me-3">
        <i class='bx bx-envelope bx-sm text-white'></i>
      </span>
      <div class="flex-grow-1">
        <h5 class="alert-heading mb-1">
          <i class='bx bx-error-circle'></i> Email Verification Required
        </h5>
        <p class="mb-2">
          Your email address <strong>{{ auth()->user()->email }}</strong> is not verified yet. 
          Please verify your email to ensure account security.
        </p>
        <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('verification.notice') }}" class="btn btn-sm btn-warning">
            <i class='bx bx-check-circle'></i> Verify Email Now
          </a>
          <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-warning">
              <i class='bx bx-refresh'></i> Resend Verification Email
            </button>
          </form>
        </div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  @endif

  @if(session('success'))
  <div class="col-12 mb-4">
    <div class="alert alert-success alert-dismissible d-flex align-items-center" role="alert">
      <span class="alert-icon rounded-circle bg-success me-3">
        <i class='bx bx-check-circle bx-sm text-white'></i>
      </span>
      <div class="flex-grow-1">
        <h5 class="alert-heading mb-1">Success!</h5>
        <p class="mb-0">{{ session('success') }}</p>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  @endif

  @if(session('status') == 'verification-link-sent')
  <div class="col-12 mb-4">
    <div class="alert alert-info alert-dismissible d-flex align-items-center" role="alert">
      <span class="alert-icon rounded-circle bg-info me-3">
        <i class='bx bx-envelope bx-sm text-white'></i>
      </span>
      <div class="flex-grow-1">
        <h5 class="alert-heading mb-1">Verification Email Sent!</h5>
        <p class="mb-0">A new verification link has been sent to your email address. Please check your inbox (and spam folder).</p>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  @endif
  <!-- Welcome Banner -->
  <div class="col-12 mb-4">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <h4 class="text-white mb-1">Welcome back, {{ Auth::user()->first_name }}! 👋</h4>
        <p class="mb-0">Find your perfect boarding house near SLSU Main Campus</p>
      </div>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Bookings</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="mb-0 me-2">{{ $bookingsCount ?? 0 }}</h4>
            </div>
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

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">My Ratings</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="mb-0 me-2">{{ $ratingsCount ?? 0 }}</h4>
            </div>
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

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Favorites</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="mb-0 me-2">{{ $favoritesCount ?? 0 }}</h4>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-danger rounded p-2">
              <i class='bx bx-heart bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Budget</p>
            <div class="d-flex align-items-end mb-2">
              <h6 class="mb-0 me-2">₱{{ number_format($preference->budget_min ?? 0) }} - ₱{{ number_format($preference->budget_max ?? 0) }}</h6>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-success rounded p-2">
              <i class='bx bx-wallet bx-sm'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Search Bar -->
  <div class="col-lg-12 mb-4">
    <form id="searchForm" class="search-container">
      <input type="text" name="query" placeholder="Search by name or location" value="{{ request('query') }}">

      <select name="price_range">
        <option value="">Price Range</option>
        <option value="0-2000" {{ request('price_range') == '0-2000' ? 'selected' : '' }}>₱0 - ₱2,000</option>
        <option value="2001-4000" {{ request('price_range') == '2001-4000' ? 'selected' : '' }}>₱2,001 - ₱4,000</option>
        <option value="4001-6000" {{ request('price_range') == '4001-6000' ? 'selected' : '' }}>₱4,001 - ₱6,000</option>
        <option value="6001+" {{ request('price_range') == '6001+' ? 'selected' : '' }}>₱6,001+</option>
      </select>

      <select name="distance">
        <option value="">Distance</option>
        <option value="0-500" {{ request('distance') == '0-500' ? 'selected' : '' }}>Within 500m</option>
        <option value="501-1000" {{ request('distance') == '501-1000' ? 'selected' : '' }}>500m - 1km</option>
        <option value="1001-2000" {{ request('distance') == '1001-2000' ? 'selected' : '' }}>1km - 2km</option>
        <option value="2001+" {{ request('distance') == '2001+' ? 'selected' : '' }}>More than 2km</option>
      </select>

      <button type="submit"><i class="bx bx-search"></i></button>
    </form>
  </div>

  <!-- Section 1: All Properties (MOVED TO TOP) -->
  <div class="col-12 recommendation-section all-properties-section">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">🏘️ All Accredited Boarding Houses</h5>
        <small class="text-muted property-count">{{ count($allProperties ?? []) }} properties available</small>
      </div>
      <div class="card-body">
        <div class="row">
          @forelse($allProperties as $property)
            <div class="col-md-4 mb-3">
              <div class="card property-card">
                <img src="{{ $property['image'] ?? asset('assets/img/boarding/default.jpg') }}" 
                     class="property-img" alt="{{ $property['title'] }}">
                <div class="card-body">
                  <h5 class="card-title">{{ $property['title'] }}</h5>
                  <p class="card-text">
                    <i class='bx bx-map'></i> {{ $property['address'] ?? 'N/A' }}<br>
                    <i class='bx bx-money'></i> ₱{{ number_format($property['price'], 2) }} / month<br>
                    @if(isset($property['distance_from_campus']))
                      <i class='bx bx-walk'></i> {{ number_format($property['distance_from_campus'], 2) }} km from campus
                    @endif
                  </p>
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-primary flex-fill view-map" 
                            data-lat="{{ $property['latitude'] }}" 
                            data-lng="{{ $property['longitude'] }}" 
                            data-title="{{ $property['title'] }}">
                      <i class='bx bx-map-pin'></i> View Map
                    </button>
                    <a href="{{ route('properties.view', $property['id']) }}" class="btn btn-sm btn-outline-primary">
                      Details
                    </a>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12">
              <p class="text-muted text-center py-4">No properties available.</p>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <!-- Section 2: Perfect Matches (Content-Based) -->
  @if(!empty($contentBasedRecs) && count($contentBasedRecs) > 0)
  <div class="col-12 recommendation-section">
    <div class="card">
      <div class="card-header section-header">
        <div>
          <h5 class="mb-1">🎯 Perfect Matches for You</h5>
          <small class="text-muted">Based on your preferences</small>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPreferencesModal">
          <i class='bx bx-edit'></i> Edit Preferences
        </button>
      </div>
      <div class="card-body">
        <div class="row">
          @foreach($contentBasedRecs as $property)
          <div class="col-md-4 mb-3">
            <div class="card property-card">
              <div class="position-relative">
                 @if($property->image && file_exists(public_path('assets/img/boarding/' . $property->image)))
            <img src="{{ asset('assets/img/boarding/' . $property->image) }}" 
                 class="property-img"
                 alt="{{ $property->title }}">
          @else
            <img src="{{ asset('assets/img/boarding/default.jpg') }}" 
                 class="property-img"
                 alt="Default"
                 onerror="this.src='{{ asset('assets/img/default-placeholder.png') }}'">
          @endif
                @if(isset($property->match_score))
                  <div class="match-badge">{{ round($property->match_score) }}% Match</div>
                @endif
              </div>
              <div class="card-body">
                <h5 class="card-title">{{ $property->title }}</h5>
                <p class="card-text">
                  <i class='bx bx-map'></i> {{ $property->distance_from_campus }} km from campus<br>
                  <i class='bx bx-money'></i> ₱{{ number_format($property->price, 2) }} / month
                </p>
                @if($property->ratings_avg_rating)
                  <div class="mb-2">
                    <span class="badge bg-warning">⭐ {{ number_format($property->ratings_avg_rating, 1) }}</span>
                    <small class="text-muted">({{ $property->ratings_count }} reviews)</small>
                  </div>
                @endif
                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-primary flex-fill view-map" 
                          data-lat="{{ $property->latitude }}" 
                          data-lng="{{ $property->longitude }}" 
                          data-title="{{ $property->title }}"
                          onclick="trackClick({{ $property->id }})">
                    <i class='bx bx-map-pin'></i> View Map
                  </button>
                  <a href="{{ route('properties.view', $property->id) }}" class="btn btn-sm btn-outline-primary">
                    Details
                  </a>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Section 3: AI Recommendations (Collaborative Filtering) -->
  @if(!empty($cfRecommendations) && count($cfRecommendations) > 0)
  <div class="col-12 recommendation-section">
    <div class="card">
      <div class="card-header section-header">
        <div>
          <h5 class="mb-1">
            @if($cfServiceStatus['available'] && $ratingsCount >= 2)
              <i class='bx bx-brain'></i> AI-Powered Recommendations
            @else
              <i class='bx bx-trending-up'></i> Popular Properties
            @endif
          </h5>
          <small class="text-muted d-flex align-items-center gap-2">
            @if($cfServiceStatus['available'] && $ratingsCount >= 2)
              <span class="badge bg-success">
                <i class='bx bx-check-circle'></i> AI Active
              </span>
              Based on students with similar preferences
            @elseif($ratingsCount < 2)
              <span class="badge bg-warning">
                <i class='bx bx-info-circle'></i> Rate More Properties
              </span>
              Popular among students (rate 2+ properties for personalized AI recommendations)
            @else
              <span class="badge bg-secondary">
                <i class='bx bx-wifi-off'></i> AI Offline
              </span>
              Showing popular properties
            @endif
          </small>
        </div>
        <div class="d-flex gap-2">
          @if($cfServiceStatus['available'])
            <span class="badge bg-label-success" data-bs-toggle="tooltip" title="Collaborative Filtering AI is running">
              <i class='bx bx-server'></i> CF Service Online
            </span>
          @else
            <span class="badge bg-label-secondary" data-bs-toggle="tooltip" title="Using content-based filtering only">
              <i class='bx bx-server'></i> CF Service Offline
            </span>
          @endif
          <a href="{{ route('recommendations.refresh') }}" class="btn btn-sm btn-outline-primary">
            <i class='bx bx-refresh'></i> Refresh
          </a>
        </div>
      </div>
      <div class="card-body">
        @if($ratingsCount < 2 && $cfServiceStatus['available'])
        <div class="alert alert-info alert-dismissible fade show" role="alert">
          <div class="d-flex align-items-center">
            <i class='bx bx-info-circle me-2 fs-4'></i>
            <div>
              <strong>Get Personalized AI Recommendations!</strong><br>
              <small>Rate at least 2 properties to unlock collaborative filtering based on students like you. 
              You currently have <strong>{{ $ratingsCount }}</strong> rating(s).</small>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
          @foreach($cfRecommendations as $rec)
          <div class="col-md-4 mb-3">
            <div class="card property-card h-100">
              <div class="position-relative">
                <img src="{{ $rec['image'] ?? asset('assets/img/boarding/default.jpg') }}" 
                     class="property-img" alt="{{ $rec['title'] }}">
                
                @if($cfServiceStatus['available'] && $ratingsCount >= 2)
                  @if(isset($rec['predicted_rating']))
                    <div class="match-badge bg-success">
                      <i class='bx bx-brain'></i> {{ round($rec['predicted_rating'] * 20) }}% AI Match
                    </div>
                  @endif
                @else
                  @if(isset($rec['ratings_avg']) && $rec['ratings_avg'] > 0)
                    <div class="match-badge bg-primary">
                      <i class='bx bx-star'></i> {{ number_format($rec['ratings_avg'], 1) }}/5
                    </div>
                  @endif
                @endif
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{ $rec['title'] }}</h5>
                <p class="card-text flex-grow-1">
                  <i class='bx bx-map'></i> {{ $rec['address'] ?? 'N/A' }}<br>
                  <i class='bx bx-money'></i> ₱{{ number_format($rec['price'], 2) }} / month<br>
                  @if(isset($rec['distance_from_campus']))
                    <i class='bx bx-walk'></i> {{ number_format($rec['distance_from_campus'], 2) }} km from campus<br>
                  @endif
                  @if(isset($rec['ratings_count']) && $rec['ratings_count'] > 0)
                    <i class='bx bx-star'></i> {{ number_format($rec['ratings_avg'], 1) }} 
                    <small class="text-muted">({{ $rec['ratings_count'] }} reviews)</small>
                  @endif
                </p>
                <div class="d-flex gap-2 mt-2">
                  <button class="btn btn-sm btn-primary flex-fill view-map" 
                          data-lat="{{ $rec['latitude'] }}" 
                          data-lng="{{ $rec['longitude'] }}" 
                          data-title="{{ $rec['title'] }}"
                          onclick="trackClick({{ $rec['property_id'] }})">
                    <i class='bx bx-map-pin'></i> View Map
                  </button>
                  <a href="{{ route('properties.view', $rec['property_id']) }}" 
                     class="btn btn-sm btn-outline-primary"
                     onclick="trackClick({{ $rec['property_id'] }})">
                    Details
                  </a>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
  @else
    @if(!$cfServiceStatus['available'])
    <div class="col-12 recommendation-section">
      <div class="card">
        <div class="card-body text-center py-5">
          <i class='bx bx-server display-1 text-muted mb-3'></i>
          <h5 class="text-muted mb-2">AI Recommendation Service Offline</h5>
          <p class="text-muted mb-4">
            The collaborative filtering AI service is currently unavailable.<br>
            Don't worry! You can still browse properties and use content-based recommendations.
          </p>
          <div class="alert alert-info d-inline-block">
            <i class='bx bx-info-circle'></i>
            <small>
              <strong>For Administrators:</strong> Start the Python CF service by running:<br>
              <code>cd python_recommender && python run.py</code>
            </small>
          </div>
        </div>
      </div>
    </div>
    @endif
  @endif
</div>

<!-- Preference Modal -->
@include('content.dashboard.preference-modal')

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deletePreferencesModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Preferences?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete your preferences? This action cannot be undone.</p>
        <p class="text-muted small mb-0">You'll need to set up your preferences again to get personalized recommendations.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form action="{{ route('preferences.destroy') }}" method="POST" class="d-inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mapTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="map"></div>
      </div>
    </div>
  </div>
</div>
@endsection