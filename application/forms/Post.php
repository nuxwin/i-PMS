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
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Post form
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0.0
 */
class Form_Post extends Zend_Form
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
        $this->setName('postFrm')
                ->setMethod('post')
                ->setEnctype('application/x-www-form-urlencoded');

        // Hidden field that contain post id
        $id = new Zend_Form_Element_Hidden('id');

        $id->getDecorator('Label')->setOption('tagClass','hidden');
        $id->getDecorator('HtmlTag')->setOption('class','hidden');


        /*
        $id->setLabel('&#160;')
                ->clearDecorators()
                ->addDecorator('ViewHelper')
                ->addDecorator(array(
                                    'element' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'hidden')
        )
                ->addDecorator(
            array('closeDt' => 'HtmlTag'), array('tag' => 'dt', 'closeOnly' => true, 'placement' => 'prepend')
        )
                ->addDecorator('Label')
                ->addDecorator(array(
                                    'openDt' => 'HtmlTag',), array('tag' => 'dt', 'openOnly' => true, 'class' => 'hidden')
        );
         */

        $category = clone $id;
        $category->setName('categorie')
                ->setValue('home');

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $teaser = new Zend_Form_Element_Textarea('teaser');
        $teaser->setLabel('Teaser')
                ->setRequired(true)
        //->setAttrib('rows', 4)
        //->setAttrib('cols', 75)
                ->addFilter('StripTags', array(
                                              'allowTags' => $this->_allowedXhtmlTags, 'allowAttribs' => $this->_allowedXhtmlAttributes)
        )
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $body = clone $teaser;
        $body->setName('body')
                ->setLabel('Content');
        //->setAttrib('rows', 25);
        // Comments checkbox (Tell whether or not the post is open for new comments
        $allowComments = new Zend_Form_Element_Checkbox('allow_comments');
        $allowComments->setLabel('Open for new comments ?')
                ->setValue('1');


        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');


        $this->addElements(array($id, $category, $title, $teaser, $body, $allowComments, $submit));
    }

}
