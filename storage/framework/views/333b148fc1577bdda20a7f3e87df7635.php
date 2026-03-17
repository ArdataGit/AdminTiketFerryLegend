<?php $__env->startSection('title', 'Transaction Detail'); ?>
<?php $__env->startSection('content'); ?>
<div class="dashboard-content">
    <h2 class="mb-4">Transaction Detail</h2>
  
    <!-- Success/Error Messages -->
    <div id="alert-container">
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(session('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?php echo e(session('warning')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
            // console.log('API Response:', data);
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
        const returnTrip = order.return || null;
        const customerOrder = order.customer || {};
        const passengers = order.passengers || {};
        const payment = item.payment || {};
        const tripay = item.tripay || {};
        const meta = order.meta || {};
        const pricing = order.pricing || {};

        const badgeClass = item.state === 'paid' ? 'success' : item.state === 'pending' ? 'warning' : 'secondary';

        // Format dates
        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            const date = new Date(dateStr + 'T00:00:00');
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }

        const outboundDate = formatDate(outbound.departure_date);
        const returnDate = returnTrip ? formatDate(returnTrip.departure_date) : 'N/A';
        const createdAt = meta.created_at || 'N/A';

        // Passenger list HTML
        let passengersHTML = '';
        
        // Adults
        if (passengers.adult) {
            Object.entries(passengers.adult).forEach(([key, pax]) => {
                passengersHTML += `
                    <div class="mb-3 p-3 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong><i class="fas fa-user-tie me-1"></i>Dewasa ${key}</strong>
                            <span class="badge bg-info text-dark">Adult</span>
                        </div>
                        <div class="small">
                            <span class="text-muted">Nama:</span> ${pax.name || 'N/A'}<br>
                            <span class="text-muted">Kontak:</span> ${pax.contact || 'N/A'}<br>
                            <span class="text-muted">No. ID:</span> ${pax.id_no || 'N/A'}
                        </div>
                    </div>`;
            });
        }
        
        // Children
        if (passengers.child) {
            Object.entries(passengers.child).forEach(([key, pax]) => {
                passengersHTML += `
                    <div class="mb-3 p-3 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong><i class="fas fa-child me-1"></i>Anak ${key}</strong>
                            <span class="badge bg-warning text-dark">Child</span>
                        </div>
                        <div class="small">
                            <span class="text-muted">Nama:</span> ${pax.name || 'N/A'}<br>
                            <span class="text-muted">Kontak:</span> ${pax.contact || 'N/A'}<br>
                            <span class="text-muted">No. ID:</span> ${pax.id_no || '-'}
                        </div>
                    </div>`;
            });
        }

        // Addons list HTML
        let addonsHTML = '';
        if (order.addons && order.addons.length > 0) {
            addonsHTML = `<div class="table-responsive"><table class="table table-sm table-borderless mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Layanan</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Harga</th>
                    </tr>
                </thead>
                <tbody>`;
            order.addons.forEach(addon => {
                addonsHTML += `
                    <tr>
                        <td>${addon.name}</td>
                        <td class="text-center">${addon.qty}</td>
                        <td class="text-end">Rp ${parseFloat(addon.price).toLocaleString('id-ID')}</td>
                    </tr>`;
            });
            addonsHTML += `</tbody></table></div>`;
        } else {
            addonsHTML = '<p class="text-muted mb-0">Tidak ada add-ons</p>';
        }

        container.innerHTML = `
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Transaction Detail - ${booking.name || 'N/A'}</h5>
                    <span class="badge bg-${badgeClass} fs-6 px-3 py-2 border border-white border-opacity-25">${item.state ? item.state.toUpperCase() : 'N/A'}</span>
                </div>
                <div class="card-body p-4">
                    <!-- Customer Information -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 shadow-sm bg-light bg-opacity-50">
                                <div class="card-header bg-white border-bottom-0 pt-3">
                                    <h6 class="card-title mb-0 fw-bold text-primary"><i class="fas fa-user-circle me-2"></i>Informasi Pemesan</h6>
                                </div>
                                <div class="card-body">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 text-muted small text-uppercase">Tipe</dt>
                                        <dd class="col-sm-8 mb-2">
                                            <span class="badge ${customerOrder.member_id > 0 ? 'bg-primary' : 'bg-secondary'} px-2">
                                                ${customerOrder.member_id > 0 ? 'Member' : 'Publik'}
                                            </span>
                                        </dd>

                                        <dt class="col-sm-4 text-muted small text-uppercase">Nama</dt>
                                        <dd class="col-sm-8 mb-2 fw-semibold">${customerOrder.name || 'N/A'}</dd>
                                        
                                        <dt class="col-sm-4 text-muted small text-uppercase">Email</dt>
                                        <dd class="col-sm-8 mb-2">${customerOrder.email || 'N/A'}</dd>
                                        
                                        <dt class="col-sm-4 text-muted small text-uppercase">Kontak</dt>
                                        <dd class="col-sm-8 mb-2">${customerOrder.contact || 'N/A'}</dd>
                                        
                                        <dt class="col-sm-4 text-muted small text-uppercase">No. ID</dt>
                                        <dd class="col-sm-8 mb-0">${customerOrder.id_no || 'N/A'}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom-0 pt-3">
                                    <h6 class="card-title mb-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Daftar Penumpang</h6>
                                </div>
                                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                                    ${passengersHTML || '<p class="text-muted">Tidak ada data penumpang</p>'}
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-50">

                    <!-- Trip Details -->
                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-ship me-2"></i>Detail Perjalanan (${order.trip_type === 'round_trip' ? 'Pulang Pergi' : 'Sekali Jalan'})</h6>
                    <div class="row g-4 mb-4">
                        <!-- Outbound -->
                        <div class="col-lg-${order.trip_type === 'round_trip' ? '6' : '12'}">
                            <div class="card border-0 shadow-sm overflow-hidden">
                                <div class="card-header bg-success bg-opacity-10 text-success border-0 py-2">
                                    <h6 class="mb-0 small fw-bold"><i class="fas fa-arrow-right me-2"></i>KEBERANGKATAN - ${outboundDate}</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="text-center flex-grow-1">
                                            <div class="fw-bold h5 mb-0">${outbound.stop_from_name}</div>
                                            <div class="text-muted small">${outbound.departure_hour}</div>
                                        </div>
                                        <div class="px-3 text-muted">
                                            <i class="fas fa-ellipsis-h"></i>
                                            <i class="fas fa-ship mx-1"></i>
                                            <i class="fas fa-ellipsis-h"></i>
                                        </div>
                                        <div class="text-center flex-grow-1">
                                            <div class="fw-bold h5 mb-0">${outbound.stop_to_name}</div>
                                            <div class="text-muted small">${outbound.arrival_hour}</div>
                                        </div>
                                    </div>
                                    <div class="p-2 bg-light rounded d-flex justify-content-between align-items-center small">
                                        <span>Kelas: <strong>${outbound.class_name}</strong></span>
                                        <span class="text-success fw-bold">Rp ${parseFloat(outbound.total || 0).toLocaleString('id-ID')}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Return -->
                        ${order.trip_type === 'round_trip' && returnTrip ? `
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm overflow-hidden">
                                <div class="card-header bg-info bg-opacity-10 text-info border-0 py-2">
                                    <h6 class="mb-0 small fw-bold"><i class="fas fa-arrow-left me-2"></i>KEPULANGAN - ${returnDate}</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="text-center flex-grow-1">
                                            <div class="fw-bold h5 mb-0">${returnTrip.stop_to_name}</div>
                                            <div class="text-muted small">${returnTrip.departure_hour}</div>
                                        </div>
                                        <div class="px-3 text-muted">
                                            <i class="fas fa-ellipsis-h"></i>
                                            <i class="fas fa-ship mx-1"></i>
                                            <i class="fas fa-ellipsis-h"></i>
                                        </div>
                                        <div class="text-center flex-grow-1">
                                            <div class="fw-bold h5 mb-0">${returnTrip.stop_from_name}</div>
                                            <div class="text-muted small">${returnTrip.arrival_hour}</div>
                                        </div>
                                    </div>
                                    <div class="p-2 bg-light rounded d-flex justify-content-between align-items-center small">
                                        <span>Kelas: <strong>${returnTrip.class_name}</strong></span>
                                        <span class="text-info fw-bold">Rp ${parseFloat(returnTrip.total || 0).toLocaleString('id-ID')}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                    </div>

                    <hr class="my-4 opacity-50">

                    <!-- Payment & Meta -->
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 shadow-sm bg-dark text-white">
                                <div class="card-header bg-transparent border-bottom border-white border-opacity-10 pt-3">
                                    <h6 class="card-title mb-0 fw-bold"><i class="fas fa-credit-card me-2 text-warning"></i>Rincian Pembayaran</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-white-50 small">Total Tagihan</span>
                                        <span class="h4 mb-0 fw-bold text-warning">Rp ${parseFloat(payment.total || pricing.total || 0).toLocaleString('id-ID')}</span>
                                    </div>
                                    <div class="mb-3 small">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-white-50">Metode</span>
                                            <span class="text-uppercase">${payment.method ? payment.method.replace('_', ' ') : 'N/A'}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-white-50">Tripay ID</span>
                                            <span class="text-truncate ms-2" style="max-width: 150px;">${tripay.transaction_id || 'N/A'}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-white-50">Status</span>
                                            <span class="badge ${tripay.status === 'paid' ? 'bg-success' : 'bg-warning text-dark'}">${tripay.status ? tripay.status.toUpperCase() : 'N/A'}</span>
                                        </div>
                                    </div>
                                    ${tripay.payment_url ? `<a href="${tripay.payment_url}" target="_blank" class="btn btn-warning btn-sm w-100 fw-bold mt-2">Buka Halaman Pembayaran</a>` : ''}
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom-0 pt-3">
                                    <h6 class="card-title mb-0 fw-bold text-primary"><i class="fas fa-concierge-bell me-2"></i>Layanan Tambahan & Info</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div class="text-muted small text-uppercase mb-2 fw-semibold">Add-ons</div>
                                        ${addonsHTML}
                                    </div>
                                    <div class="mt-3 pt-3 border-top">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span class="text-muted">Dibuat Pada</span>
                                            <span>${createdAt}</span>
                                        </div>
                                        <div class="d-flex justify-content-between small">
                                            <span class="text-muted">IP Address</span>
                                            <span>${meta.ip_address || 'N/A'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <a href="<?php echo e(url('dashboard/transaction')); ?>" class="btn btn-light border">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-warning shadow-sm" onclick="updateStatus('pending')">
                                <i class="fas fa-hourglass-half me-2"></i>Set Pending
                            </button>
                            <button type="button" class="btn btn-success shadow-sm px-4" onclick="updateStatus('paid')">
                                <i class="fas fa-check-circle me-2"></i>Konfirmasi Bayar
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.admin-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\AdminTiketFerryLegend\resources\views/admin/admin-transaction-detail.blade.php ENDPATH**/ ?>