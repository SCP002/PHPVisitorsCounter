<?php

require_once "lib/JSON.php";

/**
 * Workaround for json_decode and json_encode in php < 5.2
 */
class JsonWrapper
{
    private $servicesJsonStd = null;
    private $servicesJsonAssoc = null;

    public function __construct()
    {
        $this->servicesJsonStd = new Services_JSON();
        $this->servicesJsonAssoc = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    }

    public function decode($content, $assoc = false)
    {
        if ($assoc) {
            return $this->servicesJsonAssoc->decode($content);
        } else {
            return $this->servicesJsonStd->decode($content);
        }
    }

    public function encode($content, $decorate = true)
    {
        $result = $this->servicesJsonStd->encode($content);

        if ($decorate) {
            $result = $this->decorate($result);
        }

        return $result;
    }

    private function decorate($json)
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
