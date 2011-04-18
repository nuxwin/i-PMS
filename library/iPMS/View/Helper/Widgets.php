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
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * @see iPMS_View_Helper_Widgets_HelperAbstract
 */
require_once 'iPMS/View/Helper/Widgets/HelperAbstract.php';

/**
 * Proxy helper for retrieving widget helpers and forwarding calls
 *
 * @category    iPMS
 * @package     iPMS_View
 * @subpackage  Helper
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
class iPMS_View_Helper_Widgets extends iPMS_View_Helper_Widgets_HelperAbstract
{

    /**
     * View helper namespace
     *
     * @var string
     */
    const NS = 'iPMS_View_Helper_Widgets';

    /**
     * Default proxy to use in {@link render()}
     *
     * @var string
     */
    protected $_defaultProxy = 'sidebar';

    /**
     * Contains references to proxied helpers
     *
     * @var array
     */
    protected $_helpers = array();

    /**
     * Helper entry point
     *
     * @param iPMS_Widgets_Container $container [optional] container to operate on
     * @return iPMS_View_Helper_Widgets fluent interface, returns self
     */
    public function widgets(iPMS_Widgets_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }

        return $this;
    }

    /**
     * Magic overload: Proxy to other widgets helpers or the container
     *
     * Examples of usage from a view script or layout:
     * <code>
     * // proxy to Sidebar helper and render container:
     * echo $this->widgets()->sidebar();
     *
     * // proxy to container and find all widgets for 'contentTopRight' sidebar:
     * $widgets = $this->widgets()->findAllBySidebar('contentTopRight');
     * </code>
     *
     * @param  string $method helper name or method name in container
     * @param  array  $arguments [optional] arguments to pass returns what the proxied call returns
     * @throws iPMS_View_Exception if proxying to a helper, and the helper is not an instance of the interface specified
     * in {@link findHelper()}
     * @throws iPMS_Widgets_Exception if method does not exist in container
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
     * The helper must implement the interface {@link iPMS_View_Helper_Widgets_Helper}.
     *
     * @param string $proxy  helper name
     * @param bool $strict [optional] whether exceptions should be thrown if something goes wrong. Default is true.
     * @return iPMS_View_Helper_Widgets_Helper helper instance
     * @throws Zend_Loader_PluginLoader_Exception if $strict is true and helper cannot be found
     * @throws iPMS_View_Exception if $strict is true and helper does not implement the specified interface
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

        if (!$helper instanceof Zend_View_Helper_Widgets_Helper) {
            if ($strict) {
                require_once 'iPMS/View/Exception.php';
                $e = new iPMS_View_Exception(sprintf(
                    'Proxy helper "%s" is not an instance of iPMS_View_Helper_Widgets_Helper', get_class($helper))
                );
                $e->setView($this->view);
                throw $e;
            }

            return null;
        }

        $this->_helpers[$proxy] = $helper;

        return $helper;
    }

    /**
     * Sets the default proxy to use in {@link render()}
     *
     * @param  string $proxy default proxy
     * @return iPMS_View_Helper_Widgets fluent interface, returns self
     */
    public function setDefaultProxy($proxy)
    {
        $this->_defaultProxy = (string) $proxy;
        return $this;
    }

    /**
     * Returns the default proxy to use in {@link render()}
     *
     * @return string the default proxy to use in {@link render()}
     */
    public function getDefaultProxy()
    {
        return $this->_defaultProxy;
    }

    /**
     * Renders helper
     *
     * Implements {@link iPMS_View_Helper_Widgets_Helper::render()}.
     *
     * @param iPMS_Widgets_Container $container [optional] container to render. Default is to render the container
     * registered in the helper.
     * @return string helper output
     * @throws Zend_Loader_PluginLoader_Exception if helper cannot be found
     * @throws iPMS_View_Exception if helper doesn't implement the interface specified in {@link findHelper()}
     */
    public function render(iPMS_Widgets_Container $container = null)
    {
        $helper = $this->findHelper($this->getDefaultProxy());
        return $helper->render($container);
    }
}
