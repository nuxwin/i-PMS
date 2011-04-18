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
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Widgets interface
 *
 * @category    iPMS
 * @package     iPMS_Widget
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
interface iPMS_Widget_Interface
{
    /**
     * Make the widget content available for the view
     *
     * This method must return either an array of raw data or an array of @link Zend_Form_Element} or a
     * {@link Zend_Form} instance
     *
     * @abstract
     * @param Zend_Controller_Request_Http $request
     * @return array|Zend_Form
     */
    public function widget(Zend_Controller_Request_Http $request);

    /**
     * Make the widget settings form available for the dashboard
     *
     * This method must return either an array of {@link Zend_Form_Element} or a bool that indicate if a Form should be
     * auto-generated from the widget xml description file. If false it returned, that mean that no Form is provided for
     * the dashboard.
     *
     * @abstract
     * @param  $settings Current widget settings
     * @return array|bool either an array that contains Zend_Form_Element elements or a boolean that indicate that the
     * Form must be auto-generated from the widget xml description file
     */
    public function dashboard($settings);

    /**
     * Update Widgets settings
     *
     * @abstract
     * @param  array $settings array that contain settings to be updated
     * @param  array $oldSettings array that contains old settings
     * @return array array that contains settings to save in database
     */
    public function update($settings, $oldSettings);
}
