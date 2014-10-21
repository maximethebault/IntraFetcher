<?php

namespace Maximethebault\IntraFetcher;

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

    abstract protected function getLocalPath();
} 