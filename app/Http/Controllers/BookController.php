<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
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
    
    public function adminIndex(Request $request)
    {
     
        $perPage = $request->input('per_page', 5);
        
        if (!in_array($perPage, [5, 10, 20, 50])) {
            $perPage = 5;
        }
        
        $books = Book::paginate($perPage)->withQueryString();
        return view('admin.books.index', compact('books', 'perPage'));
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
            'quantity' => 'required|integer|min:1',
        ]);

        $book = new Book();
        $book->title = $validated['title'];
        $book->author = $validated['author'];
        $book->isbn = $validated['isbn'];
        $book->genre_id = 1;
        $book->description = $validated['description'] ?? null;
        $book->status = 'available';
        $book->featured = $request->has('featured');
        $book->quantity = $validated['quantity'];

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $book->cover_image = $path;
            $book->cover_image_url = null;
        } else {
            $book->cover_image = null;
            $book->cover_image_url = $request->input('cover_image_url');
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
            'quantity' => 'required|integer|min:1',
        ]);
        
        $book->title = $validated['title'];
        $book->author = $validated['author'];
        $book->description = $validated['description'] ?? null;
        $book->featured = $request->has('featured');
        $book->quantity = $validated['quantity'];
        
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

    public function bulkSync(\Illuminate\Http\Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No books selected.'], 400);
        }
        Book::whereIn('id', $ids)->update(['updated_at' => now()]);
        return response()->json(['status' => 'success', 'message' => 'Selected books synced.']);
    }

    public function bulkDelete(\Illuminate\Http\Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No books selected.'], 400);
        }
        $failed = [];
        foreach (Book::whereIn('id', $ids)->get() as $book) {
            if ($book->loans()->whereNull('returned_at')->exists()) {
                $failed[] = $book->id;
                continue;
            }
            if ($book->cover_image) {
                \Storage::disk('public')->delete($book->cover_image);
            }
            $book->delete();
        }
        if ($failed) {
            return response()->json(['status' => 'partial', 'message' => 'Some books could not be deleted due to active loans.', 'failed' => $failed]);
        }
        return response()->json(['status' => 'success', 'message' => 'Selected books deleted.']);
    }
}
