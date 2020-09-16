<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Message extends Model
{
    use Notifiable;
    protected $fillable = [
        'content', 'from_id', 'to_id', 'created_at', 'read_at'
    ];

    public $timestamps = false;

    protected $dates = ['created_at', 'read_at'];

    public function from(){
        return $this->belongsTo(User::class, 'from_id');
    }
}
