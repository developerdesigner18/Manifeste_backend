<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manifestation extends Model
{
    protected $table='manifestations';
    public $timestamps=false;
    protected $fillable = [
        'title',
        'address',
        'country',
        'province',
        'manifestation_hold',
        'start_time',
        'end_time',
        'type',
        'logo',
        'description'
    ];
}
