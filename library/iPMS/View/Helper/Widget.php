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
 * @package     iPMS_View
 * @subpackage  Helper
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */
/**
 * @see iZend_View_Helper_Abstract
 */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Proxy helper for retrieving widget helpers and forwarding calls
 *
 * @category    iPMS
 * @package     iPMS_View
 * @subpackage  Helper
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class iPMS_View_Helper_Widget extends Zend_View_Helper_Abstract implements Zend_Loader_Autoloader_Interface
{
    /**
     * Widgets to be rendered
     *
     * @var array
     */
    protected $_widgets = array();

    /**
     * Render a widget sidebar
     *
     * @param  string $sidebar widget area where the widget will be rendered
     * @param array $default [OPTIONAL] list of widgets to render if none was set manually for the current sidebar
     * @return iPMS_View_Helper_Widget fluent interface, returns self
     */
    public function widget($sidebar, array $default = array())
    {
        $widgetsModel = new Model_DbTable_Widgets();
        $widgets = $widgetsModel->fetchAll(array(
            'is_active = ?' => 1,
            'sidebar = ?' => $sidebar
        ));

        if (!count($widgets)) { // Render the widgets defined by user via the dashboard
            $widgets = $widgets->toArray();
            $default = false;
        } elseif (!empty($default)) { // Render the default widgets set in layout
            $widgets = $default;
            $default = true;
        } else { // no widget to render, returns self
           return $this;
        }

        // Make the widget loader available
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->unshiftAutoloader($this);

        if ('development' != APPLICATION_ENV) {
            $loader->suppressNotFoundWarnings(true);
        }

        $instances = array();
        
        foreach ($widgets as $widget) {
            try {
                $name = ($default) ? ucfirst($widget): ucfirst($widget['name']);
                $className = 'Widget_' . $name . '_' . $name;

                /**
                 * @var $instance iPMS_Widget
                 */
                $instance = new $className($widget, $this->view);

                if($default) { // options are loaded from xml file
                    $instance->setOptionsFromXml(APPLICATION_PATH . '/widgets/' . $name . '/description.xml');
                } else { // options come from the database
                    $instance->setOptions(unserialize($widget['options']));
                }

                $hash = spl_object_hash($instance);
                $instances[$hash] = $instance;
            } catch (Exception $e) {
                if ('development' == APPLICATION_ENV) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }
            }
        }

        $this->_widgets = $instances;

        // Widget loader is not longer needed
        Zend_Loader_Autoloader::getInstance()->removeAutoloader($this);

        return $this;
    }

    /**
     * Render a widget sidebar
     *
     * @return string HTML code that represent widget sidebar
     * @todo use iterator add add order feature
     */
    public function render()
    {
        $html = '';

        if(count($this->_widgets)) {
            /**
             * @var $widget iPMS_Widget
             */
            foreach($this->_widgets as $ref => $widget) {
                $beginTag = "<div id=\"$ref\" class=\"widget\">";
                $body = (string) $widget->_widget()->getContent();
                $endTag = "</div>";
                $html .= "$beginTag\n\t\t\t$body\n\t\t$endTag\n";
            }
        }

        return $html;
    }

    /**
     * Convenience method to render a widget sidebar
     *
     * @return string HTML representation of widget sidebar
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Widget autoloader
     *
     * @throws iPMS_Exception
     * @param  $class
     * @return
     */
    public function autoload($class)
    {
        if (class_exists($class, false) || interface_exists($class, false)) {
            return;
        }

        // Auto-discover the path from the class name
        // Implementation is PHP namespace-aware, and based on
        // Framework Interop Group reference implementation:
        // http://groups.google.com/group/php-standards/web/psr-0-final-proposal
        $className = ltrim($class, '\\');
        $file = '';
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $file .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        $file = substr_replace($file, 'widgets', 0, 6);

        if (Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings()) {
            @Zend_Loader::loadFile($file, APPLICATION_PATH, true);
        } else {
            Zend_Loader::loadFile($file, APPLICATION_PATH, true);
        }

        if (!class_exists($class, false) && !interface_exists($class, false)) {
            require_once 'iPMS/Exception.php';
            throw new iPMS_Exception("File \"$file\" does not exist or class \"$class\" was not found in the file");
        }
    }
}
