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
    /**
     * Week number
     *
     * @var int
     */
    private $_weekNumber;

    private $_year;
    /**
     * Length of the week number (useful to add leading zeros)
     *
     * @var int
     */
    private $_weekNumberLength;

    /**
     * @param     $weekNumber       int
     * @param     $year             int
     * @param     $weekNumberLength int
     */
    public function __construct($weekNumber, $year, $weekNumberLength = 0) {
        $this->_weekNumber = (int) $weekNumber;
        $this->_year = (int) $year;
        $this->_weekNumberLength = $weekNumberLength;
    }


    /**
     * Builds a MenuId from a string
     *
     * @param $str  string the string used in the filename, e.g. "menu40.pdf"
     * @param $time int by default, we use the current time to compute the year associated with the parsed week number. This parameter makes it possible to change that.
     *
     * @return MenuId
     *
     * @throws Excpetion\MenuIdParseException
     */
    public static function fromString($str, $time = 0) {
        if($time === 0) {
            $time = time();
        }

        $matches = array();
        if(preg_match_all('`\d+`', $str, $matches) > 1) {
            throw new MenuIdParseException('Expected only one integer in the input string, got more than one (in "' . $str . '")');
        }
        $weekNumber = $matches[0][0];
        $weekNumberLength = strlen($weekNumber);
        if($weekNumber < 0 || $weekNumber > 53) {
            throw new MenuIdParseException('Illegal value for a week number (actual=' . $weekNumber . ')');
        }
        /*
         * We've got a week number. What now?
         * We need to find the year it's associated with.
         * It's much trickier than what you'd expect because of the fact that ISO-8601 week numbers can be splitted between two years.
         *
         * The assumption we're going to work with: the given week number is close to the current week number (i.e. the week number that you can read right now in your calendar!)
         */

        // we list all possible dates
        $possibleDates = array();
        $possibleDates[(string) (date('Y', $time) - 1)] = strtotime((date('Y', $time) - 1) . 'W' . str_pad($weekNumber, 2, '0', STR_PAD_LEFT));
        $possibleDates[(string) date('Y', $time)] = strtotime((date('Y', $time)) . 'W' . str_pad($weekNumber, 2, '0', STR_PAD_LEFT));
        $possibleDates[(string) (date('Y', $time) + 1)] = strtotime((date('Y', $time) + 1) . 'W' . str_pad($weekNumber, 2, '0', STR_PAD_LEFT));

        // we calculate the differnece to the current date
        foreach($possibleDates as $idx => $possibleDate) {
            $possibleDates[$idx] = abs($time - $possibleDate);
        }

        // we take the minimum
        $minDiff = min($possibleDates);

        // we've got the minimum, let's get the matching index
        $year = array_search($minDiff, $possibleDates);

        return new MenuId($weekNumber, $year, $weekNumberLength);
    }

    /**
     * @return int
     */
    public function getYear() {
        return $this->_year;
    }

    /**
     * @param int $year
     */
    public function setYear($year) {
        $this->_year = $year;
    }

    /**
     * @return int
     */
    public function getWeekNumber() {
        return $this->_weekNumber;
    }

    /**
     * @param int $weekNumber
     */
    public function setWeekNumber($weekNumber) {
        $this->_weekNumber = $weekNumber;
    }

    /**
     * @return string
     */
    public function getWeekNumberWithLeadingZeros() {
        return str_pad($this->_weekNumber, $this->_weekNumberLength, '0', STR_PAD_LEFT);
    }

    /**
     * @return MenuId
     */
    public function increment() {
        $incremented = strtotime("+1 week", strtotime($this->_year . 'W' . str_pad($this->_weekNumber, 2, '0', STR_PAD_LEFT)));
        $weekNumber = date('W', $incremented);
        $year = date('o', $incremented);

        return new MenuId($weekNumber, $year, $this->getWeekNumberLength());
    }

    /**
     * @return int
     */
    public function getWeekNumberLength() {
        return $this->_weekNumberLength;
    }
} 