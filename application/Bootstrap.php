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
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Main bootsrap class
 *
 * @package     iPMS
 * @category    Bootstrap
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
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
		$cfg = new Zend_Config($this->getOptions());
		Zend_Registry::set('config', $cfg);

		return $cfg;
	}

	/**
	 * Execute some specific initialization for CLI mode
	 *
	 * @return void
	 */
	public function _initCli()
	{
		if(php_sapi_name() == 'cli') {
			new Zend_Application_Module_Autoloader(array(
				'namespace' => 'Core',
				'basePath' => APPLICATION_PATH . '/modules/core'));
			
		}
	}

	/**
	 * initializes the database connection and registers it in registry for further usage
	 *
	 * @return null|Zend_Db_Adapter_Abstract
	 */
	protected function __initDatabase()
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

		$autoloader = new \Doctrine\Common\ClassLoader('Doctrine', ROOT_PATH . '/library');

		// Registers doctrine autoloader on the SPL autoload stack
		$autoloader->register();

		$dCfg = new Doctrine\ORM\Configuration;

		// cache configuration - begin

		// Todo change this in production environment
		$cache = new Doctrine\Common\Cache\ArrayCache;
		$cache->setNamespace('iPMS');

		// Add Metadata cache to the doctrine configuration object
		$dCfg->setMetadataCacheImpl($cache);

		// Add Query cache to the doctrine configuration object
		$dCfg->setQueryCacheImpl($cache);

		// cache configuration - ending

		$this->bootstrap('FrontController');
		$fc = $this->getResource('FrontController');

		// Fetch all models paths
		$mods = $fc->getControllerDirectory();
		$mPaths = array();
		foreach (array_keys($mods) as $mod) {
			if(is_dir((APPLICATION_PATH . '/modules/' . $mod . '/models'))) {
				$mPaths[] = APPLICATION_PATH . '/modules/' . $mod . '/models';
			}
		}

		// Add annotation driver implementation to the doctrine configuration object
		$dCfg->setMetadataDriverImpl($dCfg->newDefaultAnnotationDriver($mPaths));

		// Sets the cache driver implementation used for the query cache (SQL cache)
		$dCfg->setQueryCacheImpl($cache);

		// Sets the directory where Doctrine generates any necessary proxy class files
		$dCfg->setProxyDir(ROOT_PATH . '/data/proxies');

		// Sets the namespace where proxy classes reside
		$dCfg->setProxyNamespace('Doctrine_Proxies');

		// Tell whether or not proxy classes must be re-generated on each request
		$dCfg->setAutoGenerateProxyClasses(false);

		$mCfg = $this->getResource('config');

		// Setup charset and collation options of MySQL Client
		$evm = new Doctrine\Common\EventManager();
		$evm->addEventSubscriber(new Doctrine\DBAL\Event\Listeners\MysqlSessionInit('utf8', 'utf8_general_ci'));

		$dem = Doctrine\ORM\EntityManager::create($mCfg->doctrine->connection->toArray(), $dCfg, $evm);

		// Registers doctrine entities manager in registry for further usage
		Zend_Registry::set('d.e.m', $dem);

		return $dem;
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

		if($this->hasPluginResource('Log')) {
			$this->bootstrap('Log');
			$log = $this->getResource('Log');
		} else {
			$log = new Zend_Log();
		}

		$log->addWriter(new Zend_Log_Writer_Firebug());

		Zend_Registry::set('logger', $log);
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

		$this->bootstrap('FrontController');

		// Ensure database is initialized for auto discovery
		if ($this->hasPluginResource('db')) {
			$this->bootstrap('db');
		}

		// Retrieve the front controller from the bootstrap registry
		$fc = $this->getResource('FrontController');

		if ($this->hasOption('zfdebug')) {
			// Create ZFDebug instance
			$zfdebug = new ZFDebug_Controller_Plugin_Debug($this->getOption('zfdebug'));

			// Register ZFDebug with the front controller
			$fc->registerPlugin($zfdebug);
		}
	}

	/**
	 * Initialize view
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

		//ZendX_JQuery_View_Helper_JQuery::enableNoConflictMode();

		return $view;
	}

	/**
	 * Initialize the router
	 *
	 * @return Zend_Controller_Router_Interface
	 * @todo move it to plugin resource
	 */
	protected function _initRouter()
	{
		$this->bootstrap('frontController');
		$fc = $this->getResource('FrontController');

		$rt = $fc->getRouter();
		$rt->removeDefaultRoutes();

		return $rt;
	}
}
