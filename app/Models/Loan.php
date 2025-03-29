<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'book_id',
        'borrowed_at',
        'due_at',
        'returned_at',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /**
     * Get the user that owns the loan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that owns the loan.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    
    /**
     * Check if the loan is overdue.
     *
     * @return bool
     */
    public function getIsOverdueAttribute()
    {
        return $this->returned_at === null && $this->due_at < Carbon::now();
    }
    
    /**
     * Get the time remaining until the due date.
     *
     * @return array|null
     */
    public function getTimeRemainingAttribute()
    {
        if ($this->returned_at !== null) {
            return null;
        }
        
        $now = Carbon::now();
        $due = $this->due_at;
        
        if ($now > $due) {
            return [
                'overdue' => true,
                'days' => 0,
                'hours' => 0,
            ];
        }
        
        $diff = $due->diff($now);
        
        return [
            'overdue' => false,
            'days' => $diff->d,
            'hours' => $diff->h,
        ];
    }
}

