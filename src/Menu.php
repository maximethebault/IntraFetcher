<?php

namespace Maximethebault\IntraFetcher;

use Maximethebault\IntraFetcher\Excpetion\InconsistentPdfException;
use Maximethebault\Pdf2Table\PdfFile2Table;

class Menu extends PdfFile
{
    /**
     * @var MenuId
     */
    private $_menuId;

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
        $pdf2Table = new PdfFile2Table($tempPath);
        $pdf2Table->parse($this->_config->getTempPath());
        $pages = $pdf2Table->getPages();
        if(count($pages) != 1) {
            throw new InconsistentPdfException('Expected 1 page, got ' . count($pages));
        }
        $cell = $pages[0]->getTable()->getCell(0, 0);
        if(!$cell) {
            throw new InconsistentPdfException('Cell(0;0) doesn\'t exist!');
        }
        $text = $cell->getText();
        if(count($text) != 1) {
            throw new InconsistentPdfException('Expected 1 line of text at cell(0;0), got ' . count($text));
        }
        $actualText = (int) $text[0];
        if($actualText != $this->_menuId->getWeekNumber()) {
            throw new InconsistentPdfException('Expected week number ' . $this->_menuId->getWeekNumber() . ', got ' . $actualText);
        }
        unlink($tempPath);
    }

    protected function getLocalPath() {
        $directory = $this->_config->getPdfPath() . $this->_menuId->getYear() . '/';
        if(!is_dir($directory)) {
            mkdir($directory);
        }
        return $directory . $this->_menuId->getWeekNumber();
    }
}