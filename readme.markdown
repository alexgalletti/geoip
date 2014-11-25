### Geo IP
Geolocation by IP system using maxmind open source geolite 2 database.

#### API Usage

Current Version: **1.0**

There are multiple methods to lookup an IP address. You may use GET or POST methods to lookup a location:

`/api/1/lookup/{ip_address}`

Parameterized query:

`/api/1/lookup?ip={ip_address}`

Sample success response:

```json
{
    "results": {
        "city": {
            "geoname_id": 4887398,
            "names": {
                "en": "Chicago"
            }
        },
        "continent": {
            "code": "NA",
            "geoname_id": 6255149,
            "names": {
                "en": "North America"
            }
        },
        "country": {
            "geoname_id": 6252001,
            "iso_code": "US",
            "names": {
                "en": "United States"
            }
        },
        "location": {
            "latitude": 41.85,
            "longitude": -87.65,
            "metro_code": 602,
            "time_zone": "America/Chicago"
        },
        "registered_country": {
            "geoname_id": 6252001,
            "iso_code": "US",
            "names": {
                "en": "United States"
            }
        },
        "subdivisions": [
            {
                "geoname_id": 4896861,
                "iso_code": "IL",
                "names": {
                    "en": "Illinois"
                }
            }
        ]
    }
}
```

Sample failure response:

```json
{
    "error": "Invalid IP address specified.",
    "results": false
}
```
