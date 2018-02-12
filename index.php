<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <title>Visitors Counter</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
<?php
// Create counter instance.
include_once "./php/Counter.php";
$counter = new Counter("./visitors-counter.json", 60 * 3, "Europe/Moscow");

// Write messages to the browser javascript console for debugging purpose.
include_once "./php/Debugger.php";
Debugger::debug("Now: " . $counter->getNowCount(), true);
Debugger::debug("Daily: " . $counter->getDailyCount(), true);
Debugger::debug("Total: " . $counter->getTotalCount(), true);
?>

<div class="counter">
    <span>Now: <?php echo $counter->getNowCount(); ?></span>
    <span>Daily: <?php echo $counter->getDailyCount(); ?></span>
    <span>Total: <?php echo $counter->getTotalCount(); ?></span>
</div>
</body>

</html>
