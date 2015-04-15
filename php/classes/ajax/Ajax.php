<?php

/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */

abstract class Ajax extends Output
{
    public function __construct()
    {
        parent::__construct();
    }

    protected abstract function processData();

    public function produceOutput()
    {
        $ans = $this->processData();
        return $ans;
    }
}

?>