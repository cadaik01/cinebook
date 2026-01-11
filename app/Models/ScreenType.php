<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreenType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
