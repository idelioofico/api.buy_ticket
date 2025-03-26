<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{

    use HasFactory;
    protected $table = "settings";
    public $timestamps=false;

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
