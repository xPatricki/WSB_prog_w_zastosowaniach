<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function index()
    {
        $activeLoans = Loan::with('book')
            ->where('user_id', Auth::id())
            ->whereNull('returned_at')
            ->get();
            
        $returnedLoans = Loan::with('book')
            ->where('user_id', Auth::id())
            ->whereNotNull('returned_at')
            ->orderBy('returned_at', 'desc')
            ->paginate(5);
            
        return view('loans.index', compact('activeLoans', 'returnedLoans'));
    }
    
    public function borrow(Book $book)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to borrow books.');
        }

        if (Auth::user()->role !== 'user') {
            return back()->with('error', 'Only regular users can borrow books.');
        }

        $hasBookAlready = Loan::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();
        
        if ($hasBookAlready) {
            return back()->with('error', 'You already have a copy of this book. You can only borrow one copy of each book.');
        }
        
        $currentlyBorrowedCount = Loan::where('user_id', Auth::id())
            ->whereNull('returned_at')
            ->count();
            
        if ($currentlyBorrowedCount >= 3) {
            return back()->with('error', 'You have reached the maximum limit of 3 borrowed books. Please return a book before borrowing another one.');
        }
        
        $activeLoans = $book->loans()->whereNull('returned_at')->count();
        $available = $book->quantity - $activeLoans;
        if ($book->status !== 'available' || $available < 1) {
            return back()->with('error', 'This book is not available for borrowing.');
        }
        
        $loan = new Loan();
        $loan->user_id = Auth::id();
        $loan->book_id = $book->id;
        $loan->borrowed_at = now();
        $loan->due_at = now()->addDays(14);
        $loan->save();
        
        $activeLoans++;
        if ($book->quantity - $activeLoans < 1) {
            $book->status = 'borrowed';
        }
        $book->save();
        
        return redirect()->route('loans.index')->with('success', 'Book borrowed successfully.');
    }
    
    public function returnBook(Loan $loan)
    {
        if ($loan->user_id !== Auth::id()) {
            return back()->with('error', 'You cannot return this book.');
        }
        
        if ($loan->returned_at !== null) {
            return back()->with('error', 'This book has already been returned.');
        }
        
        $loan->returned_at = now();
        $loan->save();
        
        $book = $loan->book;
        $book->status = 'available';
        $book->save();
        
        return back()->with('success', 'Book returned successfully.');
    }
}

