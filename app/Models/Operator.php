<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    protected $connection = 'atomic_access';
    protected $table = 'operators';
    protected $primaryKey = 'operator_db_id';

    public $incrementing = true;
    protected $keyType = 'int';
}
