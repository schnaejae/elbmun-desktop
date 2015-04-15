<?php
/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
include "Page.php";

class Debate extends Page
{

    private $committee;
    private $gsl;
    private $dodr;
    private $ssp;
    private $mod;
    private $unm;

    public function __construct()
    {
        parent::__construct();
    }

    private function generalDebate()
    {
        $members = array();
        $speakers = array();
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            if ($member->getAttribute('present') == "true") {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            if ($member->getAttribute('present') == "true") {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        foreach ($this->gsl->getElementsByTagName('speaker') as $speaker) {
            if (isset($members[$speaker->nodeValue])) {
                $speakers[] = $speaker->nodeValue;
            }
        }
        $content = "<header><h1>General Speakers List</h1></header>
                    <div class='debate-container' id='gsl-timer' style='width:40%;'>
                    <h2>Now Speaking</h2>
                    <div class='debate-content-container'>
                    <div class='progressbar-container' id='gsl-progress-bar'></div>";
        if (count($speakers)) {
            $content .= "<div class='speaker-container' id='gsl-current-speaker' data-iso='" . $speakers[0] . "' data-name='" . $members[$speakers[0]] . "'><img src='img/flags_big/" . strtoupper($speakers[0]) . ".png' alt='" . strtoupper($speakers[0]) . "' width='70%'><h3>" . $members[$speakers[0]] . "</h3></div>";
        } else {
            $content .= "<div class='speaker-container' id='gsl-current-speaker'></div>";
        }

        $content .= "
                    </div>
                    <p style='text-align: center;margin:20px;white-space: nowrap'>
                    <a class=\"btn\" id=\"gsl-start-button\"><i class='icon-play'></i></a>
                    <a class=\"btn\" id=\"gsl-pause-button\"><i class='icon-pause'></i></a>
                    <a class=\"btn\" id=\"gsl-reset-button\"><i class='icon-undo'></i></a>
                    <a class=\"btn\" id=\"gsl-next-button\"><i class='icon-step-forward'></i></a>
                    <a class=\"btn\" id=\"gsl-resize-button\"><i class='icon-resize-full'></i></a>
                    </p>
                    </div>
                    <div class='debate-container' style='width:35%;'>
                    <h2>Next Up</h2>
                    <table style='width: 80%; margin-left: auto; margin-right: auto;'>
                    <tbody id='gsl-list'>";
        for ($i = 1; $i < count($speakers); $i++) {
            $content .= "
                    <tr data-iso='" . $speakers[$i] . "' data-name='" . $members[$speakers[$i]] . "'><td class='label-cell'><img src='img/flags_small/" . $speakers[$i] . ".png' alt='" . $speakers[$i] . "' /></td><td style='font-size: 1.5em'>" . $members[$speakers[$i]] . "</td><td class='label-cell'><a href='' style='text-decoration: none;' class='gsl-list-up'><i class='icon-arrow-up'></i></a><a href='' style='text-decoration: none;' class='gsl-list-down'><i class='icon-arrow-down'></i></a></td><td class='label-cell' style='padding-right:14px'><a href='' style='text-decoration: none;' class='gsl-list-remove'><i class='icon-remove'></i></a></td></tr>";
        }
        $content .= "
                    </tbody>
                    </table>
                    </div>
                    <div class='debate-container' style='width:25%;'>
                    <h2>Present</h2>
                    <div id='gsl-pool' class='debate-content-container' style='width: 93%;margin-left:auto;margin-right:auto;'>";
        foreach ($members as $key => $value) {
            $content .= "
                        <img data-iso='" . $key . "' data-name='" . $value . "' src='img/flags_small/" . $key . ".png' alt='" . $key . "' style='cursor: pointer; " . (in_array($key, $speakers) ? "display:none;" : "") . "' title='" . $value . "'/>";
        }
        $content .= "
                    </div>
                    <h2>Settings</h2>
                    <table>
                    <tr><th class='label-cell' style='padding:8px;'>Speaking Time</th><td style='white-space: nowrap'><input type='number' id='gsl-speaking-time' class='input-mini' min='1' value='" . $this->gsl->getAttribute("speakertime") . "'/>s</td></tr>
                    <tr><th class='label-cell' style='padding:8px;'>Warning at</th><td style='white-space: nowrap'><input type='number' id='gsl-warning-time' class='input-mini' min='1' value='" . $this->gsl->getAttribute("warningtime") . "' />s</td></tr>
                    <tr><th class='label-cell' style='padding:8px;'>Auto Next</th><td><input type='checkbox' id='gsl-auto-next' " . ($this->gsl->getAttribute("autonext") == "true" ? "checked='checked'" : "") . " /></td></tr>
                    </table>
                    </div>
                    <br style='clear: left;' />";
        return $content;
    }

    private function resolutionDebate()
    {
        $members = array();
        $speakers = array();
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            if ($member->getAttribute('present') == "true") {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            if ($member->getAttribute('present') == "true") {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        foreach ($this->dodr->getElementsByTagName('speaker') as $speaker) {
            if (isset($members[$speaker->nodeValue])) {
                $speakers[] = $speaker->nodeValue;
            }
        }
        $content = "<header><h1>Debate on Draft Resolution</h1></header>
                    <div class='debate-container' id='dodr-timer' style='width:40%;'>
                    <h2>Now Speaking</h2>
                    <div class='debate-content-container'>
                    <div class='progressbar-container' id='dodr-progress-bar'></div>";
        if (count($speakers)) {
            $content .= "<div class='speaker-container' id='dodr-current-speaker' data-iso='" . $speakers[0] . "' data-name='" . $members[$speakers[0]] . "'><img src='img/flags_big/" . strtoupper($speakers[0]) . ".png' alt='" . strtoupper($speakers[0]) . "' width='70%'><h3>" . $members[$speakers[0]] . "</h3></div>";
        } else {
            $content .= "<div class='speaker-container' id='dodr-current-speaker'></div>";
        }

        $content .= "
                    </div>
                    <p style='text-align: center;margin:20px; white-space: nowrap'>
                    <a class=\"btn\" id=\"dodr-start-button\"><i class='icon-play'></i></a>
                    <a class=\"btn\" id=\"dodr-pause-button\"><i class='icon-pause'></i></a>
                    <a class=\"btn\" id=\"dodr-reset-button\"><i class='icon-undo'></i></a>
                    <a class=\"btn\" id=\"dodr-next-button\"><i class='icon-step-forward'></i></a>
                    <a class=\"btn\" id=\"dodr-resize-button\"><i class='icon-resize-full'></i></a>
                    </p>
                    </div>
                    <div class='debate-container' style='width:35%;'>
                    <h2>Next Up</h2>
                    <table style='width: 80%; margin-left: auto; margin-right: auto;'>
                    <tbody id='dodr-list'>";
        for ($i = 1; $i < count($speakers); $i++) {
            $content .= "
                    <tr data-iso='" . $speakers[$i] . "' data-name='" . $members[$speakers[$i]] . "'><td class='label-cell'><img src='img/flags_small/" . $speakers[$i] . ".png' alt='" . $speakers[$i] . "' /></td><td style='font-size: 1.5em'>" . $members[$speakers[$i]] . "</td><td class='label-cell'><a href='' style='text-decoration: none;' class='dodr-list-up'><i class='icon-arrow-up'></i></a><a href='' style='text-decoration: none;' class='dodr-list-down'><i class='icon-arrow-down'></i></a></td><td class='label-cell'  style='padding-right:14px'><a href='' style='text-decoration: none;' class='dodr-list-remove'><i class='icon-remove'></i></a></td></tr>";
        }
        $content .= "
                    </tbody>
                    </table>
                    </div>
                    <div class='debate-container' style='width:25%;'>
                    <h2>Present</h2>
                    <div id='dodr-pool' class='debate-content-container' style='width: 93%;margin-left:auto;margin-right:auto;'>";
        foreach ($members as $key => $value) {
            $content .= "
                        <img data-iso='" . $key . "' data-name='" . $value . "' src='img/flags_small/" . $key . ".png' alt='" . $key . "'style='cursor: pointer; " . (in_array($key, $speakers) ? "display:none;" : "") . "' title='" . $value . "'/>";
        }
        $content .= "
                    </div>
                    <h2>Settings</h2>
                    <table>
                    <tr><th class='label-cell' style='padding:8px;'>Speaking Time</th><td style='white-space: nowrap'><input type='number' id='dodr-speaking-time' class='input-mini' min='1' value='" . $this->dodr->getAttribute("speakertime") . "'/>s</td></tr>
                    <tr><th class='label-cell' style='padding:8px;'>Warning at</th><td style='white-space: nowrap'><input type='number' id='dodr-warning-time' class='input-mini' min='1' value='" . $this->dodr->getAttribute("warningtime") . "' />s</td></tr>
                    <tr><th class='label-cell' style='padding:8px;'>Auto Next</th><td><input type='checkbox' id='dodr-auto-next' " . ($this->dodr->getAttribute("autonext") == "true" ? "checked='checked'" : "") . " /></td></tr>
                    </table>
                    </div>
                    <br style='clear: left;' />";
        return $content;
    }

    private function singleSpeaker()
    {
        $members = array();
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            if ($member->getAttribute('present') == "true") {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            if ($member->getAttribute('present') == "true") {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        $members['_' . $this->committee->getAttribute('organization')] = "Guest Speaker";
        $content = "<header><h1>Single Speaker</h1></header>
                    <div class='debate-container' id='ssp-timer' style='width:65%;'>
                    <h2>Now Speaking</h2>
                    <div class='debate-content-container'>
                    <div class='progressbar-container' id='ssp-progress-bar'></div>";
        if ($this->ssp->getAttribute('speaker') AND isset($members[$this->ssp->getAttribute('speaker')])) {
            $content .= "<div class='speaker-container' id='ssp-current-speaker' data-iso='" . $this->ssp->getAttribute('speaker') . "' data-name='" . $members[$this->ssp->getAttribute('speaker')] . "'><img src='img/flags_big/" . strtoupper($this->ssp->getAttribute('speaker')) . ".png' alt='" . strtoupper($this->ssp->getAttribute('speaker')) . "' width='70%'><h3>" . $members[$this->ssp->getAttribute('speaker')] . "</h3></div>";
        } else {
            $content .= "<div class='speaker-container' id='ssp-current-speaker'></div>";
        }
        $content .= "
                    </div>
                    <p style='text-align: center;margin:20px;white-space: nowrap'>
                    <a class=\"btn\" id=\"ssp-start-button\"><i class='icon-play'></i></a>
                    <a class=\"btn\" id=\"ssp-pause-button\"><i class='icon-pause'></i></a>
                    <a class=\"btn\" id=\"ssp-reset-button\"><i class='icon-undo'></i></a>
                    <a class=\"btn\" id=\"ssp-remove-button\"><i class='icon-remove'></i></a>
                    </p>
                    </div>
                    <div class='debate-container' style='width:35%;'>
                    <h2>Present</h2>
                    <div id='ssp-pool' class='debate-content-container' style='width: 80%;margin-left:auto;margin-right:auto;'>";
        foreach ($members as $key => $value) {
            $content .= "
                        <img data-iso='" . $key . "' data-name='" . $value . "' src='img/flags_small/" . $key . ".png' alt='" . $key . "' style='cursor: pointer; " . ($this->ssp->getAttribute('speaker') == $key ? "display:none;" : "") . "' title='" . $value . "'/>";
        }
        $content .= "
                    </div>
                    <h2>Settings</h2>
                    <table style='width: 83%;margin-left:auto;margin-right:auto;'>
                    <tr><th class='label-cell' style='padding:8px;'>Speaking Time</th><td style='white-space: nowrap'><input type='number' id='ssp-speaking-time-min' class='input-mini' min='0' value='" . intval($this->ssp->getAttribute('speakertime') / 60) . "'/>:<input type='number' id='ssp-speaking-time-sec' class='input-mini' min='0' max='59' value='" . ($this->ssp->getAttribute('speakertime') % 60) . "'/></td></tr>
                    <tr><th class='label-cell' style='padding:8px;'>Warning at</th><td style='white-space: nowrap'><input type='number' id='ssp-warning-time' class='input-mini' min='1' value='" . intval($this->ssp->getAttribute('warningtime')) . "' />s</td></tr>
                    <tr><th class='label-cell' style='padding:8px;'>Auto Play</th><td><input type='checkbox' id='ssp-auto-play' " . ($this->ssp->getAttribute("autoplay") == "true" ? "checked='checked'" : "") . " /></td></tr>
                    </table>
                    </div>
                    <br style='clear: left;' />";
        return $content;
    }

    private function moderatedCaucus()
    {
        $members = array();
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            if ($member->getAttribute('present') == "true") {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            if ($member->getAttribute('present') == "true") {
                $members[$member->getAttribute('iso')] = $member->nodeValue;
            }
        }
        $content = "<header><h1 id='mod-heading'>Moderated Caucus - " . $this->mod->getAttribute('topic') . "</h1></header>
                    <div class='debate-container' id='mod-timer' style='width:65%;'>
                    <h2>Now Speaking</h2>
                    <div class='debate-content-container'>
                    <div class='progressbar-container' id='mod-progress-bar'></div>";
        if ($this->mod->getAttribute('speaker') AND isset($members[$this->mod->getAttribute('speaker')])) {
            $content .= "<div class='speaker-container' id='mod-current-speaker' data-iso='" . $this->mod->getAttribute('speaker') . "' data-name='" . $members[$this->mod->getAttribute('speaker')] . "'><img src='img/flags_big/" . strtoupper($this->mod->getAttribute('speaker')) . ".png' alt='" . strtoupper($this->ssp->getAttribute('speaker')) . "' width='70%'><h3>" . $members[$this->mod->getAttribute('speaker')] . "</h3></div>";
        } else {
            $content .= "<div class='speaker-container' id='mod-current-speaker'></div>";
        }
        $content .= "
                    </div>
                    <p style='text-align: center;margin:20px;white-space: nowrap'>
                    <a class=\"btn\" id=\"mod-start-button\"><i class='icon-play'></i></a>
                    <a class=\"btn\" id=\"mod-pause-button\"><i class='icon-pause'></i></a>
                    <a class=\"btn\" id=\"mod-reset-button\"><i class='icon-undo'></i></a>
                    <a class=\"btn\" id=\"mod-remove-button\"><i class='icon-remove'></i></a>
                    </p>
                    </div>
                    <div class='debate-container' style='width:35%;'>
                    <h2>Present</h2>
                    <div id='mod-pool' class='debate-content-container' style='width: 80%;margin-left:auto;margin-right:auto;'>";
        foreach ($members as $key => $value) {
            $content .= "
                        <img data-iso='" . $key . "' data-name='" . $value . "' src='img/flags_small/" . $key . ".png' alt='" . $key . "' style='cursor: pointer; " . ($this->mod->getAttribute('speaker') == $key ? "display:none;" : "") . "' title='" . $value . "'/>";
        }
        $content .= "
                    </div>
                    <h2>Duration</h2>
                    <div class='debate-content-container' style='width:50%; margin-left:auto;margin-right:auto;'>
                    <div class='progressbar-container' style='width:100%' id='mod-duration-progress-bar'></div>
                    </div>
                    <p style='text-align:center;margin:20px;'>
                    <a class=\"btn\" id=\"mod-duration-reset-button\"><i class='icon-undo'></i></a>
                    </p>
                    </div>
                    <br style='clear: left;' />
                    <table style='width:95%'>
                    <tr>
                        <td class='label-cell'>Speaking Time</td><td style='white-space: nowrap'><input type='number' id='mod-speaking-time' class='input-mini' min='1' value='" . $this->mod->getAttribute('speakertime') . "'/>s</td>
                        <td class='label-cell'>Warning at</td><td style='white-space: nowrap'><input type='number' id='mod-warning-time' class='input-mini' min='1' value='" . intval($this->mod->getAttribute('warningtime')) . "' />s</td>
                        <td class='label-cell'>Auto Play</td><td><input type='checkbox' id='mod-auto-play' " . ($this->mod->getAttribute("autoplay") == "true" ? "checked='checked'" : "") . " /></td>
                        <td class='label-cell'>Topic</td><td style='white-space: nowrap'><input type='text' id='mod-topic' min='1' value='" . $this->mod->getAttribute('topic') . "'/></td>
                        <td class='label-cell'>Duration</td><td style='white-space: nowrap'><input type='number' id='mod-duration-time' class='input-mini' min='1' value='" . $this->mod->getAttribute('duration') . "'/>min</td>
                        <td class='label-cell'>Warning at</td><td style='white-space: nowrap'><input type='number' id='mod-duration-warning' class='input-mini' min='1' value='" . intval($this->mod->getAttribute('durationwarning')) . "' />min</td>
                    </tr>
                    </table>";
        return $content;
    }

    private function unmoderatedCaucus()
    {
        $content = "<header><h1 id='unm-heading'>Unmoderated Caucus - " . $this->unm->getAttribute('topic') . "</h1></header>
                    <div class='debate-content-container' style='width:35%;margin-left:auto;margin-right:auto;'>
                    <div id='unm-progress-bar'></div>
                    </div>
                    <p style='text-align: center;margin:20px;white-space: nowrap'>
                    <a class=\"btn\" id=\"unm-start-button\"><i class='icon-play'></i></a>
                    <a class=\"btn\" id=\"unm-pause-button\"><i class='icon-pause'></i></a>
                    <a class=\"btn\" id=\"unm-reset-button\"><i class='icon-undo'></i></a>
                    </p>
                    <table style='width:30%;margin-left:auto;margin-right:auto;'>
                    <tr>
                        <td class='label-cell'>Topic</td><td style='white-space: nowrap'><input type='text' id='unm-topic' min='1' value='" . $this->unm->getAttribute('topic') . "'/></td>
                        <td class='label-cell'>Duration</td><td style='white-space: nowrap'><input type='number' id='unm-duration-time' class='input-mini' min='1' value='" . $this->unm->getAttribute('duration') . "'/>min</td>
                        <td class='label-cell'>Warning at</td><td style='white-space: nowrap'><input type='number' id='unm-duration-warning' class='input-mini' min='1' value='" . intval($this->unm->getAttribute('durationwarning')) . "' />min</td>
                    </tr>
                    </table>";
        return $content;
    }

    protected function processPage()
    {
        $this->setTitle("Run the Debate");
        $this->enableWideContent();
        $this->enableTabs();
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $this->committee = $config->getElementsByTagName("committee")->item(0);
        $this->gsl = $config->getElementsByTagName("general")->item(0);
        $this->dodr = $config->getElementsByTagName("resolution")->item(0);
        $this->ssp = $config->getElementsByTagName("single")->item(0);
        $this->mod = $config->getElementsByTagName("moderated")->item(0);
        $this->unm = $config->getElementsByTagName("unmoderated")->item(0);
        $topic = "";
        foreach ($this->committee->getElementsByTagName('topic') as $t) {
            if ($t->getAttribute('current') == "true") {
                $topic = $t->nodeValue;
            }
        }
        $present = 0;
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            if ($member->getAttribute('present') == "true") {
                $present++;
            }
        }
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            if ($member->getAttribute('present') == "true") {
                $present++;
            }
        }
        $twothird = intval($present * 2 / 3 + 1);
        $simple = intval($present / 2 + 1);
        $this->setHeader("<div class='msg-alert'><h1>" . $this->committee->getAttribute('name') . " - " . $topic . "</h1><p class='msg'>Present: " . $present . ", Two-Third: " . $twothird . ", Simple: " . $simple . "</p></div>");
        $content =
            "<section>
                    <div id=\"tabs\">
                        <ul>
                            <li><a href=\"#tabs-0\" >General</a></li>
                            <li><a href=\"#tabs-1\">Resolution</a></li>
                            <li><a href=\"#tabs-2\">Speaker</a></li>
                            <li><a href=\"#tabs-3\">Moderated</a></li>
                            <li><a href=\"#tabs-4\">Unmod.</a></li>
                            <span class=\"tabulousclear\"></span>
                        </ul>
                    <div id=\"tabs_container\">

                        <div id=\"tabs-0\">
                            " . $this->generalDebate() . "
                        </div>

                        <div id=\"tabs-1\">
                            " . $this->resolutionDebate() . "
                        </div>

                        <div id=\"tabs-2\">
                            " . $this->singleSpeaker() . "
                        </div>

                        <div id=\"tabs-3\">
                            " . $this->moderatedCaucus() . "
                        </div>

                        <div id=\"tabs-4\">
                            " . $this->unmoderatedCaucus() . "
                        </div>
                    </div>

                </div>


            </section>
            ";
        $this->setBody($content);
        $script = "
            <script src='js/progressbar.min.js'></script>
            <script src='js/debate.general.js'></script>
            <script src='js/debate.resolution.js'></script>
            <script src='js/debate.speaker.js'></script>
            <script src='js/debate.moderated.js'></script>
            <script src='js/debate.unmoderated.js'></script>
            ";
        $this->setScript($script);

    }
}

?>
