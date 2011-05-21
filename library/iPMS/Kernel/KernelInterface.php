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
 * @subpackage  Core
 * @category    Kernel
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 * @licence     
 */

namespace iPMS\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * The Kernel is the heart of the iPMS application
 *
 * This kernel was builds over the Zend_Application component to allow to mix
 * components that come from the Zend frameworks and from the Synfony Framework.
 * It add some features such as Dependency Injection, class compilation and so on...
 *
 * @package     iPMS
 * @subpackage  Core
 * @category    Kernel
 * @author      Fabien Potencier <fabien@symfony.com>
 * @modifiedBy  Laurent Declercq <l.declercq@nuxwin.com> for iPMS
 * @version     0.0.1
 */
interface KernelInterface extends \Serializable {

    /**
     * Returns an array of modules to registers.
     *
     * @return array An array of module instances.
     */
    function registerModules();

    /**
     * Loads the container configuration
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     */
    function registerContainerConfiguration(LoaderInterface $loader);

    /**
     * Shutdowns the kernel
     *
     * This method is mainly useful when doing functional testing.
     */
    function shutdown();

    /**
     * Gets the registered module instances.
     *
     * @return array An array of registered module instances
     */
    function getModules();

    /**
     * Checks if a given class name belongs to an active module.
     *
     * @param string $class A class name
     *
     * @return Boolean true if the class belongs to an active module, false otherwise
     */
    function isClassInActiveModule($class);

    /**
     * Returns a module and optionally its descendants by its name.
     *
     * @param string $name Module name
     * @param Boolean $first Whether to return the first bundle only or together with its descendants
     * @return ModuleInterface|Array A ModuleInterface instance or an array of ModuleInterface instances if $first is false
     * @throws \InvalidArgumentException when the bundle is not enabled
     */
    function getModule($name, $first = true);

    /**
     * Returns the file path for a given resource.
     *
     * A Resource can be a file or a directory.
     *
     * The resource name must follow the following pattern:
     *
     *     @BundleName/path/to/a/file.something
     *
     * where BundleName is the name of the bundle
     * and the remaining part is the relative path in the bundle.
     *
     * If $dir is passed, and the first segment of the path is Resources,
     * this method will look for a file named:
     *
     *     $dir/BundleName/path/without/Resources
     *
     * @param string  $name  A resource name to locate
     * @param string  $dir   A directory where to look for the resource first
     * @param Boolean $first Whether to return the first path or paths for all matching bundles
     *
     * @return string|array The absolute path of the resource or an array if $first is false
     *
     * @throws \InvalidArgumentException if the file cannot be found or the name is not valid
     * @throws \RuntimeException         if the name contains invalid/unsafe characters
     */
    function locateResource($name, $dir = null, $first = true);

    /**
     * Gets the name of the kernel
     *
     * @return string The kernel name
     */
    function getName();

    /**
     * Gets the environment
     *
     * @return string The current environment
     */
    function getEnvironment();

    /**
     * Checks if debug mode is enabled.
     *
     * @return Boolean true if debug mode is enabled, false otherwise
     */
    function isDebug();

    /**
     * Gets the application root dir.
     *
     * @return string The application root dir
     */
    function getRootDir();

    /**
     * Gets the current container.
     *
     * @return ContainerInterface A ContainerInterface instance
     */
    function getContainer();

    /**
     * Gets the cache directory.
     *
     * @return string The cache directory
     */
    function getCacheDir();

    /**
     * Gets the log directory.
     *
     * @return string The log directory
     */
    function getLogDir();
}
