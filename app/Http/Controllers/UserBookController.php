<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserBookController extends Controller
{
    /**
     * Display a listing of the user's borrowed books.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get current loans
        $currentLoans = $user->loans()
            ->with('book')
            ->where('status', 'active')
            ->get();
            
        // Get loan history
        $loanHistory = $user->loans()
            ->with('book')
            ->where('status', 'returned')
            ->orderBy('returned_at', 'desc')
            ->get();
            
        return view('my-books.index', [
            'currentLoans' => $currentLoans,
            'loanHistory' => $loanHistory,
        ]);
    }
    
    /**
     * Borrow a book.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function borrow(Book $book)
    {
        // Check if book is available
        if ($book->status !== 'available') {
            return redirect()->back()->with('error', 'This book is not available for borrowing.');
        }
        
        // Create a new loan
        $loan = new Loan();
        $loan->user_id = Auth::id();
        $loan->book_id = $book->id;
        $loan->borrowed_at = Carbon::now();
        $loan->due_at = Carbon::now()->addDays(14); // 2 weeks loan period
        $loan->status = 'active';
        $loan->save();
        
        // Update book status
        $book->status = 'borrowed';
        $book->save();
        
        return redirect()->route('my-books.index')->with('success', 'Book borrowed successfully.');
    }
    
    /**
     * Return a borrowed book.
     *
     * @param  \App\Models\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function return(Loan $loan)
    {
        // Check if loan belongs to user
        if ($loan->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You cannot return this book.');
        }
        
        // Update loan status
        $loan->status = 'returned';
        $loan->returned_at = Carbon::now();
        $loan->save();
        
        // Update book status
        $loan->book->status = 'available';
        $loan->book->save();
        
        return redirect()->route('my-books.index')->with('success', 'Book returned successfully.');
    }
    
    /**
     * Display the user's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return view('profile');
    }
}

