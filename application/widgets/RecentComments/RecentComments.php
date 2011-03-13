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
 * @package     iPMS_Widgets
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Widget display the most recent approved comments
 *
 * @category    i-PMS
 * @package     Widgets
 * @subpackage  Widgets_RecentComments
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class Widget_RecentComments_RecentComments extends iPMS_Widget
{

    /**
     * Widget initialization
     *
     * @return void
     */
    public function init()
    {

    }

    /**
     * Generate code for rendering process
     *
     * @return void
     */
    public function widget()
    {
	$commentsModel = new Model_DbTable_Comments();
	$comments = $commentsModel->fetchAll(null, 'id', 5)->toArray();


	if (count($comments)) {
	    $this->_comments = $comments;
	    $this->_prepareView();
	}
    }

    protected $_comments = array();
    /**
     * Tell whether or not partial must be used for widget rendering
     * @var bool
     */
    protected $_partial = true;


    public function getComments()
    {
	return $this->_comments;
    }

    /**
     * Widget dashboard settings form
     *
     * This methods must contains the widget settings form that will be shown on the Widgets dashboard screen
     *
     * @abstract
     * @param  $instance
     * @return void
     */
    public function dashBoardSettingsForm($instance)
    {
	$form = new Zend_Form();

	$form->addElement(new Zend_Form_Element_Text(array(
		    'name' => $this->getName(),
		    'value' => 10,
		    'label' => $this->getTitle()
		)));

	return $form;
    }

}
