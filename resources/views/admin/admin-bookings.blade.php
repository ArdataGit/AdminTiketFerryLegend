@extends('template.admin-dashboard')

@section('title', 'Bookings')

@section('content')
<div class="dashboard-content">
    <h2 class="mb-4">Bookings</h2>
    <div class="row mb-3">
        <div class="col-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Search booking..." style="max-width: 300px;">
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

    <!-- Bookings Table -->
    <div class="dashboard__table table-responsive">
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Booking Code</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="booking-table-body"></tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div id="pagination-info"></div>
            <nav>
                <ul class="pagination mb-0" id="pagination"></ul>
            </nav>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

const apiBase = '{{ config('services.api.base') }}';
const apiKey  = '{{ $apiKey }}';
const endpoint = `${apiBase}/vehicle.booking.order`;

let allRecords = @json($bookings) || [];
let currentPage = 1;
let perPage = 10;

function displayAlert(message, type = 'danger') {

    const alertContainer = document.getElementById('alert-container');

    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;

    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    alertContainer.appendChild(alert);

}

function renderTable(search='') {

    const tbody = document.getElementById('booking-table-body');

    let filtered = allRecords.filter(item => {

        return item.name?.toLowerCase().includes(search.toLowerCase()) ||
               item.customer_name?.toLowerCase().includes(search.toLowerCase());

    });

    const start = (currentPage - 1) * perPage;
    const paginated = filtered.slice(start, start + perPage);

    tbody.innerHTML = '';

    if(paginated.length === 0){
        tbody.innerHTML = `<tr><td colspan="7" class="text-center">No data available</td></tr>`;
        return;
    }

    paginated.forEach((item,index)=>{

        const row = document.createElement('tr');

        row.innerHTML = `
            <td>${start + index + 1}</td>
            <td>${item.name || 'N/A'}</td>
            <td>${item.stop_from_id?.[1] || 'N/A'}</td>
            <td>${item.stop_to_id?.[1] || 'N/A'}</td>
            <td>${item.customer_name || 'N/A'}</td>
            <td>
                <span class="badge bg-${item.state === 'booked' ? 'success' : 'secondary'}">
                    ${item.state || 'N/A'}
                </span>
            </td>
            <td>
                <a href="{{ url('dashboard/bookings') }}/${item.name}" class="btn btn-sm btn-primary">
                    View Detail
                </a>
            </td>
        `;

        tbody.appendChild(row);

    });

    renderPagination(filtered.length);

}

function renderPagination(totalRecords){

    const totalPages = Math.ceil(totalRecords / perPage);

    const pagination = document.getElementById('pagination');
    const info = document.getElementById('pagination-info');

    pagination.innerHTML = '';

    info.innerHTML = `Page ${currentPage} of ${totalPages}`;

    if(totalPages <= 1) return;

    const createPageItem = (page,label=page,active=false)=>{

        const li = document.createElement('li');

        li.className = `page-item ${active?'active':''}`;

        li.innerHTML = `<a class="page-link" href="#">${label}</a>`;

        li.onclick = function(e){

            e.preventDefault();

            currentPage = page;

            renderTable(document.getElementById('searchInput').value);

        };

        return li;

    };

    if(currentPage > 1){
        pagination.appendChild(createPageItem(currentPage-1,'«'));
    }

    for(let i=1;i<=totalPages;i++){

        if(
            i === 1 ||
            i === totalPages ||
            (i >= currentPage-1 && i <= currentPage+1)
        ){

            pagination.appendChild(createPageItem(i,i,i===currentPage));

        }

    }

    if(currentPage < totalPages){
        pagination.appendChild(createPageItem(currentPage+1,'»'));
    }

}

async function fetchBookings(){

    try{

        const response = await fetch(endpoint,{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'api-key':apiKey
            },
            body:JSON.stringify({
                domain:"[('state','=','booked')]",
                fields:['name','stop_from_id','stop_to_id','customer_name','state']
            })
        });

        const data = await response.json();

        if(data.records){

            allRecords = data.records;

            renderTable();

        }

    }catch(error){

        displayAlert(`Failed to fetch bookings: ${error.message}`);

        renderTable();

    }

}

let searchTimeout;

document.getElementById('searchInput').addEventListener('input',function(){

    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(()=>{

        currentPage = 1;

        renderTable(this.value.trim());

    },300);

});

if(allRecords.length > 0){

    renderTable();

}else{

    fetchBookings();

}

});
</script>
@endsection