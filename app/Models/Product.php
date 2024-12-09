<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'category_id', 'image_path'];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    // Accessor for full image URL (optional)
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
