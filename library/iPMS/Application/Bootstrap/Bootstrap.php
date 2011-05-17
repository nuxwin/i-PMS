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
 * @package     iPMS
 * @subpackage  Application
 * @category    Bootstrap
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

use \iPMS\DependencyInjection\ContainerBuilder as serviceContainer;

/**
 * Bootstrap class for Dependency Injection concerns
 *
 * This class extends the Zend_Application_Bootstrap_Bootstrap to replace the default resources container by an that
 * implements the Symfony DI ContainerInterface interface.
 *
 * @package     iPMS
 * @subpackage  Application
 * @category    Bootstrap
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
class iPMS_Application_Bootstrap_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	/**
	 * Retrieve resource container
	 *
	 * @return object
	 */

	/**
	 * Returns service container
	 * 
	 * @return \iPMS\DependencyInjection\ContainerBuilder
	 */
	public function getContainer()
	{
		if (null === $this->_container) {
			$this->setContainer(new serviceContainer());
		}
		return $this->_container;
	}
}
