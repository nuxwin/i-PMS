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
 * Action helper that load active Widgets
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class iPMS_Controller_Action_Helper_Widgets extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Widget container
     *
     * @var iPMS_Widgets_Container|null
     */
    protected $_widgetContainer = null;

    /**
     * Perform helper when called as $this->_helper->widtes() from an action controller
     *
     * Proxies to {@link getContainer()}
     *
     * @return iPMS_Widgets_Container
     */
    public function direct()
    {
        return $this->getContainer();
    }

    /**
     * Execute all Widgets
     *
     * @return void
     */
    public function preDispatch()
    {
        // Process only exception wasn't trapped in the response object and container is not null
        if(!$this->getResponse()->isException() && null!== $this->_widgetContainer) {
            /**
             * @var $request Zend_Controller_Request_Http
             */
            $request = $this->getRequest();

            // request for widget update
            /*
            if($request->isPost() && ($widgetName = $request->getParam('widgetUpdate', false))) {
                $widget = $this->_widgetContainer->findOneByName($request->getParam($widgetName));
                if(null !== $widget) {
                    $form = $widget->getForm('dashboard');

                    if($form->isValid($request->getPost('dashboard'))) {
                         $widget->update(array(), array());
                    }
                }
            }
             */

            $iterator = new IteratorIterator($this->_widgetContainer);

            /**
             * @var $widget iPMS_Widget
             */
            foreach ($iterator as $widget) {
                echo $widget->run();
            }
        }
    }

    /**
     * Remove the reference to the widgets container
     *
     * @return void
     */
    public function postDispatch()
    {
        $this->_widgetContainer = null;
    }

    /**
     * Returns widget container
     *
     * @return iPMS_Widgets_Container|null
     */
    public function getContainer()
    {
        if(null == $this->_widgetContainer) {
            $model = new Model_DbTable_Widgets();
            $widgetsOptions = $model->getOptions(true);
            //echo '<pre>';
            //    print_r($widgetsOptions);
            //echo '</pre>';
            $this->_widgetContainer = new iPMS_Widgets_Container($widgetsOptions);


        }

        return $this->_widgetContainer;
    }
}
