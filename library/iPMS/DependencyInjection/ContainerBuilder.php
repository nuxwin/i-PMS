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
 * @category    DependencyInjection
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

namespace iPMS\DependencyInjection;

use \Symfony\Component\DependencyInjection\ContainerBuilder as BaseContainerBuilder;

/**
 * Class that adds magic methods to the Symfony DI ContainerBuilder to be compatible with the Zend bootstrap classes
 *
 * This class adds some magic methods for the Symfony DI ContainerBuilder to allow to use it in place of the default
 * resource container (Zend_Registry) in bootstrap classes.
 *
 * Note: It's not recommended to use theses methods since they are only implemented for compatibility. Instead, use the
 * concrete API provided by the Symfony DI ContainerBuilder class.
 *
 * @package     iPMS
 * @category    DependencyInjection
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
class ContainerBuilder extends BaseContainerBuilder
{
	/**
	 * Gets the service associated with the given identifier
	 *
	 * @param  string $id The service identifier
	 * @return object The associated service
	 */
	public function __get($id)
	{
		return $this->get($id);
	}

	/**
	 * Sets a service or parameter
	 *
	 * @param string $id The service identifier
	 * @param object $service The service instance
	 * @return void
	 */
	public function __set($id, $service)
	{
		// @TODO To be checked - It's really a good way to do like this ?
		if(is_object($service)) {
			$this->set($id, $service);
		} else  {
			$this->setParameter($id, $service);
		}
	}

	/**
	 * Returns true if the given service is defined
	 *
	 * @param  string $id The service identifier
	 * @return Boolean true if the service is defined, false otherwise
	 */
	public function __isset($id)
	{
		return $this->has($id);
	}
}
