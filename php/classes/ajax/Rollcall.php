<?php
/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
include AJAX_CLASSPATH . "Ajax.php";

class Rollcall extends Ajax
{

    public function __construct()
    {
        parent::__construct();
    }

    private function resetRollCall()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $dom->formatOutput = true;
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            $member->setAttribute('present', 'false');
            $member->setAttribute('voting', 'false');
        }
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            $member->setAttribute('present', 'false');
        }
        $dom->save("./resources/state.xml");
    }

    private function setRollCall()
    {
        if (!isset($_POST['iso']) OR !isset($_POST['call'])) {
            return;
        }
        var_dump($_POST);
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $dom->formatOutput = true;
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            if ($member->getAttribute('iso') == $_POST['iso']) {
                switch ($_POST['call']) {
                    case "absent":
                        $member->setAttribute('present', 'false');
                        $member->setAttribute('voting', 'false');
                        break;
                    case "present":
                        $member->setAttribute('present', 'true');
                        $member->setAttribute('voting', 'false');
                        break;
                    case "voting":
                        $member->setAttribute('present', 'true');
                        $member->setAttribute('voting', 'true');
                        break;
                }
                $dom->save("./resources/state.xml");
                return;
            }
        }
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            if ($member->getAttribute('iso') == $_POST['iso']) {
                switch ($_POST['call']) {
                    case "absent":
                        $member->setAttribute('present', 'false');
                        break;
                    case "present":
                        $member->setAttribute('present', 'true');
                        break;
                }
                $dom->save("./resources/state.xml");
                return;
            }
        }
    }

    protected function processData()
    {
        if (isset($_GET['action']) OR isset($_POST['action'])) {
            $action = $_POST['action'];
            switch ($action) {
                case "resetRollCall":
                    $this->resetRollCall();
                    break;
                case "setRollCall":
                    $this->setRollCall();
                    break;
            }
        }
    }

}

?>