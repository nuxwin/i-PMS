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
 * @category    iPMS
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Main bootsrap class
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Stores a copy of the config object in the Registry for future references
	 *
	 * @return Zend_Config
	 */
	protected function _initConfig()
	{
		$config = new Zend_Config($this->getOptions());
		Zend_Registry::set('config', $config);

		return $config;
	}

	/**
	 * initializes the database connection and registers it in registry for further usage
	 *
	 * @return null|Zend_Db_Adapter_Abstract
	 */
	public function _initDatabase()
	{
		if ($this->hasPluginResource('db')) {
			$this->bootstrap('db');
			/**
			 * @var $db Zend_Db_Adapter_Abstract
			 */
			$db = $this->getResource('db');
			Zend_Registry::set('db', $db);

			return $db;
		}

		return null;
	}

	/**
	 * Initializes and returns Doctrine ORM entity manager
	 *
	 * @return \Doctrine\ORM\EntityManager
	 * @todo Resource configurator like http://framework.zend.com/wiki/x/0IAbAQ
	 */
	protected function _initDoctrine()
	{
		// doctrine loader
		require_once (ROOT_PATH . '/library/Doctrine/Common/ClassLoader.php');

		$doctrineAutoloader = new \Doctrine\Common\ClassLoader('Doctrine', ROOT_PATH . '/library');

		// Registers doctrine autoloader on the SPL autoload stack
		$doctrineAutoloader->register();

		# configure doctrine
		$doctrineConfig = new Doctrine\ORM\Configuration;

		// cache configuration - begin

		// Todo change this in production environment
		$cache = new Doctrine\Common\Cache\ArrayCache;
		$cache->setNamespace('iPMS');

		// Add Metadata cache to the doctrine configuration object
		$doctrineConfig->setMetadataCacheImpl($cache);

		// Add Query cache to the doctrine configuration object
		$doctrineConfig->setQueryCacheImpl($cache);

		// cache configuration - ending

		$this->bootstrap('FrontController');
		$frontController = $this->getResource('FrontController');

		// Fetch all modèls paths
		$modules = $frontController->getControllerDirectory();
		$modelsPaths = array();
		foreach (array_keys($modules) as $module) {
			$modelsPaths[] = APPLICATION_PATH . '/modules/' . $module . '/models';
		}

		// Add annotation driver  implementation to the doctrine configuration object
		$doctrineConfig->setMetadataDriverImpl($doctrineConfig->newDefaultAnnotationDriver($modelsPaths));

		// Sets the cache driver implementation used for the query cache (SQL cache)
		$doctrineConfig->setQueryCacheImpl($cache);

		// Sets the directory where Doctrine generates any necessary proxy class files
		$doctrineConfig->setProxyDir(ROOT_PATH . '/data/proxies');

		// Sets the namespace where proxy classes reside.
		// $doctrineConfig->setProxyNamespace('Proxies');
		$doctrineConfig->setProxyNamespace('Proxy');

		// TODO allow to retrieve entities by using alias name in DQL and some other thing (eg. Blog_Model_Post become Post)
		$doctrineConfig->setEntityNamespaces(array('Blog_Model_Post' => 'Post'));

		// Sets a boolean flag that indicates whether proxy classes should always be regenerated
		// during each script execution.
		// Todo ensure that is defaulted TRUE
		//$doctrineConfig->setAutoGenerateProxyClasses(true);

		$mainConfig = $this->getResource('config');

		$entitiesManager = Doctrine\ORM\EntityManager::create(
			$mainConfig->doctrine->connection->toArray(), $doctrineConfig);

		// Registers doctrine entities manager in registry for further usage
		Zend_Registry::set('DoctrineEntitiesManager', $entitiesManager);

		return $entitiesManager;
	}

	/**
	 * Initialize the DEBUG mode
	 *
	 * Initialize the DEBUG constant to TRUE or FALSE based on current environment.
	 * Also store a copy of Zend_Logger in the Registry for future references.
	 *
	 * Note: To make Firebug writer workable on your system, you must use Firefox >= 2, and
	 * have extensions Firebug and FirePHP installed and activated.
	 *
	 * @return void
	 */
	protected function _initDebug()
	{
		if (!defined('DEBUG')) {
			if ($this->getEnvironment() === 'development') {
				define('DEBUG', true);
			} else {
				define('DEBUG', false);
			}
		}

		$logger = new Zend_Log();
		$writer = new Zend_Log_Writer_Firebug();
		$logger->addWriter($writer);

		Zend_Registry::set('logger', $logger);
	}

	/**
	 * Initializses ZFDebug if DEBUG mode is ON
	 *
	 * @return bool
	 */
	protected function _initZFDebug()
	{
		$this->bootstrap('debug');

		if (!DEBUG) return false;

		// Ensure the front controller is initialized
		$this->bootstrap('FrontController');

		// Ensure database is initialized for auto discovery
		if ($this->hasPluginResource('db')) {
			$this->bootstrap('db');
		}

		// Retrieve the front controller from the bootstrap registry
		$front = $this->getResource('FrontController');

		if ($this->hasOption('zfdebug')) {
			// Create ZFDebug instance
			$zfdebug = new ZFDebug_Controller_Plugin_Debug($this->getOption('zfdebug'));

			// Register ZFDebug with the front controller
			$front->registerPlugin($zfdebug);
		}
	}

	/**
	 * Initialize view
	 *
	 * @return Zend_View
	 * @todo Move this in a plugin resource
	 */
	public function _initOverrideView()
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

		//ZendX_JQuery_View_Helper_JQuery::enableNoConflictMode();

		return $view;
	}

	/**
	 * Initialize the router
	 *
	 * @return Zend_Controller_Router_Interface
	 * @todo move it to plugin resource
	 */
	public function _initRouter()
	{
		$this->bootstrap('frontController');
		$front = $this->getResource('FrontController');

		$configRoutes = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'routes');
		$router = $front->getRouter();
		$router->addConfig($configRoutes, 'routes');
		$router->removeDefaultRoutes();

		return $router;
	}
}
