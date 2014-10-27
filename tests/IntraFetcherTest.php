<?php

namespace Maximethebault\IntraFetcher\Tests;

use Maximethebault\IntraFetcher\Config;
use Maximethebault\IntraFetcher\IntraFetcher;
use Maximethebault\IntraFetcher\MenuId;

include __DIR__ . '/../config.php';


class IntraFetcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IntraFetcher
     */
    private $intraFetcher;

    public function __construct() {
        if(class_exists('\ActualConfig')) {
            $config = new Config();
            $config->setInsaUsername(\ActualConfig::$username);
            $config->setInsaPassword(\ActualConfig::$password);
            $config->setPdfPath(__DIR__ . '/../tmp/');
            $config->setTempPath(__DIR__ . '/../tmp/');
            $this->intraFetcher = new IntraFetcher($config);
            $this->intraFetcher->checkForMenu();
        }
    }

    public function testNewMenu() {
        if(class_exists('\ActualConfig')) {
            $this->intraFetcher->commitChanges();
        }
    }

    public function testDateIncrement() {
        $menuId = new MenuId('52', '2004');
        $nextWeek = $menuId->increment();
        $this->assertEquals(53, $nextWeek->getWeekNumber());
        $this->assertEquals(2004, $nextWeek->getYear());
        $uberNextWeek = $nextWeek->increment();
        $this->assertEquals(1, $uberNextWeek->getWeekNumber());
        $this->assertEquals(2005, $uberNextWeek->getYear());
    }

    public function testDateParsing() {
        $menuId = MenuId::fromString('menu30.pdf', strtotime('2014-08-15'));
        $this->assertEquals(30, $menuId->getWeekNumber());
        $this->assertEquals(2014, $menuId->getYear());
        $menuId = MenuId::fromString('menu01.pdf', strtotime('2005-01-01'));
        $this->assertEquals(1, $menuId->getWeekNumber());
        $this->assertEquals(2005, $menuId->getYear());
        $menuId = MenuId::fromString('menu01.pdf', strtotime('2005-01-02'));
        $this->assertEquals(1, $menuId->getWeekNumber());
        $this->assertEquals(2005, $menuId->getYear());
        $menuId = MenuId::fromString('menu01.pdf', strtotime('2004-12-31'));
        $this->assertEquals(1, $menuId->getWeekNumber());
        $this->assertEquals(2005, $menuId->getYear());
        $menuId = MenuId::fromString('menu53.pdf', strtotime('2004-12-31'));
        $this->assertEquals(53, $menuId->getWeekNumber());
        $this->assertEquals(2004, $menuId->getYear());
        $menuId = MenuId::fromString('menu53.pdf', strtotime('2005-01-01'));
        $this->assertEquals(53, $menuId->getWeekNumber());
        $this->assertEquals(2004, $menuId->getYear());
        $menuId = MenuId::fromString('menu53.pdf', strtotime('2005-01-30'));
        $this->assertEquals(53, $menuId->getWeekNumber());
        $this->assertEquals(2004, $menuId->getYear());
        $menuId = MenuId::fromString('menu53.pdf', strtotime('2004-12-01'));
        $this->assertEquals(53, $menuId->getWeekNumber());
        $this->assertEquals(2004, $menuId->getYear());
    }
}