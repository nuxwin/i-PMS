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
 * Plugin for switching to the Dashboard layout
 *
 * Based on idea from Matthew Weier O'Phinney
 *
 * @author  Laurent Declercq
 * @version 1.0.0
 *
 */
class Dashboard_Plugin_Menu extends Zend_Controller_Plugin_Abstract
{

    /**
     * Build menu for dashboard
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ('Dashboard' == $request->getModuleName()) {
            $this->loadMenu();
        }
    }

    /**
     * Load dashboard menu
     * 
     * @return void
     */
    private function loadMenu()
    {

        $menu = new Zend_Config_Xml(APPLICATION_PATH . '/modules/Dashboard/config/menu.xml', 'navigation');
        $menuContainer = new Zend_Navigation($menu);
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $view->navigation()->setContainer($menuContainer);
        $view->jQuery()->addOnLoad('
            $("#dashboardMenu").accordion(
                {"navigation":true,"header":"h3","collapsibledisabled":true,"autoHeight":false});
        ');
    }
}
