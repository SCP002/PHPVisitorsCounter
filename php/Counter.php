<?php

require_once "JsonWrapper.php";

final class Counter
{
    public $totalCount = 0;
    public $dailyCount = 0;
    public $nowCount = 0;

    private $fileName = null;
    private $fileContents = null;
    private $expireTime = 0;

    public function __construct($fileName, $expireTime, $timezoneIdentifier)
    {
        $this->fileName = $fileName;
        $this->expireTime = $expireTime;

        date_default_timezone_set($timezoneIdentifier);

        if (!file_exists($this->fileName)) {
            $this->createFile();
        }

        $this->fileContents = JsonWrapper::decode(file_get_contents($this->fileName), true);

        $this->processTotal();
        $this->processDaily();
        $this->processNow();

        file_put_contents($this->fileName, JsonWrapper::encode($this->fileContents));
    }

    private function processTotal()
    {
        $this->fileContents["total"]["count"]++;

        $this->totalCount = $this->fileContents["total"]["count"];
    }

    private function processDaily()
    {
        $currentDay = intval(date("d"));

        if ($this->fileContents["daily"]["day"] != $currentDay) {
            $this->fileContents["daily"]["day"] = $currentDay;
            $this->fileContents["daily"]["count"] = 0;
        } else {
            $this->fileContents["daily"]["count"]++;
        }

        $this->dailyCount = $this->fileContents["daily"]["count"];
    }

    private function processNow()
    {
        $clientIP = $_SERVER["REMOTE_ADDR"];
        $ipExist = false;
        $currentTime = time();
        $expires = $currentTime + $this->expireTime;

        $currentUserData = array(
            "ip" => $clientIP,
            "expires" => $expires
        );

        foreach ($this->fileContents["now"]["users"] as $user => $data) {
            if ($data["ip"] == $clientIP) {
                $ipExist = true;
                $data["expires"] = $expires;
                $this->fileContents["now"]["users"][$user]["expires"] = $expires;
            } else if ($currentTime >= $data["expires"]) {
                unset($this->fileContents["now"]["users"][$user]);
                $this->fileContents["now"]["users"] = array_values($this->fileContents["now"]["users"]);
            }
        }

        if (!$ipExist) {
            array_push($this->fileContents["now"]["users"], $currentUserData);
        }

        $this->nowCount = count($this->fileContents["now"]["users"]);
    }

    private function createFile()
    {
        $data = array(
            "now" => array(
                "users" => array(
                    // "ip" => "CLIENT_IP",
                    // "expires" => "SESSION_EXPIRE_TIME"
                )
            ),
            "daily" => array(
                "day" => intval(date("d")),
                "count" => 0
            ),
            "total" => array(
                "count" => 0
            )
        );

        file_put_contents($this->fileName, JsonWrapper::encode($data));
    }
}
