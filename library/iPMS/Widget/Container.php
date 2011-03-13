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
 * @see Zend_Navigation_Container
 */
require_once 'iPMS/Widget/Container/Abstract.php';

/**
 * A simple container class for {@link iPMS_Widget_Widget} widgets
 *
 * @category    iPMS
 * @package     iPMS_Widget
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class iPMS_Widget_Container extends iPMS_Widget_Container_Abstract
{
	/**
	 * Creates new widget container
	 *
	 * @param array $widget [optional] widgets to add
	 * @throws iPMS_Widget_Exception if $widgets is invalid
	 */
	public function __construct($widgets = null)
	{
		if (is_array($widgets) || $widgets instanceof Zend_Config) {
			$this->addWidgets($widgets);
		} elseif (null !== $widgets) {
			require_once 'iPMS/Widget/Exception.php';
			throw new iPMS_Widget_Exception("Invalid argument: $widgets must be an array or null");
		}
	}
}
