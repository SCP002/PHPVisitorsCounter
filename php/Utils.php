<?php

class Utils
{
    public static function getClientIpAddress()
    {
        $ipKeys = array("HTTP_CLIENT_IP",
            "HTTP_X_FORWARDED_FOR",
            "HTTP_X_FORWARDED",
            "HTTP_FORWARDED_FOR",
            "HTTP_FORWARDED",
            "REMOTE_ADDR"
        );

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER)) {
                foreach (explode(",", $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);

                    return $ip;
                }
            }
        }

        return "UNKNOWN";
    }

    public static function logToConsole($message, $timestamp = false)
    {
        $message = print_r($message, true);
        $message = preg_replace('/\n/', '\n', $message);
        $message = str_replace('"', '\\"', $message);

        if ($timestamp) {
            $message = time() . ": " . $message;
        }

        $message = "PHP: " . $message;

        echo "<script>window.console.log(\"$message\");</script>";
    }

    public static function logToFile($message, $timestamp = false, $append = true, $fileName = "../php-debug.log")
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
