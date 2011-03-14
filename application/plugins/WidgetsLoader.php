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
 * @package     iPMS_Plugins
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Plugin that load all actives widgets
 *
 * @category    i-PMS
 * @package     Plugins
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class Plugin_WidgetsLoader extends Zend_Controller_Plugin_Abstract implements Zend_Loader_Autoloader_Interface
{

    /**
     * Environment (development|testing|production)
     *
     * @var string
     */
    protected $_environment;

    /**
     * Constructor
     *
     * @param  string $environment Current environment
     * @return void
     */
    public function __construct($environment)
    {
        $this->_environment = $environment;
    }

    /**
     * Load all active widgets and adds them as action helper
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // todo if module dashboard, do not process...
        // Getting all active widgets
        $widgetsModel = new Model_DbTable_Widgets();
        $widgetsRows = $widgetsModel->fetchAll(array('is_active = ?' => 1))->toArray();

        if (count($widgetsRows)) {
            $loader = Zend_Loader_Autoloader::getInstance();
            $loader->unshiftAutoloader($this);

            if ($this->_environment != 'development') {
                $loader->suppressNotFoundWarnings(true);
            }

            $widgets = array();

            foreach ($widgetsRows as $widgetRow) {
                try {
                    $widgetName = ucfirst($widgetRow['name']);
                    $widgetClassName = 'Widget_' . $widgetName . '_' . $widgetName;
                    $widgets[] = new $widgetClassName($widgetRow);
                } catch (Exception $e) {
                    if ($this->_environment == 'development') {
                        trigger_error($e->getMessage(), E_USER_WARNING);
                    }
                }
            }

            $widgetContainer = new iPMS_Widget_Container($widgets);
            Zend_Controller_Action_HelperBroker::addHelper($widgetContainer);

            // Widget loader is not longer needed
            Zend_Loader_Autoloader::getInstance()->removeAutoloader($this);
        }
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

        // Autodiscover the path from the class name
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
