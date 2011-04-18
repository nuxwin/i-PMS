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
 * Interface for widgets helpers
 *
 * @category    iPMS
 * @package     iPMS_View
 * @subpackage  Helper
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
interface iPMS_View_Widgets_Helper
{

    /**
     * Sets widgets container the helper should operate on by default
     *
     * @param iPMS_Widgets_Container $container [optional] container to operate on. Default is null, which indicates
     * that the container should be reset.
     * @return iPMS_View_Helper_Widgets_Helper fluent interface, returns self
     */
    public function setContainer(iPMS_Widgets_Container $container = null);

    /**
     * Returns the widgets container the helper operates on by default
     *
     * @return iPMS_Widgets_Container widgets container
     */
    public function getContainer();

    /**
     * Checks if the helper has a container
     *
     * @return bool whether the helper has a container or not
     */
    public function hasContainer();

    /**
     * Magic overload: Should proxy to {@link render()}.
     *
     * @return string
     */
    public function __toString();

    /**
     * Renders helper
     *
     * @param iPMS_Widgets_Container $container [optional] container to render. Default is null, which indicates that
     * the helper should render the container returned by {@link getContainer()}.
     * @return string helper output
     * @throws iPMS_View_Exception if unable to render
     */
    public function render(iPMS_Widgets_Container $container = null);
}
