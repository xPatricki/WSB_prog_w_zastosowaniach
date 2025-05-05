<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Genre;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure at least one genre exists
        $genre = Genre::first();
        if (!$genre) {
            $genre = Genre::create(['name' => 'Default']);
        }

        $books = [
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'isbn' => '9780061120084',
                'genre_id' => $genre->id,
                'description' => 'The story of racial injustice and the loss of innocence in the American South.',
                'featured' => true,
                'status' => 'available',
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'isbn' => '9780451524935',
                'genre_id' => $genre->id,
                'description' => 'A dystopian novel set in a totalitarian society.',
                'featured' => true,
                'status' => 'available',
            ],
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'isbn' => '9780743273565',
                'genre_id' => $genre->id,
                'description' => 'A story of wealth, love, and the American Dream in the 1920s.',
                'featured' => true,
                'status' => 'available',
            ],
            [
                'title' => 'Pride and Prejudice',
                'author' => 'Jane Austen',
                'isbn' => '9780141439518',
                'genre_id' => $genre->id,
                'description' => 'A romantic novel about the importance of marrying for love.',
                'featured' => true,
                'status' => 'available',
            ],
            [
                'title' => 'The Catcher in the Rye',
                'author' => 'J.D. Salinger',
                'isbn' => '9780316769488',
                'genre_id' => $genre->id,
                'description' => 'A story of teenage angst and alienation.',
                'featured' => false,
                'status' => 'available',
            ],
            [
                'title' => 'The Hobbit',
                'author' => 'J.R.R. Tolkien',
                'isbn' => '9780547928227',
                'genre_id' => $genre->id,
                'description' => 'A fantasy novel about the adventures of a hobbit.',
                'featured' => false,
                'status' => 'available',
            ],
            [
                'title' => 'Harry Potter and the Philosopher\'s Stone',
                'author' => 'J.K. Rowling',
                'isbn' => '9780747532699',
                'genre_id' => $genre->id,
                'description' => 'The first book in the Harry Potter series.',
                'featured' => false,
                'status' => 'available',
            ],
            [
                'title' => 'The Lord of the Rings',
                'author' => 'J.R.R. Tolkien',
                'isbn' => '9780618640157',
                'genre_id' => $genre->id,
                'description' => 'An epic fantasy novel about the quest to destroy the One Ring.',
                'featured' => false,
                'status' => 'available',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
