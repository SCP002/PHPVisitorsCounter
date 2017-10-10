<?php

/**
 * Custom debugger to output messages to the browser console or specified file
 */
final class Debugger
{
    public static function debug($message, $timestamp = false)
    {
        $message = print_r($message, true);
        $message = preg_replace('/\n/', '\n', $message);
        $message = str_replace('"', '\\"', $message);

        if ($timestamp) {
            $message = time() . ": " . $message;
        }

        echo "<script>console.log(\"$message\");</script>";
    }

    public static function debugToFile($message, $fileName = "php-debug.log", $timestamp = false, $append = true)
    {
        $message = print_r($message, true) . PHP_EOL;
        $flags = null;

        if ($timestamp) {
            $message = time() . ": " . $message;
        }

        if ($append) {
            $flags = FILE_APPEND;
        }

        file_put_contents($fileName, $message, $flags);
    }
}
