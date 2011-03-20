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
 * @author      Laurent Declercq <laurent.declercq@i-mscp.net>
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Widgets container
 *
 * @package     iPMS
 * @subpackage  iPMS_Widgets
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class iPMS_Widgets_Container implements Iterator, Countable
{

    /**
     * Contains Widgets
     *
     * @var array
     */
    protected $_widgets = array();

    /**
     * An index that contains the order in which to iterate Widgets
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
     * Creates a new Widgets container
     *
     * @param array $widgets [optional] Widgets to add
     * @throws iPMS_Widgets_Exception if $Widgets is invalid
     */
    public function __construct($widgets = null)
    {
        if (is_array($widgets)) {
            $this->addWidgets($widgets);
        } elseif (null !== $widgets) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception('Invalid argument: $Widgets must be an array or null');
        }
    }

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

            /**
             * @var $widget iPMS_Widget
             */
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
     * This method will inject the container as the given page's parent by
     * calling {@link iPMS_Widgets::setParent()}.
     *
     * @param  iPMS_Widget|array $widget widget to add
     * @return iPMS_Widgets_Container fluent interface, returns self
     * @throws iPMS_Widgets_Exception if widget is invalid
     */
    public function addWidget($widget)
    {
        if (is_array($widget)) {
            require_once 'iPMS/Widget.php';
            $widget = iPMS_Widget::factory($widget);
        } elseif (!$widget instanceof iPMS_Widget) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception('Invalid argument: $widget must be an instance of iPMS_Widget or an array');
        }

        // Retrieve the widget hash
        $hash = $widget->hashCode(); // Todo to be added to the iPMS_Widget abstract class

        if (array_key_exists($hash, $this->_index)) {
            // page is already in container
            return $this;
        }

        // adds page to container and sets dirty flag
        $this->_widgets[$hash] = $widget;
        $this->_index[$hash] = $widget->getOrder(); // Todo method to be added in the iPMS_Widget abstract class
        $this->_dirtyIndex = true;

        // inject self as widget parent
        //$widget->setParent($this);

        return $this;
    }


    /**
     * Adds several Widgets at once
     *
     * @param  array $widgets Widgets to add
     * @return iPMS_Widgets_Container fluent interface, returns self
     * @throws iPMS_Widgets_Exception if $Widgets is not array
     */
    public function addWidgets($widgets)
    {
        if (!is_array($widgets)) {
            require_once 'iPMS/Widgets/Exception.php';
            throw new Zend_Navigation_Exception('Invalid argument: $widget must be an array');
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
     * @return  iPMS_Widget_Container fluent interface, returns self
     */
    public function setWidgets(array $widgets)
    {
        $this->removeWidgets();
        return $this->addWidgets($widgets);
    }

    /**
     * Returns widgets in the container
     *
     * @return  array array of iPMS_Widget instances
     */
    public function getWidgets()
    {
        return $this->_widgets;
    }

    /**
     * Removes the given widget from the container
     *
     * @param   iPMS_Widget|int $widget widget to remove, either a widget instance or a specific widget order
     * @return  bool whether the removal was successful
     */
    public function removeWidget($widget)
    {
        if ($widget instanceof iPMS_Widget) {
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
     * @return  iPMS_Widgets_Container fluent interface, returns self
     */
    public function removeWidgets()
    {
        $this->_widgets = array();
        $this->_index = array();
        return $this;
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
     * Returns current widget
     *
     * Implements Iterator interface.
     *
     * @return  iPMS_Widget current widget or null
     * @throws  iPMS_Widgets_Exception if the index is invalid
     */
    public function current()
    {
        $this->_sort();
        current($this->_index);
        $hash = key($this->_index);

        if (isset($this->_widgets[$hash])) {
            return $this->_widgets[$hash];
        } else {
            require_once 'iPMS/Widgets/Exception.php';
            throw new iPMS_Widgets_Exception(
                'Corruption detected in container; invalid key found in internal iterator'
            );
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
     * Implements Iterator interface.
     *
     * @return  bool whether container has any widgets
     */
    public function hasChildren()
    {
        return $this->hasWidgets();
    }

    /**
     * Returns the child container.
     *
     * Implements Iterator interface.
     *
     * @return  iPMS_Widget null
     */
    public function getChildren()
    {
        $hash = key($this->_index);

        if (isset($this->_widgets[$hash])) {
            return $this->_widgets[$hash];
        }

        return null;
    }

    // Countable interface:

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
}
