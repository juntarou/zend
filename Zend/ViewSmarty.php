<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View_Smarty
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: View.php 20096 2010-01-06 02:05:09Z bkarwin $
 */


/**
 * Abstract master class for extension.
 */
//require_once 'Zend/View/Interface.php';
require_once 'Smarty/libs/Smarty.class.php';

/**
 * Concrete class for handling view scripts.
 *
 * @category   Zend
 * @package    Zend_View_Smarty
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Smarty implements Zend_View_Interface
{
	/**
	 * Smarty object
	 * @var Smarty
	 */
	public $_smarty;

	/**
	 * コンストラクタ
	 *
	 */
	public function __construct($tmplPath = null, $extraParams = array())
	{
		$this->_smarty = new Smarty;

		if (null !== $tmplPath) {
			$this->setScriptPath($tmplPath);
		}

		foreach ($extraParams as $key => $value) {
			$this->_smarty->$key = $value;
		}
	}

	/**
	 * テンプレートエンジンオブジェクトを返す
	 */
	public function getEngine()
	{
		return $this->_smarty;
	}

	/**
	 * テンプレートへのパスを設定
	 */
	public function setScriptPath($path)
	{
		if (is_readable($path)) {
			$this->_smarty->template_dir = $path;
			return;
		}

		throw new Exception('無効なパスが設定されました');
	}

	/**
	 * 現在のテンプレートディレクトリを取得
	 */
	public function getScriptPaths()
	{
		return array($this->_smarty->template_dir);
	}

	/**
	 * setScriptPathへのエイリアス
	 */
	public function setBasePath($path, $prefix = 'Zend_View')
	{
		return $this->setScriptPath($path);
	}

	/**
	 * setScriptPathへのエイリアス
	 */
	public function addBasePath($path, $prefix = 'Zend_view')
	{
		return $this->setScriptPath($path);
	}

	/**
	 * 変数をテンプレートに代入
	 */
	public function __set($key, $val)
	{
		$this->_smarty->assign($key, $val);	
	}

	/**
	 * empty()やisset()のテストが動作するようにする
	 */
	public function __isset($key)
	{
		return (null !== $this->_smarty->get_template_vars($key));
	}

	/**
	 * オブジェクトのプロパティに対してunsetが動作するようにする
	 */
	public function __unset($key)
	{
		$this->_smarty->clear_assign($key);
	}

	/**
	 * 変数をテンプレートへ代入
	 */
	public function assign($spec, $value = null)
	{
		if (is_array($spec)) {
			$this->_smarty->assign($spec);
			return;
		}

		$this->_smarty->assign($spec, $value);
	}

	public function config_load($file)
	{
	    $this->_smarty->config_load($file);
	}

	/**
	 * 代入済みのすべての変数を削除します。
	 */
	public function clearVars()
	{
		$this->_smarty->clear_all_assgin();
	}
	
	/**
	 * テンプレートを処理し、結果を出力
	 */
	public function render($name)
	{
		return $this->_smarty->fetch($name);
	}
}
