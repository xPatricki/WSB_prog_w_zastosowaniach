<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\Loan;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $totalBooks = Book::count();
        $loanedBooks = Book::where('status', 'borrowed')->count();
        $totalUsers = User::count();
        $activeLoans = Loan::where('status', 'active')->count();

        // Book status pie chart
        $bookStatus = [
            'available' => Book::where('status', 'available')->count(),
            'borrowed' => $loanedBooks
        ];

        // Books borrowed per month (last 12 months)
        $months = collect(range(0, 11))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        })->reverse()->values();
        $borrowedPerMonth = $months->mapWithKeys(function ($month) {
            $count = Loan::whereYear('borrowed_at', substr($month, 0, 4))
                ->whereMonth('borrowed_at', substr($month, 5, 2))
                ->count();
            return [$month => $count];
        });

        // New users per month (last 12 months)
        $usersPerMonth = $months->mapWithKeys(function ($month) {
            $count = User::whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2))
                ->count();
            return [$month => $count];
        });

        return view('admin.dashboard', [
            'totalBooks' => $totalBooks,
            'loanedBooks' => $loanedBooks,
            'totalUsers' => $totalUsers,
            'activeLoans' => $activeLoans,
            'bookStatus' => $bookStatus,
            'months' => $months,
            'borrowedPerMonth' => $borrowedPerMonth,
            'usersPerMonth' => $usersPerMonth,
        ]);
    }
}
