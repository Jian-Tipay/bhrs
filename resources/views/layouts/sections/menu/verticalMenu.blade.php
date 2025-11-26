<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- App Brand & Toggle -->
  <div class="app-brand demo d-flex align-items-center justify-content-between px-3 py-2">
    <a href="{{ url('/') }}" class="app-brand-link d-flex align-items-center text-decoration-none">
      <span class="app-brand-logo demo me-2">
        <img src="{{ asset('assets/img/slsu_logo.png') }}" 
             alt="SLSU Logo" 
             width="38" 
             height="38" 
             style="border-radius: 8px; object-fit: cover;">
      </span>
      <span class="app-brand-text demo fw-bold text-uppercase" 
            style="font-size: 1rem; color: #003366; letter-spacing: 0.5px;">
        SLSU Staysmart
      </span>
    </a>

    <!-- Toggle button -->
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto" id="menu-toggle">
      <i class="mdi menu-toggle-icon d-xl-block align-middle mdi-20px"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <!-- Menu Items -->
  <ul class="menu-inner py-1">
    @foreach ($menuData[0]->menu as $menu)
      {{-- Menu Headers --}}
      @if (isset($menu->menuHeader))
        <li class="menu-header fw-medium mt-4">
          <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
        </li>
      @else
        {{-- Check if item requires verification --}}
        @php
          $requiresVerification = isset($menu->requiresVerification) && $menu->requiresVerification;
          $isUnverified = auth()->check() && auth()->user()->role === 'user' && !auth()->user()->hasVerifiedEmail();
          $isLocked = $isUnverified && $requiresVerification;
          
          $activeClass = null;
          $currentRouteName = Route::currentRouteName();

          if ($currentRouteName === $menu->slug) {
              $activeClass = 'active';
          } elseif (isset($menu->submenu)) {
            if (is_array($menu->slug)) {
              foreach($menu->slug as $slug){
                if (str_starts_with($currentRouteName, $slug)) {
                  $activeClass = 'active open';
                }
              }
            } else {
              if (str_starts_with($currentRouteName, $menu->slug)) {
                $activeClass = 'active open';
              }
            }
          }
        @endphp

        {{-- Main Menu Item --}}
        <li class="menu-item {{ $activeClass }} {{ $isLocked ? 'disabled' : '' }}">
          @if($isLocked)
            {{-- Locked menu item redirects to verification page --}}
            <a href="{{ route('verification.notice') }}" 
               class="menu-link" 
               title="Email verification required">
              @isset($menu->icon)
                <i class="{{ $menu->icon }}"></i>
              @endisset
              <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
              <span class="badge badge-center rounded-pill bg-warning ms-auto" style="width: 20px; height: 20px;">
                <i class='bx bx-lock-alt' style="font-size: 12px;"></i>
              </span>
            </a>
          @else
            {{-- Normal accessible menu item --}}
            <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}" 
               class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" 
               @if (isset($menu->target) && !empty($menu->target)) target="_blank" @endif>
              @isset($menu->icon)
                <i class="{{ $menu->icon }}"></i>
              @endisset
              <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
              @isset($menu->badge)
                <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
              @endisset
            </a>

            {{-- Submenu --}}
            @isset($menu->submenu)
              @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
            @endisset
          @endif
        </li>
      @endif
    @endforeach
  </ul>
</aside>

<style>
  /* Optional: Style for locked menu items */
  .menu-item.disabled .menu-link {
    opacity: 0.6;
    cursor: pointer;
  }
  .menu-item.disabled .menu-link:hover {
    background-color: rgba(255, 193, 7, 0.08);
  }
</style>