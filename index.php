<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
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
Debugger::debug("PHP: Now: " . $counter->nowCount);
Debugger::debug("PHP: Daily: " . $counter->dailyCount);
Debugger::debug("PHP: Total: " . $counter->totalCount);
?>

<div class="counter">
    <span>Now: <?php echo $counter->nowCount; ?></span>
    <span>Daily: <?php echo $counter->dailyCount; ?></span>
    <span>Total: <?php echo $counter->totalCount; ?></span>
</div>
</body>

</html>
