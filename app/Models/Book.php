<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        'cover_image_url',
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
     * The genres that belong to the book.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }
    
    /**
     * Helper method to download and cache a cover image from an external URL
     *
     * @param string $url External cover image URL
     * @return string|null Path to the locally stored image or null if failed
     */
    protected function downloadAndCacheCover($url)
    {
        if (empty($url)) {
            return null;
        }
        
        try {
            // Generate a unique filename based on ISBN and URL
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'covers/' . $this->isbn . '_' . Str::random(8) . '.' . $extension;
            
            // Download the image
            $fileContents = @file_get_contents($url);
            
            if ($fileContents === false) {
                \Log::warning("Could not download cover image from {$url}");
                return null;
            }
            
            // Store the file
            if (Storage::disk('public')->put($filename, $fileContents)) {
                \Log::info("Downloaded cover image for book ID {$this->id} from {$url} to {$filename}");
                return $filename;
            }
        } catch (\Exception $e) {
            \Log::error("Error downloading cover for book ID {$this->id}: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Get the cover image URL.
     *
     * @return string
     */
    public function getCoverUrlAttribute()
    {
        // If we have a local image, use it
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        
        // If we have an external URL but haven't downloaded it yet
        if (!empty($this->cover_image_url)) {
            // Try to download it
            $localPath = $this->downloadAndCacheCover($this->cover_image_url);
            
            // If downloaded successfully, update the database
            if ($localPath) {
                $this->cover_image = $localPath;
                $this->cover_image_url = null;
                $this->save();
                
                return asset('storage/' . $localPath);
            }
            
            // Fallback to external URL
            return $this->cover_image_url;
        }
        
        // Default placeholder
        return asset('images/placeholder-book.jpg');
    }
}

