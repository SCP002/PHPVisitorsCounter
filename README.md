### PHP Visitors Counter
***
A simple visitors counter for your website.<br />
See **index.php** for usage example and configuration.<br /><br />

PHP **5.1** compatible (pre **json_encode** and **json_decode**).<br /><br />

It will create JSON file to store counter data on your host machine, which has following format:<br />
```json
{
  "now": {
    "users": [
      {
        "ip": "192.168.1.253",
        "expires": 1507545983
      },
      {
        "ip": "192.168.1.252",
        "expires": 1507545930
      },
      {
        "ip": "192.168.1.251",
        "expires": 1507546000
      }
    ]
  },
  "daily": {
    "day": 9,
    "count": 33
  },
  "total": {
    "count": 891
  }
}
```
