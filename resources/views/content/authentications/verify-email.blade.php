@extends('layouts/blankLayout')

@section('title', 'Verify Email')

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner">
      <!-- Verify Email Card -->
      <div class="card">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-4">
            <a href="{{url('/')}}" class="app-brand-link gap-2">
              <span class="app-brand-text demo text-body fw-bold">{{config('variables.templateName')}}</span>
            </a>
          </div>
          <!-- /Logo -->
          
          <h4 class="mb-2 text-center">Verify Your Email Address 📧</h4>
          <p class="text-center mb-4">
            Thanks for registering! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
          </p>

          @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          @if (session('status') == 'verification-link-sent')
            <div class="alert alert-info alert-dismissible" role="alert">
              A new verification link has been sent to your email address!
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <div class="text-center mb-4">
            <i class='bx bx-envelope bx-lg text-primary mb-3' style="font-size: 72px;"></i>
            <p class="text-muted">
              We've sent a verification email to:<br>
              <strong>{{ auth()->user()->email }}</strong>
            </p>
          </div>

          <!-- Resend Verification Email Form -->
          <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">
                <i class='bx bx-refresh me-1'></i> Resend Verification Email
              </button>
            </div>
          </form>

          <!-- Separate Buttons (outside form) -->
          <div class="d-grid gap-2 mt-2">
            <a href="{{ route('dashboard.user') }}" class="btn btn-outline-secondary">
              <i class='bx bx-arrow-back me-1'></i> Go to Dashboard
            </a>

            <!-- Logout Form -->
            <form method="GET" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-link text-muted w-100">
                <i class='bx bx-log-out me-1'></i> Logout
              </button>
            </form>
          </div>

          <div class="text-center mt-4">
            <p class="text-muted small">
              <i class='bx bx-info-circle'></i> 
              Didn't receive the email? Check your spam folder or click "Resend Verification Email" button above.
            </p>
          </div>
        </div>
      </div>
      <!-- /Verify Email Card -->
    </div>
  </div>
</div>
@endsection