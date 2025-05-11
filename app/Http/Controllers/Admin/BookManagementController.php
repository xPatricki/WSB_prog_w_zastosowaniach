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
        file_put_contents(storage_path('logs/manual_debug.log'), "[store] called at ".now()."\n[store] request: ".json_encode($request->all())."\n", FILE_APPEND);

        \Log::info('Book store step 1: incoming request', [
            'request_all' => $request->all(),
        ]);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'isbn' => 'required|unique:books|max:20',
            'genre_id' => 'required|exists:genres,id',
            'description' => 'nullable',
            'cover_image' => 'nullable|image|max:2048',
        ]);
        \Log::info('Book store step 2: validated', [
            'validated' => $validated,
            'cover_image_url_from_request' => $request->input('cover_image_url'),
        ]);

        $book = new Book();
        $book->title = $validated['title'];
        $book->author = $validated['author'];
        $book->isbn = $validated['isbn'];
        $book->genre_id = $validated['genre_id'];
        $book->description = $validated['description'] ?? null;
        \Log::info('Book store step 3: after base properties', [
            'cover_image_url_from_request' => $request->input('cover_image_url'),
            'cover_image_url_on_book' => $book->cover_image_url ?? null,
        ]);

        // Set status
        $book->status = 'available';

        // Handle cover image upload or URL
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $book->cover_image = $path;
            $book->cover_image_url = null;
            \Log::info('Book store step 4: after file upload', [
                'cover_image' => $book->cover_image,
                'cover_image_url' => $book->cover_image_url,
            ]);
        } else {
            $book->cover_image = null;
            $book->cover_image_url = $request->input('cover_image_url');
            \Log::info('Book store step 5: after setting url', [
                'cover_image_url' => $book->cover_image_url,
            ]);
        }

        \Log::info('Book store step 6: before save', [
            'book' => $book->toArray(),
        ]);

        $book->save();

        \Log::info('Book store step 7: after save', [
            'book' => $book->toArray(),
        ]);

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
if ($request->filled('cover_image_url')) {
    $book->cover_image_url = $request->input('cover_image_url');
}
        
        // Handle cover image upload or URL
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $path = $request->file('cover_image')->store('covers', 'public');
            $book->cover_image = $path;
            $book->cover_image_url = null; // Clear URL if file uploaded
        } elseif ($request->filled('cover_image_url')) {
            // Delete old image if exists
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $book->cover_image_url = $request->input('cover_image_url');
            $book->cover_image = null;
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

    /**
     * Bulk sync selected books with external source.
     * Accepts array of book IDs as 'ids' in POST body.
     * Returns JSON with per-book sync results.
     */
    public function bulkSync(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No books selected.'], 400);
        }
        $results = [];
        foreach ($ids as $id) {
            $book = \App\Models\Book::find($id);
            if (!$book) {
                $results[$id] = ['status' => 'error', 'message' => 'Book not found'];
                continue;
            }
            // Simulate sync (replace with real logic)
            // For now, just log and update timestamp
            \Log::info("[Book Sync] Syncing book ID $id ({$book->title})");
            $book->updated_at = now();
            $book->save();
            // Simulate external API call delay
            usleep(100000); // 0.1s
            $results[$id] = ['status' => 'success', 'message' => 'Synced'];
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Selected books synced.',
            'results' => $results
        ]);
    }
}

