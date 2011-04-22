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
	 * @return void
	 */
	protected function _initConfig()
    {
    	Zend_Registry::set('Config', new Zend_Config($this->getOptions()));
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
	    if(!defined('DEBUG')) {
	        if($this->getEnvironment() === 'development') {
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
		if($this->hasPluginResource('db')) {
			$this->bootstrap('db');
		}

        // Retrieve the front controller from the bootstrap registry
        $front = $this->getResource('FrontController');

        if ($this->hasOption('zfdebug'))
        {
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
