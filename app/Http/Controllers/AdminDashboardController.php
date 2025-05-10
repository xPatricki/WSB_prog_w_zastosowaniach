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
        $expiredLoans = Loan::where('status', 'active')->where('due_at', '<', Carbon::now())->count();

        // Book status pie chart
        $bookStatus = [
            'available' => Book::where('status', 'available')->count(),
            'borrowed' => $loanedBooks
        ];

        // Show only the last 6 months (including current), no future months
        $months = collect(range(0, 5))->map(function ($i) {
            return Carbon::now()->subMonths(5 - $i)->format('F');
        });
        $booksBorrowedPerMonth = $months->map(function ($monthName, $i) {
            $date = Carbon::now()->subMonths(5 - $i);
            return Loan::whereYear('borrowed_at', $date->year)
                ->whereMonth('borrowed_at', $date->month)
                ->count();
        });
        $usersPerMonth = $months->map(function ($monthName, $i) {
            $date = Carbon::now()->subMonths(5 - $i);
            return User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        });

        return view('admin.dashboard', [
            'totalBooks' => $totalBooks,
            'loanedBooks' => $loanedBooks,
            'totalUsers' => $totalUsers,
            'activeLoans' => $activeLoans,
            'expiredLoans' => $expiredLoans,
            'bookStatus' => $bookStatus,
            'months' => $months,
            'booksBorrowedPerMonth' => $booksBorrowedPerMonth,
            'usersPerMonth' => $usersPerMonth,
        ]);
    }
}
