<?php

namespace Maximethebault\IntraFetcher;

use Maximethebault\IntraFetcher\Excpetion\MenuIdParseException;

/**
 * Class MenuId
 *
 * A menu can be identified from its week number and its year
 *
 * WARNING: Pay special attention to the weeks splitted between two years, i.e. 2011-01-01 is still 2010's 52nd week.
 * To get the right year, see option 'o' of PHP's date function which gives ISO-8601 year number
 *
 * @package Maximethebault\IntraFetcher
 */
class MenuId
{
    private $_weekNumber;

    private $_year;

    function __construct($weekNumber, $year) {
        $this->_weekNumber = $weekNumber;
        $this->_year = $year;
    }


    /**
     * Builds a MenuId from a string
     *
     * @param $str string the string used in the filename, e.g. "menu40.pdf"
     *
     * @return MenuId
     *
     * @throws Excpetion\MenuIdParseException
     */
    public static function fromString($str) {
        $matches = array();
        if(preg_match_all('`\d+`', $str, $matches) > 1) {
            throw new MenuIdParseException('Expected only one integer in the input string, got more than one (in "' . $str . '")');
        }
        $weekNumber = $matches[0][0];
        if($weekNumber < 0 || $weekNumber > 52) {
            throw new MenuIdParseException('Illegal value for a week number (actual=' . $weekNumber . ')');
        }
        $currentMonth = date('n');
        if($currentMonth >= 9) {
            $oldYear = date('Y');
            $newYear = date('Y') + 1;
        }
        elseif($currentMonth < 3) {
            $oldYear = date('Y') - 1;
            $newYear = date('Y');
        }
        else {
            $oldYear = date('Y');
            $newYear = date('Y');
        }
        if($weekNumber > 26) {
            $year = $oldYear;
        }
        else {
            $year = $newYear;
        }
        return new MenuId($weekNumber, $year);
    }

    /**
     * @return mixed
     */
    public function getYear() {
        return $this->_year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year) {
        $this->_year = $year;
    }

    /**
     * @return mixed
     */
    public function getWeekNumber() {
        return $this->_weekNumber;
    }

    /**
     * @param mixed $weekNumber
     */
    public function setWeekNumber($weekNumber) {
        $this->_weekNumber = $weekNumber;
    }

    public function increment() {
        if($this->getWeekNumber() == 52) {
            $weekNumber = 1;
            $year = $this->getYear() + 1;
        }
        else {
            $weekNumber = $this->getWeekNumber() + 1;
            $year = $this->getYear();
        }
        return new MenuId($weekNumber, $year);
    }
} 