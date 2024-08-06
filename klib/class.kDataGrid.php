<?php
/**
 * class.kDataGrid.php - Kytoo Data Grids Component
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.6
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
 * 2009-05-18 (1.6) - Updated class for Kytoo Admin support and new standard graphics 
 * 2009-05-15 (1.6) - Added styles, filterfull, row filters, new sort, paging fixes, bugs + tweaks 
 * 2009-05-01 (1.5) - Class now extends kBase. Updated for Kytoo 2.0.  
 * (1.4) - Added pager support
 * (1.2) - Added recursive data grid support
 * (1.0) - Initial release
 *
*/

class kDataGrid extends kBase
{
    /**
     * @access  public
     * @var     array $cols         List of grid columns
    */
    var $cols = array();
    var $sql = '';
    var $sort = '';
    var $order = '';

    var $lookups = array();
    var $hooks = array();
    
    var $dir_base = '';

    var $name = 'kGrid';

    var $row_alt = '2';

    /* Display Options */    
    var $class_table = 'grid';
    var $class_th1 = 'tblhead1';
    var $class_th2 = 'tblhead2';
    var $class_row = 'tblrow1';
    var $color_row1 = '#fbfbfb';
    var $color_row2 = '#ebf7fe';
    var $style_width = '100%';
    var $style_row_class = 'tblrow';
    var $style_row_classh = 'tblrowh';
    var $style_row_classalt = true;
    var $style_cell_class = 'tblcell';
    var $style_corners = true;
    var $style = 'thdb';
    var $style_corners_left = '';
    var $style_corners_right = '';
    var $pager = true;
    var $pager_top = false;
    var $pager_bottom = 'right';
    var $pager_range = 10;
    var $pager_focus = 3;
    var $show_msg = true;
    
    /* Sorting Options */
    var $sort_icon = '';
    var $sort_iconb_desc = '';
    var $sort_iconb_asc = '';
    var $lang_browse_msg = 'Showing %s-%s of %s records';
    var $form_options = false;
    var $filter_rowext = false;
    var $filter_row = false;
    
    /* Data Options & Filtering */
    var $start = 0;
    var $limit = 100;
    var $page = 1;
    var $total = 0;
    var $action = '';
    var $submit = '';
    var $showcount = true;
    var $filters = array();
    var $primary_col = false;
    
    /* URL Params */
    var $param_sort = 'k_gs';
    var $param_order = 'k_go';
    
    /**
     * @access  private
     * @var     array $default_col      Default settings for a new column
    */
    var $default_col = 
        array(
        'title' => '',
        'sortable' => true,
        'filter' => false,
        'type' => false,
        'url' => false,
        'text' => false,
        'wrap' => false,
        'cellstyle' => false,
        'colstyle' => false,
        'width' => false,
        'rich' => true,
        'selectall' => true,
        'calign' => 'left'
    );
    
    function kDataGrid(&$db, $dir_base, $primary = '', $parent = '', $name = '')
    {
        $this->db =& $db;
        $this->dir_base = $dir_base;
        $this->parent = $parent;
        $this->primary = $primary;
        $this->name = $name;
        return true;
    }
    
    function addHook($field, $hook)
    {
        $this->hooks[$field] = $hook;
    }
    
    function addSubmit($submit)
    {
        $this->submit = $submit;
        return true;
    }
    
    function addFormAction($action, $title)
    {
        $this->addFormOption('a', $action, $title);
        return true;
    }
    function addFormOption($name, $value, $title)
    {
        $this->form_options[$name][$value] = $title;
        return true;
    }
    
    function setLookup($field, $values)
    {
        $this->lookups[$field] = $values;
        return true;
    }
    
    /**
     * bool addColumns(string name, mixed columns)
     *
     * Add a column to the grid.
    */
    function addColumn($name, $fields)
    {
        if(is_array($fields))
        {
            if(empty($fields['field'])) $fields['field'] = $name;
            $this->cols[$name] = array_merge($this->default_col, $fields);
            return true;
        }
        else
        {
            return false;
        }
        
    }
    
    /**
     * bool addColumns(string name, mixed columns)
     *
     * Add multiple columns to a grid.
    */
    function addColumns($columns)
    {
        if(is_array($columns))
        {
            foreach($columns as $name => $fields)
            {
                $this->addColumn($name, $fields);
            }
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * render(bool $return) - Displays the data grid
     *
     * @access      public
     * @param       string $path Absolute path
     * @return      bool
    */
    function display($return = 1, $sort = '', $order = '', $parent_id = 0, $pager = false)
    {
        $this->sort_icon = '{dir_tpl_images}' . $this->style . '-sortb.gif';
        $this->sort_iconb_desc = '{dir_tpl_images}' . $this->style . '-sortb-down.gif';
        $this->sort_iconb_asc = '{dir_tpl_images}' . $this->style . '-sortb-up.gif';
        $this->style_corners_left = '{dir_tpl_images}' . $this->style . '-left.gif';
        $this->style_corners_right = '{dir_tpl_images}' . $this->style . '-right.gif';

        $index = 1;
        $grid = $browse_msg = $sform = $fform = '';
        
        $grid = '<div class="' . $this->class_table . '">';
        
        $totalcount = $this->getCount($return, $sort, $order, $parent_id);
        $gt_width = (empty($this->style_width)) ? '' : ' width="' . $this->style_width . '"';

        if($this->pager)
        {
            if($sort)
            {
                $sort_safename = (!empty($this->cols[$sort]['safename'])) ? $this->cols[$sort]['safename'] : $sort;
            }
            $pagercode = $this->getPager($totalcount, $this->limit, $this->page, $this->dir_base . 'sort=' . $sort_safename . '&order=' . $order . '&limit=' . $this->limit . '&page=%s');
        }
        
        if($this->showcount)
        {
            $pagecount = ($totalcount < $this->limit) ? $totalcount : $this->limit;
            $pagemax = ($totalcount < ($this->page*$pagecount)) ? $totalcount : ($this->page*$pagecount);
            if($totalcount == 0)
            {
                $browse_msg .= 'No records found';
                $this->browse_title = $browse_msg;
            }
            else
            {
                $rstart = (($this->limit*($this->page-1))+1);
                $browse_msg .= sprintf($this->lang_browse_msg, $rstart, $pagemax, $totalcount);
                $this->browse_title = $browse_msg;
            }
        }
        if(!empty($_REQUEST['k_filter_field']))
        {
            $browse_msg .= ' <b>(Filtered results)</b> ';
        }
        elseif(!empty($_REQUEST['k_search']))
        {
            $browse_msg .= ' <b>(Search results)</b> ';
        }
        // Display filters
        if(!empty($this->filter['name']))
        {
            $fform = '<form class="kForm" action="' . $this->dir_base . '" method="post">' .
                     '<table' . $gt_width . ' cellpadding="4px" cellspacing="0px" border="0px"><tr>' .
                     '<td align="left" nowrap><div class="kGridForm">' . $this->filter['name'] . ':</div></td>' .
                     '<td align="left" nowrap><div class="kGridForm"><select id="k_filter" name="k_filter">' .
                     '<option value="all"> - Show All - </option>';
            foreach($this->filter['values'] as $key => $field)
            {
                $fform .= '<option value="' . $key . '">' . $field . '</option>';
            }
            $fform .= '</select></div></td><td align="left" nowrap>' .
                      '<div class="kGridForm"><input type="submit" value="Go" onClick="javascript:if(document.getElementById(\'k_filter\').value == \'\') return false;" /></div></td>' .
                      '<td><input type="hidden" name="k_filter_field" value="' . $this->filter['field'] . '" /></td>' .
                      '</td></tr></table></form>';
        }
                
        // Display search field
        if(is_array($this->searchform))
        {
            $sform = '<form class="kForm" action="' . $this->dir_base . '" method="post">' .
                     '<table' . $gt_width . ' cellpadding="4px" cellspacing="0px" border="0px"><tr>' .
                     '<td align="left" nowrap><div class="kGridForm"><input type="text" name="k_search" size="16" /></div></td>' .
                     '<td align="left" nowrap><div class="kGridForm"><input type="submit" value="Search" /></div></td><td>';
            foreach($this->searchform as $key => $field)
            {
                $sform .= '<input type="hidden" name="k_search_field[]" value="' . $key . '" />';
            }
            $sform .= '</td></tr></table></form>';
        }

        $grid .= '<table' . $gt_width . ' cellpadding="0px" cellspacing="0px" border="0px"><tr>';
        if($this->show_msg)
        {
            $grid .= '<td width="100%" align="left"><div class="kGridMsg">' . $browse_msg . ' </div></td>';
        }
        if(!empty($fform)) $grid .= '<td align="right" nowrap>' . $fform . ' </td>';
        if(!empty($sform)) $grid .= '<td align="right" nowrap>' . $sform . ' </td>';
        if(($this->pager) && (!empty($this->pager_top))) $grid .= '<td align="right" nowrap>' . $pagercode . ' </td>';
        $grid .= '</tr></table>';
        
        $grid .= '<form class="kForm" action="' . $this->dir_base . '" method="post" name="' . $this->name . '" >';
        $grid .= '<table' . $gt_width . ' cellpadding="4px" cellspacing="0px" border="0px" class="' . $this->class_table . '">';
        // Display column headings
        $grid .= '<tr>';

        if($this->style_corners)
        {
            $grid .= '<th width="12px" nowrap align="left" class="' . $this->class_th1 . '"><img src="' . $this->style_corners_left . '" /></th>';
        }
        foreach($this->cols as $col_field=>$col_title)
        {
            $ghs = $colwidth = '';
            $sort_icon = $this->sort_icon;            
            $colwidth = ($col_title['width']) ? ' width="' . $col_title['width'] . '"' : '';

            $sort_dir = 'desc';
            $sort_icon_ext = '';

            if(is_array($col_title))
            {
                $col_fieldname = (!empty($col_title['safename'])) ? $col_title['safename'] : $col_title['field'];
                $colstyle = '';
                if(($col_title['type'] == 'checkbox') && ($col_title['selectall'] == true))
                {
                    $ghs = '<a href="#" onClick="javascript:k_frmSetCheckbox(\'' . $this->name . '\',\'' . $col_field . '[]\');this.blur();">' .
                           '<img src="{dir_tpl_images}kf-checkall-dk.gif" alt="Check/Uncheck All" border="0px" /></a>';
                    $col_title['calign'] = 'center';
                    $gtitle = $ghs;
                    $colstyle = ' style="padding-right:4px;"';
                }
                elseif($col_title['sortable'] == true)
                {
                    if($sort == $col_title['field'])
                    {
                        $sort_dir = ($order == 'asc') ? 'desc' : 'asc';
                        $sort_icon = ($sort_dir == 'asc') ? $this->sort_iconb_desc : $this->sort_iconb_asc;
                    }

                    $gtitle = '<a class="tbl-sort" href="' . $this->dir_base . 'sort=' . $col_fieldname . '&order=' . $sort_dir . '&limit=' . $this->limit . '">' . $col_title['title'] . '<img src="' . $sort_icon . '" border="0px" alt="Sort" title="Sort" /></a>';
                }
                else
                {
                    $gtitle = $col_title['title'];
                }
                if(!$gtitle) $gtitle = $col_title['title'];
                $grid .= '<th nowrap class="' . $this->class_th1 . '"' . $colwidth . ' align="' . $col_title['calign'] . '" ' . $colstyle . '>' . $gtitle . '</th>';
            }
            elseif(substr($col_field,0,1) == ':')
            {
                $grid .= '<th nowrap class="' . $this->class_th1 . '"' . $colwidth . '></th>';
            }
            elseif((substr($col_field,0,1) == '+') || (substr($col_field,0,1) == '='))
            {
                $col_field = substr($col_field,1);
                $grid .= '<th nowrap class="' . $this->class_th1 . '"' . $colwidth . '></th>';
            }
            else 
            {
                  $gtitle = '<a class="tbl-sort" href="' . $this->dir_base . 'sort=' . $col_title . '&order=' . $sort_dir . '&limit=' . $this->limit . '">' . $col_title . '<img src="' . $sort_icon . '" border="0px" alt="Sort" title="Sort" /></a>';
//                $grid .= '<th nowrap class="' . $this->class_th1 . '" onMouseOver="this.className=\'' . $this->class_th2 . '\'" onMouseOut="this.className=\'' . $this->class_th1 . '\'">' . $col_title . ' <a href="' . $this->dir_base . 'sort=' . $col_field . '&order=asc"' . $colwidth . '><img src="' . $this->sort_icondown . '" border="0px" alt="Sort Ascending" title="Sort Ascending" /></a><a href="' . $this->dir_base . 'sort=' . $col_field . '&order=desc"><img src="' . $this->sort_iconup . '" border="0px" alt="Sort Descending" title="Sort Descending" /></a></th>';
            }
            if($sort == $col_title['field'])
            {
                $sort_fieldname = (!empty($col_title['safename'])) ? $col_title['safename'] : $col_title['field'];
            }
        }
        if($this->style_corners)
        {
            $grid .= '<th width="12px" align="right" nowrap class="' . $this->class_th1 . '"><img src="' . $this->style_corners_right . '" /></th>';
        }

        $grid .= '</tr>';
        $grid .= $this->getRow($return, $sort, $order, $parent_id);
        $grid .= '</table>';

        $grid .= '<table' . $gt_width . ' cellpadding="0px" cellspacing="0px" border="0px"><tr>';
        // Show form options if defined
        if($this->submit)
        {
            $grid .= '<td nowrap align="left"><div class="kGridForm">With selected:</div></td>';
            
            if(is_array($this->form_options))
            {
                foreach($this->form_options as $key => $value)
                {
                    $grid .= '<td nowrap><div class="kGridForm"><select name="' . $key . '">';
                    foreach($value as $key2 => $value2)
                    {
                        if(is_array($value2))
                        {
                            foreach($value2 as $key3 => $value3)
                            {
                                $grid .= '<option value="' . $key2 . '">' . $value3 . '</option>';
                            }
                        }
                        else
                        {
                            $grid .= '<option value="' . $key2 . '">' . $value2 . '</option>';
                        }
                    }
                    $grid .= '</select></div></td>';
                }
            }
            $grid .= '<td nowrap width="100%" align="left"><div class="kGridForm"><input type="submit" value="' . $this->submit . '" /></div></td>';
        }

        if(($this->pager) && (!empty($this->pager_bottom))) $grid .= '<td align="right" nowrap>' . $pagercode . ' </td>';

        $grid .= '</tr></table>';
        
        $grid .= '</form></div>';
        
        return $grid;

    }

    function getRow($return = 1, $sort = '', $order = '', $parent_id = 0)
    {
        // Set up static variables for recursive grids
        static $index;
        static $depth = 0;
        static $grid;
        static $rowalt = '';
        
        $sql = $this->getSql($this->sql, $sort, $order, $parent_id);
        $depth++;

        // Run query
        if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
        {
            while(!$rs->EOF)
            {
            
                // Check for recursiveness
                if($this->parent)
                {
                    // Check this record for children
                    $sqlc = $this->sql . ' WHERE ' . $this->parent . ' = ' . $rs->fields[$this->primary];
                    if(($rsc = $this->db->execute($sqlc)) && (!$rsc->EOF))
                    {
                        $has_children = true;
                    }
                    else
                    {
                        $has_children = false;
                    }
                }
                // Loop through rows
                $gr = '';
                $tab = '';
                $rowext = '';
                
                if($depth > 1)
                {
                    for($i=1;$i<$depth;$i++)
                    {
                        $tab .= '&nbsp; ';
                    }
                }
                
                // Row filters - pass row values and get back array of settings for row
                if((!empty($this->filter_row)) && (function_exists($this->filter_row)))
                {
                    $rowfilter = call_user_func($this->filter_row, $rs->fields);
                }
                $rowbg = (!empty($rowfilter['bgcolor'])) ? $rowfilter['bgcolor'] : $this->style_row_bgcolor;
                $rowbg2 = (!empty($rowfilter['bgcolor2'])) ? $rowfilter['bgcolor2'] : $this->style_row_bgcolor2;
                $rowbgh = (!empty($rowfilter['bgcolorh'])) ? $rowfilter['bgcolorh'] : $this->style_row_bgcolorh;
                
                foreach($this->hooks as $hook_field => $hook)
                {
                    if(isset($rs->fields[$hook_field]))
                    {
                        $rs->fields[$hook_field] = call_user_func($hook, $rs->fields[$hook_field]);
                    }                
                }    
                $indexc = 0;
                // Loop through columns
                foreach($this->cols as $col_field=>$col_value)
                {
                    $gc = $gcv = $gct = $wrap = $target = $classext = $colclass = '';

                    $indexc++;
                    
                    // Check for array - new list format
                    if(is_array($col_value))
                    {
                        $colwrap = ($col_value['wrap']) ? ' ' . $col_value['wrap'] : '';
                        $classext = ($col_value['classext']) ? $col_value['classext'] : '';
                    
                        if($col_value['type'] == 'checkbox')
                        {
                            $colclass = ($col_value['rich']) ? ' class="kFormRichElement"' : '';
                            $gct = '<input type="checkbox" ' . $colclass . ' name="' . $col_field . '[]" value="' . $rs->fields[$col_field] . '" id="' . $col_field . '_{' . $col_field . '}" />';
                        }
                        elseif(!empty($col_value['text']))
                        {
                            $gct = $col_value['text'];
                        }
                        else
                        {
                            $gct = $rs->fields[$col_value['field']];
                        }
                        
                        if(($col_value['lookup']) && ($col_value['lookup'][$rs->fields[$col_value['field']]]))
                        {
                            $gct = $col_value['lookup'][$rs->fields[$col_value['field']]];
                        }
                        elseif(!empty($col_value['filter']))
                        {
                            if(function_exists($col_value['filter']))
                            {
                                $gct = call_user_func($col_value['filter'], $gct);
                            }
                        }
                        elseif(!empty($col_value['filterfull']))
                        {
                            if(function_exists($col_value['filterfull']))
                            {
                                $gct = call_user_func($col_value['filterfull'], $rs->fields);
                            }
                        }

                        $ctab = ($col_field == $this->name) ? $tab : '';

                        if(!empty($col_value['href']))
                        {
                            if($col_value['target']) $target = ' target="' . $col_value['target'] . '"';
                            $gcv = $ctab . '<a href="' . $col_value['href'] . '"' . $target . '>' . $gct . '</a>';
                        }
                        else
                        {
                            $gcv = $ctab . $gct;                        
                        }
                        $cellalign = (!empty($col_value['halign'])) ? ' align="' . $col_value['halign'] . '"' : '';
                        $cellclass = (!empty($this->style_cell_class)) ? ' class="' . $this->style_cell_class . $classext . '"' : '';
                        $colspan = (($this->style_corners) && (($indexc == 1) || ($indexc == count($this->cols)))) ? ' colspan="2"' : '';
                        $colstyle = '';
                        if(($this->style_corners) && ($indexc == 1) && ($col_value['type'] == 'checkbox'))
                        {
                            $colstyle = ' style="padding-left:10px;"';
                        }
                        elseif(($this->style_corners) && ($indexc == count($this->cols)) && ($col_value['type'] == 'checkbox'))
                        {
                            $colstyle = ' style="padding-right:10px;"';
                        }
                        $gc = '<td' . $cellclass . $colspan . $cellalign . $colstyle . $colwrap . '>' . $gcv . '</td>';
                        
                        $gc = str_replace('{k_grid_field_value}', $rs->fields[$col_value['field']], $gc);
                        
                        // Loop through row data and replace fields in column value
                        foreach($rs->fields as $row_field=>$row_data)
                        {
                            $gc = str_replace('{' . $row_field . '}', $row_data, $gc);
                        }
       
                        $gr .= $gc;
                    }
                    else
                    {
                        if((!empty($this->lookups[$col_field])) && (is_array($this->lookups[$col_field])))
                        {
                            $rs->fields[$col_field] = $this->lookups[$col_field][$rs->fields[$col_field]];
                        }
                        
                        // Check for link identifier `:`
                        // Links replace all {fieldname} values with their value from row data
                        if(substr($col_field,0,1) == '=')
                        {
                            $col_field = substr($col_field, 1);
                            if(isset($col_value[$rs->fields[$col_field]]))
                            {
                                $col_value = $col_value[$rs->fields[$col_field]];
                            }
                            // Loop through row data and replace fields in column value
                            foreach($rs->fields as $row_field=>$row_data)
                            {
                                $col_value = str_replace('{' . $row_field . '}', $row_data, $col_value);
                            }
                            $gc = $col_value;
                            
                        }
                        elseif(substr($col_field,0,1) == ':')
                        {
                            // Loop through row data and replace fields in column value
                            foreach($rs->fields as $row_field=>$row_data)
                            {
                                $col_value = str_replace('{' . $row_field . '}', $row_data, $col_value);
                            }
                            $gc = $col_value;
                        }
                        elseif($col_field == $this->name)
                        {
                            $tab .= ($has_children) ? ' ' : ' ';
                            $gc = $tab . $rs->fields[$col_field];                    
                        }
                        else 
                        {
                            $gc = $rs->fields[$col_field];
                        }
                        $gr .= '<td class="' . $this->class_row . '">' . $gc . '</td>';
                    }
                }
                
                if(!empty($this->row_alt))
                {
                    $rowalt = ($rowalt == $this->row_alt) ? '' : $this->row_alt;
                }
                if(!empty($this->style_row_class))
                {
                    $rowclass = $this->style_row_class . $rowalt . $rowfilter['ext'];
                    $gclass = 'class="' . $rowclass . '" ';
                } 
                if(!empty($this->style_row_classh))
                {
                    $rowclassh = $this->style_row_classh . $rowalt . $rowfilter['ext'];
                    // Changing row className on TR is super laggy in Firefox 3.0.10
//                    $ghover = 'onMouseOver="this.className=\'' . $rowclassh . '\';" onMouseOut="this.className=\'' . $rowclass . '\';" ';
                    $ghover = 'style="background-color:' . $rowbg . '" onMouseOver="this.style.backgroundColor=\'' . $rowbgh . '\';" onMouseOut="this.style.backgroundColor=\'' . $rowbg . '\';" ';                    
                } 
                
                $grid .= '<tr ' . $gclass . $ghover . '>';
                $grid .= $gr;
                $grid .= '</tr>';
 
                if($this->parent)
                {
                    $this->getRow($return, $sort, $order, $rs->fields[$this->primary]);
                }

                $rs->MoveNext();
            }
            
        }
        $depth--;
               
        return $grid;
    }
    
    function getCount($return = 1, $sort = '', $order = '', $parent_id = 0)
    {
        // Set up static variables for recursive grids
        static $index = 0;
        static $depth = 0;
        static $grid = '';
        static $rs_count = 0;
        
        $count_sql = str_replace('SELECT *', 'SELECT COUNT(' . $this->primary_col . ') AS rs_count', $this->sql);
        $sql = $this->getSql($count_sql, $sort, $order, $parent_id, true);

        $depth++;
        
        // Run query
        if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
        {
            if($parent_id)
            {
                while(!$rs->EOF)
                {
                    // Check for recursiveness
                    if($this->parent)
                    {
                        // Check this record for children
                        $sqlc = $this->sql . ' WHERE ' . $this->parent . ' = ' . $rs->fields[$this->primary];
                        if(($rsc = $this->db->execute($sqlc)) && (!$rsc->EOF))
                        {
                            $has_children = true;
                        }
                        else
                        {
                            $has_children = false;
                        }
                    }
    
                    $rs_count++;
                    
                    if($this->parent)
                    {
                        $this->getCount($return, $sort, $order, $rs->fields[$this->primary]);
                    }
    
                    $rs->MoveNext();
                }
            }
            else
            {
                $rs_count = $rs->fields['rs_count'];
            }
        }
        $depth--;
               
        return $rs_count;
    }
        
    function getPager($total, $limit, $page, $urlformat)
    {

        $pages = ($total >= $limit) ? ceil($total/$limit) : 1;
                        
        $page = intval($page);
        
        $offset = ($limit * $page) - $limit;
    
        $pager = '<table align="right" cellpadding="0px" cellspacing="1px" border="0px" class="pager-table"><tr>' .
                 '<td class="pager-page" nowrap>Page ' . $page . ' of ' . $pages . '</td>';
        if($page > 1)
        {
            $pager .= '<td class="pager-link"><a href="' . sprintf($urlformat, 1) . '"><b>&laquo;</b></a></td>';
            if($page == 2)
            {
                $pager .= '<td class="pager-link"><a href="' . sprintf($urlformat, 1) . '">&lt;</a></td>';
            }
            else 
            {
                $pager .= '<td class="pager-link"><a href="' . sprintf($urlformat, $page - 1) . '">&lt;</a></td>';    
            }
        }
        for($i=1;$i<=$pages;$i++)
        {
            if($i == $page)
            {
                $pager .= '<td class="pager-current"><b>' . $i . '</b></td>';
            }
            else 
            {
                if($i == 1)
                {
                    $pager .= '<td class="pager-link"><a href="' . sprintf($urlformat, 1) .'">' . $i . '</a></td>';
                }
                else 
                {
                    $pager .= '<td class="pager-link"><a href="' . sprintf($urlformat, $i) . '">' . $i . '</a></td>';
                }
            }
        }
        if($pages > $page)
        {
            $pager .= '<td class="pager-link"><a href="' . sprintf($urlformat, $page+1) . '">&gt;</a></td>';
            $pager .= '<td class="pager-link"><a href="' . sprintf($urlformat, $pages) . '"><b>&raquo;</b></a></td>';
        }
        $pager .= '</tr></table>';
        
        return $pager;
    }
    
    function getSql($sql, $sort, $order, $parent_id = false, $count = false)
    {
        if($this->parent)
        {
            $criteria = $this->parent . ' = ' . $parent_id;
        }
        if($this->searchform)
        {
            if((!empty($_REQUEST['k_search'])) && (!empty($_REQUEST['k_search_field'])))
            {
                foreach($_REQUEST['k_search_field'] as $key => $value)
                {
                    $s_criteria[] = $value . ' LIKE "%' . $_REQUEST['k_search'] . '%"';
                }
                
                if((!empty($criteria)) && (sizeof($s_criteria) > 0))
                {
                    $criteria .= ' AND (' . implode(' OR ', $s_criteria) . ')';
                }
                elseif(sizeof($s_criteria) > 0)
                {
                    $criteria .= implode(' OR ', $s_criteria);
                }
            }
        }
        
        if(!empty($_REQUEST['k_filter_field']))
        {
            if($_REQUEST['k_filter'] !== 'all')
            {
                $k_filter = (is_numeric($_REQUEST['k_filter'])) ? $_REQUEST['k_filter'] : '"' . $_REQUEST['k_filter'] . '"';
                if(!empty($criteria))
                {
                    $criteria .= ' AND ';
                }
                $criteria .= ' ' . $_REQUEST['k_filter_field'] . ' = ' . $k_filter;
            }
            else
            {
                $_REQUEST['k_filter_field'] = '';
            }
        }
        
        if($criteria) $sql .= ' WHERE ' . $criteria;
        
        if((!empty($sort)) && (!empty($order)))
        {
            $sql .= ' ORDER BY ' . $sort . ' ' . $order;
        }
        $start = ($this->limit * $this->page) - $this->limit;
        
        if(!$count)
        {
            $sql .= ' LIMIT ' . $start . ',' . $this->limit;
        }

        return $sql;
    }
}