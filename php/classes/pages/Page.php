<?php

/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */

abstract class Page extends Output
{

    protected $mPageSettings;
    private $mPageContent;

    public function __construct()
    {
        parent::__construct();

        $this->mPageSettings['useTabs'] = false;
        $this->mPageContent['%title%'] = "";
        $this->mPageContent['%css%'] = "";
        $this->mPageContent['%navi%'] = "";
        $this->mPageContent['%header%'] = "";
        $this->mPageContent['%big%'] = "";
        $this->mPageContent['%body%'] = "";
        $this->mPageContent['%sponsors%'] = "";
        $this->mPageContent['%footer%'] = "";
        $this->mPageContent['%tabScript%'] = "";
        $this->mPageContent['%script%'] = "";
    }

    public function produceOutput()
    {
        $this->processPage();
        $this->createNavi();
        $this->createSponsorLogos();
        $this->createTabScripts();

        if (!file_exists($filename = CONTENT_PATH . "index.content")) {
            return;
        }
        $handle = fopen($filename, "r");
        $content = fread($handle, filesize($filename));
        fclose($handle);
        $this->mPageContent['%body%'] = $this->mPageContent['%body%'] ? "<div id=\"content\">" . $this->mPageContent['%body%'] . "</div>" : "";
        foreach ($this->mPageContent as $key => $value) {
            $content = str_replace($key, $value, $content);
        }
        return $content;
    }

    protected abstract function processPage();

    private function createNavi()
    {
        $this->mPageContent['%navi%'] = "<li><a href=\"?p=debate\">Debate</a></li>
                        <li><a href='?p=rollcall'>Roll Call</a></li>
                        <li><a href='?p=vote'>Vote</a></li>
                        <li><a href='?p=setup'>Setup</a></li>";
    }

    private function createSponsorLogos()
    {

        $result = array(
            array("Name" => "Technische Univerität Dresden", "Logo" => "technische_universitaet_dresden.gif"),
            array("Name" => "DGVN", "Logo" => "dgvn.png"),
            array("Name" => "Europäische Bewegung Sachsen", "Logo" => "europaeische_bewegung_sachsen.png"),
            array("Name" => "Sächsischer Landtag", "Logo" => "saechsischer_landtag.png"),
            array("Name" => "StuRa TU Dresden", "Logo" => "stura_tu_dresden.png")
        );
        if ($result) {
            foreach ($result as $row) {
                if ($row['Logo']) {
                    $this->mPageContent['%sponsors%'] .= "<img class=\"sponsor\" alt=\"" . $row['Name'] . "\" title=\"" . $row['Name'] . "\" src=\"" . SPONSORS_IMAGE_PATH . $row['Logo'] . "\">";
                }
            }
        }
        $dom = new DOMDocument();
        $dom->load(LIFECYCLE_PATH . LIFECYCLE . ".xml");
        $lifecycle = $dom->getElementsByTagName("lifecycle")->item(0);
        $motto = $lifecycle->getElementsByTagName("motto")->item(0)->nodeValue;
        $motto = str_replace("/", " ", $motto);
        $date = "";
        foreach ($lifecycle->getElementsByTagName("duration") as $key => $item) {
            $start = strtotime($item->getAttribute("start") . " 12:00");
            $end = strtotime($item->getAttribute("end") . " 12:00");
            if ($item->getAttribute("type") == "elbmun") {
                if (date("m", $start) == date("m", $end)) {
                    $date = date("j", $start) . " - " . date("j F", $end);
                } else {
                    $date = date("j F", $start) . " - " . date("j F", $end);
                }
                break;
            }
        }
        $this->mPageContent['%footer%'] = "
                <div class=\"footer-column\">
                    <h1 style='text-align: left;'>elbMUN Conference " . LIFECYCLE . "</h1>
                    <p>" . $date . "</p>
                    <p>" . $motto . "</p>
                    <p>Saxon State Parliament</p>
                </div>
                <div class=\"footer-column\">
                    <h1 style='text-align: left;'>Address</h1>
                    <p>Elbe Model United Nations e. V.</p>
                    <p>Technische Universit&auml;t Dresden</p>
                    <p>Juristische Fakult&auml;t</p>
                    <p>01062 Dresden</p>
                    <p>Germany</p>
                </div>

                <div class=\"footer-column\">
                    <h1 style='text-align: left;'>Executive Board</h1>
                    <p><a href=\"mailto:info@elbmun.org\">info@elbmun.org</a></p>";
        foreach ($lifecycle->getElementsByTagName("coordinator") as $item) {
            $this->mPageContent['%footer%'] .= "<p><a href=\"mailto:" . $item->getAttribute("email") . "\">" . $item->nodeValue . "</a></p>";
        }
        $this->mPageContent['%footer%'] .= "
                </div>";
    }

    private function createTabScripts()
    {
        if ($this->mPageSettings['useTabs']) {
            $this->mPageContent['%tabScript%'] = "$(\"#tabs_container div[id|='tabs']\").hide();
                $(\"#tabs-0\").show();
                $(\"#tabs a[href='#tabs-0']\").addClass(\"tabulous_active\");
                $(\"#tabs a[href|='#tabs']\").bind(\"click\", function() {
                    window.location.hash = $(this).attr(\"href\").replace('tabs-', '');
                    $(\"#tabs a[href|='#tabs']\").removeClass(\"tabulous_active\");
                    $(\"#tabs_container div[id|='tabs']\").hide();
                    $(this).addClass(\"tabulous_active\");
                    $('#'+$(this).attr(\"href\").split(\"#\")[1]).fadeIn(500);
                    return false;
                })
                var hash = window.location.hash.replace('#','');
                if(!isNaN(parseInt(hash))) {
                    $(\"body #tabs li a[href=#tabs-\"+hash+\"]\").click();
                }
                ";
        }
    }

    protected function enableTabs()
    {
        $this->mPageSettings['useTabs'] = true;
    }

    protected function disableTabs()
    {
        $this->mPageSettings['useTabs'] = false;
    }

    protected function enableWideContent()
    {
        $this->mPageContent['%big%'] = "-big";
    }

    protected function disableWideContent()
    {
        $this->mPageContent['%big%'] = "";
    }

    protected function setTitle($pTitle)
    {
        $this->mPageContent['%title%'] = $pTitle;
    }

    protected function setHeader($pHeader)
    {
        $this->mPageContent['%header%'] = $pHeader;
    }

    protected function setCss($pCss)
    {
        $this->mPageContent['%css%'] = $pCss;
    }

    protected function setBody($pBody)
    {
        $this->mPageContent['%body%'] = $pBody;
    }

    protected function appendBody($pBody)
    {
        $this->mPageContent['%body%'] .= $pBody;
    }

    protected function setScript($pScript)
    {
        $this->mPageContent['%script%'] = $pScript;
    }

    protected function appendScript($pScript)
    {
        $this->mPageContent['%script%'] .= $pScript;
    }
}

?>