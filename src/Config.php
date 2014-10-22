<?php

namespace Maximethebault\IntraFetcher;

class Config
{
    /**
     * @var string
     */
    private $_pdfPath;

    private $_cookiePath;

    private $_insaUsername;

    private $_insaPassword;

    /**
     * @return string
     */
    public function getPdfPath() {
        return $this->_pdfPath;
    }

    /**
     * Sets the path to the PDF directory.
     * The latter is organized this way :
     *  _PATH_/_YEAR_/_WEEKNUMBER_.pdf
     *
     * _PATH_ should exist, the subfolders (_YEAR) will be created automatically
     *
     * _PATH_ should have a trailing directory separator
     *
     * @param string $pdfPath
     */
    public function setPdfPath($pdfPath) {
        $this->_pdfPath = $pdfPath;
    }

    /**
     * @return mixed
     */
    public function getCookiePath() {
        return $this->_cookiePath;
    }

    /**
     * Path to the cookie file
     * WARNING: a real path must be passed (e.g. /var/www/insamiam/cookie.txt)
     *
     * @param string $cookiePath
     *
     * @see realpath
     */
    public function setCookiePath($cookiePath) {
        $this->_cookiePath = $cookiePath;
    }

    /**
     * @return mixed
     */
    public function getInsaUsername() {
        return $this->_insaUsername;
    }

    /**
     * @param mixed $insaUsername
     */
    public function setInsaUsername($insaUsername) {
        $this->_insaUsername = $insaUsername;
    }

    /**
     * @return mixed
     */
    public function getInsaPassword() {
        return $this->_insaPassword;
    }

    /**
     * @param mixed $insaPassword
     */
    public function setInsaPassword($insaPassword) {
        $this->_insaPassword = $insaPassword;
    }
}