<?php

namespace Maximethebault\IntraFetcher;

class Menu extends PdfFile
{
    /**
     * @var MenuId
     */
    private $_menuId;

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

    protected function getLocalPath() {
        // TODO: Implement getLocalPath() method.
    }
}