<?php

namespace Maximethebault\IntraFetcher\Tests;

use Maximethebault\IntraFetcher\Config;
use Maximethebault\IntraFetcher\IntraFetcher;

require __DIR__ . '/../config.php';


class IntraFetcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IntraFetcher
     */
    private $intraFetcher;

    public function __construct() {
        $config = new Config();
        $config->setInsaUsername(\ActualConfig::$username);
        $config->setInsaPassword(\ActualConfig::$password);
        $config->setPdfPath(__DIR__ . '/../tmp/');
        $config->setTempPath(__DIR__ . '/../tmp/');
        $this->intraFetcher = new IntraFetcher($config);
        $this->intraFetcher->checkForMenu();
    }

    public function testNewMenu() {
        $this->intraFetcher->commitChanges();
    }
}