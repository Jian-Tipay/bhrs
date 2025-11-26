<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme sticky-top shadow-sm" id="layout-navbar">
  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

    <!-- User Dropdown -->
    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="d-flex align-items-center">
            <div class="text-end me-2">
              <span class="fw-semibold d-block"><?php echo e(Auth::user()->name); ?></span>
              <small class="text-muted"><?php echo e(Auth::user()->studID); ?></small>
            </div>
            <div class="avatar avatar-online">
              <img src="<?php echo e(Auth::user()->profile_picture ? asset(Auth::user()->profile_picture) : asset('assets/img/avatars/1.png')); ?>" 
                  alt="avatar" style="width:40px; height:40px;" class="rounded-circle" />
            </div>
          </div>
        </a>

        <!-- Dropdown Menu -->
        <ul class="dropdown-menu dropdown-menu-end dropdown-animate">
          <!-- Profile Info -->
          <li>
            <a class="dropdown-item" href="<?php echo e(route('profile')); ?>">
              <div class="d-flex align-items-center">
                <div class="avatar avatar-online me-3">
                  <img src="<?php echo e(Auth::user()->profile_picture ? asset(Auth::user()->profile_picture) : asset('assets/img/avatars/1.png')); ?>" 
                      alt="avatar" style="width:40px; height:40px;" class="rounded-circle" />
                </div>
                <div>
                  <span class="fw-semibold d-block"><?php echo e(Auth::user()->name); ?></span>
                  <small class="text-muted">ID: <?php echo e(Auth::user()->studID); ?></small>
                </div>
              </div>
            </a>
          </li>

          <!-- My Profile Link -->
          <li>
            <a class="dropdown-item" href="<?php echo e(route('profile')); ?>">
              <i class="bx bx-user me-2"></i>
              <span class="align-middle">My Profile</span>
            </a>
          </li>

          <li><hr class="dropdown-divider"></li>

          <!-- Logout -->
          <li>
            <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class='bx bx-power-off me-2'></i>
              <span class="align-middle">Log Out</span>
            </a>
            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
              <?php echo csrf_field(); ?>
            </form>
          </li>
        </ul>
      </li>
    </ul>
    <!--/ User Dropdown -->

  </div>
</nav>
<?php /**PATH C:\Users\renzl\Downloads\myprojects\bhrs\resources\views/layouts/sections/navbar/navbar.blade.php ENDPATH**/ ?>