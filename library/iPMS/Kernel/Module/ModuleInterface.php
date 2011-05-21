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

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * ModuleInterface.
 *
 * @package     iPMS
 * @subpackage  Kernel
 * @category    Module
 * @author      Fabien Potencier <fabien@symfony.com>
 * @modifiedBy  Laurent Declercq <l.declercq@nuxwin.com> for iPMS
 * @version     0.0.1
 */
interface ModuleInterface {
    /**
     * Boots the Module
     */
    function boot();

    /**
     * Shutdowns the Module
     */
    function shutdown();

    /**
     * Builds the module
     *
     * It is only ever called once when the cache is empty.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    function build(ContainerBuilder $container);

    /**
     * Returns the container extension that should be implicitly loaded.
     *
     * @return ExtensionInterface|null The default extension or null if there is none
     */
    function getContainerExtension();

    /**
     * Returns the module parent name.
     *
     * @return string The Module parent name it overrides or null if no parent
     */
    function getParent();

    /**
     * Returns the module name (the class short name).
     *
     * @return string The Module name
     */
    function getName();

    /**
     * Gets the Module namespace.
     *
     * @return string The Module namespace
     */
    function getNamespace();

    /**
     * Gets the Module directory path.
     *
     * The path should always be returned as a Unix path (with /).
     *
     * @return string The Module absolute path
     */
    function getPath();
}
