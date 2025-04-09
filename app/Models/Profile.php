<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $guarded = [];

    public function profileImage()
    {
        $imagePath = ($this->image) ? $this->image : 'profile/default-avatar.png';
        return '/storage/' . $imagePath;
    }

    public function backgroundImage()
    {
        return $this->background ? '/storage/' . $this->background : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}



