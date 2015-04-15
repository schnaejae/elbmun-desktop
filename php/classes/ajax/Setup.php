<?php
/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
include AJAX_CLASSPATH . "Ajax.php";

class Setup extends Ajax
{

    public function __construct()
    {
        parent::__construct();
    }

    private function setGeneralName()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $dom->formatOutput = true;
        if (!isset($_POST['value'])) {
            return;
        }
        $committee->setAttribute('name', $_POST['value']);
        $dom->save("./resources/state.xml");
    }

    private function setGeneralOrganization()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $dom->formatOutput = true;
        if (!isset($_POST['value'])) {
            return;
        }
        $committee->setAttribute('organization', $_POST['value']);
        $dom->save("./resources/state.xml");
    }

    private function setGeneralTopicRemove()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $dom->formatOutput = true;
        if (!isset($_POST['index']) OR !is_numeric($_POST['index'])) {
            return;
        }
        $committee->removeChild($committee->getElementsByTagName('topic')->item($_POST['index']));
        $dom->save("./resources/state.xml");
    }

    private function setGeneralTopicAdd()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $dom->formatOutput = true;
        $committee->appendChild($dom->createElement('topic'));
        $dom->save("./resources/state.xml");
    }

    private function setGeneralTopicName()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $dom->formatOutput = true;
        if (!isset($_POST['value']) OR !isset($_POST['index']) OR !is_numeric($_POST['index'])) {
            return;
        }
        $committee->getElementsByTagName('topic')->item($_POST['index'])->nodeValue = $_POST['value'];
        $dom->save("./resources/state.xml");
    }

    private function setGeneralTopicCurrent()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $dom->formatOutput = true;
        if (!isset($_POST['index']) OR !is_numeric($_POST['index'])) {
            return;
        }
        foreach ($committee->getElementsByTagName('topic') as $key => $topic) {
            if ($key == $_POST['index']) {
                $topic->setAttribute('current', 'true');
            } else {
                $topic->setAttribute('current', 'false');
            }
        }
        $dom->save("./resources/state.xml");
    }

    private function countryMember()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/countries.xml");
        $xmlcountries = $dom->getElementsByTagName("countries")->item(0);
        $countries = array();
        foreach ($xmlcountries->getElementsByTagName("country") as $country) {
            $countries[$country->getAttribute('iso')] = $country->nodeValue;
        }
        foreach ($xmlcountries->getElementsByTagName("organization") as $country) {
            $countries[$country->getAttribute('iso')] = $country->nodeValue;
        }
        if (!isset($_POST['iso']) OR !isset($countries[$_POST['iso']])) {
            return;
        }
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $xmlmembers = $config->getElementsByTagName("committee")->item(0)->getElementsByTagName('members')->item(0);
        foreach ($xmlmembers->getElementsByTagName("observer") as $member) {
            if ($member->getAttribute('iso') == $_POST['iso']) {
                $xmlmembers->removeChild($member);
            }
        }
        $members = array();
        foreach ($xmlmembers->getElementsByTagName("state") as $member) {
            if ($member->getAttribute('iso') == $_POST['iso']) {
                return;
            }
            $members[$member->getAttribute('iso')] = array('name' => $member->nodeValue, 'present' => $member->getAttribute('present'), 'voting' => $member->getAttribute('voting'));
        }
        while ($xmlmembers->getElementsByTagName("state")->length > 0) {
            $xmlmembers->removeChild($xmlmembers->getElementsByTagName("state")->item(0));
        }
        $members[$_POST['iso']] = array('name' => $countries[$_POST['iso']], 'present' => "false", 'voting' => "false");;
        asort($members);
        foreach ($members as $iso => $value) {
            $xmlmember = $dom->createElement('state');
            $xmlmember->setAttribute("iso", $iso);
            $xmlmember->setAttribute("present", $value['present']);
            $xmlmember->setAttribute("voting", $value['voting']);
            $xmlmember->nodeValue = $value['name'];
            $xmlmembers->appendChild($xmlmember);
        }
        $dom->formatOutput = true;
        $dom->save("./resources/state.xml");
    }

    private function countryObserver()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/countries.xml");
        $xmlcountries = $dom->getElementsByTagName("countries")->item(0);
        $countries = array();
        foreach ($xmlcountries->getElementsByTagName("country") as $country) {
            $countries[$country->getAttribute('iso')] = $country->nodeValue;
        }
        foreach ($xmlcountries->getElementsByTagName("organization") as $country) {
            $countries[$country->getAttribute('iso')] = $country->nodeValue;
        }
        if (!isset($_POST['iso']) OR !isset($countries[$_POST['iso']])) {
            return;
        }
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $xmlmembers = $config->getElementsByTagName("committee")->item(0)->getElementsByTagName('members')->item(0);
        foreach ($xmlmembers->getElementsByTagName("state") as $member) {
            if ($member->getAttribute('iso') == $_POST['iso']) {
                $xmlmembers->removeChild($member);
            }
        }
        $members = array();
        foreach ($xmlmembers->getElementsByTagName("observer") as $member) {
            if ($member->getAttribute('iso') == $_POST['iso']) {
                return;
            }
            $members[$member->getAttribute('iso')] = array('name' => $member->nodeValue, 'present' => $member->getAttribute('present'));
        }
        while ($xmlmembers->getElementsByTagName("observer")->length > 0) {
            $xmlmembers->removeChild($xmlmembers->getElementsByTagName("observer")->item(0));
        }
        $members[$_POST['iso']] = array('name' => $countries[$_POST['iso']], 'present' => "false");;
        asort($members);
        foreach ($members as $iso => $value) {
            $xmlmember = $dom->createElement('observer');
            $xmlmember->setAttribute("iso", $iso);
            $xmlmember->setAttribute("present", $value['present']);
            $xmlmember->nodeValue = $value['name'];
            $xmlmembers->appendChild($xmlmember);
        }
        $dom->formatOutput = true;
        $dom->save("./resources/state.xml");
    }

    private function countryRemove()
    {
        if (!isset($_POST['iso'])) {
            return;
        }
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $xmlmembers = $config->getElementsByTagName("committee")->item(0)->getElementsByTagName('members')->item(0);
        foreach ($xmlmembers->getElementsByTagName("state") as $member) {
            if ($member->getAttribute('iso') == $_POST['iso']) {
                $xmlmembers->removeChild($member);
                break;
            }
        }
        foreach ($xmlmembers->getElementsByTagName("observer") as $member) {
            if ($member->getAttribute('iso') == $_POST['iso']) {
                $xmlmembers->removeChild($member);
                break;
            }
        }
        $dom->formatOutput = true;
        $dom->save("./resources/state.xml");
    }

    protected function processData()
    {
        if (isset($_GET['action']) OR isset($_POST['action'])) {
            $action = $_POST['action'];
            switch ($action) {
                case "setGeneralName":
                    $this->setGeneralName();
                    break;
                case "setGeneralOrganization":
                    $this->setGeneralOrganization();
                    break;
                case "setGeneralTopicRemove":
                    $this->setGeneralTopicRemove();
                    break;
                case "setGeneralTopicAdd":
                    $this->setGeneralTopicAdd();
                    break;
                case "setGeneralTopicName":
                    $this->setGeneralTopicName();
                    break;
                case "setGeneralTopicCurrent":
                    $this->setGeneralTopicCurrent();
                    break;
                case "countryMember":
                    $this->countryMember();
                    break;
                case "countryObserver":
                    $this->countryObserver();
                    break;
                case "countryRemove":
                    $this->countryRemove();
                    break;
            }
        }
    }

}

?>