<?php

/**
 * @see iPMS_View_Helper_Widget_Helper
 */
require_once 'iPMS/View/Helper/Widget/Helper.php';

/**
 * @see Zend_View_Helper_HtmlElement
 */
require_once 'Zend/View/Helper/HtmlElement.php';

/**
 * Base class for widget view helpers
 *
 * @category    iPMS
 * @package     iPMS_View
 * @subpackage  Helper_Widget
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
abstract class iPMS_View_Helper_Widget_HelperAbstract extends Zend_View_Helper_HtmlElement implements iPMS_View_Helper_Widget_Helper
{

    /**
     * Container to operate on by default
     *
     * @var iPMS_Widget_Container_Abstract
     */
    protected $_container;
    /**
     * Zone to which a widget must belong to be included when rendering
     *
     * @var string
     */
    protected $_zone = null;
    /**
     * Indentation string
     *
     * @var string
     */
    protected $_indent = '';
    /**
     * Translator
     *
     * @var Zend_Translate_Adapter
     */
    protected $_translator;
    /**
     * ACL to use when iterating widgets
     *
     * @var Zend_Acl
     */
    protected $_acl;
    /**
     * Whether invisible items should be rendered by this helper
     *
     * @var bool
     */
    protected $_renderInvisible = false;
    /**
     * ACL role to use when iterating widgets
     *
     * @var string|Zend_Acl_Role_Interface
     */
    protected $_role;
    /**
     * Whether translator should be used for widget titles
     *
     * @var bool
     */
    protected $_useTranslator = true;
    /**
     * Whether ACL should be used for filtering out widgets
     *
     * @var bool
     */
    protected $_useAcl = true;
    /**
     * Default ACL to use when iterating widget if not explicitly set in the instance by calling {@link setAcl()}
     *
     * @var Zend_Acl
     */
    protected static $_defaultAcl;
    /**
     * Default ACL role to use when iterating widgets if not explicitly set in the instance by calling {@link setRole()}
     *
     * @var string|Zend_Acl_Role_Interface
     */
    protected static $_defaultRole;

    /**
     * Sets widget container the helper operates on by default
     *
     * Implements {@link iPMS_View_Helper_Widget_Interface::setContainer()}.
     *
     * @param  iPMS_Widget_Container_Abstract $container  [optional] container to operate on. Default is null, meaning container
     * will be reset.
     * @return iPMS_View_Helper_Widget_HelperAbstract fluent interface, returns self
     */
    public function setContainer(iPMS_Widget_Container_Abstract $container = null)
    {
        $this->_container = $container;
        return $this;
    }

    /**
     * Returns the widget container helper operates on by default
     *
     * Implements {@link iPMS_View_Helper_Widget_Interface::getContainer()}.
     *
     * If a helper is not explicitly set in this helper instance by calling {@link setContainer()} or by passing it
     * through the helper entry point, this method will look in {@link Zend_Registry} for a container by using the key
     * 'iPMS_Widget'.
     *
     * If no container is set, and nothing is found in Zend_Registry, a new container will be instantiated and stored
     * in the helper.
     *
     * @return iPMS_Widget_Container_Abstract widget container
     */
    public function getContainer()
    {
        if (null === $this->_container) {
            // try to fetch from registry first
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('iPMS_Widget_Container_Abstract')) {
                $widgetContainer = Zend_Registry::get('iPMS_Widget_Container_Abstract');
                if ($widgetContainer instanceof iPMS_Widget_Container_Abstract) {
                    return $this->_container = $widgetContainer;
                }
            }

            // nothing found in registry, create new container
            require_once 'iPMS/Widget.php';
            $this->_container = new iPMS_Widget_Container();
        }

        return $this->_container;
    }

    /**
     * Sets the render zone to which a widget must belong to be included when rendering
     *
     * @param  string Widgets zone
     * @return iPMS_View_Helper_Widget_HelperAbstract fluent interface, returns self
     */
    public function setZone($zone)
    {
        $this->_zone = (string)$zone;
        return $this;
    }

    /**
     * Returns zone to which a widget must belong to be included when rendering
     *
     * @return string Widgets zone
     */
    public function getZone()
    {
        return $this->_zone;
    }

    /**
     * Set the indentation string for using in {@link render()}, optionally a number of spaces to indent with
     *
     * @param  string|int $indent indentation string or number of spaces
     * @return iPMS_View_Helper_Widget_HelperAbstract fluent interface, returns self
     */
    public function setIndent($indent)
    {
        $this->_indent = $this->_getWhitespace($indent);
        return $this;
    }

    /**
     * Returns indentation
     *
     * @return string
     */
    public function getIndent()
    {
        return $this->_indent;
    }

    /**
     * Sets translator to use in helper
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::setTranslator()}.
     *
     * @param   mixed $translator [optional] translator. Expects an object of type {@link Zend_Translate_Adapter} or
     * {@link   Zend_Translate}, or null. Default is null, which sets no translator.
     * @return  iPMS_View_Helper_Widget_HelperAbstract fluent interface, returns self
     */
    public function setTranslator($translator = null)
    {
        if (null == $translator ||
            $translator instanceof Zend_Translate_Adapter) {
            $this->_translator = $translator;
        } elseif ($translator instanceof Zend_Translate) {
            $this->_translator = $translator->getAdapter();
        }

        return $this;
    }

    /**
     * Returns translator used in helper
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::getTranslator()}.
     *
     * @return  Zend_Translate_Adapter|null translator or null
     */
    public function getTranslator()
    {
        if (null === $this->_translator) {
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zend_Translate')) {
                $this->setTranslator(Zend_Registry::get('Zend_Translate'));
            }
        }

        return $this->_translator;
    }

    /**
     * Sets ACL to use when iterating pages
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::setAcl()}.
     *
     * @param   Zend_Acl $acl [optional] ACL object. Default is null.
     * @return  iPMS_View_Helper_Widget_HelperAbstract fluent interface, returns self
     */
    public function setAcl(Zend_Acl $acl = null)
    {
        $this->_acl = $acl;
        return $this;
    }

    /**
     * Returns ACL or null if it isn't set using {@link setAcl()} or {@link setDefaultAcl()}
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::getAcl()}.
     *
     * @return  Zend_Acl|null  ACL object or null
     */
    public function getAcl()
    {
        if ($this->_acl === null && self::$_defaultAcl !== null) {
            return self::$_defaultAcl;
        }

        return $this->_acl;
    }

    /**
     * Sets ACL role(s) to use when iterating pages
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::setRole()}.
     *
     * @param   mixed $role [optional] role to set. Expects a string, an instance of type {@link Zend_Acl_Role_Interface},
     * or null. Default is null, which will set no role. if $role is invalid
     * @return  Zend_View_Helper_Navigation_HelperAbstract fluent interface, returns self
     */
    public function setRole($role = null)
    {
        if (null === $role || is_string($role) ||
            $role instanceof Zend_Acl_Role_Interface) {
            $this->_role = $role;
        } else {
            require_once 'iPMS/View/Exception.php';
            $e = new iPMS_View_Exception(sprintf(
                '$role must be a string, null, or an instance of Zend_Acl_Role_Interface; %s given', gettype($role)
            ));
            $e->setView($this->view);
            throw $e;
        }

        return $this;
    }

    /**
     * Returns ACL role to use when iterating pages, or null if it isn't set using {@link setRole()} or
     * {@link setDefaultRole()}
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::getRole()}.
     *
     * @return  string|Zend_Acl_Role_Interface|null role or null
     */
    public function getRole()
    {
        if ($this->_role === null && self::$_defaultRole !== null) {
            return self::$_defaultRole;
        }

        return $this->_role;
    }

    /**
     * Sets whether ACL should be used
     *
     * Implements {@link Zend_View_Helper_Widget_Helper::setUseAcl()}.
     *
     * @param   bool $useAcl [optional] whether ACL should be used. Default is true.
     * @return  iPMS_View_Helper_Widget_HelperAbstract fluent interface, returns self
     */
    public function setUseAcl($useAcl = true)
    {
        $this->_useAcl = (bool)$useAcl;
        return $this;
    }

    /**
     * Returns whether ACL should be used
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::getUseAcl()}.
     *
     * @return  bool whether ACL should be used
     */
    public function getUseAcl()
    {
        return $this->_useAcl;
    }

    /**
     * Sets whether translator should be used
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::setUseTranslator()}.
     *
     * @param   bool $useTranslator [optional] whether translator should be used. Default is true.
     * @return  Zend_View_Helper_Widget_HelperAbstract fluent interface, returns self
     */
    public function setUseTranslator($useTranslator = true)
    {
        $this->_useTranslator = (bool)$useTranslator;
        return $this;
    }

    /**
     * Returns whether translator should be used
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::getUseTranslator()}.
     *
     * @return  bool whether translator should be used
     */
    public function getUseTranslator()
    {
        return $this->_useTranslator;
    }

    // Magic overloads:

    /**
     * Magic overload: Proxy calls to the navigation container
     *
     * @param   string $method method name in container
     * @param   array  $arguments [optional] arguments to pass
     * @return  mixed  returns what the container returns
     * @throws  iPMS_Widget_Exception if method does not exist in container
     */
    public function __call($method, array $arguments = array())
    {
        return call_user_func_array(
            array($this->getContainer(), $method), $arguments);
    }

    /**
     * Magic overload: Proxy to {@link render()}.
     *
     * This method will trigger an E_USER_ERROR if rendering the helper causes an exception to be thrown.
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::__toString()}.
     *
     * @return  string
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
     * Implements {@link iPMS_View_Helper_Widget_Helper::hasContainer()}.
     *
     * @return  bool whether the helper has a container or not
     */
    public function hasContainer()
    {
        return null !== $this->_container;
    }

    /**
     * Checks if the helper has an ACL instance
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::hasAcl()}.
     *
     * @return  bool whether the helper has a an ACL instance or not
     */
    public function hasAcl()
    {
        return null !== $this->_acl;
    }

    /**
     * Checks if the helper has an ACL role
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::hasRole()}.
     *
     * @return  bool whether the helper has a an ACL role or not
     */
    public function hasRole()
    {
        return null !== $this->_role;
    }

    /**
     * Checks if the helper has a translator
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::hasTranslator()}.
     *
     * @return  bool whether the helper has a translator or not
     */
    public function hasTranslator()
    {
        return null !== $this->_translator;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given widget
     *
     * @param   iPMS_Widget_Abstract $widget  widget to generate HTML for
     * @return  string HTML string for the given widget
     */
    public function htmlify(iPMS_Widget_Abstract $widget)
    {
        return 'Not yet implemented';
        /*
        // get label and title for translating
        $label = $widget->getLabel();
        $title = $widget->getTitle();

        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
        if (is_string($title) && !empty($title)) {
        $title = $t->translate($title);
        }
        }

        // get attribs for anchor element
        $attribs = array(
        'id'     => $widget->getId(),
        'title'  => $title,
        'class'  => $widget->getClass(),
        'href'   => $widget->getHref(),
        'target' => $widget->getTarget()
        );

        return '<a' . $this->_htmlAttribs($attribs) . '>' . $this->view->escape($label) . '</a>';
       */
    }

    // Iterator filter methods:

    /**
     * Determines whether a widget should be accepted when iterating
     *
     * Rules:
     * - If helper has no ACL, widget is accepted
     * - If helper has ACL, but no role, widget is not accepted
     * - If helper has ACL and role:
     *  - Widget is accepted if it has no resource or privilege
     *  - Widget is accepted if ACL allows widget's resource or privilege
     *
     * @param   iPMS_Widget_Abstract $widget widget to check
     * @return  bool whether widget should be accepted
     */
    //public function accept(iPMS_Widget_Widget $widget, $recursive = true)
    public function accept(iPMS_Widget_Abstract $widget)
    {
        // accept by default
        $accept = true;

        if ($this->getUseAcl() && !$this->_acceptAcl($widget)) {
            // acl is not amused
            $accept = false;
        }

        return $accept;
    }

    /**
     * Determines whether a widget should be accepted by ACL when iterating
     *
     * Rules:
     * - If helper has no ACL, widget is accepted
     * - If widget has a resource or privilege defined, widget is accepted
     *   if the ACL allows access to it using the helper's role
     * - If widget has no resource or privilege, widget is accepted
     *
     * @param   iPMS_Widget_Abstract $widget widget to check
     * @return  bool whether widget is accepted by ACL
     */
    protected function _acceptAcl(iPMS_Widget_Abstract $widget)
    {
        if (!$acl = $this->getAcl()) {
            // no acl registered means don't use acl
            return true;
        }

        $role = $this->getRole();
        $resource = $widget->getResource();
        $privilege = $widget->getPrivilege();

        if ($resource || $privilege) {
            // determine using helper role and page resource/privilege
            return $acl->isAllowed($role, $resource, $privilege);
        }

        return true;
    }

    /**
     * Retrieve whitespace representation of $indent
     *
     * @param   int|string $indent
     * @return  string
     */
    protected function _getWhitespace($indent)
    {
        if (is_int($indent)) {
            $indent = str_repeat(' ', $indent);
        }

        return (string)$indent;
    }

    /**
     * Converts an associative array to a string of tag attributes.
     *
     * Overloads {@link Zend_View_Helper_HtmlElement::_htmlAttribs()}.
     *
     * @param   array $attribs an array where each key-value pair is converted to an attribute name and value
     * @return  string an attribute string
     */
    protected function _htmlAttribs($attribs)
    {
        // filter out null values and empty string values
        foreach ($attribs as $key => $value) {
            if ($value === null || (is_string($value) && !strlen($value))) {
                unset($attribs[$key]);
            }
        }

        return parent::_htmlAttribs($attribs);
    }

    /**
     * Normalize an ID
     *
     * Overrides {@link Zend_View_Helper_HtmlElement::_normalizeId()}.
     *
     * @param   string $value
     * @return  string
     */
    protected function _normalizeId($value)
    {
        $prefix = get_class($this);
        $prefix = strtolower(trim(substr($prefix, strrpos($prefix, '_')), '_'));

        return $prefix . '-' . $value;
    }

    /**
     * Sets default ACL to use if another ACL is not explicitly set
     *
     * @param   Zend_Acl $acl [optional] ACL object. Default is null, which sets no ACL object.
     * @return  void
     */
    public static function setDefaultAcl(Zend_Acl $acl = null)
    {
        self::$_defaultAcl = $acl;
    }

    /**
     * Sets default ACL role(s) to use when iterating pages if not explicitly
     * set later with {@link setRole()}
     *
     * @param   mixed $role [optional] role to set. Expects null, string, or an instance of
     * {@link Zend_Acl_Role_Interface}. Default is null, which sets no default role.
     * @throws  iPMS_View_Exception if role is invalid
     * @return  void
     */
    public static function setDefaultRole($role = null)
    {
        if (null === $role || is_string($role) || $role instanceof Zend_Acl_Role_Interface) {
            self::$_defaultRole = $role;
        } else {
            require_once 'iPMS/View/Exception.php';
            throw new iPMS_View_Exception('$role must be null|string|Zend_Acl_Role_Interface');
        }
    }

}
