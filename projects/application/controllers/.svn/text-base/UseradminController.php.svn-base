<?php

/**
 * UseradminController
 * ユーザー管理ページ 
 */

class UseradminController extends Zend_Controller_Base
{

    public $filters = array(
	"*"  => "StringTrim",
    );

    protected $contentTemp;


    protected $_jsFiles = array(
	    "register.js",
    );

    /**
     * SignupController init()
     * Ajax Context initialize
     */
    public function init()
    {
	// set Ajax Action Context
	/*
	$ajaxContext = $this->_helper->getHelper('AjaxContext');
	$ajaxContext->addActionContext('change', 'json')
		->initContext();
	*/
	// set content templates
	$this->contentTemp = Module_Model_Template::getInstanse();
	$this->contentTemp->addTemplates('payment', 1, 'areaCreditPayment')
		->addTemplates('payment', 2, array('10013' => 'areaBillPayment'));

	// set bread crumps
	require_once(APP_CONFIG_DIR_PATH . 'navigationLists.php');
	$contener = new Zend_Navigation($pages);
	$this->navi = $this->view->getHelper('navigation');
	$this->navi->setContainer($contener);
	parent::init();
    }

    public function postDispatch()
    {
	$this->view->sideMenufile = 'rental_side.html';
	$this->view->bread = $this->navi->breadcrumbs()->render();
	$this->view->jsFiles = $this->getJsFiles();
    }

    public function indexAction()
    {
	// Todo ログインチェック(predispatchに書くかも)
	// ぱんくず
    }

    public function mailadminAction()
    {
	
    }

    public function mailcreateAction()
    {
	
    }

    public function mailidoutAction()
    {
	
    }

    // logout
    public function logoutAction()
    {

    }

    // change service menu
    public function changeAction()
    {

    }

}
