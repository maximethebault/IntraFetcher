<?php

namespace Maximethebault\IntraFetcher\HttpRequest;

use Maximethebault\IntraFetcher\Config;
use Maximethebault\IntraFetcher\Excpetion\HttpException;

class HttpRequestManager
{
    /**
     * Timeout (in seconds) before the HTTP request fails
     */
    const REQUEST_TIMEOUT = 5;
    /**
     * Number of tries before giving up on a HTTP request
     */
    const MAXIMUM_TRIES = 3;
    /**
     * @var Config
     */
    private $_config;

    function __construct($config) {
        $this->_config = $config;
    }

    /**
     * Gets a page that needs authentification
     *
     * @param      $url   string
     * @param      $tries int number of times the page was requested so far
     *
     * @return string
     *
     * @throws \Maximethebault\IntraFetcher\Excpetion\HttpException
     */
    public function getPage($url, $tries = 0) {
        if($tries == self::MAXIMUM_TRIES) {
            throw new HttpException('Giving up on request to ' . $url . ' after too many tries');
        }
        $pageData = $this->makeRequest($url);
        if(strstr($pageData, 'INSA RENNES - SSO CAS') !== false || strstr($pageData, 'do not have the right') !== false) {
            $this->doLogin();
            return $this->getPage($url, $tries + 1);
        }
        return $pageData;
    }

    /**
     * @param        $url
     * @param        $post string
     *
     * @return mixed
     */
    private function makeRequest($url, $post = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/35.0');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::REQUEST_TIMEOUT);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->getCookiePath());
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->getCookiePath());
        if($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * Does the login!
     */
    private function doLogin() {
        // erases the cookie file, replaces it with an empty file
        unlink($this->getCookiePath());
        touch($this->getCookiePath());
        // builds the form for the login
        $loginPage = $this->makeRequest('https://cas.insa-rennes.fr/cas/login?service=http://ent.insa-rennes.fr/Login');
        if(!preg_match('`name="lt" value="(.*)"`i', $loginPage, $match)) {
            throw new HttpException('Malformed login page');
        }
        $this->makeRequest('https://cas.insa-rennes.fr/cas/login?service=http://ent.insa-rennes.fr/Login', 'username=' . urlencode($this->_config->getInsaUsername()) . '&password=' . urlencode($this->_config->getInsaPassword()) . '&lt=' . $match[1] . '&_eventId=submit&submit=Se+Connecter');
    }

    private function getCookiePath() {
        return $this->_config->getTempPath() . 'intranet_auth_cookie.txt';
    }
} 