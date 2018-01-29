<?php
/**
 * Get Work Details Action
 *
 * PHP version 5
 *
 * Copyright (c) Falvey Library 2017.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https:// Main Page
 */
namespace VuBib\Action\Work;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;

/**
 * Class Definition for GetWorkDetailsAction.
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
class GetWorkDetailsAction
{
    /**
     * Router\RouterInterface
     *
     * @var $router
     */
    protected $router;

    /**
     * Template\TemplateRendererInterface
     *
     * @var $template
     */
    protected $template;

    /**
     * Zend\Db\Adapter\Adapter
     *
     * @var $adapter
     */
    protected $adapter;

    /**
     * AttributesWorkTypeAction constructor.
     *
     * @param Router\RouterInterface             $router   for routes
     * @param Template\TemplateRendererInterface $template for templates
     * @param Adapter                            $adapter  for db connection
     */
    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null, Adapter $adapter)
    {
        $this->router = $router;
        $this->template = $template;
        $this->adapter = $adapter;
    }

    /**
     * Fetches publisher details.
     *
     * @param Array $post contains posted elements of form
     *
     * @return string $output
     */
    protected function pubName($post)
    {
        $no_of_wks = [];
        $name = $post['pub_name'];
        $table = new \VuBib\Db\Table\Publisher($this->adapter);
        $publisher_row = $table->findRecords($name);
        foreach ($publisher_row as $row) :
                $pub_row[] = $row;
        endforeach;
        foreach ($pub_row as $row) :
            $table = new \VuBib\Db\Table\WorkPublisher($this->adapter);
        $wks = $table->findRecordByPublisherId($row['id']);
        $no_wks = count($wks);
        $no_of_wks[] = $no_wks;
        endforeach;
        for ($i = 0; $i < count($no_of_wks); ++$i) {
            $pub_row[$i]['works'] = $no_of_wks[$i];
        }
        $output = array('pub_row' => $pub_row);
        echo json_encode($output);
        exit;
    }

    /**
     * Fetches publisher location details.
     *
     * @param Array $post contains posted elements of form
     *
     * @return string $output
     */
    protected function publisherIdLocs($post)
    {
        $pub_id = $post['publisher_Id_locs'];
        $table = new \VuBib\Db\Table\PublisherLocation($this->adapter);
        $pub_loc_rows = $table->getPublisherLocations($pub_id);
        foreach ($pub_loc_rows as $i => $row) {
            $pub_loc_rows[$i]['value'] = $row['location'];
            $pub_loc_rows[$i]['label'] = $row['location'];
            $pub_loc_rows[$i]['id'] = $row['id'];
        }
        foreach ($pub_loc_rows as $row) :
            $table = new \VuBib\Db\Table\WorkPublisher($this->adapter);
        $wks = $table->findRecordByLocationId($row['id']);
        $no_wks = count($wks);
        $no_of_wks[] = $no_wks;
        endforeach;
        for ($i = 0; $i < count($no_of_wks); ++$i) {
            $pub_loc_rows[$i]['works'] = $no_of_wks[$i];
        }
        $output = array('pub_locs' => $pub_loc_rows);
        echo json_encode($output);
        exit;
    }

    /**
     * Fetches agent details.
     *
     * @param Array $post contains posted elements of form
     *
     * @return string $output
     */
    protected function agName($post)
    {
        $no_of_wks = [];
        $name = $post['ag_name'];
        $table = new \VuBib\Db\Table\Agent($this->adapter);
        $ag_row = $table->getLastNameLikeRecords($name);
        foreach ($ag_row as $row) :
            $table = new \VuBib\Db\Table\WorkAgent($this->adapter);
        $wks = $table->findRecordByAgentId($row['id']);
        $no_wks = count($wks);
        $no_of_wks[] = $no_wks;
        endforeach;
        for ($i = 0; $i < count($no_of_wks); ++$i) {
            $ag_row[$i]['works'] = $no_of_wks[$i];
        }
        $output = array('ag_row' => $ag_row);
        echo json_encode($output);
        exit;
    }
    
    /**
     * Add/Edit instructions.
     *
     * @param Array $post contains posted elements of form
     *
     * @return empty
     */
    protected function insText($post)
    {
        $ins_str = $post['ins_text'];
        if (!empty($post['pg_id'])) {
            $pg_id = $post['pg_id'];
            $table = new \VuBib\Db\Table\Page_Instructions($this->adapter);
            $table->updateRecord($pg_id, $ins_str);
        } else {
            $pg_nm = $post['pg_nm'];
            $table = new \VuBib\Db\Table\Page_Instructions($this->adapter);
            $table->insertRecord($pg_nm, $ins_str);
        }
        exit;
    }
    
    /**
     * Fetches classification details.
     *
     * @param Array $post contains posted elements of form
     *
     * @return string $output
     */
    protected function folderId($post)
    {
        $fl_id = $post['folder_Id'];
        $table = new \VuBib\Db\Table\Folder($this->adapter);
        $rows = $table->getChild($fl_id);
        $output = array('folder_children' => $rows);
        echo json_encode($output);
        exit;
    }
    
    /**
     * Fetches classification parent trail.
     *
     * @param Array $post contains posted elements of form
     *
     * @return string $output
     */
    protected function flId($post)
    {
        $fl_id = $post['fl_id'];
        $table = new \VuBib\Db\Table\Folder($this->adapter);
        $src_row = $table->getParentChain($fl_id);
        $output = array('fl_row' => $src_row);
        echo json_encode($output);
        exit;
    }
 
    /**
     * Fetches Attribute options.
     *
     * @param Array $post contains posted elements of form
     *
     * @return string $output
     */
    protected function option($post)
    {
        $opt_title = $post['option'];
        $wkat_id = preg_replace("/^\w+:/", '', $post['attribute_Id']);
            //$wkat_id = $_POST['attribute_Id'];
            $table = new \VuBib\Db\Table\WorkAttribute_Option($this->adapter);
        $rows = $table->getAttributeOptions($opt_title, $wkat_id);
        $output = array('attribute_options' => $rows);
        echo json_encode($output);
        exit;
    }
    
    /**
     * Fetches workattribute options.
     *
     * @param Array $post contains posted elements of form
     *
     * @return string $output
     */
    protected function worktypeId($post)
    {
        $wkt_id = $post['worktype_Id'];
        $table = new \VuBib\Db\Table\WorkAttribute($this->adapter);
        $paginator = $table->getAttributesForWorkType($wkt_id);
        $itemsCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($itemsCount);
        $rows = [];
        foreach ($paginator as $row) :
                $rows[] = $row;
        endforeach;
        $output = array('worktype_attribute' => $rows);
        echo json_encode($output);
        exit;
    }
    
    /**
     * Fetches publisher and location details.
     *
     * @param Array $post contains posted elements of form
     *
     * @return string $output
     */
    protected function publisherId($post)
    {
        $pub_id = $post['publisher_Id'];
        $table = new \VuBib\Db\Table\PublisherLocation($this->adapter);
        $rows = $table->getPublisherLocations($pub_id);
        foreach ($rows as $i => $row) {
            $rows[$i]['value'] = $row['location'];
            $rows[$i]['label'] = $row['location'];
            $rows[$i]['id'] = $row['id'];
        }
        $output = array('publoc' => $rows);
        echo json_encode($output);
        exit;
    }
   
    /**
     * Fetches agent and no of works for each.
     *
     * @param Array $post contains posted elements of form
     *
     * @return string $output
     */
    protected function agId($post)
    {
        $ag_id = $post['ag_id'];
        $table = new \VuBib\Db\Table\WorkAgent($this->adapter);
        $wks = $table->findRecordByAgentId($ag_id);
        $output = array('ag_no_of_wks' => count($wks));
        echo json_encode($output);
        exit;
    }
    
    /**
     * Autosuggest
     *
     * @param Array $autofor     entity for which autosuggest is required
     * @param Array $search_term auto search text
     *
     * @return Array $rows
     */
    protected function getAutoSuggest($autofor, $search_term)
    {
        if ($autofor == 'publisher') {
            $table = new \VuBib\Db\Table\Publisher($this->adapter);
            $rows = $table->getLikeRecords($search_term);
            foreach ($rows as $i => $row) {
                $rows[$i]['value'] = $row['name'];
                $rows[$i]['label'] = $row['name'];
                $rows[$i]['id'] = $row['id'];
            }
            return $rows;
        }
        if ($autofor == 'agent') {
            $table = new \VuBib\Db\Table\Agent($this->adapter);
            $rows = $table->getLikeRecords($search_term);
            foreach ($rows as $i => $row) {
                $rows[$i]['id'] = $row['id'];
                $rows[$i]['label'] = $row['fname'];
                $rows[$i]['lname'] = $row['lname'];
                $rows[$i]['alternate_name'] = $row['alternate_name'];
                $rows[$i]['organization_name'] = $row['organization_name'];
            }
            return $rows;
        }
    }

    /**
     * Fetches work title
     *
     * @param Array $post contains posted elements of form
     *
     * @return string $output
     */
    public function getParentLookup($post)
    {
        $lookup_title = $post['lookup_title'];
        $table = new \VuBib\Db\Table\Work($this->adapter);
        $rows = $table->fetchParentLookup($lookup_title);
        foreach ($rows as $i => $row) {
            $rows[$i]['id'] = $row['id'];
            $rows[$i]['label'] = $row['title'];
            $rows[$i]['type'] = $row['type'];
        }
        $output = array('prnt_lookup' => $rows);
        echo json_encode($output);
        exit;
    }
    
    /**
     * Action based on post parameter set.
     *
     * @param Array $post contains posted elements of form
     *
     * @return empty
     */
    public function doPost($post)
    {
        if (isset($post['publisher_Id'])) {
            $this->publisherId($post);
        }
        if (isset($post['pub_name'])) {
            $this->pubName($post);
        }
        if (isset($post['publisher_Id_locs'])) {
            $this->publisherIdLocs($post);
        }
        if (isset($post['worktype_Id'])) {
            $this->worktypeId($post);
        }
        if (isset($post['option'])) {
            $this->option($post);
        }
        if (isset($post['folder_Id'])) {
            $this->folderId($post);
        }
        if (isset($post['fl_id'])) {
            $this->flId($post);
        }
        if (isset($post['ag_name'])) {
            $this->agName($post);
        }
        if (isset($post['ag_id'])) {
            $this->agId($post);
        }
        if (isset($post['ins_text'])) {
            $this->insText($post);
        }
        if (isset($post['lookup_title'])) {
            $this->getParentLookup($post);
        }
    }
    
    /**
     * Invokes required template
     *
     * @param ServerRequestInterface $request  server-side request.
     * @param ResponseInterface      $response response to client side.
     * @param callable               $next     CallBack Handler.
     *
     * @return HtmlResponse
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        if (isset($_GET['autofor'])) {
            $autofor = $_GET['autofor'];
            if (isset($_GET['term'])) {
                $search_term = $_GET['term'];
                $rows = $this->getAutoSuggest($autofor, $search_term);
            }
            echo json_encode($rows);
            exit;
        }
        
        $post = [];
        if ($request->getMethod() == 'POST') {
            $post = $request->getParsedBody();
            $this->doPost($post);
        }
    }
}
