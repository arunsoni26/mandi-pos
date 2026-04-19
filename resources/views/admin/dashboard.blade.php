@extends('layouts.admin-app')

@section('content')
<div class="container-fluid py-4">

    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3>Dashboard</h3>
        </div>
    </div>
    
    <!-- SUMMARY CARDS -->
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card shadow bg-success dashnum-card text-white overflow-hidden">
                <span class="round small"></span>
                <span class="round big"></span>
                <div class="card-body">
                    <span class="text-white d-block f-34 f-w-500 my-2">
                        <i class="fas fa-rupee-sign opacity-50"></i>
                        {{ number_format($totalSales, 2) }}
                    </span>
                    <p class="mb-0 opacity-50">Total Sales</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow bg-warning dashnum-card text-white overflow-hidden">
                <span class="round small"></span>
                <span class="round big"></span>
                <div class="card-body">
                    <span class="text-white d-block f-34 f-w-500 my-2">
                        <i class="fas fa-boxes opacity-50"></i>
                        {{ $totalPieces }}
                    </span>
                    <p class="mb-0 opacity-50">Total Pieces</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow bg-primary-dark dashnum-card text-white overflow-hidden">
                <span class="round small"></span>
                <span class="round big"></span>
                <div class="card-body">
                    <span class="text-white d-block f-34 f-w-500 my-2">
                        <i class="fas fa-user-minus opacity-50"></i>
                        {{ $totalCreditors }}
                    </span>
                    <p class="mb-0 opacity-50">Total Creditors</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow bg-danger dashnum-card text-white overflow-hidden">
                <span class="round small"></span>
                <span class="round big"></span>
                <div class="card-body">
                    <span class="text-white d-block f-34 f-w-500 my-2">
                        <i class="fas fa-user-plus opacity-50"></i>
                        {{ $totalDebtors }}
                    </span>
                    <p class="mb-0 opacity-50">Total Debtors</p>
                </div>
            </div>
        </div>

    </div>

    <!-- CHARTS -->
    <div class="row g-4">

        <!-- Monthly Sales -->
        <div class="col-md-6">
            <div class="card p-3 shadow rounded-4">
                <h5>Monthly Sales</h5>
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Top Creditors -->
        <div class="col-md-6">
            <div class="card p-3 shadow rounded-4">
                <h5>Top Creditors</h5>
                <canvas id="creditorChart"></canvas>
            </div>
        </div>

        <!-- Product Distribution -->
        <div class="col-md-6">
            <div class="card p-3 shadow rounded-4">
                <h5>Product Distribution</h5>
                <canvas id="productChart"></canvas>
            </div>
        </div>

    </div>
</div>
@endsection

@push('custom-scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Monthly Sales
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{
            label: 'Sales',
            data: @json($monthlySales)
        }]
    }
});

// Top Creditors
new Chart(document.getElementById('creditorChart'), {
    type: 'bar',
    data: {
        labels: @json($topCreditors->pluck('name')),
        datasets: [{
            label: 'Amount',
            data: @json($topCreditors->pluck('total'))
        }]
    }
});

// Product Distribution
new Chart(document.getElementById('productChart'), {
    type: 'pie',
    data: {
        labels: @json($products->pluck('product_name')),
        datasets: [{
            data: @json($products->pluck('total'))
        }]
    }
});
</script>

@endpush