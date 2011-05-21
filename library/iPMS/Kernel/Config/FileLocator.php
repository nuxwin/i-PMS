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
 * @category    Config
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

namespace iPMS\Kernel\Config;

use Symfony\Component\Config\FileLocator as BaseFileLocator;
use iPMS\Kernel\KernelInterface;

/**
 * FileLocator uses the KernelInterface to locate resources in bundles
 *
 * @package     iPMS
 * @subpackage  Kernel
 * @category    Config
 * @author      Fabien Potencier <fabien@symfony.com>
 * @modifiedBy  Laurent Declercq <l.declercq@nuxwin.com> for iPMS
 * @version     0.0.1
 */
class FileLocator extends BaseFileLocator
{
	/**
	 * @var KernelInterface
	 */
	private $_kernel;

	/**
	 * @var array
	 */
	private $_path;

	/**
	 * Constructor.
	 *
	 * @param KernelInterface $kernel A KernelInterface instance
	 * @param string $path The path the global resource directory
	 * @param string|array $paths A path or an array of paths where to look for resources
	 */
	public function __construct(KernelInterface $kernel, $path = null, array $paths = array())
	{
		$this->_kernel = $kernel;
		$this->_path = $path;
		$paths[] = $path;

		parent::__construct($paths);
	}

    /**
     * Returns a full path for a given file name
     *
     * @param mixed  $name The file name to locate
     * @param string $currentPath The current path
     * @param Boolean $first Whether to return the first occurrence or an array of filenames
     * @return string|array The full path to the file|An array of file paths
     * @throws \InvalidArgumentException When file is not found
     */
	public function locate($file, $currentPath = null, $first = true)
	{
		if ('@' === $file[0]) {
			return $this->_kernel->locateResource($file, $this->_path, $first);
		}

		return parent::locate($file, $currentPath, $first);
	}
}
