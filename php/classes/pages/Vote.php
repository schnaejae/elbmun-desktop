<?php
/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
include "Page.php";

class Vote extends Page
{

    private $committee;
    private $present = 0;
    private $members = 0;
    private $type;
    private $majority;
    private $veto;
    private $divide;

    public function __construct()
    {
        parent::__construct();
    }

    private function prepare()
    {
        $observer = $this->present - $this->members;
        if ($observer == 0) {
            $observerSentence = "";
        } elseif ($observer == 1) {
            $observerSentence = "and 1 Observer ";
        } else {
            $observerSentence = "and " . $observer . " Observers ";
        }
        $content = "<h1>Prepare the Vote</h1>
            <table style='width: 80%;margin-left:auto;margin-right: auto;'>
                <tr><th style='text-align: right; width: 140px; font-size:1.3em'>Vote Type</th><td class='label-cell'>Substantial</td><td><input name='vote-prepare-type' type='radio' id='vote-prepare-type-substantial' " . ($this->type == "substantial" ? "checked='checked' " : "") . "/></td><td class='label-cell'>Procedural</td><td colspan='4'><input type='radio' name='vote-prepare-type' id='vote-prepare-type-procedural' " . ($this->type == "procedural" ? "checked='checked' " : "") . "/></td></tr>
                <tr><th style='text-align: right; font-size:1.3em'>Majority Parameters</th><td class='label-cell'>Simple</td><td><input name='vote-prepare-majority' type='radio' id='vote-prepare-majority-simple' " . ($this->majority == "simple" ? "checked='checked' " : "") . "/></td><td class='label-cell'>Two-Third</td><td><input type='radio' name='vote-prepare-majority' id='vote-prepare-majority-twothird' " . ($this->majority == "twothird" ? "checked='checked' " : "") . "/></td><td class='label-cell'>Custom</td><td class='label-cell'><input type='radio' name='vote-prepare-majority' id='vote-prepare-majority-custom' " . (is_numeric($this->majority) ? "checked='checked' " : "") . "/></td><td><input class='input-mini' type='number' min='1' value='" . (is_numeric($this->majority) ? $this->majority : 9) . "' id='vote-prepare-majority-custom-number'/></td></tr>
                <tr><th style='text-align: right; font-size:1.3em'>Special Settings</th><td class='label-cell'>P5-Veto</td><td><input type='checkbox' id='vote-prepare-veto' " . ($this->veto == "true" ? "checked='checked' " : "") . "/></td><td class='label-cell'>Divide House</td><td colspan='4'><input type='checkbox' id='vote-prepare-divide' " . ($this->divide == "true" ? "checked='checked' " : "") . "/></td></tr>
                <tr><th></th><td class='msg-alert' colspan='8'><p>There " . ($this->members > 1 ? "are" : "is") . " " . ($this->members ? $this->members : "no") . " Member State" . ($this->members > 1 ? "s" : "") . " " . $observerSentence . " present</p></td></tr>
                <tr><th></th><td class='msg-alert' colspan='8'><p>Majority at <b id='vote-prepare-number'>90</b> Affirmative Votes</p></td></tr>
            </table>
            <p style='text-align:center'><a href='' class='btn' id='vote-prepare-rollcall'>Start Vote by Roll Call</a></p>
            ";
        return $content;
    }

    private function firstRound()
    {
        $content = "<h1>First Round of Voting by Roll Call</h1>
                <table>
                    <tbody id='vote-first-table'>";
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            $veto = $member->getAttribute('iso') == "cn" || $member->getAttribute('iso') == "fr" || $member->getAttribute('iso') == "ru" || $member->getAttribute('iso') == "uk" || $member->getAttribute('iso') == "us";
            if ($member->getAttribute('present') == "true") {
                $content .= "
                        <tr data-status='state' data-iso='" . $member->getAttribute('iso') . "' data-name='" . $member->nodeValue . "' data-voting='" . $member->getAttribute('voting') . "' data-veto='" . ($veto ? "true" : "false") . "'><td class='vote-list'><img src='img/flags_small/" . $member->getAttribute('iso') . ".png' alt='" . $member->getAttribute('iso') . "' /></td><td class='vote-list'>" . $member->nodeValue . "</td><td class='vote-list'><button class='vote-first-yes btn'>Yes</button></td><td class='vote-list'><button class='vote-first-no btn'>No</button></td><td class='vote-first-divide-column vote-list'><button class='vote-first-abstain btn'>Abstain</button></td><td class='vote-first-divide-column vote-list'><button class='vote-first-pass btn'>Pass</button></td><td class='vote-first-substantial-column vote-list'><button class='vote-first-yes-rights btn'>Yes (Rights)</button></td><td class='vote-first-substantial-column vote-list'><button class='vote-first-no-rights btn'>No (Rights)</button></td></tr>";
            }
        }
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $member) {
            $content .= "
                        <tr data-status='observer'><td><img src='img/flags_small/" . $member->getAttribute('iso') . ".png' alt='" . $member->getAttribute('iso') . "' /></td><td class='vote-list'>" . $member->nodeValue . "</td><td class='vote-list'><button class='vote-first-yes btn'>Yes</button></td><td class='vote-list'><button class='vote-first-no btn'>No</button></td><td class='vote-first-substantial-column vote-list'></td><td class='vote-first-divide-column vote-list'></td><td class='vote-first-divide-column  vote-list'></td><td class='vote-first-substantial-column vote-list'></td></tr>";
        }
        $content .= "
                </tbody>
            </table>
            <p style='text-align:center'><a href='' class='btn' id='vote-prepare-first-next'>Next</a></p>";
        return $content;
    }

    private function secondRound()
    {
        $content = "<h1>Second Round of Voting by Roll Call</h1>
                <table>
                    <tbody id='vote-second-table'>";
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            $veto = $member->getAttribute('iso') == "cn" || $member->getAttribute('iso') == "fr" || $member->getAttribute('iso') == "ru" || $member->getAttribute('iso') == "uk" || $member->getAttribute('iso') == "us";
            if ($member->getAttribute('present') == "true") {
                $content .= "
                        <tr data-voting='" . $member->getAttribute('voting') . "' data-iso='" . $member->getAttribute('iso') . "' data-name='" . $member->nodeValue . "' data-veto='" . ($veto ? "true" : "false") . "'><td class='vote-list'><img src='img/flags_small/" . $member->getAttribute('iso') . ".png' alt='" . $member->getAttribute('iso') . "'/></td><td class='vote-list'>" . $member->nodeValue . "</td><td class='vote-list'><button class='vote-second-yes btn'>Yes</button></td><td class='vote-list'><button class='vote-second-no btn'>No</button></td><td class='vote-list'><button class='vote-second-pass btn'>Pass</button></td><td class='vote-list'><button class='vote-second-yes-rights btn'>Yes (Rights)</button></td><td class='vote-list'><button class='vote-second-no-rights btn'>No (Rights)</button></td></tr>";
            }
        }
        $content .= "
                </tbody>
            </table>
            <p style='text-align:center'><a href='' class='btn' id='vote-prepare-second-next'>Next</a></p>";
        return $content;
    }

    private function outcome()
    {
        $content = "<h1>Outcome of the Vote</h1>
            <div class='msg-alert' style='border: 1px solid #CECECE; background-color:#FFFFFF;width: 80%;margin-left:auto;margin-right: auto;margin-bottom:1.4em;'><h1 id='vote-outcome-message'>Vote was successful</h1></div>
            <div class='msg-alert' style='border: 1px solid #CECECE; background-color:#FFFFFF;width: 80%;margin-left:auto;margin-right: auto;'><p id='vote-outcome-result'>No Results Yet</p></div>
            <h1>Rights to Explain Vote</h1>
            <table style='width: 80%;margin-left: auto;margin-right: auto;'>
                <tbody id='vote-outcome-rights'>
                </tbody>
            </table>
            ";
        return $content;
    }

    protected function processPage()
    {
        $this->setTitle("Vote");;
        $this->enableTabs();
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $this->committee = $config->getElementsByTagName("committee")->item(0);
        $topic = "";
        foreach ($this->committee->getElementsByTagName('topic') as $t) {
            if ($t->getAttribute('current') == "true") {
                $topic = $t->nodeValue;
            }
        }
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $m) {
            if ($m->getAttribute('present') == "true") {
                $this->present++;
                $this->members++;
            }
        }
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $m) {
            if ($m->getAttribute('present') == "true") {
                $this->present++;
            }
        }
        $vote = $this->committee->getElementsByTagName('vote')->item(0);
        $this->type = $vote->getAttribute('type');
        $this->majority = $vote->getAttribute('majority');
        $this->veto = $vote->getAttribute('veto');
        $this->divide = $vote->getAttribute('divide');
        $this->setHeader("<div class='msg-alert'><h1>" . $this->committee->getAttribute('name') . " - " . $topic . "</h1></div>");
        $content =
            "<section>
                    <div id=\"tabs\">
                        <ul>
                            <li><a href=\"#tabs-0\" >Prepare</a></li>
                            <li><a href=\"#tabs-1\">First Round</a></li>
                            <li><a href=\"#tabs-2\">Second Round</a></li>
                            <li><a href=\"#tabs-3\">Outcome</a></li>
                            <span class=\"tabulousclear\"></span>
                        </ul>
                    <div id=\"tabs_container\">

                        <div id=\"tabs-0\">
                            " . $this->prepare() . "
                        </div>

                        <div id=\"tabs-1\">
                            " . $this->firstRound() . "
                        </div>

                        <div id=\"tabs-2\">
                            " . $this->secondRound() . "
                        </div>

                        <div id=\"tabs-3\">
                            " . $this->outcome() . "
                        </div>
                    </div>

                </div>


            </section>
            ";
        $this->setBody($content);
        $script = "
            <script type='application/javascript'>
                var vote = {
                    present: " . $this->present . ",
                    members: " . $this->members . ",
                    type: '" . $this->type . "',
                    majority: " . (is_numeric($this->majority) ? $this->majority : "'" . $this->majority . "'") . ",
                    veto: " . $this->veto . ",
                    divide: " . $this->divide . "
                }
            </script>
            <script src='js/vote.js'></script>
            ";
        $this->setScript($script);

    }
}

?>
