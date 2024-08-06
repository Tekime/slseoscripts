<?php
/**
 * class.kMenu.php - Kytoo Menu Component
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.1
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR 
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND 
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * 2009-05-01 (1.1) - Class now extends kBase. Updated for Kytoo 2.0.  
 *
 * Usage:
 *
 *  $mymenu = new kMenu('page', 'menu', 'menu_row', 0);
 *  $mymenu->set_active($pageid);
 *  $mymenu->build_tree();
 *  $mymenu->parse(0); 
*/

class kMenu extends kBase
{
    var $block;
    var $block_menu;
    var $block_row;

    var $table;
    var $id;
    var $parent_id;
    
    var $href;
    var $href_field;
    var $level;
    var $levels;
    var $is_active = 'pg_is_active';
    var $is_hidden = 'pg_is_hidden';
    
    function menu($block, $menu_block, $menu_row, $parse = 0)
    {
        $this->block = $block;
        $this->menu_block = $menu_block;
        $this->menu_row = $menu_row;
        
        $this->active = 0;
        $this->depth = 2;
        $this->prefix = DIR_BASE . 'en/';
        $this->url_sep = '/';
        $this->url_long = 1;
        $this->levels = array();
        $this->level = 1;
        
        if($parse == 1)
        {
            $this->tpl->define_d($this->menu_block, $this->block);
            $this->tpl->define_d($this->menu_row, $this->menu_block);
		}
        return true;    
    }
    function set_fields($primary, $parent, $id, $title, $sort = 0, $order = 0)
    {
        $this->primary = $primary;
        $this->parent = $parent;
        $this->id = $id;
        $this->title = $title;
        $this->sort = $sort;
        $this->order = $order;        
    }
    function set_top($top)
    {
        $this->top = $top;
        return true;
    }
    function set_depth($depth)
    {
        $this->depth = $depth;
        return true;
    }
    function set_active($active)
    {
        $this->active = $active;
        return true;
    }
    function add_item($fields)
    {
        $this->tree[$fields[$this->primary]] = $fields;
    }
    
    function add_items($fields)
    {
        foreach($fields as $k=>$v)
        {
            $this->tree[$v[$this->primary]] = $v;
        }
    }
    
    /**
     * bool build_tree()
     *
     * This function queries the DB for all possible menu entries and organizes
     * them into an array. Doing it this way saves queries; in the case of large menus,
     * LOTS of them.
    */
    function build_tree($table)
    {
        $sql = 'SELECT '.$this->primary.', '.$this->parent.', '.$this->id.', '.$this->title .
               ', ' . $this->is_hidden;
        if(isset($this->sort))
            $sql .= ', ' . $this->sort . ' ';
        else
            $sql .= ' ';
               
        $sql .= 'FROM ' . $table . ' ';
        if(isset($this->is_active))
        {
            $sql .= 'WHERE ' . $this->is_active . ' = 1 ';
        }
        $sql .= 'ORDER BY ' . $this->primary . ' ASC';

        if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
        {
            while(!$rs->EOF)
            {
                $this->add_item($rs->fields);

                $rs->MoveNext();
            }
        }
        if(!empty($this->debug))
        {
            echo '<pre>';
            print_r($this->tree);
            echo '</pre>';
        }
    }
    
    function get_children($primary, $field = 0)
    {
        /* Must update sorting to include sort field and any additional fields
           without disrupting current structure
        */
        if(!isset($this->tree)) return false;
        foreach($this->tree as $k=>$v)
        {
            if($v[$this->parent] == $primary)            
                $list[] = $v;
        }
        if(isset($list))
        {
            if(isset($field))
            {
                foreach($list as $k=>$v)
                {
                    $sortarray[] = $v[$field];
                }
                array_multisort($sortarray,$list);
            }
            foreach($list as $k=>$v)
            {
                $children[] = $v[$this->primary];        
            }
        }
        if(!empty($children))
        {
            return $children;
        }
        else
        {
            return false;
        }
    }

    function is_child($parent, $child)
    {
        $children = $this->get_children($parent, $this->sort);

        if(!empty($children))
        {
            foreach($children as $k=>$v)
            {
                if($v == $child)
                {
                    return true;
                }
                else
                {
                    if($this->is_child($v, $child) == true)
                    {                   
                        return true;
                    }
                }
            }
        }
        else
        {
            return false;
        }

    }

    /**
     * bool parse($id)
     *
     * Parses the menu structure to template
    */
    function parse($id)
    {
        static $depth;
        static $level = 1;
        static $levels = array();

        // Get all children of current ID
        if($rows = $this->get_children($id, $this->sort))
        {
            // Loop through children
            foreach($rows as $primary => $row)
            {
                if((empty($this->tree[$row][$this->is_hidden])) && (empty($this->tree[$row]['hidden'])))
                {
                    $url = $this->prefix;
                    
                    if(isset($this->tree[$row]['url']))
                    {
                        $url = $this->tree[$row]['url'];
                    }
                    elseif(($this->url_long === 1))
                    {
                        if(isset($levels)) {
                            for($i=1;$i<$level;$i++)
                            {
                                $url .= $this->tree[$levels[$i]][$this->id] . $this->url_sep;
                            }                   
                        }
                        if(substr($url, 0, 1) !== '/')
                            $sep .= $this->url_sep;
                        else
                            $sep = '';
                        $url = $url . $this->tree[$row][$this->id];
                    }
                    else
                    {
                        $url = $this->prefix . $this->tree[$row][$this->id];
                    }
    
    
                    if(($this->active == $row) || (($this->is_child($row, $this->active)) === true))
                    {
                        $this->tpl->assign_d($this->menu_row, 'row_id', 'l' . $level .'a');
                    }
                    else
                    {
                        $this->tpl->assign_d($this->menu_row, 'row_id', 'l' . $level);                      
                    }
                    $this->tpl->assign_d($this->menu_row, 'row_href', $url);
                    $this->tpl->assign_d($this->menu_row, 'row_text', $this->tree[$row][$this->title]);
                    $this->tpl->parse_d($this->menu_row);
                    
                    if(($this->active == $row) || ($this->is_child($row, $this->active) === true))
                    {
                        $levels[$level] = $row;
                        $level++;
                        $this->parse($row);
                        $level--;
                    }
                }
            }
        }
    }
}
?>
