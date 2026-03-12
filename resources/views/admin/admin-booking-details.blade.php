@extends('template.admin-dashboard')

@section('title', 'Booking Detail')

@section('content')
<div class="dashboard-content">
    <h2 class="mb-4">Booking Detail</h2>

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

    <div id="booking-detail-container" class="card p-3 shadow-sm">
        <!-- Detail booking akan diisi via JS -->
    </div>

    <a href="{{ url('dashboard/bookings') }}" class="btn btn-secondary mt-3">Back to List</a>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Initial data from controller (adjusted to use $details directly as array of records)
    let initialData = {
        records: @json($details),
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

    function renderBookingDetail(detail) {
        const container = document.getElementById('booking-detail-container');
        if (!detail) {
            container.innerHTML = '<p class="text-danger">No booking data available</p>';
            return;
        }

        let paymentDetails;
        try {
            paymentDetails = JSON.parse(detail.payment_details || '{}');
        } catch (e) {
            paymentDetails = { card_no: 'N/A', card_type: 'N/A' };
        }

        container.innerHTML = `
            <h5>Booking Code: ${detail.name || 'N/A'}</h5>
            <p><strong>Book Date:</strong> ${detail.book_date || 'N/A'}</p>
            <p><strong>Departure Date:</strong> ${detail.departure_date ? detail.departure_date + ' (' + (detail.departure_hour || 'N/A') + ')' : 'N/A'}</p>
            <p><strong>From:</strong> ${detail.stop_from_id?.[1] || 'N/A'}</p>
            <p><strong>To:</strong> ${detail.stop_to_id?.[1] || 'N/A'}</p>
            <p><strong>Schedule:</strong> ${detail.schedule_id?.[1] || 'N/A'}</p>
            <p><strong>Customer:</strong> ${detail.customer_name || 'N/A'} (${detail.customer_contact || 'N/A'})</p>
            <p><strong>ID No:</strong> ${detail.customer_id_no || 'N/A'}</p>
            <p><strong>Order Summary:</strong> ${detail.order_summary || 'N/A'}</p>
            <p><strong>Discount:</strong> Rp${detail.line_discount_amount ? detail.line_discount_amount.toLocaleString('id-ID') : '0'}</p>
            <p><strong>Total Amount:</strong> Rp${detail.order_amount ? detail.order_amount.toLocaleString('id-ID') : '0'}</p>
            <p><strong>Status:</strong> <span class="badge bg-${detail.state === 'booked' ? 'success' : 'secondary'}">${detail.state || 'N/A'}</span></p>
            <p><strong>Payment Method:</strong> ${detail.payment_method ? detail.payment_method.toUpperCase() : 'N/A'}</p>
            <p><strong>Card Info:</strong> ${paymentDetails.card_type ? paymentDetails.card_type.toUpperCase() : 'N/A'} - ${paymentDetails.card_no || 'N/A'}</p>
        `;
    }

    // Fetch data from API
    async function fetchBookingDetail() {
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'api-key': apiKey
                },
                body: JSON.stringify({
                    params: {
                        booking_no: bookingNo
                    }
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
            // Adjust to match response structure: direct array of records
            const records = is_array(data) ? data : (data.records || []);
            if (records.length > 0) {
                renderBookingDetail(records[0]);
            } else {
                displayAlert('Booking not found', 'danger');
                renderBookingDetail(null);
            }
        } catch (error) {
            displayAlert(`Failed to fetch booking details: ${error.message}`, 'danger');
            renderBookingDetail(null);
        }
    }

    // Initial load
    if (initialData.records.length > 0) {
        renderBookingDetail(initialData.records[0]);
    } else {
        fetchBookingDetail();
    }
});
</script>
@endsection