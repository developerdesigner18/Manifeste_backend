<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poster extends Model
{
    protected $table='posters';
    public $timestamps=false;

    protected $fillable = [
        'posterimage',
        'shapeid',
        'description',
        'color'
    ];
}
