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
 * @package     iPMS
 * @subpackage  Blog
 * @category    Forms
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Post form
 *
 * @package     iPMS
 * @subpackage  Blog
 * @category    Forms
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
class Blog_Form_Post extends Zend_Form
{

    /**
     * Restricted list of HTML tags that can be used by users
     *
     * Note: XHTML tags that can compromise security are not referenced.
     *
     * @var array
     * @todo Must be replaced by HtmlPurifier ASAP
     */
    protected $_allowedXhtmlTags = array(
        'acronym', 'blockquote', 'cite', 'q', 'sup', 'sub', 'strong', 'em',
        'h6', 'h5', 'h4', 'h3', 'h2', 'h1', 'img', 'a', 'br', 'p', 'hr', 'address',
        'del', 'ins', 'dfn', 'kbd', 'pre', 'abbr',
        'menu', 'ul', 'ol', 'li', 'dl', 'dt', 'dd',
        'table', 'caption', 'tr', 'th', 'td', 'thead', 'tbody', 'tfoot',
        'span', 'div'
    );
    /**
     * Restricted list of XHTML attributes that can be used by users
     *
     * Note: Deprecated attributes are not referenced. Use style instead.
     * Attributes that can compromise security are not referenced.
     *
     * @var array
     * @todo Must be replaced by HtmlPurifier ASAP
     */
    protected $_allowedXhtmlAttributes = array(
        'class', 'id', 'style', 'title', // XHTML core attributes
        'dir', 'lang', 'xml:lang', // Language attributes
        'accesskey', 'tabindex', // keyboard attributes
        'alt', 'target', 'href', 'src', 'rel', 'width', 'height' // Others attributes
    );

	/**
	 * Form initialization
	 *
	 * @return void
	 */
	public function init()
	{
		$this->setName('postForm')
			->setAction('/post/add')
			->setElementsBelongTo('postForm')
			->setMethod('post')
			->setEnctype('application/x-www-form-urlencoded');

		$element = new Zend_Form_Element_Text('title');
		$element->setLabel('Title')
			->setAttrib('class', 'inputTitle')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');
		$this->addElement($element);

		$element = new Zend_Form_Element_Textarea('teaser');
		$element->setLabel('Teaser')
			->setAttrib('class', 'inputSummary')
			->setAttrib('rows', '5')
			->setRequired(true)
			->addFilter('StripTags', array(
			                              'allowTags' => $this->_allowedXhtmlTags,
			                              'allowAttribs' => $this->_allowedXhtmlAttributes))
			->addFilter('StringTrim');
		$this->addElement($element);

		$element = clone $element;

		$element = new Zend_Form_Element_Textarea('body');
		$element
			->setLabel('Content')
			->setAttrib('class', 'input-body')
			->setAttrib('rows', '25')
			->setRequired(true)
			->addFilter('StripTags', array(
			                              'allowTags' => $this->_allowedXhtmlTags,
			                              'allowAttribs' => $this->_allowedXhtmlAttributes))
			->addFilter('StringTrim');

		$this->addElement($element);

		$element = new Zend_Form_Element_Checkbox('allow_comments');
		$element->setLabel('Open for new comments ?')
			->setValue('1');
		$this->addElement($element);

		$element = new Zend_Form_Element_Hidden('pid');
		$element->getDecorator('Label')->setOption('tagClass', 'hidden');
		$element->getDecorator('HtmlTag')->setOption('class', 'hidden');
		$this->addElement($element);

		$element = new Zend_Form_Element_Hidden('categorie');
		$element->setValue('home');
		$element->getDecorator('Label')->setOption('tagClass', 'hidden');
		$element->getDecorator('HtmlTag')->setOption('class', 'hidden');
		$this->addElement($element);

		$element = new Zend_Form_Element_Submit('postSubmit');
		$element->setLabel('Submit');
		$this->addElement($element);

		$this->_setRequiredSuffix();
	}

	/**
	 * Sets required suffix for all required elements
	 *
	 * @return void
	 */
	protected function _setRequiredSuffix()
	{
		foreach ($this->getElements() as $element) {
			if ($element->isRequired()) {
				$element->addDecorator('Label', array('tag' => 'dt',
				                                     'escape' => false,
				                                     'requiredPrefix' => ' <span>*</span> '));
			}
		}
	}
}
