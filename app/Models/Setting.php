<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

        public   function getValue($c_type)
        {
            return Setting::where("type", $c_type)->get();
        }

        public static   function getValue2($c_type)
        {
            return Setting::where("type", $c_type)->get();
        }

        public static   function getSingleValue($c_type)
        {
            $data= Setting::where("type",$c_type)->first();
            return !empty($data)?$data->value:null;
        }


}
