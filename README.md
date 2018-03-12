### PHP Visitors Counter
***
A simple visitors counter for your website.<br /><br />

To change default session expiration timeout for current visitors, change counter file location,<br />
change default password (read about it below) or change timezone used for calculations, see:<br />

**./php/counter-ajax.php**<br /><br />

If double click on the counter div, browser will ask a password to proceed.<br />
If password is passed, should appear modal window with the table inside, which contains useful statistics.<br />
Table contents based on JSON file stored on the server.<br /><br />

If this data is not sensitive in your case, feel free to remove password protection.<br />
Quickest and dirtiest way to do this is to replace this:

```js
password = window.prompt('Enter password');
```

With this:

```js
password = 'abcd';
```

In **./js/counter-handler.js**<br /><br />

***
PHP **5.1** compatible (pre **json_encode** and **json_decode**).<br /><br />

Counter will store JSON file on your host machine, which has following format:<br />
```json
{
  "now": {
    "users": [
      {
        "expires": 1520589777,
        "visitTime": 1520589585,
        "sessionId": "ilqigc4zr",
        "clientId": "1p7snurgv",
        "clientIp": "192.168.0.5"
      },
      {
        "expires": 1520589849,
        "visitTime": 1520589615,
        "sessionId": "hwov4patq",
        "clientId": "nhd85ndxs",
        "clientIp": "192.168.0.6"
      }
    ]
  },
  "daily": {
    "day": 9,
    "users": [
      {
        "visitTime": 1520589585,
        "sessionId": "ilqigc4zr",
        "clientId": "1p7snurgv",
        "clientIp": "192.168.0.5"
      },
      {
        "visitTime": 1520589595,
        "sessionId": "o7iukkj0w",
        "clientId": "1p7snurgv",
        "clientIp": "192.168.0.5"
      },
      {
        "visitTime": 1520589615,
        "sessionId": "hwov4patq",
        "clientId": "nhd85ndxs",
        "clientIp": "192.168.0.6"
      },
      {
        "visitTime": 1520589667,
        "sessionId": "f5sf9knza",
        "clientId": "nhd85ndxs",
        "clientIp": "192.168.0.6"
      }
    ]
  },
  "total": {
    "count": 9
  }
}
```
