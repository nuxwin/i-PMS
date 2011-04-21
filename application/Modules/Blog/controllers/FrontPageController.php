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
 * Index controller (Home page)
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class Blog_FrontPageController extends Zend_Controller_Action
{
    /**
     * @return void
     */
    public function init()
    {


        //$options =  new Zend_Config_Xml(APPLICATION_PATH . '/Widgets/Login/description.xml');

        //echo '<pre>';
        //    print_r($options->toArray());
        //echo '<pre>';
        //exit;

        //$options = $options->toArray();
        //$model = new Model_DbTable_Widgets();
        //$widget = $model->find(1)->current();
        //$widget->options = $options;
        //$widget->save();

        /**
         * @var $widgetContainer iPMS_Widgets_Container
         */
       //$widgetContainer = $this->_helper->Widgets();
               //->getContainer();
       // exit;
       //$this->view->widgets($widgetContainer);
          //  echo '<pre>';
          //      print_r($widgetContainer);
          //  echo '</pre>';
        //exit;

        /*
        require_once APPLICATION_PATH .'/Widgets/Login/Login.php';
        $widgetContainer->addWidget(new Widgets_Login_Login(
            new Zend_Config_Xml(APPLICATION_PATH . '/Widgets/Login/description.xml')
        ));
        */


        //echo '<pre>';
        //    print_r($widgetContainer);
        //echo'</pre>';
        //exit;

         // Make the Widgets container available for the view
        //$this->view->Widgets()->setContainer($Widgets);
    }

    /**
     * Returns a pageable list of posts
     *
     * @return void
     */
    public function indexAction()
    {

		$model = new Blog_Model_DbTable_Posts();
        $pageablePosts = $model->getPageablePosts((int)$this->_request->getParam('page', 1), 5);
        $this->view->assign('paginator', $pageablePosts);
    }

    /**
     * Get latest forum posts
     *
     * Todo must ne available as widget
     *
     * @return array
     */
    protected function getForumRss()
    {
        $rssDoc = new DOMDocument();
        $arrFeeds = array();

        // TODO get link from database
        if ($rssDoc->load(
            'http://forum.i-mscp.net/syndication.php?fid=1,2,3,4,5,6,7,24,8,10,9,11,12,13,14,33,15,16,17,18&limit=5'
        )) {
            $maxTitleLength = 25;
            $feeds = array();

            /**
             * var $item
             */
            foreach ($rssDoc->getElementsByTagName('item') as $item) {
                $title = ucfirst(html_entity_decode($item->getElementsByTagName('title')->item(0)->nodeValue));

                // TODO create view helper for it
                $normalizedTitle = $title;
                if (strlen($normalizedTitle) >= $maxTitleLength) {
                    $normalizedTitle = substr($normalizedTitle, 0, $maxTitleLength);
                    $spacer = strrpos($normalizedTitle, ' ');
                    if ($spacer) {
                        $normalizedTitle = substr($normalizedTitle, 0, $spacer);
                    }
                    $normalizedTitle .= '...';
                }
                $itemRSS = array(
                    'title' => $title,
                    'normalizedTitle' => $normalizedTitle,
                    'link' => $item->getElementsByTagName('link')->item(0)->nodeValue,
                );
                array_push($feeds, $itemRSS);
            }
        }

        return $feeds;
    }
}
