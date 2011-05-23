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
 * @category    Bootstrap
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

use \Doctrine\Common\Cache\ApcCache,
    \Doctrine\Common\Cache\ArrayCache;

/**
 * Main bootsrap class
 *
 * @package     iPMS
 * @category    Bootstrap
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
class Bootstrap extends iPMS_Application_Bootstrap_Bootstrap
{
	/**
	 * Execute some specific initialization tasks for CLI environment
	 *
	 * @return void
	 */
	protected function _initCli()
	{
		if (php_sapi_name() == 'cli') {
			new Zend_Application_Module_Autoloader(array(
			                                            'namespace' => 'Core',
			                                            'basePath' => APPLICATION_PATH . '/modules/Core'));
		}
	}

	/**
	 * Stores all config parameters in the dependency injection (resource) container as config service
	 *
	 * @return Zend_Config
	 */
	protected function _initConfig()
	{
		return new Zend_Config($this->getOptions());
	}

	/**
	 * Initialize the logger and register it in dependency injection container
	 *
	 * Initialize the DEBUG constant to TRUE or FALSE based on current environment.
	 * Also store a copy of Zend_Logger in the Registry for future references.
	 *
	 * Note: To make Firebug writer workable on your system, you must use Firefox >= 2, and
	 * have extensions Firebug and FirePHP installed and activated.
	 *
	 * @return void
	 */
	protected function _initLogger()
	{
		if ($this->hasPluginResource('Log')) {
			$this->bootstrap('Log');
			$logger = $this->getResource('Log');
		} else {
			$logger = new Zend_Log();
		}

		$logger->addWriter(new Zend_Log_Writer_Firebug());

		return $logger;
	}

    /**
     * Initializses ZFDebug if DEBUG mode is ON and register it in service container
     *
     * @return null|ZFDebug_Controller_Plugin_Debug
     */
    protected function _initZFDebug()
    {
        if ($this->hasOption('zfdebug')) {
            // TODO to be removed
            $autoloader = $this->_application->getAutoloader()->registerNamespace('ZFDebug_');
            $this->bootstrap('FrontController');
            // Retrieve the front controller from the resource (service) container
            $frontController = $this->getResource('FrontController');

            // Ensure database is initialized for auto discovery
            //if ($this->hasPluginResource('db')) {
            //	$this->bootstrap('db');
            //}

            // Create ZFDebug instance
            $zfdebug = new ZFDebug_Controller_Plugin_Debug($this->getOption('zfdebug'));

            // Register ZFDebug with the front controller
            $frontController->registerPlugin($zfdebug);

            return $zfdebug;
        }

        return null;
    }

	/**
	 * Initializes pdo syntetics service and injects it in the dependency injection container
     *
     * @return Pdo
     */
	protected function _initPdo()
	{
		// Here, we create and register a new service called 'pdo'. We create this service by using the
        // Zend_Db component but we only retrieves from it the PDO object. By doing this, we can still use the Zend_Db
        // configuration options style in our global configuration file. Also, it allow us to not use a specific event
        // manager to configure the client charset for communication with the Database server. The ''pdo' service will
        // be injected in the 'doctrine' service when needed.
        return $this->getPluginResource('db')->getDbAdapter()->getConnection();
	}

	/**
	 * Initialize the cache syntetic service and injets it in the dependency injection container
	 *
	 * @return Doctrine\Common\Cache\ApcCache|Doctrine\Common\Cache\ArrayCache
	 * @todo Allow to use other cache implementation
	 */
	protected function _initCache()
	{
		if (extension_loaded('apc') && ini_get('apc.enabled')) {
			$cache = new ApcCache();
		} else {
			$cache = new ArrayCache();
		}

		$cache->setNamespace('iPMS');

		return $cache;
	}

	/**
	 * Initialize view and register it in the dependency injection container
	 *
	 * @return Zend_View
	 * @todo Move this in a plugin resource
	 */
	protected function _initOverrideView()
	{
		$this->bootstrap('View');

		/**
		 * @var $view Zend_View
		 */
		$view = $this->getResource('View');

		// Site title (prefix)
		$view->headTitle($view->translate('internet Multi Server Control Panel (i-MSCP) - Project Web Site'))
			->setSeparator(' - ');

		// Meta
		$view->headMeta()->setName('author', 'Laurent Declercq - i-MSCP Team')
			->setName('generator', 'i-PMS 1.0.0')
			->setName('language', 'en');

		// Copyright
		$view->headLink(array('rel' => 'copyright', 'href' => 'http://www.gnu.org/licenses/gpl-2.0.html'));

		// Favicon
		$view->headLink(array('rel' => 'shortcut icon', 'href' => '/themes/default/favicon.ico'))
			->headLink(array('rel' => 'icon', 'href' => '/themes/default/favicon.ico', 'type' => 'image/x-icon'));

		// js (fix png transparency on IE < 7)
		$view->headScript()
			->appendFile('/themes/default/js/png.js', 'text/javascript', array('conditional' => 'lt IE 7'))
			->appendScript("\tDD_belatedPNG.fix('*');" . PHP_EOL, 'text/javascript', array('conditional' => 'lt IE 7'));

		/**
		 * @var $viewRenderer Zend_Controller_Action_Helper_ViewRenderer
		 */
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');

		// Sets view basePath specification
		$viewRenderer->setView($view)
			->setViewBasePathSpec(THEME_PATH . '/default/templates/modules/:module');

		return $view;
	}

	/**
	 * Initialize the router and register it in the dependency injection container
	 *
	 * @return Zend_Controller_Router_Interface
	 * @todo move it to plugin resource
	 */
	protected function _initRouter()
	{
		$this->bootstrap('frontController');
		$frontController = $this->getResource('FrontController');

		$router = $frontController->getRouter();
		$router->removeDefaultRoutes();

		return $router;
	}

    public function __initTest()
    {
        /** @var Symfony\DepdendencyInjection\Container $dependencyInjectionContainer */
        $dependencyInjectionContainer = $this->getContainer();

        $ids = $dependencyInjectionContainer->getServiceIds();

        echo '<pre>';
        print_r($ids);
        echo '</pre>';
        exit;
    }
}
