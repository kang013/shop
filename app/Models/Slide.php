<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Slide extends Model
{
    use HasFactory;

    protected $appends = ['image_url'];

    public function category()
    {
        return $this->belongsTo(SlideCategory::class);
    }

    public function getCategoryId($index)
    {
        return SlideCategory::where(['index_name'=>$index,'status'=>1])->value('id');
    }

    public function getImageUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        return \Storage::disk('public')->url($this->attributes['image']);
    }
}
