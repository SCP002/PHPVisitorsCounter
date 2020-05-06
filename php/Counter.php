<?php

require_once "lib/BrowserDetection.php";
require_once "JsonWrapper.php";
require_once "Utils.php";

class Counter
{
    private $totalCount = null;
    private $dailyCount = null;
    private $nowCount = null;

    private $currentTime = null;

    private $browser = null;
    private $jsonWrapper = null;

    private $clientIp = null;
    private $clientBrowser = null;
    private $clientOs = null;

    private $clientId = null;
    private $sessionId = null;
    private $lastUrl = null;
    private $expireTime = null;
    private $fileName = null;

    private $fileContents = null;

    public function __construct($clientId, $sessionId, $lastUrl, $expireTime, $fileName, $timezoneIdentifier)
    {
        date_default_timezone_set($timezoneIdentifier);

        $this->currentTime = time();

        $this->browser = new BrowserDetection();
        $this->jsonWrapper = new JsonWrapper();

        $this->clientIp = Utils::getClientIpAddress();
        $this->clientBrowser = $this->browser->getName() . " " . $this->browser->getVersion();
        $this->clientOs = $this->browser->getPlatformVersion();

        if ($this->clientOs == BrowserDetection::PLATFORM_VERSION_UNKNOWN) {
            $this->clientOs = $this->browser->getPlatform() . " " . $this->browser->getPlatformVersion(true);
        }

        $this->clientOs = ($this->browser->isMobile()) ? $this->clientOs . "; Mobile" : $this->clientOs . "; Desktop";

        $this->clientId = $clientId;
        $this->sessionId = $sessionId;
        $this->lastUrl = $lastUrl;
        $this->expireTime = $expireTime;
        $this->fileName = $fileName;

        if (!file_exists($this->fileName)) {
            $this->createFile();
        }

        $this->fileContents = $this->jsonWrapper->decode(file_get_contents($this->fileName), true);

        $this->validate();

        $this->processDailyAndTotal();
        $this->processNow();

        file_put_contents($this->fileName, $this->jsonWrapper->encode($this->fileContents));
    }

    public function getTotalCount()
    {
        return $this->totalCount;
    }

    public function getDailyCount()
    {
        return $this->dailyCount;
    }

    public function getNowCount()
    {
        return $this->nowCount;
    }

    public function getJsonData()
    {
        return $this->fileContents;
    }

    private function processDailyAndTotal()
    {
        $sessionIdExist = false;
        $currentDay = intval(date("d"));

        $currentUserData = array(
            "visitTime" => $this->currentTime,
            "sessionId" => $this->sessionId,
            "clientId" => $this->clientId,
            "clientIp" => $this->clientIp,
            "clientBrowser" => $this->clientBrowser,
            "clientOs" => $this->clientOs,
            "lastUrl" => $this->lastUrl
        );

        if ($this->fileContents["daily"]["day"] != $currentDay) {
            $this->fileContents["daily"]["day"] = $currentDay;

            $this->fileContents["daily"]["users"] = array();
        } else {
            foreach ($this->fileContents["daily"]["users"] as $user => $data) {
                if ($data["sessionId"] == $this->sessionId) {
                    $sessionIdExist = true;

                    $this->fileContents["daily"]["users"][$user] = $currentUserData;
                }
            }
        }

        if (!$sessionIdExist) {
            array_push($this->fileContents["daily"]["users"], $currentUserData);

            $this->fileContents["total"]["count"]++;
        }

        $this->fileContents["daily"]["users"] = array_values($this->fileContents["daily"]["users"]); // Reindex.

        $this->dailyCount = count($this->fileContents["daily"]["users"]);
        $this->totalCount = $this->fileContents["total"]["count"];
    }

    private function processNow()
    {
        $clientIdExist = false;
        $expires = $this->currentTime + $this->expireTime;

        $currentUserData = array(
            "expires" => $expires,
            "visitTime" => $this->currentTime,
            "sessionId" => $this->sessionId,
            "clientId" => $this->clientId,
            "clientIp" => $this->clientIp,
            "clientBrowser" => $this->clientBrowser,
            "clientOs" => $this->clientOs,
            "lastUrl" => $this->lastUrl
        );

        foreach ($this->fileContents["now"]["users"] as $user => $data) {
            if ($data["clientId"] == $this->clientId) {
                $clientIdExist = true;

                $this->fileContents["now"]["users"][$user] = $currentUserData;
            } else if ($this->currentTime >= $data["expires"]) {
                unset($this->fileContents["now"]["users"][$user]);
            }
        }

        if (!$clientIdExist) {
            array_push($this->fileContents["now"]["users"], $currentUserData);
        }

        $this->fileContents["now"]["users"] = array_values($this->fileContents["now"]["users"]); // Reindex.

        $this->nowCount = count($this->fileContents["now"]["users"]);
    }

    private function createFile()
    {
        $data = array(
            "now" => array(
                "users" => array(
                    // "expires" => "SESSION_EXPIRE_TIME",
                    // "visitTime" => "LAST_VISIT_TIME",
                    // "sessionId" => "SESSION_ID",
                    // "clientId" => "CLIENT_ID",
                    // "clientIp" => "CLIENT_IP",
                    // "clientBrowser" => "CLIENT_BROWSER",
                    // "clientOs" => "CLIENT_OS",
                    // "lastUrl" => "CLIENT_ADDRESS_BAR_STRING"
                )
            ),
            "daily" => array(
                "day" => intval(date("d")),
                "users" => array(
                    // "visitTime" => "LAST_VISIT_TIME",
                    // "sessionId" => "SESSION_ID",
                    // "clientId" => "CLIENT_ID",
                    // "clientIp" => "CLIENT_IP",
                    // "clientBrowser" => "CLIENT_BROWSER",
                    // "clientOs" => "CLIENT_OS",
                    // "lastUrl" => "CLIENT_ADDRESS_BAR_STRING"
                )
            ),
            "total" => array(
                "count" => 0
            )
        );

        file_put_contents($this->fileName, $this->jsonWrapper->encode($data));
    }

    // Bug fix, encoutnered on Debian 3.16.36 with PHP 5.4.45-0+deb7u2:
    // If counter file path is ~, ["now"]["users"] becomes null.
    private function validate()
    {
        // Now
        if (!is_array($this->fileContents["now"])) {
            $this->fileContents["now"] = array();
        }
        if (!is_array($this->fileContents["now"]["users"])) {
            $this->fileContents["now"]["users"] = array();
        }

        // Daily
        if (!is_array($this->fileContents["daily"])) {
            $this->fileContents["daily"] = array();
        }
        if (!is_int($this->fileContents["daily"]["day"])) {
            $this->fileContents["daily"]["day"] = intval(date("d"));
        }
        if (!is_array($this->fileContents["daily"]["users"])) {
            $this->fileContents["daily"]["users"] = array();
        }

        // Total
        if (!is_array($this->fileContents["total"])) {
            $this->fileContents["total"] = array();
        }
        if (!is_int($this->fileContents["total"]["count"])) {
            $this->fileContents["total"]["count"] = 0;
        }
    }
}
