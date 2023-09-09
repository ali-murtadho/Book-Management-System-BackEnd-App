<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $table = "books";
    protected $primarykey = "id";
    protected $keytype = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'judul', 'tahun_terbit', 'penulis'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Book::class, "user_id", "id");
    }

    public function category(): HasMany
    {
        return $this->hasMany(Category::class, "book_id", "id");
    }
}
