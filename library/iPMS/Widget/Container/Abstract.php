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
 * Widgets container class
 *
 * @category    iPMS
 * @package     iPMS_Widget
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
abstract class iPMS_Widget_Container_Abstract extends Zend_Controller_Action_Helper_Abstract implements Iterator, Countable
{

    /**
     * Contains widgets
     *
     * @var iPMS_Widget
     */
    protected $_widgets = array();
    /**
     * An index that contains the order in which to iterate widgets
     *
     * @var array
     */
    protected $_index = array();
    /**
     * Whether index is dirty and needs to be re-arranged
     *
     * @var bool
     */
    protected $_dirtyIndex = false;

    /**
     * Sorts the widget index according to widget order
     *
     * @return  void
     */
    protected function _sort()
    {
	if ($this->_dirtyIndex) {
	    $newIndex = array();
	    $index = 0;

	    foreach ($this->_widgets as $hash => $widget) {
		$order = $widget->getOrder();
		if ($order === null) {
		    $newIndex[$hash] = $index;
		    $index++;
		} else {
		    $newIndex[$hash] = $order;
		}
	    }

	    asort($newIndex);
	    $this->_index = $newIndex;
	    $this->_dirtyIndex = false;
	}
    }

    /**
     * Notifies container that the order of widget are updated
     *
     * @return  void
     */
    public function notifyOrderUpdated()
    {
	$this->_dirtyIndex = true;
    }

    /**
     * Adds a widget to the container
     *
     * @param   iPMS_Widget_Abstract $widget widget to add
     * @return  iPMS_Widget_Container_Abstract fluent interface, returns self
     * @throws  iPMS_Widget_Exception if widget is invalid
     */
    public function addWidget(iPMS_Widget_Abstract $widget)
    {
	/*
	  if ($widget === $this) {
	  require_once 'iPMS/Widget/Exception.php';
	  throw new iPMS_Widget_Exception('A widget cannot have itself as a parent');
	  }
	 */

	if (!$widget instanceof iPMS_Widget_Abstract) {
	    require_once 'iPMS/Widget/Exception.php';
	    throw new iPMS_Widget_Exception('Invalid argument: $widget must be an instance of iPMS_Widget_Abstract');
	}

	$hash = $widget->hashCode();

	if (array_key_exists($hash, $this->_index)) {
	    // widget is already in container
	    return $this;
	}

	// adds widget to container and sets dirty flag
	$this->_widgets[$hash] = $widget;
	$this->_index[$hash] = $widget->getOrder();
	$this->_dirtyIndex = true;

	// inject self as widget parent
	$widget->setParent($this);

	return $this;
    }

    /**
     * Adds several widgets at once
     *
     * @param   array $widget widgets to add
     * @return  iPMS_Widget_Container_Abstract fluent interface, returns self
     * @throws  iPMS_Widget_Exception if $widgets is not array or Zend_Config
     */
    public function addWidgets($widgets)
    {
	//if ($widgets instanceof Zend_Config) {
	//    $widgets = $widgets->toArray();
	//}

	if (!is_array($widgets)) {
	    require_once 'iPMS/Widget/Exception.php';
	    throw new iPMS_Widget_Exception('Invalid argument: $widgets must be an array');
	}

	foreach ($widgets as $widget) {
	    $this->addWidget($widget);
	}

	return $this;
    }

    /**
     * Sets widgets this container should have, removing existing widgets
     *
     * @param   array $widgets widgets to set
     * @return  iPMS_Widget_Container_Abstract fluent interface, returns self
     */
    public function setWidgets(array $widgets)
    {
	$this->removeWidgets();
	return $this->addWidgets($widgets);
    }

    /**
     * Returns widgets in the container
     *
     * @return  array array of iPMS_Widget_Widget instances
     */
    public function getWidgets()
    {
	return $this->_widgets;
    }

    /**
     * Removes the given widget from the container
     *
     * @param   iPMS_Widget_Abstract|int $widget widget to remove, either a widget instance or a specific widget order
     * @return  bool whether the removal was successful
     */
    public function removeWidget($widget)
    {
	if ($widget instanceof iPMS_Widget_Abstract) {
	    $hash = $widget->hashCode();
	} elseif (is_int($widget)) {
	    $this->_sort();
	    if (!$hash = array_search($widget, $this->_index)) {
		return false;
	    }
	} else {
	    return false;
	}

	if (isset($this->_widgets[$hash])) {
	    unset($this->_widgets[$hash]);
	    unset($this->_index[$hash]);
	    $this->_dirtyIndex = true;
	    return true;
	}

	return false;
    }

    /**
     * Removes all widget in container
     *
     * @return  iPMS_Widget_Container_Abstract fluent interface, returns self
     */
    public function removeWidgets()
    {
	$this->_widgets = array();
	$this->_index = array();
	return $this;
    }

    /**
     * Checks if the container has the given widget
     *
     * @param   iPMS_Widget_Abstract $widget widget to look for
     * @return  bool whether widget is in container
     */
    public function hasWidget(iPMS_Widget_Abstract $widget)
    {
	if (array_key_exists($widget->hashCode(), $this->_index)) {
	    return true;
	}

	return false;
    }

    /**
     * Returns true if container contains any widgets
     *
     * @return  bool whether container has any widgets
     */
    public function hasWidgets()
    {
	return count($this->_index) > 0;
    }

    /**
     * Returns a child widget matching $property == $value, or null if not found
     *
     * @param   string $property name of property to match against
     * @param   mixed  $value value to match property against
     * @return  iPMS_Widget_Abstract|null matching widget or null
     */
    public function findOneBy($property, $value)
    {
	$iterator = new IteratorIterator($this);

	foreach ($iterator as $widget) {
	    if ($widget->get($property) == $value) {
		return $widget;
	    }
	}

	return null;
    }

    /**
     * Returns all child widgets matching $property == $value, or an empty array
     * if no widgets are found
     *
     * @param   string $property name of property to match against
     * @param   mixed $value value to match property against
     * @return  array array containing only iPMS_Widget_Abstract instances
     */
    public function findAllBy($property, $value)
    {
	$found = array();

	$iterator = new IteratorIterator($this);
	foreach ($iterator as $widget) {
	    if ($widget->get($property) == $value) {
		$found[] = $widget;
	    }
	}

	return $found;
    }

    /**
     * Returns widget(s) matching $property == $value
     *
     * @param   string $property name of property to match against
     * @param   mixed $value value to match property against
     * @param   bool $all [optional] whether an array of all matching widgets should be returned, or only the first. If
     * true, an array will be returned, even if not matching widgets are found. If false, null will be returned if no
     * matching widget is found. Default is false.
     * @return  iPMS_Widget_Abstract|null matching widget or null
     */
    public function findBy($property, $value, $all = false)
    {
	if ($all) {
	    return $this->findAllBy($property, $value);
	} else {
	    return $this->findOneBy($property, $value);
	}
    }

    /**
     * Magic overload: Proxy calls to finder methods
     *
     * Examples of finder calls:
     * <code>
     * // METHOD                        // SAME AS
     * $widget->findByLabel('foo');     // $widget->findOneBy('label', 'foo');
     * $widget->findOneByLabel('foo');  // $widget->findOneBy('label', 'foo');
     * $widget->findAllByClass('foo');  // $widget->findAllBy('class', 'foo');
     * </code>
     *
     * @param   string $method method name
     * @param   array  $arguments method arguments
     * @throws  iPMS_Widget_Exception if method does not exist
     */
    public function __call($method, $arguments)
    {
	if (@preg_match('/(find(?:One|All)?By)(.+)/', $method, $match)) {
	    return $this->{$match[1]}($match[2], $arguments[0]);
	}

	require_once 'iPMS/Widget/Exception.php';
	throw new iPMS_Widget_Exception(sprintf('Bad method call: Unknown method %s::%s', get_class($this), $method));
    }

    /**
     * Returns an array representation of all widgets in container
     *
     * @return  array
     */
    public function toArray()
    {
	$widgets = array();

	$this->_dirtyIndex = true;
	$this->_sort();
	$indexes = array_keys($this->_index);

	foreach ($indexes as $hash) {

	    $widgets[] = $this->_widgets[$hash]->toArray();
	}
	return $widgets;
    }

    /**
     * Returns current widget
     *
     * Implements Iterator interface.
     *
     * @return  iPMS_Widget_Abstract Widget current widget or null
     * @throws  iPMS_Widget_Exception if the index is invalid
     */
    public function current()
    {
	$this->_sort();
	current($this->_index);
	$hash = key($this->_index);

	if (isset($this->_widgets[$hash])) {
	    return $this->_widgets[$hash];
	} else {
	    require_once 'iPMS/Widget/Exception.php';
	    throw new iPMS_Widget_Exception('Corruption detected in container; invalid key found in internal iterator');
	}
    }

    /**
     * Returns hash code of current widget
     *
     * Implements Iterator interface.
     *
     * @return  string hash code of current widget
     */
    public function key()
    {
	$this->_sort();
	return key($this->_index);
    }

    /**
     * Moves index pointer to next widget in the container
     *
     * Implements Iterator interface.
     *
     * @return  void
     */
    public function next()
    {
	$this->_sort();
	next($this->_index);
    }

    /**
     * Sets index pointer to first widget in the container
     *
     * Implements Iterator interface.
     *
     * @return  void
     */
    public function rewind()
    {
	$this->_sort();
	reset($this->_index);
    }

    /**
     * Checks if container index is valid
     *
     * Implements Iterator interface.
     *
     * @return  bool
     */
    public function valid()
    {
	$this->_sort();
	return current($this->_index) !== false;
    }

    /**
     * Proxy to hasWidgets()
     *
     * Implements RecursiveIterator interface.
     *
     * @return  bool whether container has any widgets
     */
    /*
      public function hasChildren()
      {
      return $this->hasWidgets();
      }
     */

    /**
     * Returns the child container.
     *
     * Implements RecursiveIterator interface.
     *
     * @return  iPMS_Widget_Abstract|null
     */
    /*
      public function getChildren()
      {
      $hash = key($this->_index);

      if (isset($this->_widgets[$hash])) {
      return $this->_widgets[$hash];
      }

      return null;
      }
     */

    /**
     * Returns number of widgets in container
     *
     * Implements Countable interface.
     *
     * @return  int number of widgets in the container
     */
    public function count()
    {
	return count($this->_index);
    }

    /**
     * Run initialization routine for each registered widget
     *
     * @return void
     */
    public function init()
    {
	if ($this->getRequest()->getControllerName() == strtolower('widgets')) {
	    $iterator = new IteratorIterator($this);

	    foreach ($iterator as $widget) {
		$widget->init();
	    }
	}
    }

    /**
     * Hook into action controller preDispatch() workflow
     *
     * @return void
     */
    public function preDispatch()
    {
	$iterator = new IteratorIterator($this);
	$view = $this->getView();

	/**
	 * @var $widget iPMS_Widget
	 */
	foreach ($iterator as $widget) {
	    if ($this->getRequest()->getControllerName() == strtolower('widgets')) {
		$widget->setContent($widget->dashBoardSettingsForm($widget));
	    } else {
		$content = $widget->widget();
		if (!empty($content)) {
		    $widget->setContent($content);
		    if ($widget->hasPartial()) { // process this ine the view ?
			$view->addScriptPath(APPLICATION_PATH . '/widgets/' . $widget . '/partial');
		    }
		} else {
		    $this->removeWidget($widget);
		}

		$content = '';
	    }
	}

	// Make the container available for the view by registering itself as container if needed
	if (count($this)) {
	    $view->Widget()->setContainer($this);
	}
    }

    /**
     * Hook into action controller postDispatch() workflow
     *
     * @return void
     */
    public function postDispatch()
    {
	// Avoid multiple call of widget logic (eg. when view action helper is used)
	Zend_Controller_Action_HelperBroker::removeHelper($this->getName());
    }

    /**
     * Get view
     *
     * @return Zend_View_Abstract
     */
    public function getView()
    {
	$actionController = $this->getActionController();
	$view = $actionController->view;

	if (!$view instanceof Zend_View_Abstract) {
	    require_once 'iPMS/Widget/Exception.php';
	    throw new iPMS_Widget_Exception('Unable to get view instance for widget rendering');
	}

	return $view;
    }

}
