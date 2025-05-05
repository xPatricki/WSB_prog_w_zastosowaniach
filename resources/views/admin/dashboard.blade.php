@extends('layouts.admin')
@section('admin-content')
<div class="container-fluid">
    <h1 class="mb-4">Admin Dashboard</h1>
    <div class="row mb-4 justify-content-center">
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Books</h5>
                    <p class="card-text display-6">{{ $totalBooks }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Books Loaned</h5>
                    <p class="card-text display-6">{{ $loanedBooks }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-6">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Active Loans</h5>
                    <p class="card-text display-6">{{ $activeLoans }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">Book Status</div>
                <div class="card-body">
                    <canvas id="pieChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">Books Borrowed Per Month</div>
                <div class="card-body">
                    <canvas id="barChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">New Users Per Month</div>
                <div class="card-body">
                    <canvas id="lineChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Available', 'Borrowed'],
            datasets: [{
                data: [{{ $bookStatus['available'] }}, {{ $bookStatus['borrowed'] }}],
                backgroundColor: ['#007bff', '#28a745']
            }]
        }
    });
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Books Borrowed',
                data: {!! json_encode(array_values($borrowedPerMonth->toArray())) !!},
                backgroundColor: '#28a745'
            }]
        }
    });
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'New Users',
                data: {!! json_encode(array_values($usersPerMonth->toArray())) !!},
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23,162,184,0.2)',
                fill: true
            }]
        }
    });
</script>
@endsection
