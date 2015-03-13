<?php

return [
    'monolog.logfile' => __DIR__.'/../storage/debug.log',
    'twig.path'       => __DIR__.'/../views',
    'twig.options'    => [
        'cache' => __DIR__.'/../storage/cache'
    ],
    'maxmind.download'    => 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz',
    'maxmind.database'    => __DIR__.'/../storage/database.mmdb',
    'maxmind.cache'       => __DIR__.'/../storage/cache.json',
    'maxmind.user_id'     => getenv('maxmind.user_id'),
    'maxmind.license_key' => getenv('maxmind.license_key'),
];
