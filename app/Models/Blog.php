<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Writer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes as EloquentSoftDeletes;
use Illuminate\Support\Facades\Storage;


class Blog extends Model
{
    use HasFactory;
    use EloquentSoftDeletes;

    protected $fillable =[
        'name',
        'writer_id',
        'slug',
        'intro',
        'content',
        'photo',
    ];

    protected static function boot()
    {
        parent::boot();

        /** @var Model $model */
        static::updating(function ($model) {
            if ($model->isDirty('image') && ($model->getOriginal('photo') !== null)) {
                Storage::disk('public')->delete($model->getOriginal('photo'));
            }
        });
    }

     public function writer(): BelongsTo
    {
        return $this->belongsTo(Writer::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

}
