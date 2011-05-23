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
 */

require_once 'Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
                                 'iPMS' => __DIR__ . '/../library',
                                 'Symfony' => __DIR__ . '/../library',
                                 'Doctrine' => __DIR__ . '/../library',
								 'Core'     => __DIR__ . '/modules',
                                 'Blog' => __DIR__ . '/modules'
                            ));

$loader->registerPrefixes(array(
                               'Zend_' => '/var/www/imscp/library',
                               'iPMS_' => __DIR__ . '/../library',
                               'ZendX_' => '/var/www/imscp/library',
                               'ZFDebug_' => __DIR__ . '/../library'
                          ));

$loader->register();

use iPMS\Kernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Application Kernel
 *
 * Note: This class will be removed ASAP.
 * 
 * @package     iPMS
 * @subpackage  Core
 * @category    Kernel
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
final class ApplicationKernel extends Kernel
{
	/**
	 * Returns an array of modules to registers.
	 *
	 * @return array An array of module instances
	 */
	public function registerModules()
	{
		$modules = array(
			new Core\modules\Doctrine\DoctrineModule(),
			new Blog\BlogModule(),
		);

		return $modules;
	}

	/**
	 * Register container configuration.
	 *
	 * @param Symfony\Component\Config\Loader\LoaderInterface $loader
	 * @return void
	 */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
	    $loader->load(__DIR__ . '/configs/application.yml');
    }
}
