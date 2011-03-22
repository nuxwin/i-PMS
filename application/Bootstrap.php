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
 * @version     1.0.0
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

        //$moduleLoader->addResourceType('plugin', 'plugins/', 'Plugin');

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
        //$fc->registerPlugin(new Plugin_WidgetsLoader($this->getEnvironment()), 1);
    }

    /**
     * Add prefix path for iPMS action helpers
     * 
     * @return void
     */
    public function _initHelpers()
    {
        Zend_Controller_Action_HelperBroker::addPrefix('iPMS_Controller_Action_Helper');
    }

    /**
     * Initialize view
     *
     * @return Zend_View
     * @todo move it to plugin resource
     */
    public function _initSetupView()
    {

        $this->bootstrap('View');
        $view = $this->getResource('View');

        $view->headTitle('internet Multi Server Control Panel (i-MSCP) - Project Web Site');
        $view->headLink(array('rel' => 'favicon', 'href' => '/favicon.ico'));
        $view->headScript()
            ->appendFile('/js/png.js', 'text/javascript', array('conditional' => 'lt IE 7'))
            ->appendScript("\tDD_belatedPNG.fix('*');\n", 'text/javascript', array('conditional' => 'lt IE 7'));
        $view->addHelperPath('iPMS/View/Helper/', 'iPMS_View_Helper_');

        // Add jQuery support
        ZendX_JQuery::enableView($view);

        $jquery = $view->jQuery();

        // Set jquery core and ui versions to be used
        // See http://code.google.com/intl/fr/apis/libraries/devguide.html#jquery for available versions
        $jquery->setVersion('1.5.1');
        $jquery->setUiVersion('1.8.11');
        $jquery->addStyleSheet('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/themes/smoothness/jquery-ui.css');

        // Will enable both jquery (core) and jquery (UI)
        $jquery->uiEnable();

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
        $fc = $this->getResource('FrontController');
        $configRoutes = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'routes');
        $router = $fc->getRouter();
        $router->addConfig($configRoutes, 'routes');
        $router->removeDefaultRoutes();

        return $router;
    }

}
