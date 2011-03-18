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
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * @see iPMS_Widget_Interface.php
 */
require_once 'iPMS/Widgets/Interface.php';

/**
 * Abstract class for widget
 *
 * @category    iPMS
 * @package     iPMS_Widget
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     1.0.0
 */
abstract class iPMS_Widget implements iPMS_Widget_Interface
{
    /**
     * Widgets id
     *
     * @var string|null
     */
    protected $_id;

    /**
     * Widget order used by widget container
     *
     * @var int|null
     */
    protected $_order;

    /**
     * Widgets name
     *
     * @var string|null
     */
    protected $_name;

    /**
     * Widgets title
     *
     * @var string|null
     */
    protected $_title;

    /**
     * Widgets description
     *
     * @var string|null
     */
    protected $_description;

    /**
     * Widgets version
     *
     * @var string|null
     */
    protected $_version;

    /**
     * Widgets author
     *
     * @var string|null
     */
    protected $_author;

    /**
     * Widgets author email
     *
     * @var string|null
     */
    protected $_email;

    /**
     * Widgets license
     *
     * @var string|null
     */
    protected $_license;

    /**
     * Widgets loading type
     *
     * @var string
     */
    protected $_loadType = 'server';

    /**
     * Tells whether the widget is active
     *
     * @var
     */
    protected $_isActive;

    /**
     * Widgets custom properties
     *
     * @var array
     */
    protected $_properties = array();

    /**
     * Widgets parameters
     *
     * @var array
     */
    protected $_parameters = array();

    /**
     * Widgets content
     *
     * @var string
     */
    protected $_content;


    /**
     * @var Zend_View_Abstract
     */
    //protected $view;

    /**
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Constructor
     *
     * @param  string|array $options either an array of widget options or a string that represent the name of widget
     */
    public function __construct($options)
    {
        $this->_parameters = new stdClass();
        $this->setOptions($options);
    }

    /**
     * Sets widget properties and parameters from an associative array
     *
     * Each key in the array corresponds to the according set*() method, and each word is separated by underscores,
     * e.g. the option 'name' corresponds to setName(), and the option 'load_type' corresponds to the method
     * setLoadType().
     *
     * If the options name is 'params', it will be treated as array of widget parameters.
     *
     * @throws iPMS_Widget_Exception if invalid options are given
     * @param  string| array $options associative array of options to set
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if (is_string($value)) {
                $this->set($key, $value);
            } elseif (is_array($value) && $key === 'params') {
                $this->setOptions($value['param']);
            } else {
                $this->setParams($value);
            }
        }

        return $this;
    }

    /**
     * Sets widget properties using options from an xml file
     *
     * @throws iPMS_Widget_Exception if $file is not readable or if invalid options are given
     * @param null $file xml file that contains widget properties
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setOptionsFromXml($file)
    {
        if (is_readable($file)) {
            $config = new Zend_Config_Xml($file);
            $options = $config->toArray();
        } else {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception('File $file doesn\'t exist or is not readable');
        }

        return $this->setOptions($options);
    }

    /**
     * Sets the given parameter
     *
     * @throws iPMS_Widget_Exception if a parameter has no name
     * @param  array $params array that contains widget parameters
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setParams(array $param)
    {
        if (isset($param['name'])) {
            $name = lcfirst($this->_normalizePropertyName($param['name']));
            unset($param['name']);
            $this->_parameters->{$name} = $param;
        } else {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception('All parameters must have a name');
        }
    }

    /**
     * Returns the value of the given parameter
     *
     * @param  $name parameter name
     * @return mixed parameter value or null
     */
    public function getParam($param)
    {
        if (isset($this->_parameters->{$param})) {
            return $this->_parameters->{$param};
        }

        return null;
    }

    /**
     * Sets widget id
     *
     * @throws iPMS_Widget_Exception if $id is not a string or number or null
     * @param  string|int $id widget id
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setId($id)
    {
        if (null !== $id && !is_string($id) && !is_numeric($id)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception(
                'Invalid argument: $id must be a string, number or null'
            );
        }

        $this->_id = null === $id ? $id : (string) $id;

        return $this;
    }

    /**
     * Returns widget id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets widget order to use in widget container
     *
     * @param  int $order [optional] widget order in container. Default is null, which sets no specific order.
     * @return iPMS_Widget fluent interface, returns self
     * @throws iPMS_Widgets_Exception if order is not integer or null
     */
    public function setOrder($order = null)
    {
        if (is_string($order)) {
            $temp = (int) $order;
            if ($temp < 0 || $temp > 0 || $order == '0') {
                $order = $temp;
            }
        }

        if (null !== $order && !is_int($order)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception(
                'Invalid argument: $order must be an integer or null, or a string that casts to an integer'
            );
        }

        $this->_order = $order;

        // notify parent, if any
        //if (isset($this->_parent)) {
        //    $this->_parent->notifyOrderUpdated();
        //}

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
     * Sets widget title
     *
     * @throws iPMS_Widget_Exception if $title is not a string or null
     * @param  $title widget title
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setTitle($title)
    {
        if (null !== $title && !is_string($title)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception(
                'Invalid argument: $title must be a string or null'
            );
        }

        $this->_title = $title;

        return $this;
    }

    /**
     * Returns widget title
     *
     * @return string|null widget title
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Sets widget name
     *
     * @throws iPMS_Widget_Exception if $name is not a string or null
     * @param  $name widget name
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setName($name)
    {
        if (null !== $name && !is_string($name)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception(
                'Invalid argument: $name must be a string or null'
            );
        }

        $this->_name = $name;

        return $this;
    }

    /**
     * Returns widget name
     *
     * @return string|null widget name
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets widget description
     *
     * @throws iPMS_Widget_Exception if $description is not a string or null
     * @param  $description widget description
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setDescription($description)
    {
        if (null !== $description && !is_string($description)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception(
                'Invalid argument: $description must be a string or null'
            );
        }

        $this->_description = $description;

        return $this;
    }

    /**
     * Returns widget description
     *
     * @return string|null widget description
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Sets widget version
     *
     * @throws iPMS_Widget_Exception
     * @param  $version widget version
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setVersion($version)
    {
        if (null !== $version && !is_string($version)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception(
                'Invalid argument: $version must be a string or null'
            );
        }

        $this->_version = $version;

        return $this;
    }

    /**
     * Returns widget version
     *
     * @return string|null widget version
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Sets widget author
     *
     * @throws iPMS_Widget_Exception if $author is not a string or null
     * @param  $author widget author
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setAuthor($author)
    {
        if (null !== $author && !is_string($author)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception(
                'Invalid argument: $author must be a string or null'
            );
        }

        $this->_author = $author;

        return $this;
    }

    /**
     * Returns widget author
     *
     * @return string|null widget author
     */
    public function getAuthor()
    {
        return $this->_author;
    }

    /**
     * Sets widget author email
     *
     * @throws iPMS_Widget_Exception if $email is not a valid email or null
     * @param  $email widget author email
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setEmail($email)
    {
        if (null !== $email && !is_string($email)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception('Invalid argument: $email must be a string or null');
        }

        $this->_email = $email;

        return $this;
    }

    /**
     * Returns widget author email
     *
     * @return string widget author email
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Sets widget license
     *
     * @throws iPMS_Widget_Exception if $license is not a string or null
     * @param  $license widget license
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setLicense($license)
    {
        if (null !== $license && !is_string($license)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception('Invalid argument: $license must be a string or null');
        }

        $this->_license = $license;

        return $this;
    }

    /**
     * Returns widget license
     *
     * @return string|null widget license
     */
    public function getLicense()
    {
        return $this->_license;
    }

    /**
     * Sets widget loading type
     *
     * @throws iPMS_Widget_Exception if loading type is not 'server or 'client'
     * @param  $loadType loading type (server|client)
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setLoadType($loadType)
    {
        $loadType = strtolower($loadType);

        if ('server' !== $loadType && !$loadType !== 'client') {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception("Invalid argument: $loadType must be 'server' or 'client'");
        }

        $this->_loadType = $loadType;

        return $this;
    }

    /**
     * Returns widget loading type
     *
     * @return string server or client
     */
    public function getLoadType()
    {
        return $this->_loadType;
    }

    /**
     * Sets widget status
     *
     * @throws iPMS_Widget_Exception if $id is not a string or number or null
     * @param  string|int $isActive widget $isActive
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setIsActive($isActive)
    {
        if (null !== $isActive && !is_string($isActive) && !is_numeric($isActive)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception('Invalid argument: $isActive must be a string, number or null');
        }

        $this->_isActive = null === $isActive ? $isActive : (string) $isActive;

        return $this;
    }

    /**
     * Returns widget id
     *
     * @return string|null
     */
    public function getIsActive()
    {
        return $this->_isActive;
    }

    /**
     * Sets widget content
     *
     * Content is passed to the view as this without any treatment.
     *
     * @param  string $content widget content
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setContent($content)
    {
        $this->_content = $content;

        return $this;
    }

    /**
     * Returns widget content
     *
     * @return string|null widget content
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Sets the given property
     *
     * If $property is a native property (id, name description, version, etc), the matching set method will be used.
     * Otherwise, it will be set as a custom property.
     *
     * @throws iPMS_Widget_Exception if property name is invalid
     * @param  string $property property name
     * @param  mixed  $value value to set
     * @return iPMS_Widget fluent interface, returns self
     */
    public function set($property, $value)
    {
        if (!is_string($property) || empty($property)) {
            require_once 'Zend/Navigation/Exception.php';
            throw new iPMS_Widgets_Exception(
                'Invalid argument: $property must be a non-empty string'
            );
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
     * If $property is native property (id, name description, version, etc), the matching get method will be used.
     * Otherwise, it will return the matching custom property, or null if not found.
     *
     * @param  string $property property name
     * @return mixed the $property's value or null
     * @throws iPMS_Widget_Exception if $property is invalid
     */
    public function get($property)
    {
        if (!is_string($property) || empty($property)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception(
                'Invalid argument: $property must be a non-empty string'
            );
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
     * Sets a property
     *
     * Magic overload for enabling <code>$widget->propname = $value</code>.
     *
     * @throws iPMS_Widget_Exception if property name is invalid
     * @param  string $name property name
     * @param  mixed  $value value to set
     * @return void
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Returns a property, or null if it doesn't exist
     *
     * Magic overload for enabling <code>$widget->propname</code>.
     *
     * @throws iPMS_Widget_Exception if property name is invalid
     * @param  string $name property name
     * @return mixed property value or null
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
     * Returns true it's a native property (id, name description, version, etc), and true or false if it's a custom
     * property depending on whether the property actually is set).
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

        return isset($this->_parameters[$name]);
    }

    /**
     * Unsets the given property
     *
     * Magic overload for enabling <code>unset($widget->propname)</code>.
     *
     * @throws Zend_Navigation_Exception if $name is native property
     * @param  string $name property name
     * @return void
     */
    public function __unset($name)
    {
        $method = 'set' . self::_normalizePropertyName($name);
        if (method_exists($this, $method)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception(sprintf('Un-setting native $parameter "%s" is not allowed', $name));
        }

        if (isset($this->_properties[$name])) {
            unset($this->_properties[$name]);
        }
    }

    /**
     * Normalizes a property or parameter name
     *
     * @param  string property property|parameter name to normalize
     * @return string normalized property|parameter name
     */
    protected static function _normalizePropertyName($property)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));
    }

    /**
     * Make the widget content available for the view
     *
     * @return iPMS_Widget fluent interface, returns self
     */
    final public function run()
    {
        /*
        $content = $this->widget();
        $this->setContent($content);

        if ($content != '' && file_exists(APPLICATION_PATH . '/Widgets/' . $this->_name . '/partial/widget.phtml')) {
            $this->_renderPartial('widget.phtml');
        }

        return $this;
         */
    }

    /**
     * Make the widget settings available for the dashboard
     *
     * @return PMS_Widget fluent interface, returns self
     */
    final public function _dashboard()
    {
        /*
        $content = $this->dashboard(array());
        $this->setContent($content);

        if ($content != '' && file_exists(APPLICATION_PATH . '/Widgets/' . $this->_name . '/partial/dashboard.phtml')) {
            $this->_renderPartial('dashboard.phptml');
        }

        return $this;
         */
    }

    /**
     * Updated widget options
     *
     * @return void
     */
    final public function _update()
    {
    }

    /**
     * Returns request object
     *
     * @return null|Zend_Controller_Request_Http
     */
    public function getRequest()
    {
        if (null == $this->_request) {
            $this->_request = clone Zend_Controller_Front::getInstance()->getRequest();
        }

        return $this->_request;
    }

    /**
     * Return widget class name
     *
     * @return string
     */
    public function __toString()
    {
        // Todo change to array representation of the widget
        return get_class($this);
    }

    /**
     * Render widget partial
     *
     * @return void
     */
    private function _renderPartial($partial)
    {
        /*
        try {
            $view = clone $this->view;
            $view->addScriptPath(APPLICATION_PATH . '/Widgets/' . $this->_name . '/partial/');
            $view->assign('widget', $this);
            $this->setContent($view->render($partial)); // update widget content
        } catch (Exception $e) {
            trigger_error('Unable to render the partial for widget', E_USER_ERROR);
        }
         */
    }

    /**
     * Factory for iPMS_Widget classes
     *
     * This method will resolve the class to instantiate by using the 'name' that must be the full name of the class to
     * construct. A valid widget class must extend {@link iPMS_Widget}.
     *
     * @throws iPMS_Widgets_Exception if $options is not array
     * @throws iPMS_Exception if Zend_Loader is unable to load the class
     * @throws iPMS_Widgets_Exception if something goes wrong during instantiation of the widget
     * @throws iPMS_Widgets_Exception if the given widget class does not extend this class
     * @throws iPMS_Widget_Exception if unable to determine which class to instantiate
     * @param  array $options options used for creating widget
     * @return iPMS_Widget a widget instance
     */
    public static function factory($options)
    {
        if (!is_array($options)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new Zend_Navigation_Exception('Invalid argument: $options must be an array');
        }

        if (isset($options['name']) && is_string($options['name']) && !empty($options['name'])) {
            $className = ucwords($options['name']);
        } else {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception('Invalid argument: Unable to determine class to instantiate');
        }

        if (!class_exists($className)) {
            require_once 'Zend/Loader.php';
            @Zend_Loader::loadClass($className);
        }

        $widget = new $className($options);

        if (!$widget instanceof iPMS_Widget) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new Zend_Navigation_Exception(sprintf(
                'Invalid argument: Detected class "%s", which is not an instance of iPMS_Widget', $className));
        }

        return $widget;
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
}
