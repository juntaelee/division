<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $connection = 'ServiceDB_XKeeperDB';
    protected $table = 'TB-Status';
    protected $primaryKey = 'Status';

    public $timestamps = false;
}
