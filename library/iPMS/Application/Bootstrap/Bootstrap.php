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

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Bootstrap class for Dependency Injection concerns
 *
 * This class extends the Zend_Application_Bootstrap_Bootstrap to make it compatible
 * with the Symfony dependency injection container. The DI container replaces the
 * default resources container (Zend_Registry) to support dependency injection.
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
	 * Returns service container
	 * 
	 * @return Symfony\Component\DependencyInjection\ContainerBuilder
	 */
	public function getContainer()
	{
		if (null === $this->_container) {
			$this->setContainer(new ContainerBuilder());
		}
		return $this->_container;
	}

    /**
     * Retrieve a resource from the container
     *
     * During bootstrap resource initialization, you may return a value. If
     * you do, it will be stored in the {@link setContainer() container}.
     * You can use this method to retrieve that value.
     *
     * If no value was returned, this will return a null value.
     *
     * @param  string $name
     * @return null|mixed
     */
	public function getResource($name)
	{
        $resource  = strtolower($name);
        $container = $this->getContainer();
        if ($this->hasResource($resource)) {
            //return $container->{$resource};
	        return $container->get($resource);
        }
        return null;
	}

    /**
     * Determine if a resource has been stored in the container
     *
     * During bootstrap resource initialization, you may return a value. If
     * you do, it will be stored in the {@link setContainer() container}.
     * You can use this method to determine if a value was stored.
     *
     * @param  string $name
     * @return bool
     */
	public function hasResource($name)
	{
        $resource  = strtolower($name);
        $container = $this->getContainer();
        //return isset($container->{$resource});
		return $container->has($resource);
	}

    /**
     * Execute a resource
     *
     * Checks to see if the resource has already been run. If not, it searches
     * first to see if a local method matches the resource, and executes that.
     * If not, it checks to see if a plugin resource matches, and executes that
     * if found.
     *
     * Finally, if not found, it throws an exception.
     *
     * @param  string $resource
     * @return void
     * @throws Zend_Application_Bootstrap_Exception When resource not found
     */
    protected function _executeResource($resource)
    {
        $resourceName = strtolower($resource);

        if (in_array($resourceName, $this->_run)) {
            return;
        }

        if (isset($this->_started[$resourceName]) && $this->_started[$resourceName]) {
            throw new Zend_Application_Bootstrap_Exception('Circular resource dependency detected');
        }

        $classResources = $this->getClassResources();
        if (array_key_exists($resourceName, $classResources)) {
            $this->_started[$resourceName] = true;
            $method = $classResources[$resourceName];
            $return = $this->$method();
            unset($this->_started[$resourceName]);
            $this->_markRun($resourceName);

            if (null !== $return) {
                //$this->getContainer()->{$resourceName} = $return;
	            $this->getContainer()->set($resourceName, $return);
            }

            return;
        }

        if ($this->hasPluginResource($resource)) {
            $this->_started[$resourceName] = true;
            $plugin = $this->getPluginResource($resource);
            $return = $plugin->init();
            unset($this->_started[$resourceName]);
            $this->_markRun($resourceName);

            if (null !== $return) {
                //$this->getContainer()->{$resourceName} = $return;
	            $this->getContainer()->set($resourceName, $return);
            }

            return;
        }

        throw new Zend_Application_Bootstrap_Exception('Resource matching "' . $resource . '" not found');
    }
}
