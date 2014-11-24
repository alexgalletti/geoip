<?php

return [
    'monolog.logfile'   => __DIR__.'/../storage/debug.log',
    'twig.path'         => __DIR__.'/../views',
    'twig.options'    => [
        'cache' => __DIR__.'/../storage/cache'
    ],
    'maxmind_download'  => 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz',
    'maxmind_database'  => __DIR__.'/../storage/database.mmdb',
];
