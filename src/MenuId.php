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
     * @throws Excpetion\MenuIdParseException
     */
    public static function fromString($str) {
        $matches = array();
        if(preg_match_all('`\d+`', $str, $matches) > 1) {
            throw new MenuIdParseException('Expected only one integer in the input string, got more than one (in "' . $str . '")');
        }
        $weekNumber = $matches[0][0];
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
} 