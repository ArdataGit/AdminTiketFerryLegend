@extends('template.admin-dashboard')

@section('title', 'Master Vehicle Types')

@section('content')
<div class="dashboard-content">
    <h2 class="mb-4">Master Vehicle Types</h2>
    <div class="d-flex justify-content-between mb-3">
        <h5>List Vehicle Types</h5>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by vehicle type or class..." style="max-width: 300px;">
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

    <!-- Vehicle Types Table -->
    <div class="dashboard__table table-responsive">
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Vehicle Type</th>
                    <th>Class Names</th>
                </tr>
            </thead>
            <tbody id="vehicle-type-table-body">
                @forelse ($vehicleTypes as $index => $item)
                    <tr data-id="{{ $item['id'] }}">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['name'] ?? '-' }}</td>
                        <td>
                            @if (!empty($item['class_ids']))
                                {{ implode(', ', array_column($item['class_ids'], 'name')) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center">No data available</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Search functionality (client-side filtering)
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = this.value.trim().toLowerCase();
            const rows = document.querySelectorAll('#vehicle-type-table-body tr');
            rows.forEach(row => {
                const vehicleType = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const classNames = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                row.style.display = vehicleType.includes(searchTerm) || classNames.includes(searchTerm) ? '' : 'none';
            });
        }, 300);
    });
});
</script>
@endsection