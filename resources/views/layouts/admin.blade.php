@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Admin Navigation</div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.books.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                        Books
                    </a>
                    <a href="{{ route('admin.loans.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.loans.*') ? 'active' : '' }}">
                        Loans
                    </a>
                    @if(strtolower(auth()->user()->role) === 'admin')
                        <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            Users
                        </a>
                        <a href="{{ route('admin.settings') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                            Settings
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-9">
            @yield('admin-content')
        </div>
    </div>
</div>
@endsection

