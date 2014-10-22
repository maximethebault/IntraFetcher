<?php

namespace Maximethebault\IntraFetcher;

class StringReplacer
{
    /**
     * Base string in which we're going to do the replacements
     *
     * @var string
     */
    private $_baseStr;
    /**
     * The MenuId we'll to remplace in the basestring
     *
     * @var MenuId
     */
    private $_toReplace;

    public function __construct($baseStr, $toReplace) {
        $this->_baseStr = $baseStr;
        $this->_toReplace = $toReplace;
    }

    public function doReplace($with) {
        $numberLength = $this->_toReplace->getWeekNumberLength();
        $with = str_pad($with, $numberLength, '0', STR_PAD_LEFT);
        return str_replace($this->_toReplace->getWeekNumberWithLeadingZeros(), $with, $this->_baseStr);
    }
}