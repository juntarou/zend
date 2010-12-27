<?php

//require_once "Zend/Filter/Input.php";
class Module_Validation_Fillter extends Zend_Filter_Input
{
    
    // fields set
    protected static $forms = array(
	"span"              => "",
	"payment"           => "",
	"userName"          => "",
	"creditCardName"    => "",
	"creditCardNumber"  => "",
	"price"             => "",
    );

    public static function getInstance($rules, $validRoles, array $data = null, array $options = null)
    {
	return new MyValidation($rules,$validRoles,$data,$options);
    }

    public static function getRulesFilters($key)
    {
	return self::setChainRules($key);
    }

    protected static function setChainRules($key)
    {
	$chain = new Zend_Validate();
	if ($key == "span") {
	    $chain->addValidator(new Zend_Validate_Digits(), true);
	    $chain->addValidator(new Zend_Validate_NotEmpty(), true);	    
	} elseif ($key == "payment") {
	    $chain->addValidator(new Zend_Validate_NotEmpty(), true);
	    $chain->addValidator(new Zend_Validate_Digits(), true);
	} elseif ($key == "userName") {
	    $chain->addValidator(new Zend_Validate_NotEmpty(), true);
	    //$chain->addValidator(new Zend_Validate_Regex(), true);
	} elseif ($key == "creditCardName") {
	    $chain->addValidator(new Zend_Validate_NotEmpty(), true);
	} elseif ($key == "creditCardNumber") {
	    $chain->addValidator(new Zend_Validate_NotEmpty(), true);
	    $chain->addValidator(new Zend_Validate_Digits(), true);
	}
	$validators = array(
		$key => $chain,
	);
	return $validators;
    }

    public static function getForms()
    {
	return self::$forms;
    }

    public function setForms($data)
    {
	$forms = self::getForms();
	return array_merge($forms,$data);
    }

    public function getError()
    {
	$error = $this->_invalidErrors;
	$key = key($error);
	$data['field'] = $key;
	$data['rule'] = $error[$key][0];
	return $data;
    }

    public function getMyErrorMessage()
    {
	$messages = self::getMessageTemplate(); 
	$fields = self::getFieldsLibrary();
	$data = $this->getError();
	$field = $fields->$data['field'];
	$resMsg = $messages->$data['rule'];
	$resMsg = str_replace('%field%', $field, $resMsg);
	return $resMsg;
    }

    public static function getMessageTemplate() {
	return new Zend_Config(require APP_CONFIG_DIR_PATH . "validMessages.php");
    }

    public static function getFieldsLibrary()
    {
	return new Zend_Config(require APP_CONFIG_DIR_PATH . "fieldsLibrary.php");
    }

}
