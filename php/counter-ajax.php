<?php

require_once "JsonWrapper.php";
require_once "Counter.php";

$password = "abcd";

$jsonWrapper = new JsonWrapper();

$counter = new Counter(
    $_POST["clientId"],
    $_POST["sessionId"],
    60 * 3,
    "../visitors-counter.json",
    "Europe/Moscow"
);

if ($_POST["password"] == $password) {
    $response = $counter->getJsonData();
} else {
    $response = array(
        "total" => $counter->getTotalCount(),
        "daily" => $counter->getDailyCount(),
        "now" => $counter->getNowCount()
    );
}

echo $jsonWrapper->encode($response);
