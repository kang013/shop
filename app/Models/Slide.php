<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;


    public function category()
    {
        return $this->belongsTo(SlideCategory::class);
    }

    public function getCategoryId($index)
    {
        return SlideCategory::where(['index_name'=>$index,'status'=>1])->value('id');
    }
}
