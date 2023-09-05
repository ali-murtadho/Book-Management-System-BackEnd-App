<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $table = "categories";
    protected $primarykey = "id";
    protected $keytype = "int";
    public $timestamps = true;
    public $incrementing = true;

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, "book_id", "id");
    }
}
