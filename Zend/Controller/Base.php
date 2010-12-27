<?php

/**
 * BaseController
 * ベースコントローラー
 */
class Zend_Controller_Base extends Zend_Controller_Action
{

    protected function getJsFiles()
    {

	if (!empty($this->_jsFiles)) {
	    return $this->_jsFiles;
	}

	return null;

    }

    protected function setJsFiles(array $files)
    {

	$this->_jsFiles = $files; 

    }

}
