@extends('template.admin-dashboard')

@section('title', 'Master Prices')

@section('content')
<div class="dashboard-content">
    <h2 class="mb-4">Master Prices</h2>
    <div class="d-flex justify-content-between mb-3">
        <h5>List Prices</h5>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Search prices..." style="max-width: 300px;">
        </div>
    </div>

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
    </div>

    <!-- Prices Table -->
    <div class="dashboard__table table-responsive">
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Class</th>
                    <th>Price (IDR)</th>
                </tr>
            </thead>
            <tbody id="prices-table-body">
                <!-- Data will be populated via JavaScript -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // API configuration
    const apiBase = '{{ config('services.api.base') }}';
    const apiKey = '{{ $apiKey }}';
    const endpoint = `${apiBase}/vehicle.booking.price/get_prices`;

    // Initial data from controller
    let initialData = {
        records: @json($prices),
        count: {{ $count }}
    };

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

    function refreshTable(search = '', data = initialData) {
        const tbody = document.getElementById('prices-table-body');
        tbody.innerHTML = '';

        let filteredRecords = data.records.filter(item => {
            return (
                item.stop_from_id[1]?.toLowerCase().includes(search.toLowerCase()) ||
                item.stop_to_id[1]?.toLowerCase().includes(search.toLowerCase()) ||
                item.class_id[1]?.toLowerCase().includes(search.toLowerCase())
            );
        });

        if (filteredRecords.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">No data available</td></tr>';
            return;
        }

        filteredRecords.forEach((item, index) => {
            const row = document.createElement('tr');
            row.setAttribute('data-id', item.id);
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.stop_from_id[1] || 'N/A'}</td>
                <td>${item.stop_to_id[1] || 'N/A'}</td>
                <td>${item.class_id[1] || 'N/A'}</td>
                <td>${item.price ? item.price.toLocaleString('id-ID') : 'N/A'}</td>
            `;
            tbody.appendChild(row);
        });
    }

    // Fetch data from API
    async function fetchPrices() {
        try {
            const response = await fetch(endpoint, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'api-key': apiKey
                },
                body: JSON.stringify({
                    params: {
                        price_date: "2025-09-05",
                        route_ids: [1],
                        vehicle_type_ids: [1]
                    }
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
            if (data.records && data.count !== undefined) {
                refreshTable('', data);
            } else {
                displayAlert('Invalid API response format', 'danger');
                refreshTable(); // Fallback to initial data
            }
        } catch (error) {
            displayAlert(`Failed to fetch prices: ${error.message}`, 'danger');
            refreshTable(); // Fallback to initial data
        }
    }

    // Search functionality
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            refreshTable(this.value.trim());
        }, 300);
    });

    // Initial load
    if (initialData.records.length > 0) {
        refreshTable();
    } else {
        fetchPrices();
    }
});
</script>
@endsection