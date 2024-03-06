<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'series',
        'review',
        'cover',
        'status',
        'read_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'book_user')->withPivot('rating', 'read_at');
    }
}
