@extends('layouts.admin')
@section('admin-content')
<div class="container py-5">
    <h1 class="mb-4">Admin Panel</h1>
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-bar-chart-fill display-4 text-primary mb-3"></i>
                    <h5 class="card-title">Dashboard</h5>
                    <p class="card-text">View library statistics and charts</p>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary mt-auto">Go</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-book-fill display-4 text-success mb-3"></i>
                    <h5 class="card-title">Manage Books</h5>
                    <p class="card-text">Add, edit, or remove books</p>
                    <a href="{{ route('admin.books.index') }}" class="btn btn-success mt-auto">Go</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-people-fill display-4 text-info mb-3"></i>
                    <h5 class="card-title">Manage Users</h5>
                    <p class="card-text">View and manage users</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-info mt-auto">Go</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-journal-check display-4 text-warning mb-3"></i>
                    <h5 class="card-title">Manage Loans</h5>
                    <p class="card-text">View and manage book loans</p>
                    <a href="{{ route('admin.loans.index') }}" class="btn btn-warning mt-auto">Go</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
