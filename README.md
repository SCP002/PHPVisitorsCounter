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
        "expires": 1521313987,
        "visitTime": 1521313807,
        "sessionId": "gx0f6ob9p",
        "clientId": "ghjo3b704",
        "clientIp": "192.168.0.4",
        "clientBrowser": "Internet Explorer 11.0",
        "clientOs": "Windows 7; Desktop",
        "lastUrl": "http:\/\/192.168.0.2\/"
      }
    ]
  },
  "daily": {
    "day": 17,
    "users": [
      {
        "visitTime": 1521313789,
        "sessionId": "d00ocqpya",
        "clientId": "q09rpdwt7",
        "clientIp": "192.168.0.3",
        "clientBrowser": "Firefox 55.0",
        "clientOs": "Windows 8.1; Desktop",
        "lastUrl": "http:\/\/192.168.0.2\/?test=12345"
      },
      {
        "visitTime": 1521313807,
        "sessionId": "gx0f6ob9p",
        "clientId": "ghjo3b704",
        "clientIp": "192.168.0.4",
        "clientBrowser": "Internet Explorer 11.0",
        "clientOs": "Windows 7; Desktop",
        "lastUrl": "http:\/\/192.168.2\/"
      }
    ]
  },
  "total": {
    "count": 6
  }
}
```
