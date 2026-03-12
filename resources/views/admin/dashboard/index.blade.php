@extends('template.admin-dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="dashboard-welcome">
    <!-- Hero Banner Area -->
    <div class="welcome-banner p-4 p-md-5 mb-4 text-white rounded-4 shadow-lg position-relative overflow-hidden" 
         style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
        
        <!-- Background Decoration Vector -->
        <div class="position-absolute top-0 end-0 h-100 w-50 d-none d-md-block" 
             style="background: url('{{ asset('img/bg/bg_1.png') }}') no-repeat center right; background-size: cover; opacity: 0.1; transform: scale(1.5);">
        </div>
        
        <div class="row align-items-center position-relative z-1">
            <div class="col-lg-8">
                <div class="d-inline-block px-3 py-1 bg-white bg-opacity-25 rounded-pill mb-3 animate__animated animate__fadeInDown">
                    <i class="fas fa-calendar-day me-2"></i> {{ date('l, d F Y') }}
                </div>
                
                <h1 class="display-5 fw-bold mb-3 animate__animated animate__fadeInLeft">
                    Selamat Datang, {{ Auth::user()->name ?? 'Admin Ferry Legend' }}! 👋
                </h1>
                
                <p class="lead mb-4 text-white fw-light animate__animated animate__fadeInUp animate__delay-1s" style="max-width: 600px;">
                    Hari ini adalah hari yang luar biasa untuk melayani penumpang kita dengan senyuman. Kelola seluruh daftar pemesanan, jadwal kapal tiket, dan pantau rute penyeberangan dengan mudah dari panel ini.
                </p>
                
                <div class="d-flex flex-wrap gap-3 animate__animated animate__fadeInUp animate__delay-2s">
                    <a href="{{ route('bookings.index') }}" class="btn btn-light btn-lg px-4 fw-bold shadow-sm d-flex align-items-center gap-2 text-primary" style="border-radius: 12px;">
                        <i class="feather feather-book-open"></i> Kelola Pemesanan
                    </a>
                    <a href="{{ route('schedule.index') }}" class="btn btn-outline-light btn-lg px-4 fw-bold d-flex align-items-center gap-2" style="border-radius: 12px;">
                        <i class="feather feather-calendar"></i> Atur Jadwal Kapal
                    </a>
                </div>
            </div>
            
            <!-- Illustration / Icon on Right Side -->
            <div class="col-lg-4 d-none d-lg-block text-center animate__animated animate__zoomIn animate__delay-1s">
                <i class="icofont-ship fa-10x" style="font-size: 150px; color: rgba(255,255,255,0.7); transform: rotate(-5deg);"></i>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="row g-4 mt-2">
        <h5 class="mb-1 fw-bold text-gray-800 animate__animated animate__fadeIn">🚀 Akses Cepat (Quick Actions)</h5>
        
        <!-- Action 1 -->
        <div class="col-md-6 col-xl-3 animate__animated animate__fadeInUp animate__delay-1s">
            <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                <div class="card-body text-center p-4">
                    <div class="icon-circle mb-3 mx-auto" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: #e0f2fe; color: #0284c7;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>
                    </div>
                    <h5 class="fw-bold mb-2">Master Rute</h5>
                    <p class="text-muted small mb-3">Tambahkan atau sesuaikan rute perjalanan baru untuk penyeberangan.</p>
                    <a href="{{ route('route.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">Buka Rute &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Action 2 -->
        <div class="col-md-6 col-xl-3 animate__animated animate__fadeInUp animate__delay-2s">
            <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                <div class="card-body text-center p-4">
                    <div class="icon-circle mb-3 mx-auto" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: #dcfce7; color: #16a34a;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </div>
                    <h5 class="fw-bold mb-2">Master Harga</h5>
                    <p class="text-muted small mb-3">Atur matriks harga tiket kapal untuk berbagai jenis penumpang.</p>
                    <a href="{{ route('prices.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-4">Buka Harga &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Action 3 -->
        <div class="col-md-6 col-xl-3 animate__animated animate__fadeInUp animate__delay-3s">
            <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                <div class="card-body text-center p-4">
                    <div class="icon-circle mb-3 mx-auto" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: #fef9c3; color: #ca8a04;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <h5 class="fw-bold mb-2">Titik Dermaga</h5>
                    <p class="text-muted small mb-3">Kelola lokasi pelabuhan / terminal halte keberangkatan dan tujuan.</p>
                    <a href="{{ route('stops.index') }}" class="btn btn-sm btn-outline-warning rounded-pill px-4">Buka Dermaga &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Action 4 -->
        <div class="col-md-6 col-xl-3 animate__animated animate__fadeInUp animate__delay-4s">
            <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                <div class="card-body text-center p-4">
                    <div class="icon-circle mb-3 mx-auto" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: #f3e8ff; color: #9333ea;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    </div>
                    <h5 class="fw-bold mb-2">Pengaturan WEB</h5>
                    <p class="text-muted small mb-3">Atur nama instansi, logo, kontak informasi, dan hak akses admin.</p>
                    <a href="{{ route('settings.index') }}" class="btn btn-sm btn-outline-dark rounded-pill px-4">Buka Web Settings &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Styles */
.hover-lift {
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease;
    border-top: 4px solid transparent;
}
.hover-lift:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    border-top: 4px solid #4e73df;
}
.icofont-ship {
    opacity: 0.8;
}
.welcome-banner {
    position: relative;
    z-index: 1;
}
</style>
@endsection
