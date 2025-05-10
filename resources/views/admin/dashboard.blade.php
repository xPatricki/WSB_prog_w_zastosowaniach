@extends('layouts.admin')
@section('admin-content')
<div class="container-fluid">
    <h1 class="mb-4">Admin Dashboard</h1>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<div class="d-flex mb-4 w-100 gap-4" style="justify-content:space-between;">
    <div class="card text-white bg-primary flex-fill" style="min-width:0;">
        <div class="card-body text-center">
            <h5 class="card-title">Total Titles</h5>
            <p class="card-text display-6">{{ $totalBooks }}</p>
        </div>
    </div>
    <div class="card text-white bg-success flex-fill" style="min-width:0;">
        <div class="card-body text-center">
            <h5 class="card-title">Books Loaned</h5>
            <p class="card-text display-6">{{ $loanedBooks }}</p>
        </div>
    </div>
    <div class="card text-white bg-info flex-fill" style="min-width:0;">
        <div class="card-body text-center">
            <h5 class="card-title">Total Users</h5>
            <p class="card-text display-6">{{ $totalUsers }}</p>
        </div>
    </div>
    <div class="card text-white bg-warning flex-fill" style="min-width:0;">
        <div class="card-body text-center">
            <h5 class="card-title">Active Loans</h5>
            <p class="card-text display-6">{{ $activeLoans }}</p>
        </div>
    </div>
    <div class="card text-white bg-danger flex-fill position-relative" style="min-width:0;">
        <div class="card-body text-center position-relative">
            <h5 class="card-title">Loans Expired</h5>
            <p class="card-text display-6 mb-0">{{ $expiredLoans }}</p>
            @if($expiredLoans == 0)
                <span class="position-absolute bottom-0 end-0 p-2" style="font-size:2rem;"><i class="bi bi-check-circle-fill text-success" title="All good"></i></span>
            @else
                <span class="position-absolute bottom-0 end-0 p-2" style="font-size:2rem;"><i class="bi bi-x-circle-fill text-danger" title="Expired loans"></i></span>
            @endif
        </div>
    </div>
</div>
    <div class="row justify-content-center">
        <div class="col-lg-4 mb-4">
            <div class="card" style="height: 320px;">
                <div class="card-header">Book Status</div>
                <div class="card-body d-flex align-items-center justify-content-center" style="height: 260px;">
                    <canvas id="pieChart" width="260" height="260"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card" style="height: 320px;">
                <div class="card-header">Books Borrowed Per Month ({{ date('Y') }})</div>
                <div class="card-body d-flex align-items-center justify-content-center" style="height: 260px;">
                    <canvas id="barChart" width="260" height="260"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card" style="height: 320px;">
                <div class="card-header">New Users Per Month</div>
                <div class="card-body d-flex align-items-center justify-content-center" style="height: 260px;">
                    <canvas id="lineChart" width="260" height="260"></canvas>
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
                data: {!! json_encode(array_values($booksBorrowedPerMonth->toArray())) !!},
                backgroundColor: '#28a745'
            }]
        },
        options: {
            plugins: {
                tooltip: { enabled: false }, // Disable tooltip for bar chart
                legend: { display: false }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Books Borrowed'
                    },
                    ticks: {
                        precision: 0,
                        callback: function(value) { return Number.isInteger(value) ? value : null; }
                    }
                }
            }
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
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'New Users'
                    },
                    ticks: {
                        precision: 0,
                        callback: function(value) { return Number.isInteger(value) ? value : null; }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                }
            }
        }
    });
</script>
@endsection
