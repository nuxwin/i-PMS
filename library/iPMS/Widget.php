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
 * @package     iPMS_Widget
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */
require_once 'iPMS/Widget/Abstract.php';

/**
 * Abstract class for widget
 *
 * @category    iPMS
 * @package     iPMS_Widget
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     1.0.0
 */
abstract class iPMS_Widget extends iPMS_Widget_Abstract
{

    /**
     * Make widget content available for the view
     *
     * This method contains the code that will be rendered to the sidebar when the widget is added.
     *
     * @abstract
     * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param array $instance The settings for the particular instance of the widget
     * @return void
     */
    //abstract function widget($args, $instance);
    abstract function widget();

    /**
     * Widget initialization Initialization
     *
     * The following method contain the code that is run every time the widget is loaded - either when activated, used
     * on a page, updated and so on.
     *
     * @abstract
     * @return void
     */
    abstract public function init();



    //abstract public function run();

    /**
     * Widget dashboard settings form
     *
     * This methods must contains the widget settings form that will be shown on the Widgets dashboard screen
     *
     * @abstract
     * @param  $instance
     * @return void
     */
    abstract function dashBoardSettingsForm($instance);

    /**
     * Update widget settings
     *
     * This methods is called when the user click on the "save" button from the setting page on the widget dashboard
     * screen. This automatically handle saving to the database options. This method also allow to read in and validate
     * user input data.
     *
     * @abstract
     * @param  $oldInstance
     * @param  $newInstance
     * @return void
     */
    //abstract function update($oldInstance, $newInstance);

    /**
     * Set widget options
     *
     * @param  $options
     * @return void
     */
    protected function setWidgetOptions($options)
    {
	$this->setOptions(array('widgetOpts' => $options));
    }

    /**
     * Set control options
     *
     * @param  $options
     * @return void
     */
    protected function setControlOptions($options)
    {
	$this->setOptions(array('controlsOpts' => $options));
    }

    /**
     * Convenience method to build Dashboard settings form
     * 
     * @param  $params
     * @return string|Zend_Form
     * @Todo Making this as a view helper
     */
    public function buildDashboardSettingsForm($params)
    {

	$form = '';

	if (count($params)) {
	    $form = new Zend_Form(array(
			'name' => $this->getName(),
			'id' => 'widget-' . $this->getId(),
			'action' => '/dashboard/widget/' . $this
		    ));

	    foreach ($params as $param) {
		$element = 'Zend_Form_Element_' . $param['type'];
		$form->addElement(new $element(array('label' => $param['label'],
			    'name' => $param['name'],
			    'value' => $param['value'])
		));
	    }

	    $form->addElement(new Zend_Form_Element_Submit(
			    array('name' => 'submit',
				'label' => 'save'
			    )
	    ));
	}

	return $form;
    }

}
