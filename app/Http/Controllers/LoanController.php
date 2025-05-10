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
            ->get();
            
        return view('loans.index', compact('activeLoans', 'returnedLoans'));
    }
    
    public function borrow(Book $book)
    {
        $activeLoans = $book->loans()->whereNull('returned_at')->count();
        $available = $book->quantity - $activeLoans;
        if ($book->status !== 'available' || $available < 1) {
            return back()->with('error', 'This book is not available for borrowing.');
        }
        
        $loan = new Loan();
        $loan->user_id = Auth::id();
        $loan->book_id = $book->id;
        $loan->borrowed_at = now();
        $loan->due_at = now()->addDays(14); // 2 weeks loan period
        $loan->save();
        
        // If after this loan there are no copies left, set status to 'borrowed'
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

