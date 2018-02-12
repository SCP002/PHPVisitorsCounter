<?php

class Utils
{
    public static function getClientIP()
    {
        $ipKeys = array("HTTP_CLIENT_IP",
            "HTTP_X_FORWARDED_FOR",
            "HTTP_X_FORWARDED",
            "HTTP_FORWARDED_FOR",
            "HTTP_FORWARDED",
            "REMOTE_ADDR");

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
}
