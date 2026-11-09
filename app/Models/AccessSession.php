<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class AccessSession extends Model
{
    protected $connection = 'atomic_access';
    protected $table = 'access_sessions';
    protected $primaryKey = 'session_id';

    public $incrementing = false;
    protected $keyType = 'string';
}
