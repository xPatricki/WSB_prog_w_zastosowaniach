<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'description' => 'The story of racial injustice and the loss of innocence in the American South.',
                'featured' => true,
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'description' => 'A dystopian novel set in a totalitarian society.',
                'featured' => true,
            ],
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'description' => 'A story of wealth, love, and the American Dream in the 1920s.',
                'featured' => true,
            ],
            [
                'title' => 'Pride and Prejudice',
                'author' => 'Jane Austen',
                'description' => 'A romantic novel about the importance of marrying for love.',
                'featured' => true,
            ],
            [
                'title' => 'The Catcher in the Rye',
                'author' => 'J.D. Salinger',
                'description' => 'A story of teenage angst and alienation.',
                'featured' => false,
            ],
            [
                'title' => 'The Hobbit',
                'author' => 'J.R.R. Tolkien',
                'description' => 'A fantasy novel about the adventures of a hobbit.',
                'featured' => false,
            ],
            [
                'title' => 'Harry Potter and the Philosopher\'s Stone',
                'author' => 'J.K. Rowling',
                'description' => 'The first book in the Harry Potter series.',
                'featured' => false,
            ],
            [
                'title' => 'The Lord of the Rings',
                'author' => 'J.R.R. Tolkien',
                'description' => 'An epic fantasy novel about the quest to destroy the One Ring.',
                'featured' => false,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}

