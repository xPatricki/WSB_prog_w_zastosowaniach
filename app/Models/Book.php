<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'author',
        'description',
        'cover_image',
        'status',
        'featured',
        'quantity',
    ];

    /**
     * Get the loans for the book.
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
    
    /**
     * Get the cover image URL.
     *
     * @return string
     */
    public function getCoverUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        
        return asset('images/placeholder-book.jpg');
    }
}

