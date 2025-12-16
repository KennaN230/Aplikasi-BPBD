<div class="sidebar">
  {{-- Brand --}}
  <div class="brand">
    <div class="d-flex align-items-center gap-1">
      <img src="{{ asset('gambar/Logo_Kabupaten_Malang 1.png') }}" width="30" height="30" alt="logo">
      <img src="{{ asset('gambar/logoBPBD.png') }}" width="30" height="30" alt="logo">
      <div>
        <h6 class="mb-1">Informasi Kejadian Kab Malang</h6>
      </div>
    </div>
    <div class="line"></div>
  </div>

  {{-- Menu --}}
  <ul class="menu">
    <li>
      <a href="{{ route('dashboard') }}" 
         class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
        <i class="bi bi-house-door"></i>
        <span>Beranda</span>
      </a>
    </li>

    <li>
      <a href="{{ route('kejadian') }}" 
         class="{{ request()->routeIs('kejadian*') ? 'is-active' : '' }}">
        <i class="bi bi-exclamation-triangle"></i>
        <span>Kejadian</span>
      </a>
    </li>

    <li>
      <a href="{{ url('/gempa') }}" 
         class="{{ request()->is('gempa*') ? 'is-active' : '' }}">
        <i class="bi bi-activity"></i>
        <span>Gempa Bumi</span>
      </a>
    </li>

    <li>
      <a href="{{ route('aktivitas-gunung.index') }}" 
         class="{{ request()->routeIs('aktivitas-gunung.*') ? 'is-active' : '' }}">
        <i class="bi bi-triangle-fill me-2"></i>
        <span>Aktivitas Gunung Aktif</span>
      </a>
    </li>

    <li>
      <a href="{{ route('rain.index') }}" 
         class="{{ request()->routeIs('rain.*') ? 'is-active' : '' }}">
        <i class="bi bi-cloud-rain-heavy"></i>
        <span>Hari Hujan & Tanpa Hujan</span>
      </a>
    </li>

    <li>
      <a href="{{ url('/gelombang') }}" 
         class="{{ request()->is('gelombang*') ? 'is-active' : '' }}">
        <i class="bi bi-tsunami"></i>
        <span>Tinggi Gelombang</span>
      </a>
    </li>

    <li>
      <a href="{{ url('/titikpanas') }}" 
         class="{{ request()->is('titikpanas*') ? 'is-active' : '' }}">
        <i class="bi bi-thermometer-half"></i>
        <span>Titik Panas</span>
      </a>
    </li>

    <li>
      <a href="{{ url('/petugas') }}" 
         class="{{ request()->is('petugas*') ? 'is-active' : '' }}">
        <i class="bi bi-people"></i>
        <span>Menu Master</span>
      </a>
    </li>

    <li>
      <a href="#" class="{{ request()->is('destana*') ? 'is-active' : '' }}">
        <i class="bi bi-building"></i>
        <span>DESTANA Kab.Malang</span>
      </a>
    </li>

    <li>
      <a href="#" class="{{ request()->is('spab*') ? 'is-active' : '' }}">
        <i class="bi bi-shield"></i>
        <span>SPAB Kab.Malang</span>
      </a>
    </li>
  </ul>
</div>
