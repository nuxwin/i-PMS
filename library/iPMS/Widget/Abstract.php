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
 * @package     iPMS_Widget
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Base class for widgets
 *
 * @category    iPMS
 * @package     iPMS_Widget
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
//abstract class iPMS_Widget_Abstract extends Zend_Controller_Action_Helper_Abstract
abstract class iPMS_Widget_Abstract
{

    /**
     * Widget name
     *
     * @var string|null
     */
    protected $_name;
    /**
     * Widget id
     *
     * @var string|null
     */
    protected $_id;
    /**
     * Style class for this widget (CSS)
     *
     * @var string|null
     */
    protected $_class;
    /**
     * A more descriptive title for this widget
     *
     * @var string|null
     */
    protected $_title;
    /**
     * @var string widget content to be rendered
     */
    protected $_content;
    /**
     * This widget's target area (Area where the widget should be rendered)
     *
     * @var string|null
     */
    protected $_targetArea;
    /**
     * Widget order used by parent container
     *
     * @var int|null
     */
    protected $_order;
    /**
     * ACL resource associated with this widget
     *
     * @var string|Zend_Acl_Resource_Interface|null
     */
    protected $_resource;
    /**
     * ACL privilege associated with this widget
     *
     * @var string|null
     */
    protected $_privilege;
    /**
     * Whether this widget is active
     *
     * @var bool
     */
    protected $_isActive = false;
    /**
     * Widget container (The container that contain all widgets)
     *
     * @var iPMS_Widget_Container_Abstract|null
     */
    protected $_parent;
    /**
     * Custom widget properties, used by __set(), __get() and __isset()
     *
     * @var array
     */
    protected $_properties = array();

    /**
     * Widget constructor
     *
     * @param  array|Zend_Config $options [optional] widget options. Default is null, which should set defaults.
     * @throws iPMS_Widget_Exception if invalid options are given
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
    }

    /**
     * Sets widget properties using a Zend_Config object
     *
     * @param  Zend_Config $config config object to get properties from
     * @return iPMS_Widget_Abstract fluent interface, returns self
     * @throws iPMS_Widget_Exception if invalid options are given
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }

    /**
     * Sets widget properties using options from an associative array
     *
     * Each key in the array corresponds to the according set*() method, and each word is separated by underscores,
     * e.g. the option 'target' corresponds to setTarget(), and the option 'reset_params' corresponds to the method
     * setResetParams().
     *
     * @param  array $options associative array of options to set
     * @return iPMS_Widget_Abstract fluent interface, returns self
     * @throws iPMS_Widget_Exception if invalid options are given
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Sets widget name
     *
     * @param  string $name new widget name
     * @return iPMS_Widget_Abstract fluent interface, returns self
     * @throws iPMS_Widget_Exception if empty/no string is given
     */
    public function setName($name)
    {
        if (null !== $name && !is_string($name)) {
            require_once 'iPMS/Widget/Exception.php';
            throw new iPMS_Widget_Exception('Invalid argument: $name must be a string or null');
        }

        $this->_name = $name;
        return $this;
    }

    /**
     * Returns widget name
     *
     * @return string widget name or null
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets page id
     *
     * @param  string|null $id [optional] id to set. Default is null, which sets no id.
     * @return iPMS_Widget_Abstract fluent interface, returns self
     * @throws iPMS_Widget_Exception if not given string or null
     */
    public function setId($id = null)
    {
        if (null !== $id && !is_string($id) && !is_numeric($id)) {
            require_once 'iPMS/Widget/Exception.php';
            throw new iPMS_Widget_Exception('Invalid argument: $id must be a string, number or null');
        }

        $this->_id = null === $id ? $id : (string)$id;

        return $this;
    }

    /**
     * Returns widget id
     *
     * @return string|null widget id or null
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets widget CSS class
     *
     * @param  string|null $class [optional] CSS class to set. Default is null, which sets no CSS class.
     * @return iPMS_Widget_Abstract fluent interface, returns self
     * @throws iPMS_Widget_Exception if not given string or null
     */
    public function setClass($class = null)
    {
        if (null !== $class && !is_string($class)) {
            require_once 'iPMS/Widget/Exception.php';
            throw new iPMS_Widget_Exception('Invalid argument: $class must be a string or null');
        }

        $this->_class = $class;
        return $this;
    }

    /**
     * Returns widget class (CSS)
     *
     * @return string|null widget's CSS class or null
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Sets widget title
     *
     * @param  string $title [optional] widget title. Default is null, which sets no title.
     * @return iPMS_Widget_Abstract fluent interface, returns self
     * @throws iPMS_Widget_Exception if not given string or null
     */
    public function setTitle($title = null)
    {
        if (null !== $title && !is_string($title)) {
            require_once 'iPMS/Widget/Exception.php';
            throw new iPMS_Widget_Exception('Invalid argument: $title must be a non-empty string');
        }

        $this->_title = $title;
        return $this;
    }

    /**
     * Returns widget title
     *
     * @return string|null widget title or null
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set widget content
     *
     * @throws iPMS_Widget_Exception
     * @param  $content content to be rendered
     * @return iPMS_Widget_Abstract
     */
    public function setContent($content)
    {
        if (null !== $content && !is_string($content)) {
            require_once 'iPMS/Widget/Exception.php';
            throw new iPMS_Widget_Exception('Invalid argument: $content must be a string or null');
        }

        $this->_content = $content;
        return $this;
    }

    /**
     * Return widget content
     *
     * @return string Content to be rendered
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Sets widget target Area
     *
     * @param  string|null $targetArea target to set. Default is null, which sets no target.
     * @return iPMS_Widget_Abstract fluent interface, returns self
     * @throws Zend_Navigation_Exception if target is not string or null
     * @todo check that the area is valid
     */
    public function setTargetArea($targetArea)
    {
        if (!is_string($targetArea)) {
            require_once 'iPMS/Widget/Exception.php';
            throw new iPMS_Widget_Exception('Invalid argument: $target must be a string');
        }

        $this->_targetArea = $targetArea;
        return $this;
    }

    /**
     * Returns widget target area
     *
     * @return string|null widget target or null
     */
    public function getTargetArea()
    {
        return $this->_targetArea;
    }

    /**
     * Sets widget order to use in parent container
     *
     * @param int $order [optional] widget order in container. Default is null, which sets no specific order.
     * @return iPMS_Widget_Abstract fluent interface, returns self
     * @throws iPMS_Widget_Exception if order is not integer or null
     */
    public function setOrder($order = null)
    {
        if (is_string($order)) {
            $temp = (int)$order;
            if ($temp < 0 || $temp > 0 || $order == '0') {
                $order = $temp;
            }
        }

        if (null !== $order && !is_int($order)) {
            require_once 'iPMS/Widget/Exception.php';
            throw new iPMS_Widget_Exception(
                'Invalid argument: $order must be an integer or null, or a string that casts to an integer'
            );
        }

        $this->_order = $order;

        // notify parent container that order was updated
        if (isset($this->_parent)) {
            $this->_parent->notifyOrderUpdated();
        }

        return $this;
    }

    /**
     * Returns widget order used in parent container
     *
     * @return int|null widget order or null
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Sets ACL resource associated with this widget
     *
     * @param  string|Zend_Acl_Resource_Interface $resource [optional] resource  to associate with widget. Default is
     * null, which sets no resource.
     * @throws iPMS_Widget_Exception if $resource if invalid
     * @return iPMS_Widget_Abstract fluent interface, returns self
     */
    public function setResource($resource = null)
    {
        if (null === $resource || is_string($resource) ||
            $resource instanceof Zend_Acl_Resource_Interface) {
            $this->_resource = $resource;
        } else {
            require_once 'iPMS/Widget/Exception.php';
            throw new iPMS_Widget_Exception(
                'Invalid argument: $resource must be null, a string, or an instance of Zend_Acl_Resource_Interface'
            );
        }

        return $this;
    }

    /**
     * Returns ACL resource associated with this widget
     *
     * @return string|Zend_Acl_Resource_Interface|null ACL resource or null
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * Sets ACL privilege associated with this widget
     *
     * @param  string|null $privilege [optional] ACL privilege to associate with this page. Default is null, which sets
     * no privilege.
     * @return iPMS_Widget_Abstract fluent interface, returns self
     */
    public function setPrivilege($privilege = null)
    {
        $this->_privilege = is_string($privilege) ? $privilege : null;
        return $this;
    }

    /**
     * Returns ACL privilege associated with this widget
     *
     * @return string|null ACL privilege or null
     */
    public function getPrivilege()
    {
        return $this->_privilege;
    }

    /**
     * Sets whether widget should be considered active or not
     *
     * @param  bool $isActive [optional] whether widget should be considered active or not. Default is true.
     * @return iPMS_Widget_Abstract fluent interface, returns self
     */
    public function setIsActive($isActive = true)
    {
        $this->_isActive = (bool)$isActive;
        return $this;
    }

    /**
     * Returns whether widget should be considered active or not
     *
     * @return bool whether widget should be considered active
     */
    public function isActive()
    {
        return $this->_isActive;
    }

    /**
     * Proxy to isActive()
     *
     * @return bool whether widget should be considered active
     */
    public function getActive()
    {
        return $this->isActive();
    }

    /**
     * Sets parent container
     *
     * @param iPMS_Widget_Container_Abstract $parent [optional] new parent to set. Default is null which will set no
     * parent.
     * @return iPMS_Widget_Abstract fluent interface, returns self
     */
    public function setParent(iPMS_Widget_Container_Abstract $parent = null)
    {

        /*
        if ($parent === $this) {
        require_once 'iPMS/Widget/Exception.php';
        throw new iPMS_Widget_Exception('A widget cannot have itself as a parent');
        }
       */

        // return if the given parent already is parent
        if ($parent === $this->_parent) {
            return $this;
        }

        // remove from old parent
        if (null !== $this->_parent) {
            $this->_parent->removeWidget($this);
        }

        // set new parent
        $this->_parent = $parent;

        // add to parent if widget and not already a child
        // Todo make sure for that
        /*
        if (null !== $this->_parent && !$this->_parent->hasWidget($this, false)) {
        $this->_parent->addWidget($this);
        }
       */

        return $this;
    }

    /**
     * Returns parent container
     *
     * @return iPMS_Widget_Container_Abstract|null parent container or null
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Sets the given property
     *
     * If the given property is native (id, class, title, etc), the matching set method will be used. Otherwise, it will
     * be set as a custom property.
     *
     * @param  string $property property name
     * @param  mixed  $value value to set
     * @return iPMS_Widget_Abstract fluent interface, returns self
     * @throws iPMS_Widget_Exception if property name is invalid
     */
    public function set($property, $value)
    {
        if (!is_string($property) || empty($property)) {
            require_once 'iPMS/Widget/Exception.php';
            throw new iPMS_Widget_Exception('Invalid argument: $property must be a non-empty string');
        }

        $method = 'set' . self::_normalizePropertyName($property);

        if ($method != 'setOptions' && $method != 'setConfig' && method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $this->_properties[$property] = $value;
        }

        return $this;
    }

    /**
     * Returns the value of the given property
     *
     * If the given property is native (id, class, title, etc), the matching get method will be used. Otherwise, it will
     * return the matching custom property, or null if not found.
     *
     * @param  string $property property name
     * @return mixed the property's value or null
     * @throws iPMS_Widget_Exception if property name is invalid
     */
    public function get($property)
    {
        if (!is_string($property) || empty($property)) {
            require_once 'iPMS/Widget/Exception.php';
            throw new iPMS_Widget_Exception('Invalid argument: $property must be a non-empty string');
        }

        $method = 'get' . self::_normalizePropertyName($property);

        if (method_exists($this, $method)) {
            return $this->$method();
        } elseif (isset($this->_properties[$property])) {
            return $this->_properties[$property];
        }

        return null;
    }

    /**
     * Sets a custom property
     *
     * Magic overload for enabling <code>$widget->propname = $value</code>.
     *
     * @param  string $name property name
     * @param  mixed  $value value to set
     * @return void
     * @throws iPMS_Widget_Exception if property name is invalid
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Returns a property, or null if it doesn't exist
     *
     * Magic overload for enabling <code>$page->propname</code>.
     *
     * @param  string $name property name
     * @return mixed property value or null
     * @throws iPMS_Widget_Exception if property name is invalid
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Checks if a property is set
     *
     * Magic overload for enabling <code>isset($widget->propname)</code>.
     *
     * Returns true if the property is native (id, class, title, etc), and true or false if it's a custom property
     * (depending on whether the property actually is set).
     *
     * @param  string $name property name
     * @return bool whether the given property exists
     */
    public function __isset($name)
    {
        $method = 'get' . self::_normalizePropertyName($name);
        if (method_exists($this, $method)) {
            return true;
        }

        return isset($this->_properties[$name]);
    }

    /**
     * Unsets the given custom property
     *
     * Magic overload for enabling <code>unset($widget->propname)</code>.
     *
     * @param  string $name property name
     * @return void
     * @throws iPMS_Widget_Exception if the property is native
     */
    public function __unset($name)
    {
        $method = 'set' . self::_normalizePropertyName($name);
        if (method_exists($this, $method)) {
            require_once 'iPMS/Widget/Exception.php';
            throw new iPMS_Widget_Exception(sprintf('Unsetting native property "%s" is not allowed', $name));
        }

        if (isset($this->_properties[$name])) {
            unset($this->_properties[$name]);
        }
    }

    /**
     * Returns widget label
     *
     * Magic overload for enabling <code>echo $widget</code>.
     *
     * @return string widget label
     */
    public function __toString()
    {
        //return $this->_label;
        return $this->_name;
    }

    /**
     * Returns custom properties as an array
     *
     * @return array an array containing custom properties
     */
    public function getCustomProperties()
    {
        return $this->_properties;
    }

    /**
     * Returns a hash code value for the widget
     *
     * @return string a hash code value for this widget
     */
    public final function hashCode()
    {
        return spl_object_hash($this);
    }

    /**
     * Returns an array representation of the widget
     *
     * @return array associative array containing all widget properties
     */
    public function toArray()
    {
        return array_merge(
            $this->getCustomProperties(),
            array(
                 'name' => $this->getName(),
                 'id' => $this->getId(),
                 'class' => $this->getClass(),
                 'title' => $this->getTitle(),
                 'targetArea' => $this->getTargetArea(),
                 'order' => $this->getOrder(),
                 'resource' => $this->getResource(),
                 'privilege' => $this->getPrivilege(),
                 'isActive' => $this->isActive(),
            ));
    }

    /**
     * Normalizes a property name
     *
     * @param  string $property property name to normalize
     * @return string normalized property name
     */
    protected static function _normalizePropertyName($property)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));
    }

    /**
     * Tell whether or not partial must be used for widget rendering
     *
     * @var bool
     */
    protected $_partial = false;

    /**
     * Add the current widget to the widgets container for rendering an setup script path for widget if needed
     *
     * @return void
     */
    protected function _prepareView()
    {

        //$view = $this->getActionController()->view;
        //$view->Widget()->setContainer()
        //$this->view->Widget()->getContainer()->addWidget($this);
        // Add widget partial path if needed
        //if($this->hasPartial()) {
        //$this->view->addScriptPath(APPLICATION_PATH . '/widgets/' . $this . '/partial');
        //}
    }

    /**
     * @return bool
     */
    public function hasPartial()
    {
        return (bool)$this->_partial;
    }

    // Proxy

    /**
     * Retrieve current action controller
     *
     * @return Zend_Controller_Action
     */
    public function getActionController()
    {
        return $this->getParent()->getActionController();
    }

    /**
     * Retrieve front controller instance
     *
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        return Zend_Controller_Front::getInstance();
    }

    /**
     * getRequest() -
     *
     * @return Zend_Controller_Request_Abstract $request
     */
    public function getRequest()
    {
        return $this->getParent()->getRequest();
    }

    /**
     * getResponse() -
     *
     * @return Zend_Controller_Response_Abstract $response
     */
    public function getResponse()
    {
        return $this->getParent()->getResponse();
    }

}
