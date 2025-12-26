<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = ['name', 'genre', 'description'];

    public function tracks()
    {
        return $this->belongsToMany(Track::class)
                    ->withPivot('position')
                    ->withTimestamps()
                    ->orderBy('position');
    }
}
