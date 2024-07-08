<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'id','province_id','topic_id','event_type_id','company_id','deleted_at','user_id'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function province(){

        return $this->belongsTo(Province::class);
    }

    public function event_type(){

        return $this->belongsTo(EventType::class);
    }

    public function topic(){

        return $this->belongsTo(EventTopic::class);
    }


    public function producer(){

        return $this->belongsTo(Company::class,'company_id','id');
    }

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }
}
