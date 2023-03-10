<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['title','body','icon','seen','user_id'];

    public static function notify($title, $body='', $icon) {
        return Notification::create([
            "title" => $title,
            "body" => $body,
            "seen" => '0',
            "icon" => $icon,
            "user_id" => optional(request()->user())->id
        ]);
    }

    public static function notifyUser($title, $body='', $icon, $user) {
        return Notification::create([
            "title" => $title,
            "body" => $body,
            "seen" => 0,
            "icon" => $icon,
            "user_id" => $user
        ]);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
}
