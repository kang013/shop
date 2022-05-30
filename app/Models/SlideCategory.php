<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlideCategory extends Model
{
    use HasFactory;

    public function slide()
    {
        return $this->hasMany(slide::class);
    }
}
