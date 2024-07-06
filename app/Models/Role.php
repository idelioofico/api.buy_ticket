<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded=[];

    public function users(){
      return $this->hasMany(User::class);
    }
    public function permission(){
      return $this->belongsToMany(Permission::class);
    }

    public function isSuper(){

      if($this->name=="super"){
        return true;
      }else{
        return false;
      }
    }

    public function isAdmin(){

      if($this->name=="admin"){
        return true;
      }else{
        return false;
      }
    }


    public function hasPermission($permission){

      if($this->permission()->where("description",$permission)->first()){

          return true;
      }else{
        return false;
      }

    }

    public function hasAnypermission($permission){

      if($this->permission()->whereIn("description","$permission")->first()){
          return true;
      }else{
          return false;
      }

    }

    protected static function booted()
    {
      // static::addGlobalScope(new EstadoScope);
    }
}
