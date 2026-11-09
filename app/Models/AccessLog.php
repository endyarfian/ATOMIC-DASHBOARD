<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $connection = 'atomic_access';
    protected $table = 'access_logs';

    public $timestamps = false;
}
