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
 * @subpackage  Helper_Widget
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */
/**
 * @see iPMS_View_Helper_Widget_HelperAbstract
 */
require_once 'iPMS/View/Helper/Widget/HelperAbstract.php';

/**
 * Helper for rendering widgets
 *
 * @category    iPMS
 * @package     iPMS_View
 * @subpackage  Helper_Widget
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class iPMS_View_Helper_Widget_Box extends iPMS_View_Helper_Widget_HelperAbstract
{

    /**
     * CSS class to use for the ul element
     *
     * @var string
     */
    protected $_ulClass = 'widgets';
    /**
     * Partial view script to use for rendering widget box
     *
     * @var string|array
     */
    protected $_partial = null;

    /**
     * View helper entry point: Retrieves helper and optionally sets container to operate on
     *
     * @param   iPMS_Widget_Container_Abstract $container [optional] container to operate on
     * @return  iPMS_View_Helper_Widget_Box fluent interface, returns self
     */
    public function box(iPMS_Widget_Container_Abstract $container = null)
    {
	if (null !== $container) {
	    $this->setContainer($container);
	}

	return $this;
    }

    /**
     * Sets CSS class to use for the first 'ul' element when rendering
     *
     * @param   string $ulClass CSS class to set
     * @return  iPMS_View_Helper_Widget_Box fluent interface, returns self
     */
    public function setUlClass($ulClass)
    {
	if (is_string($ulClass)) {
	    $this->_ulClass = $ulClass;
	}

	return $this;
    }

    /**
     * Returns CSS class to use for the first 'ul' element when rendering
     *
     * @return  string CSS class
     */
    public function getUlClass()
    {
	return $this->_ulClass;
    }

    /**
     * Sets which partial view script to use for rendering widget
     *
     * @param   string|array $partial partial view script or null. If an array is given, it is expected to contain two
     * values; the partial view script to use, and the module where the script can be found.
     * @return  iPMS_View_Helper_Widget_Box fluent interface, returns self
     */
    public function setPartial($partial)
    {
	if (null === $partial || is_string($partial) || is_array($partial)) {
	    $this->_partial = $partial;
	}

	return $this;
    }

    /**
     * Returns partial view script to use for rendering menu
     *
     * @return  string|array|null
     */
    public function getPartial()
    {
	return $this->_partial;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given widget if the widget's href is not empty, and a
     * 'span' element if it is empty
     *
     * Overrides {@link iPMS_View_Helper_Widget_Abstract::htmlify()}.
     *
     * @param   iPMS_Widget_Abstract $widget widget to generate HTML for
     * @return  string HTML string for the given page
     */
    public function htmlify(iPMS_Widget_Abstract $widget)
    {
	return parent::htmlify($widget);

	/*
	  // get label and title for translating
	  $label = $widget->getLabel();
	  $title = $widget->getTitle();

	  // translate label and title?
	  if ($this->getUseTranslator() && $t = $this->getTranslator()) {
	  if (is_string($label) && !empty($label)) {
	  $label = $t->translate($label);
	  }
	  if (is_string($title) && !empty($title)) {
	  $title = $t->translate($title);
	  }
	  }

	  // get attribs for element
	  $attribs = array(
	  'id'     => $widget->getId(),
	  'title'  => $title,
	  'class'  => $widget->getClass()
	  );

	  // does page have a href?
	  if ($href = $widget->getHref()) {
	  $element = 'a';
	  $attribs['href'] = $href;
	  $attribs['target'] = $widget->getTarget();
	  } else {
	  $element = 'span';
	  }

	  return '<' . $element . $this->_htmlAttribs($attribs) . '>'
	  . $this->view->escape($label)
	  . '</' . $element . '>';
	 */
    }

    /**
     * Normalizes given render options
     *
     * @param  array $options [optional] options to normalize
     * @return array normalized options
     */
    protected function _normalizeOptions(array $options = array())
    {
	if (isset($options['indent'])) {
	    $options['indent'] = $this->_getWhitespace($options['indent']);
	} else {
	    $options['indent'] = $this->getIndent();
	}

	if (isset($options['ulClass']) && $options['ulClass'] !== null) {
	    $options['ulClass'] = (string) $options['ulClass'];
	} else {
	    $options['ulClass'] = $this->getUlClass();
	}

	return $options;
    }

    // Render methods:

    /**
     * Renders a normal widget box (called from {@link renderBox()})
     *
     * @param   iPMS_Widget_Container_Abstract $container container to render
     * @param   string $ulClass CSS class for first UL
     * @param   string $indent initial indentation
     * @return  string
     */
    protected function _renderBox(iPMS_Widget_Container_Abstract $container, $ulClass, $indent)
    {
	//$html = 'iPMS_View_Helper_Widget_Box::_renderBox() isNot Yet Implemented (line 347)';

	$html = '';

	// Create iterator
	$iterator = new IteratorIterator($container);

	foreach ($iterator as $widget) {
	    if ($widget->isActive() && $this->accept($widget)) {
		if ($widget->hasPartial()) {
		    $html .= $this->_renderWidgetPartial($widget);
		}
	    } else {
		continue;
	    }
	}

	/*


	  // find deepest active
	  if ($found = $this->findActive($container, $minDepth, $maxDepth)) {
	  $foundWidget = $found['widget'];
	  $foundDepth = $found['depth'];
	  } else {
	  $foundWidget = null;
	  }

	  // create iterator
	  $iterator = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::SELF_FIRST);
	  if (is_int($maxDepth)) {
	  $iterator->setMaxDepth($maxDepth);
	  }

	  // iterate container
	  $prevDepth = -1;
	  foreach ($iterator as $widget) {
	  $zone = $this->_zone;
	  if(null !== $zone && $zone != $widget->_target) continue;

	  // NXW addon
	  if($widget->hasPartial()) {
	  return $this->_renderWidgetPartial($widget);
	  }

	  $depth = $iterator->getDepth();
	  $isActive = $widget->isActive(true);
	  if ($depth < $minDepth || !$this->accept($widget)) {
	  // widget is below minDepth or not accepted by acl/visibility
	  continue;
	  } else if ($onlyActive && !$isActive) {
	  // widget is not active itself, but might be in the active branch
	  $accept = false;
	  if ($foundWidget) {
	  if ($foundWidget->hasWidget($widget)) {
	  // accept if widget is a direct child of the active widget
	  $accept = true;
	  } else if ($foundWidget->getParent()->hasWidget($widget)) {
	  // widget is a sibling of the active widget...
	  if (!$foundWidget->hasWidgets() ||
	  is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
	  // accept if active widget has no children, or the children are too deep to be rendered
	  $accept = true;
	  }
	  }
	  }

	  if (!$accept) {
	  continue;
	  }
	  }

	  // make sure indentation is correct
	  $depth -= $minDepth;
	  $myIndent = $indent . str_repeat('        ', $depth);

	  if ($depth > $prevDepth) {
	  // start new ul tag
	  if ($ulClass && $depth ==  0) {
	  $ulClass = ' class="' . $ulClass . '"';
	  } else {
	  $ulClass = '';
	  }
	  $html .= $myIndent . '<ul' . $ulClass . '>' . self::EOL;
	  } else if ($prevDepth > $depth) {
	  // close li/ul tags until we're at current depth
	  for ($i = $prevDepth; $i > $depth; $i--) {
	  $ind = $indent . str_repeat('        ', $i);
	  $html .= $ind . '    </li>' . self::EOL;
	  $html .= $ind . '</ul>' . self::EOL;
	  }
	  // close previous li tag
	  $html .= $myIndent . '    </li>' . self::EOL;
	  } else {
	  // close previous li tag
	  $html .= $myIndent . '    </li>' . self::EOL;
	  }

	  // render li tag and widget
	  $liClass = $isActive ? ' class="active"' : '';
	  $html .= $myIndent . '    <li' . $liClass . '>' . self::EOL
	  . $myIndent . '        ' . $this->htmlify($widget) . self::EOL;

	  // store as previous depth for next iteration
	  $prevDepth = $depth;
	  }

	  if ($html) {
	  // done iterating container; close open ul/li tags
	  for ($i = $prevDepth+1; $i > 0; $i--) {
	  $myIndent = $indent . str_repeat('        ', $i-1);
	  $html .= $myIndent . '    </li>' . self::EOL
	  . $myIndent . '</ul>' . self::EOL;
	  }
	  $html = rtrim($html, self::EOL);
	  }
	 */
	return $html;
    }

    /**
     * Renders helper
     *
     * Renders a HTML 'ul' for the given $container. If $container is not given, the container registered in the helper
     * will be used.
     *
     * @param   iPMS_Widget_Container_Abstract $container [optional] container to create menu from. Default is to use
     * the container retrieved from {@link getContainer()}.
     * @param   array $options [optional] options for controlling rendering
     * @return  string rendered widget box
     */
    public function renderBox(iPMS_Widget_Container_Abstract $container = null, array $options = array())
    {
	if (null === $container) {
	    $container = $this->getContainer();
	}

	$options = $this->_normalizeOptions($options);
	$html = $this->_renderBox($container, $options['ulClass'], $options['indent']);

	return $html;
    }

    /**
     * Renders the given widget $container by invoking the partial view helper
     *
     * The container will simply be passed on as a model to the view script as-is, and will be available in the partial
     * script as 'widgetContainer', e.g. <code>echo 'Number of widgets: ', count($this->widgetContainer);</code>.
     *
     * @param  iPMS_Widget_Container_Abstract $container [optional] container to pass to view script. Default is to use
     * the container registered in the helper.
     * @param  string|array $partial [optional] partial view script to use. Default is to use the partial registered in
     * the helper. If an array is given, it is expected to contain two values; the partial view script to use, and the
     * module where the script can be found.
     * @return string helper output
     */
    public function renderPartial(iPMS_Widget_Container_Abstract $container = null, $partial = null)
    {
	if (null === $container) {
	    $container = $this->getContainer();
	}

	if (null === $partial) {
	    $partial = $this->getPartial();
	}

	if (empty($partial)) {
	    require_once 'iPMS/View/Exception.php';
	    throw new Zend_View_Exception();
	    $e = new iPMS_View_Exception('Unable to render widgets: No partial view script provided');
	    $e->setView($this->view);
	    throw $e;
	}

	$model = array(
	    'widgetContainer' => $container
	);

	if (is_array($partial)) {
	    if (count($partial) != 2) {
		require_once 'iPMS/View/Exception.php';
		$e = new iPMS_View_Exception(
				'Unable to render widget box: A view partial supplied as an array must contain two values: '
				. 'partial view script and module where script can be found'
		);
		$e->setView($this->view);
		throw $e;
	    }

	    return $this->view->partial($partial[0], $partial[1], $model);
	}

	return $this->view->partial($partial, null, $model);
    }

    /**
     * Renders the given widget by invoking the partial view helper
     *
     * The widget will simply be passed on as a model to the view script as-is, and will be available in the partial
     * script as his name followed by the 'Widget' suffix.
     *
     * The name of partial script is automatically discovered by following naming convention where the script must have
     * the same name in lowercase as the rendered widget
     *
     * @param iPMS_Widget_Abstract $widget Widget to pass to view script
     * @return string helper output
     */
    protected function _renderWidgetPartial(iPMS_Widget_Abstract $widget)
    {
	$widgetName = lcfirst($widget->getName());
	return $this->view->partial($widgetName . '.phtml', array($widgetName . 'Widget' => $widget));
    }

    /**
     * Renders widget box
     *
     * Implements {@link iPMS_View_Helper_Widget_Helper::render()}.
     *
     * If a partial view is registered in the helper, the widget box will be rendered using the given partial script.
     * If no partial is registered, the widget box will be rendered as an 'ul' element by the helper's internal method.
     *
     * @see renderPartial()
     * @see renderMenu()
     *
     * @param  iPMS_Widget_Container_Abstract $container [optional] container to render. Default is to render the
     * container registered in the helper.
     * @return string helper output
     */
    public function render(iPMS_Widget_Container_Abstract $container = null)
    {
	if ($partial = $this->getPartial()) {
	    return $this->renderPartial($container, $partial);
	} else {
	    return $this->renderBox($container);
	}
    }

}
