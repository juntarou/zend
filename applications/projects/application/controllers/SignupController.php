<?php

/**
 * SignupController
 * ドメイン検索,ドメイン申し込み登録
 */

class SignupController extends Zend_Controller_Base
{
    public $filters = array(
	"*"  => "StringTrim",
    );

    protected $contentTemp;


    protected $ajaxContext;


    protected $_selectBoxCloumn = array();


    protected $_session;


    protected $_jsFiles = array(
	    "register.js",
    );


    /**
     * SignupController init()
     * Ajax Context initialize
     */
    public function init()
    {
	// set session
	$this->_session = new Zend_Session_Namespace('register');	

	// set Ajax Action Context
	$this->ajaxContext = $this->_helper->getHelper('AjaxContext');
	$this->ajaxContext->addActionContext('sdomain', 'json')
		->addActionContext('registerinit','json')
		->addActionContext('registerload', 'json')
		->addActionContext('vregister', 'json')
		->addActionContext('spayment', 'json')
		->addActionContext('zipsearch','json')
		//->setAutoJsonSerialization(false)
		->initContext();

	// set content templates
	/*
	$this->contentTemp = Module_Model_Template::getInstanse();
	$this->contentTemp->addTemplates('payment', 1, 'areaCreditPayment')
		->addTemplates('payment', 2, array('10013' => 'areaBillPayment'));
	 */
	$this->_selectBoxCloumn = new Zend_Config(require APP_CONFIG_DIR_PATH . "registerData.php");

	parent::init();
    }

    public function postDispatch()
    {
	//$this->view->sideMenufile = 'rental_side.html';
	//$this->view->bread = $this->navi->breadcrumbs()->render();
	$this->view->jsFiles = $this->getJsFiles();
    }

    /**
     * domain search page
     */
    public function indexAction()
    {
	$domains = new Zend_Config(require APP_CONFIG_DIR_PATH . "DomainData.php");
	$resDomains = $this->_checkAllowDate($domains);

	//set js file
	$this->setJsFiles(array("httpxml.js"));
	$this->view->jsFiles = $this->getJsFiles();
        //action body
	$this->view->checkBox = $resDomains;
	// get の受け取り
        if ($id = $this->_request->getQuery('id')) {
	    //TODO セッションは後々APIでやりとりする
	    //とりあえず今はネイティブを使用
	    //$session = new Zend_Session_Namespace('domains');
	    $tmpDomain = NULL;
	    if (!empty($this->_session->domains)) {
		foreach ($this->_session->domains as $dom) {
		    if ($dom['token'] == $id){
			$tmpDomain = $dom['url'];
			$this->_session->resDomain = $tmpDomain;
			break;
		    }
		}
	    }
	    if ($tmpDomain != "") {
		// 登録ページへリダイレクト
		$this->_forward("register");
	    } else {
		// エラーページへリダイレクト
	    }
	}

    }

    /**
     * user register input page
     */
    public function registerAction()
    {
	// サービスタイプを取得
	$serviceType = $this->_request->getQuery('serviceType', null);

	// サービスタイプがnullはそもそもあり得ないので
	// 不正アクセスとしてエラーページへ
	if (is_null($serviceType)) {
	    // disp error page
	}

	$this->view->serviceType = 'contract_info';

	// セレクトボックス情報を取得
	$this->view->span = $this->_getSelectBoxArray($this->_selectBoxCloumn,"span");
	// 支払い方法
	$this->view->payment = $this->_getSelectBoxArray($this->_selectBoxCloumn,"payment");
	// カード有効期限
	$this->view->creditCardExpYear = $this->_setYearSelectBox();
	// 都道府県
	$this->view->wtransSate = $this->_getSelectBoxArray($this->_selectBoxCloumn, "wtransSate");
	// url
	$this->view->ajaxUrl = "http://logico.lab.in.nttpc.co.jp/php_front/signup/registerinit/json";
	if (!is_null($this->_session->resDomain)) {
		// validation
		
	}
	
    }


    /**
     * AJAX Request action
     * is XmlHttpRequest only
     * @return header application/json
     */
    public function sdomainAction()
    {
	if ($this->_request->isPost()) {
	    $post = $this->_request->getPost();   
	    // TODOこのドメインURLは本来APIから取得する
	    // とりあえず静的に代入
	    // idにはアクセストークンを入れる
	    // array("token" => $token, "enable" => 0:利用不可 or 1:利用可, "url" => string)
	    $responseDomains = array(
		array("token" => 1, "enable" => 0, "url" => "http://test.response.domain.jp"),	
		array("token" => 2, "enable" => 1, "url" => "http://test.secondry.domain.com"),
		array("token" => 3, "enable" => 1, "url" => "http://hogehoge.co.jp"),
	    );
	    //$session = new Zend_Session_Namespace('domains');
	    $this->_session->domains = $responseDomains;
	    $this->view->json = $responseDomains; 
	}
    }

    public function registerinitAction()
    {
	if ($this->_request->getPost('reFlag')) {
	    if ($post = $this->_request->getPost()) {
		
		// set Templates
		$post['template'] = $this->_setTamplate($post);
		
		$changeText = array();
		$changeTextFilter = $this->_arrayFileter($post,array("span","payment"));

		$changeText = $this->_getSelectBoxArrayOne($this->_selectBoxCloumn,$changeTextFilter);
		// TODO APIへ金額を取りにいく
		// とりあえず今は静的にセット
		//if ($isApiResponse) {
		    $count = count($changeText);
		    $changeText["text" . ($count + 1)] = "1500" . SYMBOL_YEN;
		//}
		$this->view->post = $post;
		$this->view->changeText = $changeText;
		$this->_session->post = $post;
		$this->_session->changeText =$changeText;
	    }
	} else {
	    $this->view->post = $this->_session->post;
	    $this->view->changeText = $this->_session->changeText;
	}

    }

    public function registerloadAction()
    {
	$session = new Zend_Session_Namespace('params');	
	if (!empty($session->params)) {
	    $this->view->json = $session->params;
	}
    }

    public function zipsearchAction()
    {
	$ocmId = 10013;
	$post = $this->_request->getPost();
	if (empty($post)) {
	    // redirect
	}
	$postCode = $post['wtransZip'];
	$client = new Zend_XmlRpc_Client('http://yubin.senmon.net/service/xmlrpc/');

	try{
	    $result = $client->call('yubin.fetchAddressByPostcode',array($postCode));
	} catch(Exception $e) {
	    // redirect
	    $result = null;
	}

	$post['wtransCity'] = $result[0]['city'];
	$post['wtransAddress'] = $result[0]['town'];
	$post['template'] = $this->_setTamplate($post);
	//var_dump($result);
	$this->view->json = $post;
    }

    /**************************************
     *
     * non action method
     * callback & getter setter etc
     *
     **************************************/

    public static function _getSelectBoxArray($data,$key)
    {
	$tmp=array();
	foreach ($data->$key as $key => $d) {
	    $tmp[$key] = $d;
	}
	return $tmp;
    }

    public static function _getSelectBoxArrayOne($data,$post)
    {
	$tmp = array();
	$num = 1;
	foreach ($data as $key => $val) {
	    if (isset($post[$key])) {
		$tmp['text' . $num] = $val->get($post[$key]);
		$num++;
	    }
	}	
	return $tmp;
    }

    private static function _arrayFileter($post,$keys)
    {
	$tmp = array();
	foreach ($keys as $val) { 
	   $tmp[$val] = $post[$val]; 
	}
	return $tmp;
    }

    private function _checkAllowDate($data)
    {
	$now = time();
	$tmpData = array();
	$tmpClass = NULL;
	$startTime = 0;
	$endTime = 0;
	foreach ($data as $key => $d) {
	    $tmpClass = new StdClass;
	    $tmpClass->num = $d->num;
	    $tmpClass->name = $key;
	    $startTime = strtotime($d->startDate);
	    $endTime   = strtotime($d->endDate);
	    if ($startTime < $now && $endTime > $now) {
		$tmpClass->disabled = true;
	    }else{
		$tmpClass->disabled = false;
	    }
	    $tmpData[] = $tmpClass;
	}
	return $tmpData;
    }

    private function _setYearSelectBox()
    {
	$nowYear = date("Y",time());
	$selectBox = range($nowYear,$nowYear + 5);
	foreach($selectBox as $key => $s) {
	    $tmp[($key + 1) . ':' . $s] = $s . '年';
	}
	return $tmp;
    }

    private function _setTamplate($post)
    {
	$tmp = array();
	if ($post['payment']) {
	    if ($post['payment'] === PAYMENT_TYPE_CREDIT) {
		$billTypeId = PAYMENT_TEMP_TYPE_CREDIT_ID;
	    } else {
		$billTypeId = '10013';
	    }
	    $paymentTmp = "payment_" . $billTypeId;
	    $tmp[] = $paymentTmp;    
	}
	// 適時追加ここから
	return $tmp;
    }

    // error templates
    // page not found
    public function notfoundErrorAction()
    {

    }

    // session error
    public function sessionErrorAction()
    {

    }

    // Injustice accsess error
    public function injusticeErrorAction()
    {

    }

}

