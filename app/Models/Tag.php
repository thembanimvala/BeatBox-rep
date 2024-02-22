<?php

namespace App\Models;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
    ];

    public function blog(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class);
    }
}
