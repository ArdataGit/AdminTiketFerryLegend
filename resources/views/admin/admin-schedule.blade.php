@extends('template.admin-dashboard')

@section('title', 'Master Schedule')

@section('content')
<div class="dashboard-content">
    <h2 class="mb-4">Master Schedule</h2>
    <div class="d-flex justify-content-between mb-3">
        <h5>List Schedules</h5>
    </div>
    <!-- Filter Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body bg-light">
            <form id="searchScheduleForm" method="GET" action="{{ route('schedule.index') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label for="departure_date" class="form-label mb-0">Departure Date</label>
                    <input type="date" class="form-control" id="departure_date" name="departure_date" 
                           value="{{ $request->input('departure_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label for="stop_from_id" class="form-label mb-0">From</label>
                    <select class="form-select" id="stop_from_id" name="stop_from_id" required>
                        <option value="">-- Select Stop --</option>
                        @foreach($stops as $stop)
                            <option value="{{ $stop['id'] }}" {{ $request->input('stop_from_id') == $stop['id'] ? 'selected' : '' }}>
                                {{ $stop['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="stop_to_id" class="form-label mb-0">To</label>
                    <select class="form-select" id="stop_to_id" name="stop_to_id" required>
                        <option value="">-- Select Stop --</option>
                        @foreach($stops as $stop)
                            <option value="{{ $stop['id'] }}" {{ $request->input('stop_to_id') == $stop['id'] ? 'selected' : '' }}>
                                {{ $stop['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end mt-4">
                    <button type="submit" class="btn btn-primary w-100">Search Schedules</button>
                    @if($request->has('departure_date'))
                        <a href="{{ route('schedule.index') }}" class="btn btn-secondary ms-2">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Filter current results..." style="max-width: 300px;">
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

    <!-- Schedules Table -->
    <div class="dashboard__table table-responsive">
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Schedule</th>
                    <th>Route</th>
                    <th>Vehicle Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Departure Hour</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody id="schedule-table-body">
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
    const endpoint = `${apiBase}/vehicle.booking.schedule/search_schedule`;

    // Initial data from controller
    let initialData = {
        records: @json($schedule),
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
        const tbody = document.getElementById('schedule-table-body');
        tbody.innerHTML = '';

        // Ensure records is an array, fallback if null or object
        const records = Array.isArray(data.records) ? data.records : [];

        // Filter records based on search input
        let filteredRecords = records.filter(item => {
            return (
                (item.schedule_id && item.schedule_id[1] && item.schedule_id[1].toLowerCase().includes(search.toLowerCase())) ||
                (item.route_id && item.route_id[1] && item.route_id[1].toLowerCase().includes(search.toLowerCase())) ||
                (item.vehicle_type_id && item.vehicle_type_id[1] && item.vehicle_type_id[1].toLowerCase().includes(search.toLowerCase())) ||
                (item.stop_from_id && item.stop_from_id[1] && item.stop_from_id[1].toLowerCase().includes(search.toLowerCase())) ||
                (item.stop_to_id && item.stop_to_id[1] && item.stop_to_id[1].toLowerCase().includes(search.toLowerCase()))
            );
        });

        if (filteredRecords.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">No schedules available for the selected filters.</td></tr>';
            return;
        }

        filteredRecords.forEach((item, index) => {
            const row = document.createElement('tr');
            row.setAttribute('data-id', item.id);
            
            // Format prices
            let priceHtml = 'N/A';
            const currencyFormatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' });
            
            if (item.price && Array.isArray(item.price) && item.price.length > 0) {
                if (item.price.length === 2) {
                    const adultPrice = Math.max(...item.price);
                    const childPrice = Math.min(...item.price);
                    priceHtml = `
                        <div class="fw-bold">${currencyFormatter.format(adultPrice)}</div>
                        <div class="text-muted small">Child: ${currencyFormatter.format(childPrice)}</div>
                    `;
                } else {
                    priceHtml = currencyFormatter.format(item.price[0]);
                }
            }
            
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.schedule_id ? item.schedule_id[1] : 'N/A'}</td>
                <td>${item.route_id ? item.route_id[1] : 'N/A'}</td>
                <td>${item.vehicle_type_id ? item.vehicle_type_id[1] : 'N/A'}</td>
                <td>${item.stop_from_id ? item.stop_from_id[1] : 'N/A'}</td>
                <td>${item.stop_to_id ? item.stop_to_id[1] : 'N/A'}</td>
                <td>${item.departure_hour || 'N/A'}</td>
                <td>${priceHtml}</td>
            `;
            tbody.appendChild(row);
        });
    }



    // Local search functionality to filter the currently fetched records
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if(searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                refreshTable(this.value.trim());
            }, 300);
        });
    }

    // Initial load - data is already provided by controller during rendering
    refreshTable();
});
</script>
@endsection