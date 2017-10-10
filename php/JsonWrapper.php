<?php

require_once "lib/JSON.php";

/**
 * Workaround for json_decode and json_encode in php < 5.2
 */
final class JsonWrapper
{
    public static function decode($content, $assoc = false)
    {
        if ($assoc) {
            $servicesJson = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        } else {
            $servicesJson = new Services_JSON();
        }

        return $servicesJson->decode($content);
    }

    public static function encode($content, $decorate = true)
    {
        $servicesJson = new Services_JSON();

        $result = $servicesJson->encode($content);

        if ($decorate) {
            $result = self::decorate($result);
        }

        return $result;
    }

    private static function decorate($json)
    {
        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $indentStr = "  ";
        $newLine = PHP_EOL;

        for ($i = 0; $i < $strLen; $i++) {
            $char = $json[$i];

            if ($char == '"') {
                if (!preg_match('`"(\\\\\\\\|\\\\"|.)*?"`s', $json, $m, null, $i)) {
                    return $json;
                }

                $result .= $m[0];
                $i += strLen($m[0]) - 1;

                continue;
            } else if ($char == '}' || $char == ']') {
                $result .= $newLine;
                $pos--;
                $result .= str_repeat($indentStr, $pos);
            }

            $result .= $char;

            if ($char == ':') {
                $result .= ' ';
            } else if ($char == ',' || $char == '{' || $char == '[') {
                $result .= $newLine;

                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                $result .= str_repeat($indentStr, $pos);
            }
        }

        return $result;
    }
}
