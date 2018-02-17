<?php

require_once "JsonWrapper.php";
require_once "Utils.php";

class Counter
{
    private $totalCount = null;
    private $dailyCount = null;
    private $nowCount = null;

    private $jsonWrapper = null;
    private $clientIp = null;

    private $clientId = null;
    private $sessionId = null;
    private $expireTime = null;
    private $fileName = null;

    private $fileContents = null;

    public function __construct($clientId, $sessionId, $expireTime, $fileName, $timezoneIdentifier)
    {
        date_default_timezone_set($timezoneIdentifier);

        $this->jsonWrapper = new JsonWrapper();
        $this->clientIp = Utils::getClientIpAddress();

        $this->clientId = $clientId;
        $this->sessionId = $sessionId;
        $this->expireTime = $expireTime;
        $this->fileName = $fileName;

        if (!file_exists($this->fileName)) {
            $this->createFile();
        }

        $this->fileContents = $this->jsonWrapper->decode(file_get_contents($this->fileName), true);

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

    private function processDailyAndTotal()
    {
        $sessionIdExist = false;
        $currentDay = intval(date("d"));

        $currentUserData = array(
            "sessionId" => $this->sessionId,
            "clientIp" => $this->clientIp
        );

        if ($this->fileContents["daily"]["day"] != $currentDay) {
            $this->fileContents["daily"]["day"] = $currentDay;

            $this->fileContents["daily"]["users"] = array();
        } else {
            foreach ($this->fileContents["daily"]["users"] as $user => $data) {
                if ($data["sessionId"] == $this->sessionId) {
                    $sessionIdExist = true;
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
        $currentTime = time();
        $expires = $currentTime + $this->expireTime;

        $currentUserData = array(
            "expires" => $expires,
            "clientId" => $this->clientId,
            "clientIp" => $this->clientIp
        );

        foreach ($this->fileContents["now"]["users"] as $user => $data) {
            if ($data["clientId"] == $this->clientId) {
                $clientIdExist = true;

                $this->fileContents["now"]["users"][$user]["expires"] = $expires;
            } else if ($currentTime >= $data["expires"]) {
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
                    // "clientId" => "CLIENT_ID",
                    // "clientIp" => "CLIENT_IP"
                )
            ),
            "daily" => array(
                "day" => intval(date("d")),
                "users" => array(
                    // "sessionId" => "SESSION_ID",
                    // "clientIp" => "CLIENT_IP"
                )
            ),
            "total" => array(
                "count" => 0
            )
        );

        file_put_contents($this->fileName, $this->jsonWrapper->encode($data));
    }
}
