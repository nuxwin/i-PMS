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
class iPMS_Widgets_Container
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
        /*
        if ($widget === $this) {
            require_once 'Zend/Navigation/Exception.php';
            throw new Zend_Navigation_Exception(
                'A page cannot have itself as a parent');
        }
         */

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

        // inject self as page parent
        //$page->setParent($this);

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
}
