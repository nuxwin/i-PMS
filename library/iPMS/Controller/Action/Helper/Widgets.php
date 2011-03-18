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
 * Action helper that load active Widgets
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0.0
 */
class iPMS_Controller_Action_Helper_Widgets extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Widget container
     *
     * @var iPMS_Widgets_Container|null
     */
    protected $_widgetContainer = null;

    /**
     * Retrieve all active Widgets
     *
     * @return iPMS_Widgets_Container widget container
     */
    public function direct()
    {
        $model = new Model_DbTable_Widgets();
        $widgets = $model->getWidgetsOptions();

        $container = new iPMS_Widgets_Container($widgets);

        return $container;
    }

    /**
     * Execute all Widgets
     *
     * @return void
     */
    public function __preDispatch()
    {
        //$iterator = new IteratorIterator($this->_widgetContainer); // Todo implement iterator
        $iterator = $this->_widgetContainer;

        /**
         * @var $widget iPMS_Widget
         */
        foreach ($iterator as $widget) {
            $widget->run();
        }
    }

    /**
     * Remove the reference to the widget container
     *
     * @return void
     */
    public function postDispatch()
    {
        $this->_widgetContainer = null;
    }
}
