<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $table = "notifications";
    protected $fillable=['notifiable_id','read_at','comment_id','created_at','updated_at'];
    protected $hidden =['created_at','updated_at'];
    public $timestamps = true;

    public function comments(){
        return $this->belongsTo('App\Models\Comment','comment_id');
    }

}
