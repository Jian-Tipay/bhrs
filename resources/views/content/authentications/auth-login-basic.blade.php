@extends('layouts/blankLayout')

@section('title', 'Login - SLSU StaySmart')

@section('page-style')
<!-- Page CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
<style>
  /* Split Layout Styling */
  .auth-split {
    display: flex;
    min-height: 100vh;
    overflow: hidden;
  }

  /* Left Side (Image and Welcome) */
  .auth-left {
    flex: 1;
    background: url('{{ asset('assets/img/slsu_bg.jpg') }}') center center / cover no-repeat;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    animation: fadeInLeft 1.2s ease-in-out;
  }

  /* Overlay for text contrast */
  .auth-left::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 20, 50, 0.65);
    z-index: 0;
  }

  .auth-left-content {
    position: relative;
    z-index: 1;
    text-align: center;
    padding: 2rem;
    animation: slideUp 1.2s ease-in-out;
  }

  .auth-left-content img {
    width: 120px;
    height: auto;
    margin-bottom: 1.5rem;
    animation: fadeIn 1.8s ease-in-out;
  }

  .auth-left-content h3 {
    font-weight: 700;
    font-size: 1.75rem;
    animation: fadeInUp 1.5s ease;
  }

  .auth-left-content p {
    font-size: 1rem;
    color: #dbe4ff;
    animation: fadeInUp 2s ease;
  }

  /* Right Side (Form) */
  .auth-right {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    padding: 2rem;
    animation: fadeInRight 1.2s ease-in-out;
  }

  .card {
    animation: popIn 0.9s ease-in-out;
    transition: all 0.3s ease;
  }

  .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
  }

  /* Responsive */
  @media (max-width: 992px) {
    .auth-left {
      display: none;
    }
  }

  /* Animations */
  @keyframes fadeInLeft {
    from { opacity: 0; transform: translateX(-50px); }
    to { opacity: 1; transform: translateX(0); }
  }

  @keyframes fadeInRight {
    from { opacity: 0; transform: translateX(50px); }
    to { opacity: 1; transform: translateX(0); }
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes slideUp {
    from { opacity: 0; transform: translateY(60px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes popIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }
</style>
@endsection

@section('content')
<div class="auth-split">
  
  <!-- LEFT SIDE (SLSU Image + Welcome Text) -->
  <div class="auth-left">
    <div class="auth-left-content">
      <img src="{{ asset('assets/img/slsu_logo.png') }}" alt="SLSU Logo">
      <h3>Welcome to SLSU StaySmart</h3>
      <p>Your smart companion in finding the best boarding houses around SLSU.</p>
    </div>
  </div>

  <!-- RIGHT SIDE (Login Form) -->
  <div class="auth-right">
    <div class="w-100" style="max-width: 420px;">
      <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body">

          <div class="text-center mb-3">
            <h4 class="fw-bold text-primary">Sign in to StaySmart</h4>
          </div>

          {{-- Display incorrect credentials message --}}
          @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ $errors->first() }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <!-- Loading Overlay -->
          <div id="loading-overlay" style="
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>

          <!-- Login Form -->
          <form id="formAuthentication" class="mb-3" action="{{ route('auth.login.process') }}" method="POST">
            @csrf
            <div class="mb-4">
              <label for="studId" class="form-label fw-semibold">Student Number</label>
              <input type="text" class="form-control rounded-pill" id="studId" name="studId"
                     placeholder="Enter your SLSU Student Number" autofocus required value="{{ old('studId') }}">
            </div>

            <div class="mb-4 form-password-toggle">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label fw-semibold" for="password">Password</label>
                <a href="#"><small>Forgot Password?</small></a>
              </div>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control rounded-start-pill" 
                       name="password" placeholder="••••••••••" required />
                <span class="input-group-text cursor-pointer rounded-end-pill">
                  <i class="bx bx-hide"></i>
                </span>
              </div>
            </div>

            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" id="remember" name="remember">
              <label class="form-check-label" for="remember">Remember Me</label>
            </div>

            <div class="d-grid">
              <button class="btn btn-primary rounded-pill fw-semibold" type="submit">
                Sign In
              </button>
            </div>
          </form>

          <p class="text-center mt-3">
            <span>New to StaySmart?</span>
            <a href="{{ url('auth/register-basic') }}">
              <span class="fw-semibold text-primary">Create an Account</span>
            </a>
          </p>

          <p class="text-center text-muted small mt-4">
            © {{ date('Y') }} SLSU StaySmart — Smart Boarding House Recommender for SLSU Students
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
  // Show loading overlay on form submit
  document.getElementById('formAuthentication').addEventListener('submit', function() {
      document.getElementById('loading-overlay').style.display = 'flex';
  });
</script>
@endsection
