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
 * Widget interface
 *
 * @category    iPMS
 * @package     iPMS_Widget
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
interface iPMS_Widget_Interface
{
    /**
     * Sets widget properties from an associative array
     *
     * @abstract
     * @throws iPMS_Widget_Exception if invalid options are given
     * @param array $options array that contain widget options
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setOptions(array $options);

    /**
     * Sets widget parameters from an associative array
     *
     * @abstract
     * @throws iPMS_Widget_Exception if a parameter has no name
     * @param array $params array that contains widget parameters
     * @return iPMS_Widget fluent interface, returns self
     */
    public function setParams(array $params);

    /**
     * Returns a widget parameter
     *
     * @abstract
     * @param  $param widget parameter name
     * @return mixed parameter value or null
     */
    public function getParam($param);

    /**
     * Make the widget content available for the view
     *
     * @abstract
     * @return void
     */
    public function widget();

    /**
     * Make the widget settings available for the dashboard
     *
     * @abstract
     * @return void
     */
    public function dashboard();

    /**
     * Update the widgets options (either a property or parameter)
     *
     * @abstract
     * @return void
     */
    public function update();
}
