<?php

namespace Maximethebault\IntraFetcher;

use finfo;

abstract class PdfFile
{
    /**
     * Remote name of the PDF file
     *
     * @var string
     */
    private $_remoteName;
    /**
     * Holds the PDF filecontent
     *
     * @var string
     */
    private $_pdfData;

    public function __construct($remoteName, $pdfData) {
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
        if($finfo->buffer($data) == 'application/pdf') {
            return true;
        }
        return false;
    }

    public function getFileHash() {
        if(file_exists($this->getLocalPath())) {
            return md5_file($this->getLocalPath());
        }
        return null;
    }

    public function getDataHash() {
        return md5($this->_pdfData);
    }

    /**
     * Once we're done with the fetching and once we analyzed everything, we'll want to save all the updated/new files to the disk
     * That's what this method does!
     */
    public function commitChanges() {
    }

    /**
     * @return string
     */
    public function getRemoteName() {
        return $this->_remoteName;
    }

    abstract protected function getLocalPath();
} 