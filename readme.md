## Geo IP
Geolocation by IP  using maxmind's open source geolite 2 database. **Now using [Lumen](https://github.com/laravel/lumen)**

**Check out the Lumen branch for a newer version of this repo**

### Package Installation
---
Run: `composer install -vvv`

### Documentation
---
Current API Version: **2**

There are multiple methods to lookup an IP address. You may use `GET` or `POST` methods to lookup an IP. If no IP is specified the clients IP will be used.

#### Status Codes
---
| Status Code | Description                                                            |
|-------------|------------------------------------------------------------------------|
| 500         | There is an error with the database or server, please try again later. |
| 400         | Invalid IP address specified.                                          |
| 404         | There is no location data for the specified IP.                        |

#### URL Explanation
---
`/api/{version}/{endpoint}/{parameter}`

#### Standard Lookup
---
`/api/2/lookup/{ip_address}`

#### Parameterized Lookup:
---
`/api/2/lookup?ip={ip_address}`

#### Sample Response:
---
```json
{
    "ip": "74.92.188.245",
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

#### Get Database Metadata:
---
> Deprecated: This method is no longer available v2 of the API.

`/api/1/metadata`

#### Change Log
---
**2015-03-13**
* The `metadata` endpoint was deprecated
* Database is now remote with caching
* API update to v2
