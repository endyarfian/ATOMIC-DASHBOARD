<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $connection = 'atomic_access';
    protected $table = 'devices';
    protected $primaryKey = 'device_id';

    public $incrementing = false;
    protected $keyType = 'string';
}
