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
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * @see iPMS_View_Helper_Widgets_Helper
 */
require_once 'iPMS/View/Helper/Widgets/Helper.php';

/**
 * @see Zend_View_Helper_HtmlElement
 */
require_once 'Zend/View/Helper/HtmlElement.php';


/**
 * Base class for widgets helpers
 *
 * @category    iPMS
 * @package     iPMS_View
 * @subpackage  Helper
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
abstract class iPMS_View_Helper_Widgets_HelperAbstract extends Zend_View_Helper_HtmlElement
    implements iPMS_View_Widgets_Helper
{

    /**
     * Container to operate on by default
     *
     * @var iPMS_Widgets_Container
     */
    protected $_container;

    /**
     * Sets widget container the helper operates on by default
     *
     * Implements Implements {@link iPMS_View_Helper_Widgets_Helper::setContainer()}.
     *
     * @param  iPMS_Widgets_Container $container [optional] container to operate on. Default is null, meaning container
     * will be reset.
     * @return iPMS_View_Helper_Widget fluent interface, returns self
     */
    public function setContainer(iPMS_Widgets_Container $container = null)
    {
        $this->_container = $container;
        return $this;
    }

    /**
     * Returns the widget container helper operates on by default
     *
     * If a helper is not explicitly set in this helper instance by calling {@link setContainer()} or by passing it
     * through the helper entry point, this method will look in {@link Zend_Registry} for a container by using the key
     * 'iPMS_Widgets'.
     *
     * If no container is set, and nothing is found in Zend_Registry, a new container will be instantiated and stored in
     * the helper.
     *
     * Implements Implements {@link iPMS_View_Helper_Widgets_Helper::getContainer()}.
     *
     * @return iPMS_Widgets_Container widget container
     */
    public function getContainer()
    {
        if (null === $this->_container) {
            // try to fetch from registry first
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('iPMS_Widgets')) {
                $widgets = Zend_Registry::get('iPMS_Widgets');
                if ($widgets instanceof iPMS_Widgets_Container) {
                    return $this->_container = $widgets;
                }
            }

            // nothing found in registry, create new container
            require_once 'iPMS/Widgets/Container.php';
            $this->_container = new iPMS_Widgets_Container();
        }

        return $this->_container;
    }

    /**
     * Magic overload: Proxy calls to the widget container
     *
     * @param  string $method method name in container
     * @param  array  $arguments  [optional] arguments to pass
     * @return mixed returns what the container returns
     * @throws iPMS_Widgets_Exception if method does not exist in container
     */
    public function __call($method, array $arguments = array())
    {
        return call_user_func_array(array($this->getContainer(), $method), $arguments);
    }

    /**
     * Magic overload: Proxy to {@link render()}.
     *
     * This method will trigger an E_USER_ERROR if rendering the helper causes an exception to be thrown.
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::__toString()}.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $e) {
            $msg = get_class($e) . ': ' . $e->getMessage();
            trigger_error($msg, E_USER_ERROR);
            return '';
        }
    }

    /**
     * Checks if the helper has a container
     *
     * Implements {@link iPMS_View_Helper_Widgets_Helper::hasContainer()}.
     *
     * @return bool whether the helper has a container or not
     */
    public function hasContainer()
    {
        return null !== $this->_container;
    }
}
