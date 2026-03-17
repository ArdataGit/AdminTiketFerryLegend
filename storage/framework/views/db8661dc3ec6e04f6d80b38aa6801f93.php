<?php $__env->startSection('title', 'Transactions'); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-content">
    <h2 class="mb-4">Transactions</h2>
    <div class="row mb-3">
        <div class="col-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Search transaction..." style="max-width: 300px;">
        </div>
    </div>

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
    </div>

    <!-- Transactions Table -->
    <div class="dashboard__table table-responsive">
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Booking Name</th>
                    <th>Customer Name</th>
                    <th>Type</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="transaction-table-body"></tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // API configuration
    const apiBase = 'https://tiket.cargo.or.id/api/';
    const endpoint = `${apiBase}transactions`;

    let currentData = { records: [], count: 0 };

    function displayAlert(message, type = 'danger') {
        const alertContainer = document.getElementById('alert-container');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.appendChild(alert);
    }

    function refreshTable(search = '', data = currentData) {
        const tbody = document.getElementById('transaction-table-body');
        tbody.innerHTML = '';

        let filteredRecords = data.records.filter(item => {
            return item.booking_name?.toLowerCase().includes(search.toLowerCase()) ||
                   item.customer_name?.toLowerCase().includes(search.toLowerCase());
        });

        if (filteredRecords.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">No data available</td></tr>';
            return;
        }

        filteredRecords.forEach((item, index) => {
            const row = document.createElement('tr');
            row.setAttribute('data-id', item.id || index);
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.booking_name || 'N/A'}</td>
                <td>${item.customer_name || 'N/A'}</td>
                <td>
                    <span class="badge bg-${item.member_id == 1 ? 'primary' : 'secondary'}">
                        ${item.member_id == 1 ? 'Member' : 'Publik'}
                    </span>
                </td>
                <td>${item.total_amount || 'N/A'}</td>
                <td>${item.payment_method || 'N/A'}</td>
                <td><span class="badge bg-${item.state === 'paid' ? 'success' : item.state === 'pending' ? 'warning' : 'secondary'}">${item.state || 'N/A'}</span></td>
                <td>
                    <a href="<?php echo e(url('dashboard/transactions')); ?>/${item.id}" class="btn btn-sm btn-primary">View Detail</a>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // Fetch data from API
    async function fetchTransactions() {
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
            if (data.success && data.data && data.pagination) {
                currentData = { records: data.data, count: data.pagination.total };
                refreshTable('', currentData);
            } else {
                displayAlert('Invalid API response format', 'danger');
                refreshTable(); // Fallback to empty data
            }
        } catch (error) {
            displayAlert(`Failed to fetch transactions: ${error.message}`, 'danger');
            refreshTable(); // Fallback to empty data
        }
    }

    // Search functionality
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            refreshTable(this.value.trim(), currentData);
        }, 300);
    });

    // Initial load - always fetch from API
    fetchTransactions();
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.admin-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\AdminTiketFerryLegend\resources\views/admin/admin-transaction.blade.php ENDPATH**/ ?>