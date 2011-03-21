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
     * Widget id
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
     * Widget name
     *
     * @var string|null
     */
    protected $_name;

    /**
     * Widget title
     *
     * @var string|null
     */
    protected $_title;

    /**
     * Widget description
     *
     * @var string|null
     */
    protected $_description;

    /**
     * Widget version
     *
     * @var string|null
     */
    protected $_version;

    /**
     * Widget author
     *
     * @var string|null
     */
    protected $_author;

    /**
     * Widget author email
     *
     * @var string|null
     */
    protected $_email;

    /**
     * Widget license
     *
     * @var string|null
     */
    protected $_license;

    /**
     * Widget loading type
     *
     * @var string
     */
    protected $_load = 'server';

    /**
     * Tells whether the widget is active
     *
     * @var
     */
    protected $_isActive;

    /**
     * Widget custom properties
     *
     * @var array
     */
    protected $_properties = array();

    /**
     * Widget parameters
     *
     * @var array
     */
    protected $_parameters = array();

    /**
     * Widget forms definition
     */
    protected $_forms = array();

    /**
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Constructor
     *
     * @throws iPMS_Widget_Exception if invalid options are given
     * @param  array|Zend_Config $options [optional] widget options Default is null, which should set defaults.
     */
    public function __construct($options = null)
    {
        $this->_parameters = new stdClass();

        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }

        // do custom initialization
        $this->_init();
    }

    /**
     * Initializes widget (used by subclasses)
     *
     * @return void
     */
    protected function _init()
    {
    }

    /**
     * Sets widget properties using a Zend_Config object
     *
     * @throws iPMS_Widgets_Exception if invalid options are given
     * @param  Zend_Config $config config object to get properties from
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
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
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Sets the given form(s)
     *
     * @param  array $forms array that contains widget parameter(s)
     * @return iPMS_Widget fluent interface, returns self
     */
    protected function setForms($forms)
    {
        $forms = $forms['form'];

        if(array_key_exists('name', $forms)) {
            $this->_forms[$forms['name']] = $forms;
        } else {
            foreach($forms as $form) {
                $this->_forms[$form['name']] = $form;
            }
        }
    }

    /**
     * Sets the given parameter(s)
     *
     * @param  array $params array that contains widget parameters
     * @return iPMS_Widget fluent interface, returns self
     */
    protected function setParams($params)
    {
        $params = $params['param'];

        if(array_key_exists('name', $params)) {
            $name = lcfirst($this->_normalizePropertyName($params['name']));
            $this->_parameters->{$name} = $params['value'];
        } else {
            foreach($params as $param) {
                $name = lcfirst($this->_normalizePropertyName($param['name']));
                $this->_parameters->{$name} = $param['value'];
            }
        }
    }

    /**
     * Returns the value of the given parameter
     *
     * @param  $name parameter name
     * @return mixed parameter value or null
     */
    public function getParam($name)
    {
        if (isset($this->_parameters->{$name})) {
            return $this->_parameters->{$name};
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
     * @param  $load loading type (server|client)
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setLoad($load)
    {
        $load = strtolower($load);

        if ('server' !== $load && !$load !== 'client') {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception("Invalid argument: $load must be 'server' or 'client'");
        }

        $this->_load = $load;

        return $this;
    }

    /**
     * Returns widget loading type
     *
     * @return string server or client
     */
    public function getLoad()
    {
        return $this->_load;
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
    /*
    public function setContent($content)
    {
        $this->_content = $content;

        return $this;
    }
     */

    /**
     * Returns widget content
     *
     * @return string|null widget content
     */
    /*
    public function getContent()
    {
        return $this->_content;
    }
     */

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
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception('Invalid argument: $property must be a non-empty string');
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
            throw new iPMS_Widgets_Exception('Invalid argument: $property must be a non-empty string');
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
     * @throws iPMS_Widgets_Exception
     * @param string $context context for which the widget is run
     * @return iPMS_Widget fluent interface, returns self
     */
    final public function run($context = 'site')
    {
        if($context == 'site') {
            $result = $this->widget($this->getRequest());
        } elseif($context == 'dashboard') {
           $result = $this->dashboard(array(), array());
        } else {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception('Invalid argument: $context must be either \'site\' or \'dashboard\'');
        }

        return $result;

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
     * @return iPMS_Widget fluent interface, returns self
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
     * @param  array|Zend_Config $options options used for creating widget
     * @return iPMS_Widget a widget instance
     */
    public static function factory($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (!is_array($options)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new Zend_Navigation_Exception('Invalid argument: $options must be an array or Zend_Config');
        }

        if (isset($options['name']) && is_string($options['name']) && !empty($options['name'])) {
            $className = ucwords($options['name']);
        } else {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception('Invalid argument: Unable to determine class to instantiate');
        }

        if (!class_exists($className)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($className);
        }

        $widget = new $className($options);

        if (!$widget instanceof iPMS_Widget) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new Zend_Navigation_Exception(sprintf(
                'Invalid argument: Detected class "%s", which is not an instance of iPMS_Widget', $className)
            );
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

    /**
     * Returns a form definition
     *
     * @throws iPMS_Widgets_Exception if form definition was not found
     * @param  $name name of form for which definition must be returned
     * @return array form definition
     */
    private function getFormDefinition($name)
    {
        if(isset($this->_forms[$name])) {
            return $this->_forms[$name];
        } else {
            throw new iPMS_Widgets_Exception('Form definition not found');
        }
    }

    /**
     * Returns a Form builds from widget's form definition
     *
     * A widget can define Forms in its  xml description file. The Forms can be  used, either as main output of the
     * widget, or on the dashboard screen for setting purpose. The alternative way is to build the Form in the
     * {@link iPMS_Widget_Interface::widget()} or {@link iPMS_Widget_Interface::dashboard()} mehtods directly. See the
     * 'Login' widget to learn more.
     *
     * @throws iPMS_Widgets_Exception if form definition was not foundd)
     * @param string $name name of form to build
     * @return Zend_Form
     */
    protected function getForm($name)
    {
        $formDef = $this->getFormDefinition($name);
        $elementStack = array();

        foreach($formDef['element'] as $elementDef) {
            /**
             * @var $element Zend_Form_Element|Zend_Form_Element_Select
             */
            $className = 'Zend_Form_Element_'.ucfirst($elementDef['type']);
            $element = new $className($elementDef['name'], array(
                'label'     => $elementDef['label'],
                'value'     => $elementDef['val'],
                'helper'    => 'form' . ucfirst($elementDef['type']),
                'required'  => $elementDef['required']
            ));

            if($elementDef['type'] == 'select') {
                if(isset($elementDef['callback'])) {
                    $options = call_user_func_array($elementDef['callback']['function'],
                        (array) isset($elementDef['callback']['argument'])
                            ? $elementDef['callback']['argument']
                            : array()
                    );

                    if($elementDef['callback']['function'] == 'range')
                        $options = array_combine($options, $options);
                    
                    $element->addMultiOptions($options);

                    if(isset($elementDef['callback']['selected']))
                        $element->setValue($elementDef['callback']['selected']);

                } elseif(isset($elementDef['option'])) {
                    foreach($elementDef['option'] as $option) {
                        $element->addMultiOptions(array($option['val'] => $option['label']));

                        if(isset($option['selected']))
                            $element->setValue($option['val']);
                    }
                }
            }

            if(isset($elementDef['id'])) // CSS id selector for the element
                $element->setAttrib('id', $elementDef['id']);
            if(isset($elementDef['class'])) // CSS class selector for the element
                $element->setAttrib('class', $elementDef['class']);
            if(isset($elementDef['filter']))
                $element->addFilters((array) $elementDef['filter']);
            if(isset($elementDef['validator']))
                $element->addValidators((array) $elementDef['validator']);

            $elementStack[] = $element;
        }

        // Submit element
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('submit');
        $elementStack[] = $submit;

        $form = new Zend_Form();
        $form->setAction($this->getRequest()->getBaseUrl().'?widget='.$this->hashCode())
            ->setName(isset($formDef['name']) ? $formDef['name'] : $this)
            ->setElementsBelongTo(isset($formDef['name']) ? $formDef['name'] : $this)
            ->addElements($elementStack);

        if(isset($formDef['description'])) {
            $form->setDescription($formDef['description'])
                ->addDecorator('Description', array('placement' => 'prepend'));
        }

        return $form;
    }
}
