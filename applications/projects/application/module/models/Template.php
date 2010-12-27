<?php

class Module_Model_Template
{
    
    protected $templates = array();

    public static function getInstanse()
    {
	return new Module_Model_Template();
    }

    public function addTemplates($lavel, $areaId, $tempName)
    {
	$this->templates[$lavel][$areaId] = $tempName;
	return $this;
    }

    public function getTemplate($postData, $ocnId = null)
    {
	$tmpData = array();
	foreach ($this->templates as $key => $t) {
	    foreach ($postData as $key2 => $p) {
		if ($key === $key2) {
		    if (is_array($t)) {
			if (is_array($t[$postData[$key]])) {
			    $tmpData[] = $t[$postData[$key]][$ocnId] . '_' . $ocnId;
			} else {
			    $tmpData[] = $t[$postData[$key]];	    
			}
		    } else {
			$tmpData[] = $t;
		    }
		}
	    }
	    	
	}
	return $tmpData;
    }


}
