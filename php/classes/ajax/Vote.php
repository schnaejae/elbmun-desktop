<?php
/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
include AJAX_CLASSPATH . "Ajax.php";

class Vote extends Ajax
{

    public function __construct()
    {
        parent::__construct();
    }

    private function setType()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $vote = $config->getElementsByTagName("committee")->item(0)->getElementsByTagName('vote')->item(0);
        $dom->formatOutput = true;
        if (!isset($_POST['value'])) {
            return;
        }
        $vote->setAttribute('type', $_POST['value']);
        $dom->save("./resources/state.xml");
    }

    private function setMajority()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $vote = $config->getElementsByTagName("committee")->item(0)->getElementsByTagName('vote')->item(0);
        $dom->formatOutput = true;
        if (!isset($_POST['value'])) {
            return;
        }
        $vote->setAttribute('majority', $_POST['value']);
        $dom->save("./resources/state.xml");
    }

    private function setVeto()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $vote = $config->getElementsByTagName("committee")->item(0)->getElementsByTagName('vote')->item(0);
        $dom->formatOutput = true;
        if (!isset($_POST['value'])) {
            return;
        }
        $vote->setAttribute('veto', $_POST['value']);
        $dom->save("./resources/state.xml");
    }

    private function setDivide()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $vote = $config->getElementsByTagName("committee")->item(0)->getElementsByTagName('vote')->item(0);
        $dom->formatOutput = true;
        if (!isset($_POST['value'])) {
            return;
        }
        $vote->setAttribute('divide', $_POST['value']);
        $dom->save("./resources/state.xml");
    }

    protected function processData()
    {
        if (isset($_GET['action']) OR isset($_POST['action'])) {
            $action = $_POST['action'];
            switch ($action) {
                case "setType":
                    $this->setType();
                    break;
                case "setMajority":
                    $this->setMajority();
                    break;
                case "setVeto":
                    $this->setVeto();
                    break;
                case "setDivide":
                    $this->setDivide();
                    break;
            }
        }
    }

}

?>