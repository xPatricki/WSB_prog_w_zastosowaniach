<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
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
    
    public function index(Request $request)
    {
        $query = Book::query();
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }
        
        $books = $query->paginate(12);
        
        return view('books.index', compact('books'));
    }
    
    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }
    
    public function adminIndex()
    {
        $books = Book::paginate(10);
        return view('admin.books.index', compact('books'));
    }
    
    public function create()
    {
        return view('admin.books.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'isbn' => 'required|max:20|unique:books',
            'description' => 'nullable',
            'cover_image' => 'nullable|image|max:2048',
        ]);
        
        $book = new Book();
        $book->title = $validated['title'];
        $book->author = $validated['author'];
        $book->isbn = $validated['isbn'];
        $book->genre_id = 1; // Ustawiam domyślnie genre_id=1, należałoby też dodać wybór gatunku w formularzu
        $book->description = $validated['description'] ?? null;
        $book->status = 'available';
        $book->featured = $request->has('featured');
        
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $book->cover_image = $path;
        }
        
        $book->save();
        
        return redirect()->route('admin.books.index')
            ->with('success', 'Book added successfully.');
    }
    
    public function edit(Book $book)
    {
        return view('admin.books.edit', compact('book'));
    }
    
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'description' => 'nullable',
            'cover_image' => 'nullable|image|max:2048',
        ]);
        
        $book->title = $validated['title'];
        $book->author = $validated['author'];
        $book->description = $validated['description'] ?? null;
        $book->featured = $request->has('featured');
        
        if ($request->hasFile('cover_image')) {
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
    
    public function destroy(Book $book)
    {
        if ($book->loans()->where('returned_at', null)->exists()) {
            return redirect()->route('admin.books.index')
                ->with('error', 'Cannot delete book with active loans.');
        }
        
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        
        $book->delete();
        
        return redirect()->route('admin.books.index')
            ->with('success', 'Book deleted successfully.');
    }
}
