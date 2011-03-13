<?php

/**
 * i-PMS - internet Project Management System
 * Copyright (C) 2011 by Laurent Declercq
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @category    iPMS
 * @package     iPMS_View
 * @subpackage  Helper
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */
/**
 * @see iPMS_View_Helper_Widget_HelperAbstract
 */
require_once 'iPMS/View/Helper/Widget/HelperAbstract.php';

/**
 * Proxy helper for retrieving widget helpers and forwarding calls
 *
 * @category    iPMS
 * @package     iPMS_View
 * @subpackage  Helper
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class iPMS_View_Helper_Widget extends iPMS_View_Helper_Widget_HelperAbstract
{
    /**
     * View helper namespace
     *
     * @var string
     */
    const NS = 'iPMS_View_Helper_Widget';

    /**
     * Default proxy to use in {@link render()}
     *
     * @var string
     */
    protected $_defaultProxy = 'box';
    /**
     * Contains references to proxied helpers
     *
     * @var array
     */
    protected $_helpers = array();
    /**
     * Whether container should be injected when proxying
     *
     * @var bool
     */
    protected $_injectContainer = true;
    /**
     * Whether ACL should be injected when proxying
     *
     * @var bool
     */
    protected $_injectAcl = true;
    /**
     * Whether translator should be injected when proxying
     *
     * @var bool
     */
    protected $_injectTranslator = true;

    /**
     * Helper entry point
     *
     * @param   iPMS_Widget_Container_Abstract $container [optional] container to operate on
     * @return  iPMS_View_Helper_Widget fluent interface, returns self
     */
    public function widget(iPMS_Widget_Container_Abstract $container = null)
    {
	if (null !== $container) {
	    $this->setContainer($container);
	}

	return $this;
    }

    /**
     * Magic overload: Proxy to other widget helpers or the container
     *
     * Examples of usage from a view script or layout:
     * <code>
     * // proxy to Box helper and render container:
     * echo $this->widget()->box();
     *
     * // proxy to Breadcrumbs helper and set indentation:
     * $this->widget()->collection()->setIndent(8);
     *
     * // proxy to container and find all widgets by target :
     * $sidebarWidget = $this->widget()->findAllByTarget('sidebar');
     * </code>
     *
     * @param   string $method helper name or method name in container
     * @param   array  $arguments [optional] arguments to pass
     * @return  mixed  returns what the proxied call returns
     * @throws  iPMS_View_Exception if proxying to a helper, and the helper is not an instance of the interface specified
     * in {@link findHelper()}
     * @throws  iPMS_Widget_Exception if method does not exist in container
     */
    public function __call($method, array $arguments = array())
    {
	// check if call should proxy to another helper
	if ($helper = $this->findHelper($method, false)) {
	    return call_user_func_array(array($helper, $method), $arguments);
	}

	// default behaviour: proxy call to container
	return parent::__call($method, $arguments);
    }

    /**
     * Returns the helper matching $proxy
     *
     * The helper must implement the interface {@link iPMS_View_Helper_Widget_Helper}.
     *
     * @param   string $proxy helper name
     * @param   bool $strict [optional] whether exceptions should be thrown if something goes wrong. Default is true.
     * @return  iPMS_View_Helper_Widget_Helper helper instance
     * @throws  Zend_Loader_PluginLoader_Exception if $strict is true and helper cannot be found
     * @throws  iPMS_View_Exception if $strict is true and helper does not implement the specified interface
     */
    public function findHelper($proxy, $strict = true)
    {
	if (isset($this->_helpers[$proxy])) {
	    return $this->_helpers[$proxy];
	}

	if (!$this->view->getPluginLoader('helper')->getPaths(self::NS)) {
	    $this->view->addHelperPath(str_replace('_', '/', self::NS), self::NS);
	}

	if ($strict) {
	    $helper = $this->view->getHelper($proxy);
	} else {
	    try {
		$helper = $this->view->getHelper($proxy);
	    } catch (Zend_Loader_PluginLoader_Exception $e) {
		return null;
	    }
	}

	if (!$helper instanceof iPMS_View_Helper_Widget_Helper) {
	    if ($strict) {
		require_once 'iPMS/View/Exception.php';
		$e = new Zend_View_Exception(sprintf(
					'Proxy helper "%s" is not an instance of iPMS_View_Helper_Widget_Helper', get_class($helper)
			));
		$e->setView($this->view);
		throw $e;
	    }

	    return null;
	}

	$this->_inject($helper);
	$this->_helpers[$proxy] = $helper;

	return $helper;
    }

    /**
     * Injects container, ACL, and translator to the given $helper if this helper is configured to do so
     *
     * @param   iPMS_View_Helper_Widget_Helper $helper helper instance
     * @return  void
     */
    protected function _inject(iPMS_View_Helper_Widget_Helper $helper)
    {
	if ($this->getInjectContainer() && !$helper->hasContainer()) {
	    $helper->setContainer($this->getContainer());
	}

	if ($this->getInjectAcl()) {
	    if (!$helper->hasAcl()) {
		$helper->setAcl($this->getAcl());
	    }
	    if (!$helper->hasRole()) {
		$helper->setRole($this->getRole());
	    }
	}

	if ($this->getInjectTranslator() && !$helper->hasTranslator()) {
	    $helper->setTranslator($this->getTranslator());
	}
    }

    /**
     * Sets the default proxy to use in {@link render()}
     *
     * @param   string $proxy default proxy
     * @return  iPMS_View_Helper_Widget fluent interface, returns self
     */
    public function setDefaultProxy($proxy)
    {
	$this->_defaultProxy = (string) $proxy;
	return $this;
    }

    /**
     * Returns the default proxy to use in {@link render()}
     *
     * @return  string the default proxy to use in {@link render()}
     */
    public function getDefaultProxy()
    {
	return $this->_defaultProxy;
    }

    /**
     * Sets whether container should be injected when proxying
     *
     * @param   bool $injectContainer [optional] whether container should be injected when proxying. Default is true.
     * @return  iPMS_View_Helper_Widget fluent interface, returns self
     */
    public function setInjectContainer($injectContainer = true)
    {
	$this->_injectContainer = (bool) $injectContainer;
	return $this;
    }

    /**
     * Returns whether container should be injected when proxying
     *
     * @return  bool whether container should be injected when proxying
     */
    public function getInjectContainer()
    {
	return $this->_injectContainer;
    }

    /**
     * Sets whether ACL should be injected when proxying
     *
     * @param   bool $injectAcl [optional] whether ACL should be injected when proxying. Default is true.
     * @return  iPMS_View_Helper_Widget  fluent interface, returns self
     */
    public function setInjectAcl($injectAcl = true)
    {
	$this->_injectAcl = (bool) $injectAcl;
	return $this;
    }

    /**
     * Returns whether ACL should be injected when proxying
     *
     * @return  bool whether ACL should be injected when proxying
     */
    public function getInjectAcl()
    {
	return $this->_injectAcl;
    }

    /**
     * Sets whether translator should be injected when proxying
     *
     * @param   bool $injectTranslator [optional] whether translator should be injected when proxying. Default is true.
     * @return  iPMS_View_Helper_Widget fluent interface, returns self
     */
    public function setInjectTranslator($injectTranslator = true)
    {
	$this->_injectTranslator = (bool) $injectTranslator;
	return $this;
    }

    /**
     * Returns whether translator should be injected when proxying
     *
     * @return bool whether translator should be injected when proxying
     */
    public function getInjectTranslator()
    {
	return $this->_injectTranslator;
    }

    /**
     * Renders helper
     *
     * @param   iPMS_Widget_Container_Abstract $container [optional] container to render. Default is to render the
     * container registered in the helper.
     * @return  string helper output
     * @throws  Zend_Loader_PluginLoader_Exception if helper cannot be found
     * @throws  iPMS_View_Exception if helper doesn't implement the interface specified in {@link findHelper()}
     */
    public function render(iPMS_Widget_Container_Abstract $container = null)
    {
	$helper = $this->findHelper($this->getDefaultProxy());
	return $helper->render($container);
    }

}
