<?php

require_once "JsonWrapper.php";
require_once "Counter.php";

$jsonWrapper = new JsonWrapper();

$counter = new Counter(
    $_POST["clientId"],
    $_POST["sessionId"],
    60 * 3,
    "../visitors-counter.json",
    "Europe/Moscow"
);

$response = array(
    "total" => $counter->getTotalCount(),
    "daily" => $counter->getDailyCount(),
    "now" => $counter->getNowCount()
);

echo $jsonWrapper->encode($response);
