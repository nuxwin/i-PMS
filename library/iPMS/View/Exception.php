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
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */
/**
 * @see iPMS_Exception
 */
require_once 'iPMS/Exception.php';

/**
 * Exception for iPMS_View class.
 *
 * @category   iPMS
 * @package    iPMS_View
 */
class iPMS_View_Exception extends iPMS_Exception
{

    /**
     * @var Zend_View_Abstract
     */
    protected $view = null;

    /**
     * Set the view
     *
     * @param Zend_View_Interface $view
     * @return iPMS_View_Exception
     */
    public function setView(Zend_View_Interface $view = null)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Get view
     *
     * @return Zend_View_Abstract
     */
    public function getView()
    {
        return $this->view;
    }

}
