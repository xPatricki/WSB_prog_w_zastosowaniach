<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookManagementController extends Controller
{
    /**
     * Display a listing of the books.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Book::query();
        
        // Apply search filter
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($request->has('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }
        
        $books = $query->paginate(10);
        
        return view('admin.books.index', [
            'books' => $books,
        ]);
    }

    /**
     * Show the form for creating a new book.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $genres = Genre::all();
        
        return view('admin.books.create', [
            'genres' => $genres,
        ]);
    }

    /**
     * Store a newly created book in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'isbn' => 'required|unique:books|max:20',
            'genre_id' => 'required|exists:genres,id',
            'description' => 'nullable',
            'cover_image' => 'nullable|image|max:2048',
        ]);
        
        $book = new Book();
        $book->title = $validated['title'];
        $book->author = $validated['author'];
        $book->isbn = $validated['isbn'];
        $book->genre_id = $validated['genre_id'];
        $book->description = $validated['description'] ?? null;
        $book->status = 'available';
        
        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $book->cover_image = $path;
        }
        
        $book->save();
        
        return redirect()->route('admin.books.index')
            ->with('success', 'Book added successfully.');
    }

    /**
     * Show the form for editing the specified book.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        $genres = Genre::all();
        
        return view('admin.books.edit', [
            'book' => $book,
            'genres' => $genres,
        ]);
    }

    /**
     * Update the specified book in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'isbn' => 'required|max:20|unique:books,isbn,' . $book->id,
            'genre_id' => 'required|exists:genres,id',
            'description' => 'nullable',
            'cover_image' => 'nullable|image|max:2048',
        ]);
        
        $book->title = $validated['title'];
        $book->author = $validated['author'];
        $book->isbn = $validated['isbn'];
        $book->genre_id = $validated['genre_id'];
        $book->description = $validated['description'] ?? null;
        
        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            
            $path = $request->file('cover_image')->store('covers', 'public');
            $book->cover_image = $path;
        }
        
        $book->save();
        
        return redirect()->route('admin.books.index')
            ->with('success', 'Book updated successfully.');
    }

    /**
     * Remove the specified book from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        // Check if book has active loans
        if ($book->loans()->where('status', 'active')->exists()) {
            return redirect()->route('admin.books.index')
                ->with('error', 'Cannot delete book with active loans.');
        }
        
        // Delete cover image if exists
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        
        $book->delete();
        
        return redirect()->route('admin.books.index')
            ->with('success', 'Book deleted successfully.');
    }
}

