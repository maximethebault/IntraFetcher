<?php

namespace Maximethebault\IntraFetcher;

use DateTime;
use Maximethebault\IntraFetcher\Excpetion\InconsistentPdfException;
use Maximethebault\Pdf2Table\PdfFile2Table;
use Maximethebault\Pdf2Table\TableCell;

class Menu extends PdfFile
{
    /**
     * @var MenuId
     */
    private $_menuId;
    /**
     * @var \Maximethebault\Pdf2Table\Table
     */
    private $_menuTable;

    function __construct($_config, $remoteName, $pdfData) {
        parent::__construct($_config, $remoteName, $pdfData);
        $this->_menuId = MenuId::fromString($remoteName);
    }

    /**
     * @param $menu1 Menu
     * @param $menu2 Menu
     *
     * @return int
     */
    public static function sortByAscendingDate($menu1, $menu2) {
        if($menu1->_menuId->getYear() == $menu2->_menuId->getYear()) {
            return $menu1->_menuId->getWeekNumber() - $menu2->_menuId->getWeekNumber();
        }
        else {
            return $menu1->_menuId->getYear() - $menu2->_menuId->getYear();
        }
    }

    /**
     * Checks the consistency of the Menu and throws an Exception if an inconsistency is detected
     *
     * @throws Excpetion\InconsistentPdfException
     */
    public function checkConsistency() {
        $tempName = uniqid();
        // temp pdf path
        $tempPath = $this->_config->getTempPath() . $tempName;
        file_put_contents($tempPath, $this->_pdfData);
        $pdf2Table = new PdfFile2Table($tempPath);
        $pdf2Table->parse($this->_config->getTempPath());
        // first, check global structure
        $pages = $pdf2Table->getPages();
        if(count($pages) != 1) {
            throw new InconsistentPdfException('Expected 1 page, got ' . count($pages));
        }
        $this->_menuTable = $pages[0]->getTable();
        $cell = $pages[0]->getTable()->getCell(0, 0);
        if(!$cell) {
            throw new InconsistentPdfException('Cell(0;0) doesn\'t exist!');
        }
        $text = $cell->getTextline();
        if(count($text) != 1) {
            throw new InconsistentPdfException('Expected 1 line of text at cell(0;0), got ' . count($text));
        }
        // check week number
        $actualText = (int) $text[0]->getText();
        if($actualText != $this->_menuId->getWeekNumber()) {
            throw new InconsistentPdfException('Expected week number ' . $this->_menuId->getWeekNumber() . ', got ' . $actualText);
        }
        // check columns & rows heading
        /** @var $checkForText TableCell[] */
        $checkForText = array();
        $dejeuner = $pages[0]->getTable()->getCell(0, 1);
        $dejeuner2 = $pages[0]->getTable()->getCell(0, 2);
        $checkForText['déjeuner'] = $dejeuner;
        if($dejeuner != $dejeuner2) {
            throw new InconsistentPdfException('Expected columns "déjeuner" to be spanned, but they weren\'t!');
        }
        $diner = $pages[0]->getTable()->getCell(0, 3);
        $checkForText['dîner'] = $diner;
        $locale = setlocale(LC_TIME, array('fr', 'fra', 'french', 'fr_FR', 'fr_FR@euro'));
        if(!$locale) {
            throw new \Exception('French locale must be available in order to do consistency checks on the Menu data');
        }
        $weekIterator = new DateTime();
        $weekIterator->setISODate($this->_menuId->getYear(), $this->_menuId->getWeekNumber());
        for($i = 1; $i <= 7; $i++) {
            $day = $pages[0]->getTable()->getCell($i, 0);
            $checkForText[strftime('%A %d', $weekIterator->getTimestamp())] = $day;
            $weekIterator->modify('+1 day');
        }

        foreach($checkForText as $cellName => $cellObject) {
            if($cell == null) {
                throw new InconsistentPdfException('Cell ' . $cellName . ' not found');
            }
            $texts = $cellObject->getTextline();
            if(count($texts) != 1) {
                throw new InconsistentPdfException('Expected 1 line of text at cell ' . $cellName . ', got ' . count($text));
            }
            $actualText = strtolower($texts[0]->getText());
            $expectedText = strtolower($cellName);
            if(levenshtein($expectedText, $actualText, 1, 2, 1) > 1) {
                // doesn't throw if it's just a typo, or if it lacks a leading zero
                // will throw if it's "lundi 8" instead of "lundi 9"
                throw new InconsistentPdfException('Expected text ' . $expectedText . ', got ' . $actualText);
            }
        }


        unlink($tempPath);
    }

    /**
     * @return MenuId
     */
    public function getMenuId() {
        return $this->_menuId;
    }

    /**
     * @return \Maximethebault\Pdf2Table\Table
     */
    public function getMenuTable() {
        return $this->_menuTable;
    }

    protected function getLocalPath() {
        $directory = $this->_config->getPdfPath() . $this->_menuId->getYear() . '/';
        if(!is_dir($directory)) {
            mkdir($directory);
        }
        return $directory . $this->_menuId->getWeekNumber();
    }
}