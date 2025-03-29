<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\Loan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'totalBooks' => Book::count(),
            'registeredUsers' => User::count(),
            'activeLoans' => Loan::where('status', 'active')->count(),
            'overdueBooks' => Loan::where('status', 'active')
                ->where('due_at', '<', Carbon::now())
                ->count(),
        ];
        
        // Get monthly growth
        $lastMonthBooks = Book::where('created_at', '<', Carbon::now()->subMonth())->count();
        $lastMonthUsers = User::where('created_at', '<', Carbon::now()->subMonth())->count();
        
        $stats['bookGrowth'] = $lastMonthBooks > 0 
            ? round(($stats['totalBooks'] - $lastMonthBooks) / $lastMonthBooks * 100, 1) 
            : 100;
            
        $stats['userGrowth'] = $lastMonthUsers > 0 
            ? round(($stats['registeredUsers'] - $lastMonthUsers) / $lastMonthUsers * 100, 1) 
            : 100;
        
        // Get recent activity
        $recentActivity = Loan::with(['user', 'book'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get popular books
        $popularBooks = Book::withCount(['loans' => function ($query) {
                $query->where('borrowed_at', '>', Carbon::now()->subMonth());
            }])
            ->orderBy('loans_count', 'desc')
            ->take(5)
            ->get();
            
        return view('admin.dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'popularBooks' => $popularBooks,
        ]);
    }
    
    /**
     * Display the admin settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        return view('admin.settings');
    }
}

