<?php
/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
include "Page.php";

class Rollcall extends Page
{
    protected function processPage()
    {
        $this->setTitle("Roll Call");
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $committee = $config->getElementsByTagName("committee")->item(0);
        $topic = "";
        foreach ($committee->getElementsByTagName('topic') as $t) {
            if ($t->getAttribute('current') == "true") {
                $topic = $t->nodeValue;
            }
        }
        $this->setHeader("<div class='msg-alert'><h1>" . $committee->getAttribute('name') . " - " . $topic . "</h1></div>");
        $content =
            "<section>
                <h1>Roll Call</h1>
                <p><a class='rc-reset btn'>New Session</a></p>
                <table>
                    <tbody id='rc-table'>";
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            if ($member->getAttribute('present') == "true" AND $member->getAttribute('voting') == "true") {
                $call = "voting";
            } elseif ($member->getAttribute('present') == "true" AND $member->getAttribute('voting') == "false") {
                $call = "present";
            } else {
                $call = "absent";
            }
            $content .= "
                        <tr data-iso='" . $member->getAttribute('iso') . "'><td><img src='img/flags_small/" . $member->getAttribute('iso') . ".png' alt='" . $member->getAttribute('iso') . "' /></td><td>" . $member->nodeValue . "</td><td><button class='rc-voting btn" . ($call == "voting" ? " highlight" : "") . "'>Present and Voting</button></td><td><button class='rc-present btn" . ($call == "present" ? " highlight" : "") . "'>Present</button></td><td><button  class='rc-absent btn" . ($call == "absent" ? " highlight" : "") . "'>Absent</button></td></tr>";
        }
        foreach ($committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            $content .= "
                        <tr data-iso='" . $member->getAttribute('iso') . "'><td><img src='img/flags_small/" . $member->getAttribute('iso') . ".png' alt='" . $member->getAttribute('iso') . "' /></td><td>" . $member->nodeValue . "</td><td></td><td><button class='rc-present btn" . ($member->getAttribute('present') == "true" ? " highlight" : "") . "'>Present</button></td><td><button  class='rc-absent btn" . ($member->getAttribute('present') == "false" ? " highlight" : "") . "'>Absent</button></td></tr>";
        }
        $content .= "
                </tbody>
            </table>
            </section>
            ";
        $this->setBody($content);
        $script = "<script src='js/rollcall.js'></script>";
        $this->setScript($script);

    }
}

?>
