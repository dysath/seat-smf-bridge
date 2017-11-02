<?php

return [

        'smf' => [
            'driver'    => 'mysql',
            'host'      => env('SMF_HOST', 'localhost'),
            'port'      => env('SMF_PORT', '3306'),
            'database'  => env('SMF_DATABASE', 'forge'),
            'username'  => env('SMF_USERNAME', 'forge'),
            'password'  => env('SMF_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => 'smf_',
            'strict'    => true,
            'engine'    => null,
            'modes'     => [
                // For now, disable the full_group_by restriction.
                // There are just too many queries not optimized
                // for this, for now.
                //'ONLY_FULL_GROUP_BY',

                // All of the other modes though, they should stay!
                'STRICT_TRANS_TABLES',
                'NO_ZERO_IN_DATE',
                'NO_ZERO_DATE',
                'ERROR_FOR_DIVISION_BY_ZERO',
                'NO_AUTO_CREATE_USER',
                'NO_ENGINE_SUBSTITUTION',
            ],
        ],
];
