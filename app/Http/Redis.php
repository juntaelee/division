<?php

namespace App\Http;

require 'Predis/Autoloader.php';

// Predis\Autoloader::register();

class Redis extends \Predis\Client
{
    const SENTINELS = ['tcp://222.234.223.140:26379', 'tcp://222.234.223.141:26379', 'tcp://222.234.223.150:26379'];
    const PASSWORD = 'XKeeperCache';

    public function __construct($database = 0)
    {
        $options = [
            'replication' => 'sentinel',
            'service' => 'mymaster',
            'parameters' => [
                'database' => $database, 'password' => self::PASSWORD
            ]
        ];

        parent::__construct(self::SENTINELS, $options);
    }
}
