@extends('template.admin-dashboard')

@section('title', 'Master Stops')

@section('content')
<div class="dashboard-content">
    <h2 class="mb-4">Master Stops</h2>
    <div class="d-flex justify-content-between mb-3">
        <h5>List Stops</h5>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Search stops..." style="max-width: 300px;">
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="alert-container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Stops Table -->
    <div class="dashboard__table table-responsive">
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody id="stops-table-body">
                <!-- Data will be populated via JavaScript -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Server-side data passed from controller
    const stopsData = @json($stops);

    // Function to show alerts
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alert-container');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.innerHTML = '';
        alertContainer.appendChild(alert);
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 3000);
    }

    // Function to refresh table data
    function refreshTable(records, search = '') {
        const tbody = document.getElementById('stops-table-body');
        tbody.innerHTML = '';

        // Filter records based on search term
        const filteredRecords = records.filter(item => 
            item.name.toLowerCase().includes(search.toLowerCase())
        );

        if (filteredRecords.length === 0) {
            tbody.innerHTML = '<tr><td colspan="2" class="text-center">No data available</td></tr>';
            return;
        }

        filteredRecords.forEach((item, index) => {
            const row = document.createElement('tr');
            row.setAttribute('data-id', item.id);
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.name || '-'}</td>
            `;
            tbody.appendChild(row);
        });
    }

    // Search functionality
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = this.value.trim();
            refreshTable(stopsData, searchTerm);
        }, 300); // Debounce to prevent excessive filtering
    });

    // Initial table render
    refreshTable(stopsData);
});
</script>
@endsection