<?php
/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
include AJAX_CLASSPATH . "Ajax.php";

class Debate extends Ajax
{

    public function __construct()
    {
        parent::__construct();
    }

    private function addGeneralSpeaker()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $gsl = $config->getElementsByTagName("general")->item(0);
        $dom->formatOutput = true;
        $members = array();
        $speakers = array();
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            if ($member->getAttribute('present')) {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            if ($member->getAttribute('present')) {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        foreach ($gsl->getElementsByTagName('speaker') as $speaker) {
            if (isset($members[$speaker->nodeValue])) {
                $speakers[] = $speaker->nodeValue;
            }
        }
        if (!isset($_POST['iso']) OR !isset($members[$_POST['iso']]) OR in_array($_POST['iso'], $speakers)) {
            return;
        }
        $speaker = $dom->createElement("speaker");
        $speaker->nodeValue = $_POST['iso'];
        $gsl->appendChild($speaker);
        $dom->save("./resources/state.xml");
    }

    private function removeGeneralSpeaker()
    {
        if (!isset($_POST['iso'])) {
            return;
        }
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $gsl = $config->getElementsByTagName("general")->item(0);
        $dom->formatOutput = true;
        foreach ($gsl->getElementsByTagName('speaker') as $speaker) {
            if ($speaker->nodeValue == $_POST['iso']) {
                $gsl->removeChild($speaker);
                $dom->save("./resources/state.xml");
                return;
            }
        }
    }

    private function moveGeneralSpeaker()
    {
        if (!isset($_POST['iso']) OR !isset($_POST['direction'])) {
            return;
        }
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $gsl = $config->getElementsByTagName("general")->item(0);
        $dom->formatOutput = true;
        foreach ($gsl->getElementsByTagName('speaker') as $key => $speaker) {
            if ($speaker->nodeValue == $_POST['iso']) {
                if ($_POST['direction'] == "up" && $key >= 2) {
                    $gsl->insertBefore($gsl->getElementsByTagName('speaker')->item($key), $gsl->getElementsByTagName('speaker')->item($key - 1));
                } elseif ($_POST['direction'] == "down" AND $gsl->getElementsByTagName('speaker')->item($key + 1) != null) {
                    $gsl->insertBefore($gsl->getElementsByTagName('speaker')->item($key + 1), $gsl->getElementsByTagName('speaker')->item($key));
                }
                $dom->save("./resources/state.xml");
                return;
            }
        }
    }

    private function setGeneralSpeakingTime()
    {
        if (isset($_POST['value']) AND is_numeric($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("general")->item(0);
            $gsl->setAttribute("speakertime", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function setGeneralWarningTime()
    {
        if (isset($_POST['value']) AND is_numeric($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("general")->item(0);
            $gsl->setAttribute("warningtime", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function setGeneralAutoNext()
    {
        if (isset($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("general")->item(0);
            $gsl->setAttribute("autonext", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function addResolutionSpeaker()
    {
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $gsl = $config->getElementsByTagName("resolution")->item(0);
        $dom->formatOutput = true;
        $members = array();
        $speakers = array();
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            if ($member->getAttribute('present')) {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            if ($member->getAttribute('present')) {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        foreach ($gsl->getElementsByTagName('speaker') as $speaker) {
            if (isset($members[$speaker->nodeValue])) {
                $speakers[] = $speaker->nodeValue;
            }
        }
        if (!isset($_POST['iso']) OR !isset($members[$_POST['iso']]) OR in_array($_POST['iso'], $speakers)) {
            return;
        }
        $speaker = $dom->createElement("speaker");
        $speaker->nodeValue = $_POST['iso'];
        $gsl->appendChild($speaker);
        $dom->save("./resources/state.xml");
    }

    private function removeResolutionSpeaker()
    {
        if (!isset($_POST['iso'])) {
            return;
        }
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $gsl = $config->getElementsByTagName("resolution")->item(0);
        $dom->formatOutput = true;
        foreach ($gsl->getElementsByTagName('speaker') as $speaker) {
            if ($speaker->nodeValue == $_POST['iso']) {
                $gsl->removeChild($speaker);
                $dom->save("./resources/state.xml");
                return;
            }
        }
    }

    private function moveResolutionSpeaker()
    {
        if (!isset($_POST['iso']) OR !isset($_POST['direction'])) {
            return;
        }
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $gsl = $config->getElementsByTagName("resolution")->item(0);
        $dom->formatOutput = true;
        foreach ($gsl->getElementsByTagName('speaker') as $key => $speaker) {
            if ($speaker->nodeValue == $_POST['iso']) {
                //$gsl->removeChild($speaker);
                if ($_POST['direction'] == "up" && $key >= 2) {
                    $gsl->insertBefore($gsl->getElementsByTagName('speaker')->item($key), $gsl->getElementsByTagName('speaker')->item($key - 1));
                } elseif ($_POST['direction'] == "down" AND $gsl->getElementsByTagName('speaker')->item($key + 1) != null) {
                    $gsl->insertBefore($gsl->getElementsByTagName('speaker')->item($key + 1), $gsl->getElementsByTagName('speaker')->item($key));
                }
                $dom->save("./resources/state.xml");
                return;
            }
        }
    }

    private function setResolutionSpeakingTime()
    {
        if (isset($_POST['value']) AND is_numeric($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("resolution")->item(0);
            $gsl->setAttribute("speakertime", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function setResolutionWarningTime()
    {
        if (isset($_POST['value']) AND is_numeric($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("resolution")->item(0);
            $gsl->setAttribute("warningtime", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function setResolutionAutoNext()
    {
        if (isset($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("resolution")->item(0);
            $gsl->setAttribute("autonext", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function addSpeakerSpeaker()
    {

        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $speaker = $config->getElementsByTagName("single")->item(0);
        $dom->formatOutput = true;
        $members = array();
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            if ($member->getAttribute('present')) {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            if ($member->getAttribute('present')) {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        if (isset($_POST['iso']) AND (isset($members[$_POST['iso']]) OR $_POST['iso'] == "" OR $_POST['iso'] == "_" . $committee->getAttribute("organization"))) {
            var_dump($_POST);
            $speaker->setAttribute("speaker", $_POST['iso']);
            echo $speaker->getAttribute("single");
            $dom->save("./resources/state.xml");
        }
    }

    private function setSpeakerSpeakingTime()
    {
        if (isset($_POST['value']) AND is_numeric($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("single")->item(0);
            $gsl->setAttribute("speakertime", $_POST['value']);
            $dom->save("./resources/state.xml");
        }

    }

    private function setSpeakerWarningTime()
    {
        if (isset($_POST['value']) AND is_numeric($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("single")->item(0);
            $gsl->setAttribute("warningtime", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function setSpeakerAutoPlay()
    {
        if (isset($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("single")->item(0);
            $gsl->setAttribute("autoplay", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function addModeratedSpeaker()
    {

        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $speaker = $config->getElementsByTagName("moderated")->item(0);
        $dom->formatOutput = true;
        $members = array();
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            if ($member->getAttribute('present')) {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            if ($member->getAttribute('present')) {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        if (isset($_POST['iso']) AND (isset($members[$_POST['iso']]) OR $_POST['iso'] == "" OR $_POST['iso'] == "_" . $committee->getAttribute("organization"))) {
            var_dump($_POST);
            $speaker->setAttribute("speaker", $_POST['iso']);
            echo $speaker->getAttribute("single");
            $dom->save("./resources/state.xml");
        }
    }

    private function setModeratedSpeakingTime()
    {
        if (isset($_POST['value']) AND is_numeric($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("moderated")->item(0);
            $gsl->setAttribute("speakertime", $_POST['value']);
            $dom->save("./resources/state.xml");
        }

    }

    private function setModeratedWarningTime()
    {
        if (isset($_POST['value']) AND is_numeric($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("moderated")->item(0);
            $gsl->setAttribute("warningtime", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function setModeratedDurationTime()
    {
        if (isset($_POST['value']) AND is_numeric($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("moderated")->item(0);
            $gsl->setAttribute("duration", $_POST['value']);
            $dom->save("./resources/state.xml");
        }

    }

    private function setModeratedWarningDuration()
    {
        if (isset($_POST['value']) AND is_numeric($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("moderated")->item(0);
            $gsl->setAttribute("durationwarning", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function setModeratedAutoPlay()
    {
        if (isset($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("moderated")->item(0);
            $gsl->setAttribute("autoplay", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function setModeratedTopic()
    {
        if (isset($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("moderated")->item(0);
            $gsl->setAttribute("topic", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function setUnmoderatedDurationTime()
    {
        if (isset($_POST['value']) AND is_numeric($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("unmoderated")->item(0);
            $gsl->setAttribute("duration", $_POST['value']);
            $dom->save("./resources/state.xml");
        }

    }

    private function setUnmoderatedWarningDuration()
    {
        if (isset($_POST['value']) AND is_numeric($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("unmoderated")->item(0);
            $gsl->setAttribute("durationwarning", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    private function setUnmoderatedTopic()
    {
        if (isset($_POST['value'])) {
            $dom = new DOMDocument();
            $dom->load("./resources/state.xml");
            $config = $dom->getElementsByTagName("config")->item(0);
            $gsl = $config->getElementsByTagName("unmoderated")->item(0);
            $gsl->setAttribute("topic", $_POST['value']);
            $dom->save("./resources/state.xml");
        }
    }

    protected function processData()
    {
        if (isset($_GET['action']) OR isset($_POST['action'])) {
            $action = $_POST['action'];
            switch ($action) {
                case "addGeneralSpeaker":
                    $this->addGeneralSpeaker();
                    break;
                case "removeGeneralSpeaker":
                    $this->removeGeneralSpeaker();
                    break;
                case "moveGeneralSpeaker":
                    $this->moveGeneralSpeaker();
                    break;
                case "setGeneralSpeakingTime":
                    $this->setGeneralSpeakingTime();
                    break;
                case "setGeneralWarningTime":
                    $this->setGeneralWarningTime();
                    break;
                case "setGeneralAutoNext":
                    $this->setGeneralAutoNext();
                    break;
                case "addResolutionSpeaker":
                    $this->addResolutionSpeaker();
                    break;
                case "removeResolutionSpeaker":
                    $this->removeResolutionSpeaker();
                    break;
                case "moveResolutionSpeaker":
                    $this->moveResolutionSpeaker();
                    break;
                case "setResolutionSpeakingTime":
                    $this->setResolutionSpeakingTime();
                    break;
                case "setResolutionWarningTime":
                    $this->setResolutionWarningTime();
                    break;
                case "setResolutionAutoNext":
                    $this->setResolutionAutoNext();
                    break;
                case "addSpeakerSpeaker":
                    $this->addSpeakerSpeaker();
                    break;
                case "setSpeakerSpeakingTime":
                    $this->setSpeakerSpeakingTime();
                    break;
                case "setSpeakerWarningTime":
                    $this->setSpeakerWarningTime();
                    break;
                case "setSpeakerAutoPlay":
                    $this->setSpeakerAutoPlay();
                    break;
                case "addModeratedSpeaker":
                    $this->addModeratedSpeaker();
                    break;
                case "setModeratedSpeakingTime":
                    $this->setModeratedSpeakingTime();
                    break;
                case "setModeratedWarningTime":
                    $this->setModeratedWarningTime();
                    break;
                case "setModeratedDurationTime":
                    $this->setModeratedDurationTime();
                    break;
                case "setModeratedWarningDuration":
                    $this->setModeratedWarningDuration();
                    break;
                case "setModeratedAutoPlay":
                    $this->setModeratedAutoPlay();
                    break;
                case "setModeratedTopic":
                    $this->setModeratedTopic();
                    break;
                case "setUnmoderatedDurationTime":
                    $this->setUnmoderatedDurationTime();
                    break;
                case "setUnmoderatedWarningDuration":
                    $this->setUnmoderatedWarningDuration();
                    break;
                case "setUnmoderatedTopic":
                    $this->setUnmoderatedTopic();
                    break;
            }
        }
    }

}


?>