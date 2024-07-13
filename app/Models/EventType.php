<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;

    protected $hidden = [
        'id','created_at','updated_at'
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
