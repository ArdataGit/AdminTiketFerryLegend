@extends('template.admin-dashboard')
@section('title', 'Transaction Detail')
@section('content')
<div class="dashboard-content">
    <h2 class="mb-4">Transaction Detail</h2>
  
    <!-- Success/Error Messages -->
    <div id="alert-container">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    <!-- Transaction Detail -->
    <div id="transaction-detail">
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, script starting...');
  
    // API configuration
    const apiBase = 'https://tiket.cargo.or.id/api/';
  
    // Extract ID from URL
    const pathSegments = window.location.pathname.split('/');
    const id = pathSegments[pathSegments.length - 1];
    const endpoint = `${apiBase}transactions/${id}`;
  
    console.log('Transaction ID from URL:', id);
    console.log('Endpoint constructed:', endpoint);

    function displayAlert(message, type = 'danger') {
        const alertContainer = document.getElementById('alert-container');
        const existingAlerts = alertContainer.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.appendChild(alert);
    }

    // Fetch single transaction from API
    async function fetchTransaction() {
        console.log('fetchTransaction() called');
      
        if (!id || isNaN(id) || id === 'null') {
            displayAlert('Transaction ID not found', 'danger');
            return;
        }

        try {
            const response = await fetch(endpoint, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.data) {
                displayTransaction(data.data);
            } else {
                displayAlert('Invalid API response format', 'danger');
            }
        } catch (error) {
            console.error('Fetch error:', error);
            displayAlert(`Failed to fetch transaction: ${error.message}`, 'danger');
        }
    }

    function displayTransaction(item) {
        const container = document.getElementById('transaction-detail');
        const booking = item.booking || {};
        const order = item.order || {};
        const outbound = order.outbound || {};
        const returnTrip = order.return || {};
        const customerOrder = order.customer || {};
        const passengers = order.passengers || {};
        const payment = item.payment || {};
        const tripay = item.tripay || {};
        const meta = order.meta || {};

        const badgeClass = item.state === 'paid' ? 'success' : item.state === 'pending' ? 'warning' : 'secondary';

        // Format dates
        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            const date = new Date(dateStr + 'T00:00:00');
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }

        const outboundDate = formatDate(outbound.departure_date);
        const returnDate = formatDate(returnTrip.departure_date);
        const createdAt = meta.created_at || 'N/A';

        // Passenger count
        const adultsCount = passengers.adult ? Object.keys(passengers.adult).length : 0;
        const childrenCount = passengers.child ? Object.keys(passengers.child).length : 0;
        const infantsCount = passengers.infant ? Object.keys(passengers.infant).length : 0;

        // Passengers list HTML
        let passengersHTML = '';
        if (adultsCount > 0) {
            Object.entries(passengers.adult).forEach(([key, pax]) => {
                passengersHTML += `
                    <div class="mb-3 p-3 border rounded bg-light">
                        <strong>Dewasa ${key}</strong><br>
                        Nama: ${pax.name || 'N/A'}<br>
                        No. ID: ${pax.id_no || 'N/A'}
                    </div>`;
            });
        }
        // Tambahkan child & infant jika ada di masa depan

        container.innerHTML = `
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Transaction Detail - ${booking.name || 'N/A'} (ID: ${id})</h5>
                    <span class="badge bg-${badgeClass} fs-5 px-3 py-2">${item.state ? item.state.toUpperCase() : 'N/A'}</span>
                </div>
                <div class="card-body p-4">
                    <!-- Customer Information -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6">
                            <div class="card h-100 border-light">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0"><i class="fas fa-user me-2 text-primary"></i>Informasi Pemesan</h6>
                                </div>
                                <div class="card-body p-3">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 fw-bold text-muted">Tipe:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge bg-${customerOrder.member_id == 1 ? 'primary' : 'secondary'}">
                                                ${customerOrder.member_id == 1 ? 'Member' : 'Publik'}
                                            </span>
                                        </dd>

                                        <dt class="col-sm-4 fw-bold text-muted">Nama:</dt>
                                        <dd class="col-sm-8">${customerOrder.name || 'N/A'}</dd>
                                        <dt class="col-sm-4 fw-bold text-muted">Email:</dt>
                                        <dd class="col-sm-8">${customerOrder.email || 'N/A'}</dd>
                                        <dt class="col-sm-4 fw-bold text-muted">Kontak:</dt>
                                        <dd class="col-sm-8">${customerOrder.contact || 'N/A'}</dd>
                                        <dt class="col-sm-4 fw-bold text-muted">No. ID:</dt>
                                        <dd class="col-sm-8">${customerOrder.id_no || 'N/A'}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card h-100 border-light">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0"><i class="fas fa-users me-2 text-primary"></i>Data Penumpang (${adultsCount} Dewasa)</h6>
                                </div>
                                <div class="card-body p-3">
                                    ${passengersHTML || '<p class="text-muted">Tidak ada data penumpang</p>'}
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Trip Details: Outbound & Return -->
                    <h5 class="mb-3"><i class="fas fa-ship me-2 text-primary"></i>Detail Perjalanan (Pulang Pergi)</h5>
                    <div class="row g-4 mb-4">
                        <!-- Outbound -->
                        <div class="col-lg-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-arrow-right me-2"></i>Pergi - ${outboundDate}</h6>
                                </div>
                                <div class="card-body p-3">
                                    <p><strong>Rute:</strong> ${outbound.stop_from_name} → ${outbound.stop_to_name}</p>
                                    <p><strong>Berangkat:</strong> ${outbound.departure_hour} WIB</p>
                                    <p><strong>Tiba:</strong> ${outbound.arrival_hour} WIB</p>
                                    <p><strong>Kelas:</strong> ${outbound.class_name}</p>
                                    <p><strong>Harga Total Leg:</strong> Rp ${(parseFloat(outbound.total || 0)).toLocaleString('id-ID')}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Return -->
                        <div class="col-lg-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-arrow-left me-2"></i>Pulang - ${returnDate}</h6>
                                </div>
                                <div class="card-body p-3">
                                    <p><strong>Rute:</strong> ${returnTrip.stop_from_name} → ${returnTrip.stop_to_name}</p>
                                    <p><strong>Berangkat:</strong> ${returnTrip.departure_hour} WIB</p>
                                    <p><strong>Tiba:</strong> ${returnTrip.arrival_hour} WIB</p>
                                    <p><strong>Kelas:</strong> ${returnTrip.class_name}</p>
                                    <p><strong>Harga Total Leg:</strong> Rp ${(parseFloat(returnTrip.total || 0)).toLocaleString('id-ID')}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Payment & Meta -->
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="card h-100 border-light">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0"><i class="fas fa-credit-card me-2 text-primary"></i>Informasi Pembayaran</h6>
                                </div>
                                <div class="card-body p-3">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5 fw-bold text-muted">Total Tagihan:</dt>
                                        <dd class="col-sm-7 fw-bold text-success fs-5">Rp ${parseFloat(payment.total || 0).toLocaleString('id-ID')}</dd>
                                        <dt class="col-sm-5 fw-bold text-muted">Metode:</dt>
                                        <dd class="col-sm-7 text-uppercase">${payment.method ? payment.method.replace('_', ' ') : 'N/A'}</dd>
                                        <dt class="col-sm-5 fw-bold text-muted">Tripay ID:</dt>
                                        <dd class="col-sm-7 small">${tripay.transaction_id || 'N/A'}</dd>
                                        ${tripay.payment_url ? `
                                        <dt class="col-sm-5 fw-bold text-muted">Link Pembayaran:</dt>
                                        <dd class="col-sm-7"><a href="${tripay.payment_url}" target="_blank" class="btn btn-sm btn-primary">Buka Halaman Pembayaran</a></dd>` : ''}
                                        <dt class="col-sm-5 fw-bold text-muted">Status Tripay:</dt>
                                        <dd class="col-sm-7"><span class="badge bg-${tripay.status === 'paid' ? 'success' : 'warning'}">${tripay.status ? tripay.status.toUpperCase() : 'N/A'}</span></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card h-100 border-light">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Tambahan</h6>
                                </div>
                                <div class="card-body p-3">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5 fw-bold text-muted">Dibuat Pada:</dt>
                                        <dd class="col-sm-7">${createdAt}</dd>
                                        <dt class="col-sm-5 fw-bold text-muted">IP Address:</dt>
                                        <dd class="col-sm-7 small">${meta.ip_address || 'N/A'}</dd>
                                        <dt class="col-sm-5 fw-bold text-muted">Add-ons:</dt>
                                        <dd class="col-sm-7">${order.addons && order.addons.length > 0 ? order.addons.join(', ') : 'Tidak ada'}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-start gap-3 flex-wrap">
                        <a href="{{ url('dashboard/transactions') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Transaksi
                        </a>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-warning" onclick="updateStatus('pending')">
                                <i class="fas fa-clock me-2"></i>Set Pending
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="updateStatus('paid')">
                                <i class="fas fa-check me-2"></i>Set Paid
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Global updateStatus function
    window.updateStatus = async function(newState) {
        if (!confirm(`Apakah Anda yakin ingin mengubah status menjadi ${newState.toUpperCase()}?`)) {
            return;
        }

        try {
            const statusEndpoint = `${apiBase}transactions/${id}/status`;
            const response = await fetch(statusEndpoint, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ state: newState })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                displayAlert('Status berhasil diperbarui!', 'success');
                setTimeout(() => fetchTransaction(), 1500);
            } else {
                displayAlert('Gagal memperbarui status', 'danger');
            }
        } catch (error) {
            console.error('Status update error:', error);
            displayAlert(`Error: ${error.message}`, 'danger');
        }
    };

    // Initial fetch
    fetchTransaction();
});
</script>
@endsection