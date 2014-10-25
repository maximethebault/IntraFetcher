<?php

namespace Maximethebault\IntraFetcher;

use finfo;

abstract class PdfFile
{
    /**
     * @var Config
     */
    protected $_config;
    /**
     * Holds the PDF filecontent
     *
     * @var string
     */
    protected $_pdfData;
    /**
     * Remote name of the PDF file
     *
     * @var string
     */
    private $_remoteName;

    public function __construct($_config, $remoteName, $pdfData) {
        $this->_config = $_config;
        $this->_remoteName = $remoteName;
        $this->_pdfData = $pdfData;
    }

    /**
     * Whether the data is a PDF
     *
     * @param $data
     *
     * @return bool
     */
    public static function isPdfData($data) {
        $finfo = new finfo(FILEINFO_MIME);
        if(strstr($finfo->buffer($data), 'application/pdf') !== false) {
            return true;
        }
        return false;
    }

    public function isNew() {
        return ($this->getFileHash() == null);
    }

    public function isUpdated() {
        return ($this->getFileHash() != null && $this->getFileHash() != $this->getDataHash());
    }

    /**
     * Once we're done with the fetching and once we analyzed everything, we'll want to save all the updated/new files to the disk
     * That's what this method does!
     */
    public function commitChanges() {
        file_put_contents($this->getLocalPath(), $this->_pdfData);
    }

    /**
     * @return string
     */
    public function getRemoteName() {
        return $this->_remoteName;
    }

    abstract protected function getLocalPath();

    private function getFileHash() {
        if(file_exists($this->getLocalPath())) {
            return md5_file($this->getLocalPath());
        }
        return null;
    }

    private function getDataHash() {
        return md5($this->_pdfData);
    }
} 