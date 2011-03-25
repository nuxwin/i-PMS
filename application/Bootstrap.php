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


// Prevent direct access
defined('APPLICATION_PATH') or die;

/**
 * Bootsrap
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0.0
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * Initialize view
     *
     * @return Zend_View
     */
    public function _initSetupView()
    {

        $this->bootstrap('View');
        $view = $this->getResource('View');

        $view->headTitle('internet Multi Server Control Panel (i-MSCP) - Project Web Site')
            ->setSeparator(' - ');
        $view->headLink(array('rel' => 'favicon', 'href' => '/favicon.ico'));
        $view->headScript()
            ->appendFile('/js/png.js', 'text/javascript', array('conditional' => 'lt IE 7'))
            ->appendScript("\tDD_belatedPNG.fix('*');\n", 'text/javascript', array('conditional' => 'lt IE 7'));
        $view->addHelperPath('iPMS/View/Helper/', 'iPMS_View_Helper_');
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
