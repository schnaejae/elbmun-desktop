<?php

/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */

abstract class Output
{

    public function __construct()
    {
        date_default_timezone_set("Europe/Berlin");

        if (get_magic_quotes_gpc()) {
            $this->array_stripslashes($_GET);
            $this->array_stripslashes($_POST);
            $this->array_stripslashes($_COOKIE);
        }
    }

    public abstract function produceOutput();

    protected function array_stripslashes($pVar)
    {
        if (is_string($pVar)) {
            $pVar = htmlspecialchars(stripslashes($pVar));
            return $pVar;
        } else {
            if (is_array($pVar)) {
                foreach ($pVar as $key => $value) {
                    $pVar[$key] = $this->array_stripslashes($value);
                }
            }
            return $pVar;
        }
    }

    protected function cleanInput($pInput)
    {
        $input = trim(addslashes($pInput));
        return $pInput;
    }
}

?>