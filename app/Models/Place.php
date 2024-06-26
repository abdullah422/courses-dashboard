<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;
    public $fillable=[
        'id',
        'name',
    ];
    public function users(){
        return $this->hasMany(User::class);
    }
}
