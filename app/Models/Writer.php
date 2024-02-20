<?php

namespace App\Models;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Writer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable =[
        'writer_id',
        'name',
        'slug',
        'bio',
    ];

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }


    // public function blogs(): HasMany
    // {
    //     return $this->hasMany(Blog::class);
    // }

}
