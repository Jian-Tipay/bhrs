@extends('layouts/blankLayout')

@section('title', 'Account Pending Approval')

@section('page-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
<style>
  body {
    background: linear-gradient(135deg, #003366 0%, #0099cc 100%);
    font-family: 'Public Sans', sans-serif;
    min-height: 100vh;
  }w

  .pending-card {
    animation: popIn 0.6s ease-in-out;
    transition: all 0.3s ease;
  }

  .pending-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
  }

  .pending-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 30px;
    background: linear-gradient(135deg, #003366, #0099cc);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
  }

  .pending-icon i {
    font-size: 50px;
    color: white;
  }

  @keyframes pulse {
    0%, 100% {
      transform: scale(1);
      box-shadow: 0 0 0 0 rgba(0, 153, 204, 0.7);
    }
    50% {
      transform: scale(1.05);
      box-shadow: 0 0 0 20px rgba(0, 153, 204, 0);
    }
  }

  @keyframes popIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }

  .pending-title {
    font-size: 28px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 15px;
  }

  .pending-message {
    font-size: 16px;
    color: #718096;
    line-height: 1.6;
    margin-bottom: 30px;
  }

  .info-box {
    background: #e7f3ff;
    border-left: 4px solid #0099cc;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: left;
  }

  .info-box h5 {
    font-size: 15px;
    font-weight: 600;
    color: #003366;
    margin-bottom: 12px;
  }

  .info-box ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .info-box li {
    font-size: 14px;
    color: #4a5568;
    padding: 6px 0;
    padding-left: 28px;
    position: relative;
  }

  .info-box li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #48bb78;
    font-weight: bold;
    font-size: 16px;
  }

  .btn-back {
    display: inline-block;
    padding: 12px 30px;
    background: linear-gradient(135deg, #003366, #0099cc);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
  }

  .btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 153, 204, 0.3);
    color: white;
  }

  .contact-info {
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid #e2e8f0;
  }

  .contact-info p {
    font-size: 14px;
    color: #718096;
    margin-bottom: 8px;
  }

  .contact-info a {
    color: #0099cc;
    text-decoration: none;
    font-weight: 600;
  }

  .contact-info a:hover {
    text-decoration: underline;
  }

  .alert-success-custom {
    background: #d4edda;
    border-left: 4px solid #28a745;
    color: #155724;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    font-size: 14px;
  }
</style>
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">
      
      <div class="card pending-card">
        <div class="card-body text-center" style="padding: 3rem 2.5rem;">
          
          <!-- Success Alert -->
          @if(session('success'))
            <div class="alert-success-custom">
              <i class="bx bx-check-circle me-2"></i>
              <strong>{{ session('success') }}</strong>
            </div>
          @endif

          <!-- Pending Icon -->
          <div class="pending-icon">
            <i class="bx bx-time-five"></i>
          </div>

          <!-- Title -->
          <h1 class="pending-title">Account Pending Approval</h1>
          
          <!-- Message -->
          <p class="pending-message">
            Thank you for registering with <strong>SLSU StaySmart</strong>! 
            Your account has been created successfully and is currently under review.
          </p>

          <!-- Info Box -->
          <div class="info-box">
            <h5><i class="bx bx-info-circle me-2"></i>What happens next?</h5>
            <ul>
              <li>Our admin team will review your registration details</li>
              <li>You'll receive an email notification once approved</li>
              <li>The approval process typically takes 1-2 business days</li>
              <li>Check your email regularly for updates</li>
            </ul>
          </div>

          <!-- Action Button -->
          <a href="{{ url('/') }}" class="btn-back">
            <i class="bx bx-home-alt me-2"></i>Return to Login
          </a>

          <!-- Contact Info -->
          <div class="contact-info">
            <p><strong>Need help or have questions?</strong></p>
            <p>
              Contact us at: 
              <a href="mailto:support@slsustaysmart.edu.ph">support@slsustaysmart.edu.ph</a>
            </p>
          </div>

        </div>
      </div>

      <!-- Footer -->
      <div class="text-center mt-4">
        <p class="text-white small mb-0">
          © {{ date('Y') }} SLSU StaySmart. All rights reserved.
        </p>
      </div>

    </div>
  </div>
</div>
@endsection