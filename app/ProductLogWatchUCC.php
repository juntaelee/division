<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductLogWatchUCC extends Model
{
    // protected $connection = 'LogDB_XKeeperVer3';
    protected $connection = 'mysql';
    protected $table = 'TB-ProductLog_WatchUCC';
    protected $primaryKey = 'Log';

    public $timestamps = false;
}
