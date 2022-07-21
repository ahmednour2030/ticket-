<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ticket extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * @var string[]
     */
    protected $fillable = [
      'title',
      'description',
      'price',
      'is_vip',
    ];

    /**
     * @var string[]
     */
    protected $appends= [
        'image'
    ];

    /**
     * @return string
     */
    public function getImageAttribute(): string
    {
        $image = $this->media()->first();
        return $image ? $image->getUrl() : '';
        //  return $this->getFirstMediaUrl('image');
        //  return $this->media->getFullUrl();
    }
}
