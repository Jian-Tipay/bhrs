@php
  $width = $width ?? '120'; // Adjust size as needed
@endphp

<img 
  src="{{ asset('assets/img/slsu_logo.png') }}" 
  alt="SLSU StaySmart Logo" 
  width="{{ $width }}" 
  class="rounded-circle shadow-sm"
/>
