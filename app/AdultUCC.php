<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdultUCC extends Model
{
    protected $connection = 'ServiceDB_XKeeperVer3';
    protected $table = 'TB-AdultUCC';
    protected $primaryKey = 'UCC';

    public $timestamps = false;
}
