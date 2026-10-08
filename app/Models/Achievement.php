<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// Achievement model, to be filled later
class Achievement extends Model
{
    protected $fillable = ['title', 'subtitle', 'image_url'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
