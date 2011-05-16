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
 * @category    Controllers
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for IPMS controllers with DependencyInjection
 *
 * @package     iPMS
 * @category    Controllers
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
abstract class iPMS_Controller_Action extends Zend_Controller_Action implements iPMS_Controller_DependencyInjection
{
	/**
	 * @var Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $_container;

	/**
	 * Sets the Container associated with this Controller - Implements the iPMS_Controller_DependencyInjection interface
	 *
	 * @param ContainerInterface $container A ContainerInterface instance
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		$this->_container = $container;
	}

	/**
	 * Returns true if the service id is defined - Implements the iPMS_Controller_DependencyInjection interface
	 *
	 * @param  string  $id The service id
	 * @return Boolean true if the service id is defined, false otherwise
	 */
	public function has($id)
	{
		return $this->_container->has($id);
	}

	/**
	 * Gets a service by id - Implements the iPMS_Controller_DependencyInjection interface
	 *
	 * @param  string $id The service id
	 * @return object The service
	 */
	public function get($id)
	{
		return $this->_container->get($id);
	}
}
