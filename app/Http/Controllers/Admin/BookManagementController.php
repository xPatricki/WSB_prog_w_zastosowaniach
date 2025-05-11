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
     * Bulk add books from a list of ISBNs
     * Each book will be synchronized with external sources automatically.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkAdd(Request $request)
    {
        file_put_contents(storage_path('logs/manual_debug.log'), "[bulkAdd] called at ".now()."\n[bulkAdd] request: ".json_encode($request->all())."\n", FILE_APPEND);
        
        $validated = $request->validate([
            'isbns' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'featured' => 'sometimes|boolean',
        ]);
        
        file_put_contents(storage_path('logs/manual_debug.log'), "[bulkAdd] validated data: ".json_encode($validated)."\n", FILE_APPEND);
        
        // Parse ISBNs from input (separated by commas or newlines)
        $isbns = preg_split('/[\n\r,\s]+/', $validated['isbns'], -1, PREG_SPLIT_NO_EMPTY);
        
        file_put_contents(storage_path('logs/manual_debug.log'), "[bulkAdd] parsed ISBNs: ".json_encode($isbns)."\n", FILE_APPEND);
        
        if (empty($isbns)) {
            file_put_contents(storage_path('logs/manual_debug.log'), "[bulkAdd] Error: No valid ISBNs provided\n", FILE_APPEND);
            return redirect()->route('admin.books.index')
                ->with('error', 'No valid ISBNs provided.');
        }
        
        $quantity = $validated['quantity'];
        $featured = isset($validated['featured']);
        
        $added = 0;
        $skipped = 0;
        $notFound = 0;
        $messages = [];
        
        foreach ($isbns as $isbn) {
            $isbn = trim($isbn);
            if (empty($isbn)) continue;
            
            // Check if book already exists
            if (\App\Models\Book::where('isbn', $isbn)->exists()) {
                $messages[] = "ISBN {$isbn}: Already exists in database";
                $skipped++;
                continue;
            }
            
            try {
                file_put_contents(storage_path('logs/manual_debug.log'), "[bulkAdd] Processing ISBN: {$isbn}\n", FILE_APPEND);
                
                // Call the OpenLibrary API
                $apiUrl = "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data";
                file_put_contents(storage_path('logs/manual_debug.log'), "[bulkAdd] Calling API: {$apiUrl}\n", FILE_APPEND);
                
                $response = file_get_contents($apiUrl);
                $bookData = json_decode($response, true);
                
                file_put_contents(storage_path('logs/manual_debug.log'), "[bulkAdd] API response: ".substr(json_encode($bookData), 0, 300)."...\n", FILE_APPEND);
                
                if (!isset($bookData["ISBN:{$isbn}"])) {
                    $messages[] = "ISBN {$isbn}: No data found";
                    $notFound++;
                    file_put_contents(storage_path('logs/manual_debug.log'), "[bulkAdd] No data found for ISBN: {$isbn}\n", FILE_APPEND);
                    continue;
                }
                
                $openLibraryData = $bookData["ISBN:{$isbn}"];
                
                // Create new book
                $book = new \App\Models\Book();
                $book->isbn = $isbn;
                $book->status = 'available';
                $book->quantity = $quantity;
                $book->featured = $featured;
                $book->genre_id = 1; // Default to genre ID 1
                
                file_put_contents(storage_path('logs/manual_debug.log'), "[bulkAdd] Creating new book with ISBN: {$isbn}\n", FILE_APPEND);
                
                // Set data from API
                if (isset($openLibraryData['title'])) {
                    $book->title = $openLibraryData['title'];
                } else {
                    $book->title = "Book " . $isbn; // Fallback title
                }
                
                if (isset($openLibraryData['authors']) && is_array($openLibraryData['authors']) && !empty($openLibraryData['authors'])) {
                    $book->author = implode(', ', array_map(function($author) {
                        return $author['name'];
                    }, $openLibraryData['authors']));
                } else {
                    $book->author = "Unknown"; // Fallback author
                }
                
                if (isset($openLibraryData['notes'])) {
                    if (is_array($openLibraryData['notes']) && isset($openLibraryData['notes']['value'])) {
                        $book->description = $openLibraryData['notes']['value'];
                    } elseif (is_string($openLibraryData['notes'])) {
                        $book->description = $openLibraryData['notes'];
                    }
                }
                
                // Handle cover image
                if (isset($openLibraryData['cover']) && isset($openLibraryData['cover']['large'])) {
                    $book->cover_image_url = $openLibraryData['cover']['large'];
                } else {
                    // Try the OpenLibrary covers API as fallback
                    $book->cover_image_url = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg";
                }
                
                try {
                    file_put_contents(storage_path('logs/manual_debug.log'), "[bulkAdd] Before save, book data: ".json_encode($book->toArray())."\n", FILE_APPEND);
                    $book->save();
                    file_put_contents(storage_path('logs/manual_debug.log'), "[bulkAdd] Book saved successfully with ID: {$book->id}\n", FILE_APPEND);
                    $added++;
                    $messages[] = "ISBN {$isbn}: Added successfully";
                } catch (\Exception $saveEx) {
                    file_put_contents(storage_path('logs/manual_debug.log'), "[bulkAdd] Error saving book: {$saveEx->getMessage()}\n", FILE_APPEND);
                    throw $saveEx; // Re-throw to be caught by the outer catch block
                }
                
            } catch (\Exception $e) {
                \Log::error("[Bulk Add] Error adding ISBN {$isbn}: " . $e->getMessage());
                $messages[] = "ISBN {$isbn}: Error - " . $e->getMessage();
                $skipped++;
            }
        }
        
        $status = $added > 0 ? 'success' : 'warning';
        $message = "Added {$added} books" . 
                  ($skipped > 0 ? ", skipped {$skipped} duplicates" : "") .
                  ($notFound > 0 ? ", {$notFound} not found" : "");
        
        // Store detailed messages in session flash data
        session()->flash('bulk_messages', $messages);
        
        return redirect()->route('admin.books.index')
                ->with($status, $message);
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
        $updatedCount = 0;
        $errorCount = 0;
        
        foreach ($ids as $id) {
            $book = \App\Models\Book::find($id);
            if (!$book) {
                $results[$id] = ['status' => 'error', 'message' => 'Book not found'];
                $errorCount++;
                continue;
            }
            
            try {
                \Log::info("[Book Sync] Syncing book ID $id ({$book->title}) with ISBN: {$book->isbn}");
                
                // Skip books without ISBN
                if (empty($book->isbn)) {
                    $results[$id] = ['status' => 'error', 'message' => 'Missing ISBN'];
                    $errorCount++;
                    continue;
                }
                
                // Call the OpenLibrary API
                $isbn = trim($book->isbn);
                $response = file_get_contents("https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data");
                
                if (!$response) {
                    $results[$id] = ['status' => 'error', 'message' => 'API connection failed'];
                    $errorCount++;
                    continue;
                }
                
                $bookData = json_decode($response, true);
                
                if (!isset($bookData["ISBN:{$isbn}"])) {
                    $results[$id] = ['status' => 'error', 'message' => 'No data found for ISBN'];
                    $errorCount++;
                    continue;
                }
                
                $openLibraryData = $bookData["ISBN:{$isbn}"];
                
                // Update book data
                if (isset($openLibraryData['title'])) {
                    $book->title = $openLibraryData['title'];
                }
                
                if (isset($openLibraryData['authors']) && is_array($openLibraryData['authors']) && !empty($openLibraryData['authors'])) {
                    $book->author = implode(', ', array_map(function($author) {
                        return $author['name'];
                    }, $openLibraryData['authors']));
                }
                
                if (isset($openLibraryData['notes'])) {
                    if (is_array($openLibraryData['notes']) && isset($openLibraryData['notes']['value'])) {
                        $book->description = $openLibraryData['notes']['value'];
                    } elseif (is_string($openLibraryData['notes'])) {
                        $book->description = $openLibraryData['notes'];
                    }
                }
                
                // Handle cover image
                if (isset($openLibraryData['cover']) && isset($openLibraryData['cover']['large'])) {
                    $book->cover_image_url = $openLibraryData['cover']['large'];
                } else {
                    // Try the OpenLibrary covers API as fallback
                    $book->cover_image_url = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg";
                }
                
                $book->updated_at = now();
                $book->save();
                
                $results[$id] = ['status' => 'success', 'message' => 'Book updated from OpenLibrary API'];
                $updatedCount++;
                
            } catch (\Exception $e) {
                \Log::error("[Book Sync] Error syncing book ID $id: " . $e->getMessage());
                $results[$id] = ['status' => 'error', 'message' => 'Sync error: ' . $e->getMessage()];
                $errorCount++;
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => "Synced {$updatedCount} books successfully" . ($errorCount > 0 ? ", {$errorCount} with errors." : "."),
            'results' => $results
        ]);
    }
}

