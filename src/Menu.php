<?php

namespace Maximethebault\IntraFetcher;

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

    public function checkConsistency() {
    }

    protected function getLocalPath() {
        $directory = $this->_config->getPdfPath() . $this->_menuId->getYear() . '/';
        if(!is_dir($directory)) {
            mkdir($directory);
        }
        return $directory . $this->_menuId->getWeekNumber();
    }
}