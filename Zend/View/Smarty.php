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

	public $_helper;

	public $_loaderTypes = array('filter', 'helper');

	public $_loaders = array();

    /**
     * Callback for escaping.
     *
     * @var string
     */
    private $_escape = 'htmlspecialchars';

    /**
     * Encoding to use in escaping mechanisms; defaults to utf-8
     * @var string
     */
    private $_encoding = 'UTF-8';

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

    /**
     * Get a helper by name
     *
     * @param  string $name
     * @return object
     */
    public function getHelper($name)
    {
        return $this->_getPlugin('helper', $name);
    }

    /**
     * Retrieve a plugin object
     *
     * @param  string $type
     * @param  string $name
     * @return object
     */
    private function _getPlugin($type, $name)
    {
        $name = ucfirst($name);
        switch ($type) {
            case 'filter':
                $storeVar = '_filterClass';
                $store    = $this->_filterClass;
                break;
            case 'helper':
                $storeVar = '_helper';
                $store    = $this->_helper;
                break;
        }

        if (!isset($store[$name])) {
            $class = $this->getPluginLoader($type)->load($name);
            $store[$name] = new $class();
            if (method_exists($store[$name], 'setView')) {
                $store[$name]->setView($this);
            }
        }

        $this->$storeVar = $store;
        return $store[$name];
    }

    /**
     * Retrieve plugin loader for a specific plugin type
     *
     * @param  string $type
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader($type)
    {
        $type = strtolower($type);
        if (!in_array($type, $this->_loaderTypes)) {
            require_once 'Zend/View/Exception.php';
            $e = new Zend_View_Exception(sprintf('Invalid plugin loader type "%s"; cannot retrieve', $type));
            $e->setView($this);
            throw $e;
        }

        if (!array_key_exists($type, $this->_loaders)) {
            $prefix     = 'Zend_View_';
            $pathPrefix = 'Zend/View/';

            $pType = ucfirst($type);
            switch ($type) {
                case 'filter':
                case 'helper':
                default:
                    $prefix     .= $pType;
                    $pathPrefix .= $pType;
                    $loader = new Zend_Loader_PluginLoader(array(
                        $prefix => $pathPrefix
                    ));
                    $this->_loaders[$type] = $loader;
                    break;
            }
        }
        return $this->_loaders[$type];
    }

    /**
     * Adds to the stack of helper paths in LIFO order.
     *
     * @param string|array The directory (-ies) to add.
     * @param string $classPrefix Class prefix to use with classes in this
     * directory; defaults to Zend_View_Helper
     * @return Zend_View_Abstract
     */
    public function addHelperPath($path, $classPrefix = 'Zend_View_Helper_')
    {
        return $this->_addPluginPath('helper', $classPrefix, (array) $path);
    }

    /**
     * Add a prefixPath for a plugin type
     *
     * @param  string $type
     * @param  string $classPrefix
     * @param  array $paths
     * @return Zend_View_Abstract
     */
    private function _addPluginPath($type, $classPrefix, array $paths)
    {
        $loader = $this->getPluginLoader($type);
        foreach ($paths as $path) {
            $loader->addPrefixPath($classPrefix, $path);
        }
        return $this;
    }

     /**
     * Escapes a value for output in a view script.
     *
     * If escaping mechanism is one of htmlspecialchars or htmlentities, uses
     * {@link $_encoding} setting.
     *
     * @param mixed $var The output to escape.
     * @return mixed The escaped value.
     */
    public function escape($var)
    {
        if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities'))) {
            return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_encoding);
        }

        if (1 == func_num_args()) {
            return call_user_func($this->_escape, $var);
        }
        $args = func_get_args();
        return call_user_func_array($this->_escape, $args);
    }   

    /**
     * Return list of all assigned variables
     *
     * Returns all public properties of the object. Reflection is not used
     * here as testing reflection properties for visibility is buggy.
     *
     * @return array
     */
    public function getVars()
    {
	$vars = $this->_smarty->getTemplateVars();
	unset($vars["SCRIPT_NAME"]);
	return $vars;
    }
}
