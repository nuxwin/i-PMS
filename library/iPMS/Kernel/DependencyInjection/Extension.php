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
 * @subpackage  Kernel
 * @category    DependencyInjection
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

namespace iPMS\Kernel\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container;

/**
 * Provides useful features shared by many extensions.
 *
 * @package     iPMS
 * @subpackage  Kernel
 * @category    DependencyInjection
 * @author      Fabien Potencier <fabien@symfony.com>
 * @modifiedBy  Laurent Declercq <l.declercq@nuxwin.com> for iPMS
 * @version     0.0.1
 */
abstract class Extension implements ExtensionInterface
{
	private $_classes = array();

	/**
	 * Gets the classes to cache.
	 *
	 * @return array An array of classes
	 */
	public function getClassesToCompile()
	{
		return $this->_classes;
	}

	/**
	 * Adds classes to the class cache.
	 *
	 * @param array $classes An array of classes
	 */
	public function addClassesToCompile(array $classes)
	{
		$this->_classes = array_merge($this->_classes, $classes);
	}

	/**
	 * Returns the base path for the XSD files.
	 *
	 * @return string The XSD base path
	 */
	public function getXsdValidationBasePath()
	{
		return false;
	}

	/**
	 * Returns the namespace to be used for this extension (XML namespace).
	 *
	 * @return string The XML namespace
	 */
	public function getNamespace()
	{
		return 'http://example.org/schema/dic/' . $this->getAlias();
	}

	/**
	 * Returns the recommended alias to use in XML.
	 *
	 * This alias is also the mandatory prefix to use when using YAML.
	 *
	 * @return string The alias
	 */
	public function getAlias()
	{
		$className = get_class($this);
		if (substr($className, -9) != 'Extension') {
			throw new \BadMethodCallException(
				'This extension does not follow the naming convention; you must overwrite the getAlias() method.');
		}
		$classBaseName = substr(strrchr($className, '\\'), 1, -9);

		return Container::underscore($classBaseName);
	}
}
