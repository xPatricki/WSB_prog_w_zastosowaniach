<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
        

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }
        

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

        // Handle cover image upload or URL
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

            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $path = $request->file('cover_image')->store('covers', 'public');
            $book->cover_image = $path;
            $book->cover_image_url = null;
        } elseif ($request->filled('cover_image_url')) {

            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            

            $coverUrl = $request->input('cover_image_url');
            $localPath = $this->downloadAndStoreCoverImage($coverUrl, $book->isbn);
            
            if ($localPath) {

                $book->cover_image = $localPath;
                $book->cover_image_url = null;

            } else {

                $book->cover_image_url = $coverUrl;
                $book->cover_image = null;

            }
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

        if ($book->loans()->where('status', 'active')->exists()) {
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

    /**
     * Bulk add books from a list of ISBNs
     * Each book will be synchronized with external sources automatically.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkAdd(Request $request)
    {

        $validated = $request->validate([
            'isbns' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'featured' => 'sometimes|boolean',
        ]);
        


        $isbns = preg_split('/[\n\r,\s]+/', $validated['isbns'], -1, PREG_SPLIT_NO_EMPTY);
        

        if (empty($isbns)) {

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
            

            if (\App\Models\Book::where('isbn', $isbn)->exists()) {
                $messages[] = "ISBN {$isbn}: Already exists in database";
                $skipped++;
                continue;
            }
            
            try {

                
                // Call the OpenLibrary API
                $apiUrl = "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data";
                
                $response = file_get_contents($apiUrl);
                $bookData = json_decode($response, true);
                
                if (!isset($bookData["ISBN:{$isbn}"])) {
                    $messages[] = "ISBN {$isbn}: No data found";
                    $notFound++;
                    continue;
                }
                
                $openLibraryData = $bookData["ISBN:{$isbn}"];
                

                $book = new \App\Models\Book();
                $book->isbn = $isbn;
                $book->status = 'available';
                $book->quantity = $quantity;
                $book->featured = $featured;
                $book->genre_id = 1;
                

                if (isset($openLibraryData['title'])) {
                    $book->title = $openLibraryData['title'];
                } else {
                    $book->title = "Book " . $isbn;
                }
                
                if (isset($openLibraryData['authors']) && is_array($openLibraryData['authors']) && !empty($openLibraryData['authors'])) {
                    $book->author = implode(', ', array_map(function($author) {
                        return $author['name'];
                    }, $openLibraryData['authors']));
                } else {
                    $book->author = "Unknown";
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
                    $book->save();
                    $added++;
                    $messages[] = "ISBN {$isbn}: Added successfully";
                } catch (\Exception $saveEx) {
                    throw $saveEx;
                }
                
            } catch (\Exception $e) {

                $messages[] = "ISBN {$isbn}: Error - " . $e->getMessage();
                $skipped++;
            }
        }
        
        $status = $added > 0 ? 'success' : 'warning';
        $message = "Added {$added} books" . 
                  ($skipped > 0 ? ", skipped {$skipped} duplicates" : "") .
                  ($notFound > 0 ? ", {$notFound} not found" : "");
        

        session()->flash('bulk_messages', $messages);
        
        return redirect()->route('admin.books.index')
                ->with($status, $message);
    }
    
    /**
     * Helper method to download external cover images and store them locally
     * Returns the local storage path or null if download fails
     * 
     * @param string $imageUrl The external URL of the image
     * @param string $isbn The ISBN of the book (for naming)
     * @return string|null The local storage path if successful, null if failed
     */
    protected function downloadAndStoreCoverImage($imageUrl, $isbn)
    {
        if (empty($imageUrl)) {
            return null;
        }
        
        try {

            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'covers/' . $isbn . '_' . Str::random(8) . '.' . $extension;
            

            $response = Http::timeout(5)->get($imageUrl);
            
            if ($response->successful()) {

                Storage::disk('public')->put($filename, $response->body());
                


                
                return $filename;
            }
        } catch (\Exception $e) {
            // Silent fail on production
        }
        
        return null;
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

