<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LoadingItemPhoto extends Model
{
    protected $table = 'loading_item_photos';

    protected $fillable = [
        'loading_item_id', 'order_id', 'path', 'thumb_path', 'uploaded_by',
    ];

    protected static function booted(): void
    {
        static::deleting(function (LoadingItemPhoto $photo) {
            $disk = Storage::disk('public');
            if ($photo->path && $disk->exists($photo->path)) {
                $disk->delete($photo->path);
            }
            if ($photo->thumb_path && $disk->exists($photo->thumb_path)) {
                $disk->delete($photo->thumb_path);
            }
        });
    }

    public function loadingItem()
    {
        return $this->belongsTo(LoadingItem::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->path);
    }

    public function getThumbUrlAttribute(): string
    {
        return asset('storage/'.$this->thumb_path);
    }
}
