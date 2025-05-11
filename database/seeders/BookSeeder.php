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
        
        // Book seeding removed - books will be added manually through the bulk add feature
        // You can use the ISBN_LIST.txt file to bulk add books instead
        
        // Create various genre categories for books
        $genres = [
            'Fiction',
            'Science Fiction',
            'Mystery',
            'Romance',
            'Fantasy',
            'Biography',
            'History',
            'Children',
            'Young Adult',
            'Self-Help'
        ];
        
        foreach ($genres as $genreName) {
            if (Genre::where('name', $genreName)->count() === 0) {
                Genre::create(['name' => $genreName]);
            }
        }
    }
}
