@extends('layouts/contentNavbarLayout')

@section('title', 'Profile Settings')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-1">👤 Profile Settings</h4>
        <p class="mb-0 text-muted">Manage your account information and preferences</p>
      </div>
    </div>
  </div>

  <!-- Profile Information -->
  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-body text-center">
        <div class="mb-3">
          <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Profile" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
          <button class="btn btn-sm btn-primary">
            <i class='bx bx-upload'></i> Change Photo
          </button>
        </div>
        <h5 class="mb-1">{{ Auth::user()->first_name ?? Auth::user()->name }}</h5>
        <p class="text-muted mb-3">Landlord</p>
        <div class="d-flex justify-content-center gap-2">
          <span class="badge bg-label-success">
            <i class='bx bx-check-circle'></i> Verified
          </span>
          <span class="badge bg-label-primary">
            <i class='bx bx-crown'></i> Premium
          </span>
        </div>
      </div>
    </div>

    <div class="card mt-4">
      <div class="card-body">
        <h6 class="mb-3">Account Statistics</h6>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Member Since</span>
          <span class="fw-bold">{{ Auth::user()->created_at->format('M Y') }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Total Properties</span>
          <span class="fw-bold">0</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Total Bookings</span>
          <span class="fw-bold">0</span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="text-muted">Average Rating</span>
          <span class="fw-bold">0.0 ⭐</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Profile Forms -->
  <div class="col-lg-8">
    <!-- Personal Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Personal Information</h5>
      </div>
      <div class="card-body">
        <form>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">First Name *</label>
              <input type="text" class="form-control" value="{{ Auth::user()->first_name ?? '' }}" placeholder="First Name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Name *</label>
              <input type="text" class="form-control" value="{{ Auth::user()->last_name ?? '' }}" placeholder="Last Name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email *</label>
              <input type="email" class="form-control" value="{{ Auth::user()->email }}" placeholder="Email">
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone Number *</label>
              <input type="tel" class="form-control" placeholder="+63 XXX XXX XXXX">
            </div>
            <div class="col-md-12">
              <label class="form-label">Address</label>
              <textarea class="form-control" rows="2" placeholder="Your complete address"></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary">
                <i class='bx bx-save'></i> Save Changes
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Business Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Business Information</h5>
      </div>
      <div class="card-body">
        <form>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Company Name</label>
              <input type="text" class="form-control" placeholder="Company Name (Optional)">
            </div>
            <div class="col-md-6">
              <label class="form-label">Business Registration No.</label>
              <input type="text" class="form-control" placeholder="DTI/SEC Number (Optional)">
            </div>
            <div class="col-md-12">
              <label class="form-label">Business Address</label>
              <textarea class="form-control" rows="2" placeholder="Business address if different from personal"></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary">
                <i class='bx bx-save'></i> Save Changes
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Change Password -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Change Password</h5>
      </div>
      <div class="card-body">
        <form>
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label">Current Password *</label>
              <input type="password" class="form-control" placeholder="Enter current password">
            </div>
            <div class="col-md-6">
              <label class="form-label">New Password *</label>
              <input type="password" class="form-control" placeholder="Enter new password">
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm New Password *</label>
              <input type="password" class="form-control" placeholder="Confirm new password">
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary">
                <i class='bx bx-lock'></i> Update Password
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Notification Preferences -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Notification Preferences</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="emailNotif" checked>
            <label class="form-check-label" for="emailNotif">
              Email Notifications
            </label>
          </div>
          <small class="text-muted">Receive notifications about new bookings via email</small>
        </div>

        <div class="mb-3">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="bookingNotif" checked>
            <label class="form-check-label" for="bookingNotif">
              Booking Alerts
            </label>
          </div>
          <small class="text-muted">Get notified when you receive new booking requests</small>
        </div>

        <div class="mb-3">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="reviewNotif" checked>
            <label class="form-check-label" for="reviewNotif">
              Review Notifications
            </label>
          </div>
          <small class="text-muted">Receive alerts when tenants leave reviews</small>
        </div>

        <div class="mb-3">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="marketingNotif">
            <label class="form-check-label" for="marketingNotif">
              Marketing Communications
            </label>
          </div>
          <small class="text-muted">Receive updates about new features and promotions</small>
        </div>

        <button type="submit" class="btn btn-primary">
          <i class='bx bx-save'></i> Save Preferences
        </button>
      </div>
    </div>

    <!-- Danger Zone -->
    <div class="card border-danger">
      <div class="card-header bg-label-danger">
        <h5 class="mb-0 text-danger">Danger Zone</h5>
      </div>
      <div class="card-body">
        <p class="mb-3">Once you delete your account, there is no going back. Please be certain.</p>
        <button class="btn btn-danger">
          <i class='bx bx-trash'></i> Delete Account
        </button>
      </div>
    </div>
  </div>
</div>
@endsection