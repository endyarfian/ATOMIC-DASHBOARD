<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class MqttMessage extends Model
{
    protected $connection = 'atomic_access';
    protected $table = 'mqtt_messages';

    public $timestamps = false;
}
