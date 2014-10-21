<?php

namespace Maximethebault\IntraFetcher;

use Maximethebault\IntraFetcher\HttpRequest\HttpRequestManager;
use URL\Normalizer;

class IntraFetcher
{
    /**
     * @var Config
     */
    private $_config;
    /**
     * @var HttpRequestManager
     */
    private $_httpRequestManager;
    /**
     * @var string
     */
    private $_baseUrl;
    /**
     * Used when the menus have just been fetched and haven't been sorted yet
     *
     * @var Menu[]
     */
    private $_rawMenu;
    /**
     * New menus compared to last time
     *
     * @var Menu[]
     */
    private $_newMenu;
    /**
     * Updated menus compared to last time
     *
     * @var Menu[]
     */
    private $_updatedMenu;

    /**
     * @param $config Config
     */
    public function __construct($config) {
        $this->_config = $config;
        $this->_httpRequestManager = new HttpRequestManager($this->_config);
        $this->_rawMenu = array();
        $this->_newMenu = array();
        $this->_updatedMenu = array();
    }

    /**
     * To be called when all operations with the PDF files are complete and we want to save the current state.
     * This should typically not be called when an error happened if we want to replay the fetching later!
     * This is also a protection against half-done work, in case of timeout limits or such
     */
    public function commitChanges() {
    }

    public function getUpdatedMenu() {
    }

    public function getNewMenu() {
    }

    public function fetchRawMenu() {
        $this->fetchMenuFromIntranetPage();
        $this->fetchMenuFromUrlGuessing();
    }

    public function organizeRawMenu() {
    }

    public function checkMenu() {
    }

    /**
     *
     * @return Menu[]
     */
    private function fetchMenuFromIntranetPage() {
        $pageData = $this->_httpRequestManager->getPage('http://intranet.insa-rennes.fr/index.php?id=56');
        // extracts PDF's urls from the page
        $matches = array();
        if(preg_match_all('`<a href="(.*)"(?:.*)>(?:.*)menu(?:.*)</a>`i', $pageData, $matches)) {
            // for each URL found
            foreach($matches[1] as $url) {
                if(strstr('intranet.insa-rennes.fr', $url) === false) {
                    $url = 'http://intranet.insa-rennes.fr/' . $url;
                }
                // gets a normalized URL
                $un = new Normalizer();
                $un->setUrl($url);
                $url = $un->normalize();
                // gets the path to the PDF file
                $urlParts = explode('/', $url);
                $baseName = array_pop($urlParts);
                $this->_baseUrl = implode('/', $urlParts);
                // download the PDF
                $pdfData = $this->_httpRequestManager->getPage($url);
                // check if the file exists and is a PDF
                if(!PdfFile::isPdfData($pdfData)) {
                    continue;
                }
                // get the file's basename and registers it as a new menu
                $filename = array_shift(explode('?', $baseName));
                $this->_rawMenu[] = new Menu($filename, $pdfData);
            }
        }
    }

    /**
     *
     * @return Menu[]
     */
    private function fetchMenuFromUrlGuessing() {
        if(!$this->_baseUrl) {
            $this->_baseUrl = 'http://intranet.insa-rennes.fr/fileadmin/ressources_intranet/Restaurant';
        }
        $this->_baseUrl = $this->_baseUrl . '/';
        // we need to sort the menus we already got from the intranet
        usort($this->_rawMenu, array('Menu', 'sortByAscendingDate'));
        // get latest menu
        $latestMenu = $this->_rawMenu[count($this->_rawMenu) - 1];
        $basename = $latestMenu->getRemoteName();
        // we'll get the number of the week from the filename, and do a loop from it!
        $currentId = MenuId::fromString($basename);
        $partToReplace = $currentId->getWeekNumber();
        while(true) {
            $menuRemotePath = str_replace($partToReplace, $currentId->getWeekNumber(), $basename);
            $menuRemotePathAlt = null;
            if($currentId->getWeekNumber() < 10) {
                $menuRemotePathAlt = str_replace($partToReplace, '0' . $currentId->getWeekNumber(), $basename);
            }
            $dlUrl = $this->_baseUrl . $menuRemotePath;
            $pdfData = $this->_httpRequestManager->getPage($dlUrl);
            if(!PdfFile::isPdfData($pdfData)) {
                if(!$menuRemotePathAlt) {
                    break;
                }
            }


            $currentId = $currentId->increment();
        }
    }
}