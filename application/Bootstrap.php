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
     * Initialize view
     *
     * @return Zend_View
     */
    public function _initSetupView()
    {
        $this->bootstrap('View');
        $view = $this->getResource('View');

        // Site title
        $view->headTitle('internet Multi Server Control Panel (i-MSCP) - Project Web Site')
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
