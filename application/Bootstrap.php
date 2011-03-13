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
 * @author      Laurent Declercq <laurent.declercq@i-mscp.net>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Bootsrap
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0.0
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Init autoloader
	 * 
	 * @return Zend_Application_Module_Autoloader
	 */
	protected function _initAutoload()
	{
		$moduleLoader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath' => APPLICATION_PATH
		));

		$moduleLoader->addResourceType('plugin', 'plugins/', 'Plugin');
		//$moduleLoader->addResourceType('widget', 'widgets/login', 'Widget_Login');

		return $moduleLoader;
	}

	/**
	 * Initialize plugins
	 *
	 * @return void
	 * @todo can be done in configuration file
	 */
	public function _initPlugins()
	{
		$this->bootstrap('FrontController');

		/**
		 * @var $fc Zend_Controller_Front
		 */
		$fc = $this->getResource('FrontController');
		//$fc->registerPlugin(new Plugin_PermissionsCheck(Zend_Auth::getInstance(), Model_Acl::getInstance()));
		$fc->registerPlugin(new Plugin_WidgetsLoader($this->getEnvironment()), 1);

	}

	/**
	 * Initialize view
	 *
	 * @return Zend_View
	 */
	public function _initOverrideView() {

		$this->bootstrap('View');
        $view = $this->getResource('View');

		//echo '<pre>';
		//print_r($view);
		//echo '</pre>';

		//exit;

		//$view->doctype('XHTML5');
		$view->headTitle('internet Multi Server Control Panel (i-MSCP) - Project Web Site');

		// Define common Meta
		//$view->headMeta()
			//->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
			//->appendHttpEquiv('Content-Style-Type', 'text/css')
			//->appendHttpEquiv('Content-Script-Type', 'text/javascript');

		// Define favicon
		$view->headLink(array('rel' => 'favicon', 'href' => '/favicon.ico'));

		// Define common js scripts
		$view->headScript()
			->appendFile('/js/png.js', 'text/javascript', array('conditional' => 'lt IE 7'))
			->appendScript("\tDD_belatedPNG.fix('*');\n", 'text/javascript', array('conditional' => 'lt IE 7'));

		// Add our view helper path
		$view->addHelperPath('iPMS/View/Helper/', 'iPMS_View_Helper_');

		// Add it to the ViewRenderer
		//$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		//$viewRenderer->setView($view);

		// The view will be registered by the bootstrap
		//return $view;
	}

	/**
	 * @return void
	 */
	public function _initRouter() {

		$this->bootstrap('frontController');
		$fc = $this->getResource('FrontController');
		$router = $fc->getRouter();
		$configRoutes = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'routes');
		$router->addConfig($configRoutes, 'routes');
		//$restRoute = new Zend_Rest_Route($fc);
		//$router->addRoute('rest', $restRoute);

		//$this->bootstrap('FrontController');
		//$router = $this->getResource('FrontController')->getRouter();
		//$configRoutes = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'routes');

		// Add all routes in it
		//$router->addConfig($configRoutes, 'routes');

		// Removing default routes
		$router->removeDefaultRoutes();

		return $router;
	}
}

