<?php

return [

    'download'    => 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz',
    'database'    => storage_path('app/database.mmdb'),
    'cache'       => storage_path('app/cache.json'),
    'user_id'     => env('maxmind.user_id'),
    'license_key' => env('maxmind.license_key'),

];
