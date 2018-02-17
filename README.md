### PHP Visitors Counter
***
A simple visitors counter for your website.<br /><br />

To change default session expiration timeout for current visitors, change counter file location<br />
or change timezone used for calculations, see ./php/counter-ajax.php.

PHP **5.1** compatible (pre **json_encode** and **json_decode**).<br /><br />

It will create JSON file to store counter data on your host machine, which has following format:<br />
```json
{
  "now": {
    "users": [
      {
        "expires": 1518806599,
        "clientId": "95zqttin9",
        "clientIp": "192.168.0.100"
      },
      {
        "expires": 1518806980,
        "clientId": "ypjl2hyj8",
        "clientIp": "192.168.0.102"
      }
    ]
  },
  "daily": {
    "day": 16,
    "users": [
      {
        "sessionId": "668pq9ev9",
        "clientIp": "192.168.0.100"
      },
      {
        "sessionId": "8ba3s67wb",
        "clientIp": "192.168.0.102"
      },
      {
        "sessionId": "a506rfl12",
        "clientIp": "192.168.0.103"
      }
    ]
  },
  "total": {
    "count": 10
  }
}
```
