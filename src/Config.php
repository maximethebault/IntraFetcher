<?php

namespace Maximethebault\IntraFetcher;

class Config
{
    /**
     * @var string
     */
    private $_pdfPath;
    /**
     * @var string
     */
    private $_tempPath;

    private $_insaUsername;

    private $_insaPassword;

    public function __construct() {
        $this->_tempPath = __DIR__ . '/../tmp/';
    }


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
     * _PATH_ should exist, the subfolders (_YEAR_) will be created automatically
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

    /**
     * @return string
     */
    public function getTempPath() {
        return $this->_tempPath;
    }

    /**
     * Sets the path for temporary files. Don't forget the trailing slash!
     * Should be a real path (e.g., /var/www/insamiam/tmp)
     *
     * @param string $tempPath
     */
    public function setTempPath($tempPath) {
        $this->_tempPath = $tempPath;
    }
}