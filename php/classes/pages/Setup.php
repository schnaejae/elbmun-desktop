<?php
/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
include "Page.php";

class Setup extends Page
{

    private $committee;

    public function __construct()
    {
        parent::__construct();
    }

    private function generalSettings()
    {
        $content = "<h1>General Committee Settings</h1>
            <table style='width: 80%;margin-left:auto;margin-right: auto;'>
                <tr><th style='text-align: right; width: 140px; font-size:1.3em'>Name</th><td><input class='input-xxlarge' type='text' id='setup-general-name' value='" . $this->committee->getAttribute('name') . "'/></td></tr>
                <tr><th style='text-align: right; width: 140px; font-size:1.3em'>Organization</th><td><select class='input-xlarge' id='setup-general-organization'><option value='un' " . ($this->committee->getAttribute('organization') == "un" ? "selected='selected'" : "") . ">United Nations</option><option value='eu' " . ($this->committee->getAttribute('organization') == "eu" ? "selected='selected'" : "") . ">European Union</option></select></td></tr>
                </table>
                <table style='width: 80%;margin-left:auto;margin-right: auto;'>
                <tr><th style='text-align: center; font-size:1.3em;width:10px'>Current</th><th style='font-size:1.3em'>Topic</th><th style='width: 10px; font-size:1.3em' id='setup-general-topic-add'><a href='' style='color:#4AC8E9;text-decoration:none;'><i class='icon-plus'></i></a></th></tr>
                <tbody id='setup-general-topic'>
                ";
        foreach ($this->committee->getElementsByTagName('topic') as $topic) {
            $content .= "
                    <tr><td><input type='radio' name='setup-general-topic-current' class='setup-general-topic-current' " . ($topic->getAttribute('current') == "true" ? "checked='checked'" : "") . "></td><td><input type='text' class='setup-general-topic-name input-xxlarge' placeholder='Topic' value='" . $topic->nodeValue . "'/></td><td style='font-size:1.3em'><a href='' style='text-decoration:none;' class='setup-general-topic-remove'><i class='icon-remove'></i></a></th></tr>";

        }
        $content .= "
                </tbody>
                </table>";
        return $content;
    }

    private function countrySettings()
    {
        $members = array();
        $observers = array();
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('state') as $member) {
            $members[$member->getAttribute('iso')] = $member->nodeValue;
        }
        foreach ($this->committee->getElementsByTagName('members')->item(0)->getElementsByTagName('observer') as $observer) {
            $observers[$observer->getAttribute('iso')] = $observer->nodeValue;
        }
        $this->setTitle("Setup");;
        $this->enableTabs();
        $dom = new DOMDocument();
        $dom->load("./resources/countries.xml");
        $countries = $dom->getElementsByTagName("countries")->item(0);
        $firstLetter = 0;
        $content = "<h1>Membership Settings</h1>
                <table>
                    <tbody id='setup-countries-table'>";
        foreach ($countries->getElementsByTagName('country') as $country) {
            if ($firstLetter < ord($country->nodeValue)) {
                $firstLetter = ord($country->nodeValue);
                $letter = strtoupper(substr($country->nodeValue, 0, 1));
                $content .= "
                        <tr><th class='vote-list'>" . $letter . "</th><th colspan='4'></th>";
            }
            $content .= "
                        <tr data-iso='" . $country->getAttribute('iso') . "'><td class='vote-list'><img src='img/flags_small/" . $country->getAttribute('iso') . ".png' alt='" . $country->getAttribute('iso') . "' /></td><td class='vote-list'>" . $country->nodeValue . "</td><td class='vote-list'><button class='setup-countries-member btn" . (isset($members[$country->getAttribute('iso')]) ? " highlight" : "") . "'>Member</button></td><td class='vote-list'><button class='setup-countries-observer btn" . (isset($observers[$country->getAttribute('iso')]) ? " highlight" : "") . "'>Observer</button></td><td class='vote-list'><button class='setup-countries-none btn" . ((!isset($members[$country->getAttribute('iso')]) AND !isset($observers[$country->getAttribute('iso')])) ? " highlight" : "") . "'>None</button></td></tr>";
        }
        $content .= "
                        <tr><th class='vote-list'>NGO/GO</th><th colspan='4'></th>";
        foreach ($countries->getElementsByTagName('organization') as $country) {
            $content .= "
                        <tr data-iso='" . $country->getAttribute('iso') . "'><td class='vote-list'><img src='img/flags_small/" . $country->getAttribute('iso') . ".png' alt='" . $country->getAttribute('iso') . "' /></td><td class='vote-list'>" . $country->nodeValue . "</td><td class='vote-list'><td class='vote-list'><button class='setup-countries-observer btn" . (isset($observers[$country->getAttribute('iso')]) ? " highlight" : "") . "'>Observer</button></td><td class='vote-list'><button class='setup-countries-none btn" . ((!isset($members[$country->getAttribute('iso')]) AND !isset($observers[$country->getAttribute('iso')])) ? " highlight" : "") . "'>None</button></td></tr>";
        }
        $content .= "
                </tbody>
            </table>";
        return $content;
    }

    protected function processPage()
    {
        $this->setTitle("Setup");;
        $this->enableTabs();
        $dom = new DOMDocument();
        $dom->load("./resources/state.xml");
        $config = $dom->getElementsByTagName("config")->item(0);
        $this->committee = $config->getElementsByTagName("committee")->item(0);
        $content =
            "<section>
                    <div id=\"tabs\">
                        <ul>
                            <li><a href=\"#tabs-0\" >General</a></li>
                            <li><a href=\"#tabs-1\">Countries</a></li>
                            <span class=\"tabulousclear\"></span>
                        </ul>
                    <div id=\"tabs_container\">

                        <div id=\"tabs-0\">
                            " . $this->generalSettings() . "
                        </div>

                        <div id=\"tabs-1\">
                         " . $this->countrySettings() . "
                        </div>
                    </div>

                </div>


            </section>
            ";
        $this->setBody($content);
        $script = "
            <script src='js/setup.js'></script>
            ";
        $this->setScript($script);

    }
}

?>
