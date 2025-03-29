<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredBooks = Book::where('featured', true)->take(4)->get();
        
        return view('home', [
            'featuredBooks' => $featuredBooks
        ]);
    }
}

