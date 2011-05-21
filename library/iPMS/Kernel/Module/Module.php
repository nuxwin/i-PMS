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
 * @category    Module
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

namespace iPMS\Kernel\Module;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container;

/**
 * An implementation of BundleInterface that adds a few conventions
 * for DependencyInjection extensions and Console commands.
 *
 * @package     iPMS
 * @subpackage  Kernel
 * @category    Module
 * @author      Fabien Potencier <fabien@symfony.com>
 * @modifiedBy  Laurent Declercq <l.declercq@nuxwin.com> for iPMS
 * @version     0.0.1
 */
abstract class Module extends ContainerAware implements ModuleInterface
{
	/**
	 *
	 */
	protected $name;

	/**
	 *
	 */
	protected $reflected;

	/**
	 *
	 */
	protected $extension;

	/**
	 * Boots the Module
	 */
	public function boot()
	{
	}

	/**
	 * Shutdowns the Module
	 */
	public function shutdown()
	{
	}

	/**
	 * Builds the module
	 *
	 * It is only ever called once when the cache is empty.
	 *
	 * This method can be overridden to register compilation passes,
	 * other extensions, ...
	 *
	 * @param ContainerBuilder $container A ContainerBuilder instance
	 */
	public function build(ContainerBuilder $container)
	{
	}

	/**
	 * Returns the module's container extension
	 *
	 * @return ExtensionInterface|null The container extension
	 */
	public function getContainerExtension()
	{
		if (null === $this->extension) {
			$basename = preg_replace('/Module$/', '', $this->getName());

			$class = $this->getNamespace() . '\\DependencyInjection\\' . $basename . 'Extension';

			if (class_exists($class)) {
				$extension = new $class();

				// check naming convention
				$expectedAlias = Container::underscore($basename);
				if ($expectedAlias != $extension->getAlias()) {
					throw new \LogicException(sprintf(
						'The extension alias for the default extension of a module must be the underscored version of ' .
						'the module name ("%s" vs "%s")',
						$expectedAlias, $extension->getAlias()
					));
				}

				$this->extension = $extension;
			} else {
				$this->extension = false;
			}
		}

		if ($this->extension) {
			return $this->extension;
		}
	}

	/**
	 * Gets the Module namespace.
	 *
	 * @return string The Module namespace
	 */
	public function getNamespace()
	{
		if (null === $this->reflected) {
			$this->reflected = new \ReflectionObject($this);
		}

		return $this->reflected->getNamespaceName();
	}

	/**
	 * Gets the Module directory path.
	 *
	 * @return string The Module absolute path
	 */
	public function getPath()
	{
		if (null === $this->reflected) {
			$this->reflected = new \ReflectionObject($this);
		}

		return dirname($this->reflected->getFileName());
	}

	/**
	 * Returns the module parent name
	 *
	 * @return string The Module parent name it overrides or null if no parent
	 */
	public function getParent()
	{
		return null;
	}

	/**
	 * Returns the module name (the class short name)
	 *
	 * @return string The Module name
	 */
	final public function getName()
	{
		if (null !== $this->name) {
			return $this->name;
		}

		$name = get_class($this);
		$pos = strrpos($name, '\\');

		return $this->name = false === $pos ? $name : substr($name, $pos + 1);
	}
}
