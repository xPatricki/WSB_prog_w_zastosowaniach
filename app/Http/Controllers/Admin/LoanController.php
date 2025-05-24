<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Display a listing of the loans.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'due_at');
        $sortDirection = $request->input('sort_direction', 'asc');
        $status = $request->input('status', 'active');
        

        $loansQuery = Loan::with(['user', 'book']);
        

        if ($status === 'active') {
            $loansQuery->whereNull('returned_at');
        } elseif ($status === 'returned') {
            $loansQuery->whereNotNull('returned_at');
        }
        

        if ($search) {
            $loansQuery->where(function($query) use ($search) {
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('book', function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%");
                });
            });
        }
        

        if ($sortBy === 'due_at') {
            $loansQuery->orderBy('due_at', $sortDirection);
        } elseif ($sortBy === 'borrowed_at') {
            $loansQuery->orderBy('borrowed_at', $sortDirection);
        } elseif ($sortBy === 'user') {
            $loansQuery->join('users', 'loans.user_id', '=', 'users.id')
                    ->orderBy('users.name', $sortDirection)
                    ->select('loans.*');
        } elseif ($sortBy === 'book') {
            $loansQuery->join('books', 'loans.book_id', '=', 'books.id')
                    ->orderBy('books.title', $sortDirection)
                    ->select('loans.*');
        }
        

        $loans = $loansQuery->paginate(10)->withQueryString();
        

        return view('admin.loans.index', compact('loans', 'search', 'sortBy', 'sortDirection', 'status'));
    }
    
    /**
     * Cancel a loan (return the book early)
     *
     * @param  \App\Models\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function cancel(Loan $loan)
    {

        if ($loan->returned_at) {
            return back()->with('error', 'This book has already been returned.');
        }
        

        $loan->returned_at = now();
        $loan->save();
        

        $book = $loan->book;
        $book->status = 'available';
        $book->save();
        
        return back()->with('success', 'Loan has been cancelled and book marked as returned.');
    }
    
    /**
     * Modify loan due date
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function modifyDueDate(Request $request, Loan $loan)
    {

        $request->validate([
            'due_at' => 'required|date|after:borrowed_at',
        ]);
        

        if ($loan->returned_at) {
            return back()->with('error', 'Cannot modify due date for already returned books.');
        }
        

        $loan->due_at = Carbon::parse($request->due_at);
        $loan->save();
        
        return back()->with('success', 'Loan due date has been updated.');
    }
}
