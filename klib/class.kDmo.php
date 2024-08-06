<?php
/**
 * class.kDmo.php - Kytoo Complex Data Manipulation Object
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.0
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
 * 2009-05-01 (1.0) - Class now extends kBase. Updated for Kytoo 2.0.  
 * 2005-10-25 (0.9.6) - First release for custom project.
 *
*/

class kDmo extends kBase {

    /**
     * @access  public
     * @var     array $errors            List of paths where template files are located
    */
    var $errors = array();
    
    /**
     * @access  public
     * @var     string $name            'form' = Form style, 'report' = Report style
    */
    var $name = '';
    /**
     * @access  public
     * @var     string $templates
    */
    var $template = '';
    /**
     * @access  public
     * @var     array $locks
    */
    var $locks = array();
    /**
     * @access  public
     * @var     string $table
    */
    var $table;
    /**
     * @access  public
     * @var     string $id_col
    */
    var $id_col;
    /**
     * @access  public
     * @var     string $prefix
    */
    var $prefix = 'dmo_';
    /**
     * @access  public
     * @var     string $field_prefix
    */
    var $field_prefix = '';
    /**
     * @access  public
     * @var     string $sql
    */
    var $sql = '';
    /**
     * @access  public
     * @var     array $list
    */
    var $list = array();
    /**
     * @access  public
     * @var     array $dropdowns
    */
    var $dropdowns = array();
    /**
     * @access  public
     * @var     array $lookups
    */
    var $lookups = array();
    /**
     * @access  public
     * @var     array $fields
    */
    var $fields = array();
    /**
     * @access  public
     * @var     array $hooks
    */
    var $hooks = array();
    /**
     * @access  public
     * @var     string $message
    */
    var $message = '';
    /**
     * @access  public
     * @var     string $select
    */
    var $select = '';
    /**
     * @access  public
     * @var     array $subtemplates
    */
    var $subtemplates = array();
    /**
     * @access  public
     * @var     array $checkboxes
    */
    var $checkboxes = array();
    /**
     * @access  public
     * @var     array $radiobuttons
    */
    var $radiobuttons = array();
    /**
     * @access  public
     * @var     array $searchFields
    */
    var $searchFields = array();
    /**
     * @access  public
     * @var     array $dateformats
    */    
    var $dateformats = array();
    /**
     * @access  public
     * @var     array $dynamics
    */
    var $dynamics = array();
    /**
     * @access  public
     * @var     array $forceInsert
    */
    var $forceInsert = array();
    /**
     * @access  public
     * @var     string $where
    */
    var $where;
    /**
     * @access  public
     * @var     string $orderby
    */
    var $orderby;
    /**
     * @access  public
     * @var     string $order
    */
    var $order = 'ASC';
    /**
     * @access  public
     * @var     int $offset
    */
    var $offset = 0;
    /**
     * @access  public
     * @var     int $limit
    */
    var $limit = 30;
    /**
     * @access  public
     * @var     string $html_first
    */
	var $html_first = '|<<';
    /**
     * @access  public
     * @var     string $html_prev
    */
	var $html_prev = '<<';
    /**
     * @access  public
     * @var     string $html_next
    */
	var $html_next = '>>';
    /**
     * @access  public
     * @var     string $html_last
    */
	var $html_last = '>>|';
    /**
     * @access  public
     * @var     string $pager_class
    */
	var $pager_class = 'rsPager';
    /**
     * @access  public
     * @var     string $title
    */
    var $title;
    /**
     * @access  public
     * @var     string $item_name
    */
    var $item_name;
    /**
     * @access  public
     * @var     string $items_name
    */
    var $items_name;

    /**
     * @access  public
     * @var     string $no_image
    */
    var $no_image = 'transparent.gif';
    
    var $image_sm_maxwidth = 50;
    var $image_sm_maxheight = 36;
        
    /**
     * dmo - Class instance
    */
    function kDmo(&$_app, $name, $table, $id_col, $item_name, $items_name, $name_col = 0)
    {
        // Creates a local reference to all classes registered in the resourcepool
        foreach($_app->get_registered() as $method) {
            $this->$method =& $_app->$method;
        }
        $this->name = $name;
        $this->table = $table;
        $this->id_col = $id_col;
        $this->name_col = $name_col;
        $this->item_name = $item_name;
        $this->items_name = $items_name;
        
        return true;
    }

    /**
     * add_checkbox - Adds a checkbox element to the form
     *
     * @access      public
     * @return      bool
    */
    function add_checkbox($name, $default = 0)
    {
        $this->checkboxes[$name]['default'] = $default;
        return true;    
    }

    /**
     * add_criteria() - Adds criteria to the current form's data source query
     *
     * @access      public
     * @return      bool
    */
    function add_criteria($field, $criteria)
    {
        $this->criteria[$field] = $criteria;
    }

    /**
     * addCriteria() - Adds criteria to the form's recordsource
     *
     * @access      public
     * @return      bool
    */
    function addCriteria($field, $criteria)
    {
        $this->criteria[] = array('field' => $field, 'criteria' => $criteria);
        return true;
    }
    
    /**
     * add_dateformat() - Adds date formatting to specified field
     *
     * @access      public
     * @param       string $path Absolute path
     * @return      bool
    */
    function add_dateformat($field, $format)
    {
        $this->dateformats[$field] = $format;
        return true;
    }
    
    /**
     * add_dropdown - Adds a dropdown element to the form
     *
     * @access      public
     * @return      bool
    */
    function add_dropdown($type, $name, $default = 0, $data = 0, $filter = 0)
    {
        if($data !== 0)
        {
            $this->dropdowns[$name]['data'] = $data;
            $this->dropdowns[$name]['default'] = $default;
            $this->dropdowns[$name]['type'] = $type;
            $this->dropdowns[$name]['filter'] = $filter;
            return true;
        }
        elseif(array_key_exists($type, $this->lists->lists))
        {
            // Get default list data from $this->lists. Duplicating this here so we can
            // manipulate later on a per-dropdown basis if we desire.
            $this->dropdowns[$name]['data'] = $this->lists->lists[$type]['data'];
            $this->dropdowns[$name]['default'] = $default;
            $this->dropdowns[$name]['type'] = $type;
            return true;
        }
        else 
        {
            return false;
        }
        
    }
    
    /**
     * add_dynamic() - Adds a dynamic block to the form
     *
     * @access      public
     * @param       string $path Absolute path
     * @return      bool
    */
    function add_dynamic($name, $value)
    {
        $this->dynamics[$name] = $value;
        return true;
    }
    
    /**
     * add_field() - add_fields the form
     *
     * @access      public
     * @return      bool
    */
    function add_field($name, $type, $minlength, $maxlength, $required = 0)
    {
        $this->fields[$name]['type'] = $type;
        $this->fields[$name]['minlength'] = $minlength;
        $this->fields[$name]['maxlength'] = $maxlength;
        $this->fields[$name]['required'] = $required;
        return true;
    }
    
    /**
     * add_file() - Adds a file upload field to the form
     *
    */
    function add_file($field, $dest)
    {
        $this->files[$field] = $dest;
        return true;
    }
    
    /**
     * add_hook() - Adds a function hook to a field name.
     *
     * The current field value in list view is passed to the hook function 
     * and return value is assigned to the varname plus '_func'.
    */
    function add_hook($field, $function)
    {
        $this->hooks[$field] = $function;
        return true;
    }

    /**
     * add_image() - Adds a file upload field to the form
     *
    */
    function add_image($field, $dest, $thumb = 0, $db_insert = 0)
    {
        $this->images[$field]['dest'] = $dest;
        $this->images[$field]['thumb'] = $thumb;
        $this->images[$field]['db_insert'] = $db_insert;
        return true;
    }
    
    /**
     * add_lookup() - Adds a lookup field to form
     *
     * @access      public
     * @param       string $path Absolute path
     * @return      bool
    */
    function add_lookup($listname, $field)
    {
        $this->lookups[$field] = $listname;
        return true;
    }

   
    /**
     * add_radiobutton() - Adds a radio button element to the form
     *
     * @access      public
     * @return      bool
    */
    function add_radiobutton($name, $values, $default = 0)
    {
        $this->radiobuttons[$name]['values'] = $values;
        $this->radiobuttons[$name]['default'] = $default;
        return true;
    }
    
    /**
     * addSearchField() - Adds a field to list of search fields for auto-criteria generation in lists
     *
     * @access      public
     * @return      bool
    */
    function addSearchField($field)
    {
        $this->searchFields[] = $field;
        return true;
    }
    
    /**
     * addSearchFields() - Adds fields to list of search fields for auto-criteria generation in lists
     *
     * @access      public
     * @return      bool
    */
    function addSearchFields($fields)
    {
        foreach($fields as $key=>$value)
        {
            $this->searchFields[] = $value;
        }
        return true;
    }
    
    /**
     * addValidator() - Adds fields to list of search fields for auto-criteria generation in lists
     *
     * @access      public
     * @return      bool
    */
    function addValidator($field, $validator)
    {
        $this->validators[$field] = $validator;
        return true;
    }
    
    /**
     * define_d() - Define's a dynamic block in the form's template
     *
     * @access      public
     * @return      bool
    */
    function define_d($name)
    {
        $this->subtemplates[] = $name;
        return true;
    }

    /**
     * delete() - Deletes all records in $ids
     *
     * @access      public
     * @return      bool
    */
    function delete($ids)
    {
        foreach($ids as $key=>$value)
        {
            $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->id_col . ' = ' . $value;
            
            if(!$this->db->execute($sql))
            {
                $this->errors[] = $this->lang['action_delete_error'];
            }
        }
        if($this->errors)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * getPager() - Gets pager HTML for the current recordset
     *
     * @access      public
     * @return      bool
    */
	function getPager($url)
	{
	    // Search URL for ? and append & or ?
	    
		if(eregi('[?]', $url))
		{
			$url = $url . '&';
		}
		else 
		{
			$url = $url . '?';			
		}
		
        $sql = $this->params['select'] . $this->params['criteria'];
        $total = 0;

	    if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
	    {
	        $total = $rs->NumRows();
	    }

		if($this->offset > 0)
		{
			$html = '<a class="' . $this->pager_class . '" href="' . $url . 'offset=0&count=' . $this->limit .
					'&orderby=' . $this->orderby . '&order=' . $this->order . '&search=0">' .
					$this->html_first . '</a>&nbsp; ';
		}
		else 
		{
			$html = $this->html_first . '&nbsp; ';
		}

		if($this->offset - $this->limit > 0)
		{
			$html .= '<a class="' . $this->pager_class . '" href="' . $url . 'offset=' . ($this->offset - $this->limit) . 
			    	'&count=' . $this->limit . '&orderby=' . $this->orderby . '&order=' . $this->order .
			    	'&search=0">' . $this->html_prev . '</a>&nbsp; ';
		}
		else 
		{
			$html .= $this->html_prev . '&nbsp; ';
		}
		
		$viewing = '&nbsp;' . $this->offset . ' to ';

		if($this->offset + $this->limit > $total)
		{
    		$viewing .= $total;
		}
		else 
		{
    		$viewing .= $this->offset + $this->limit;		    
		}
		$viewing .= '&nbsp;';
        $viewing .= 'of ' . $total . ' ';
		
		$html .= $viewing;
		$this->viewing = 'View records ' . $viewing;
		
		if($this->offset + $this->limit < $total)
		{
			$html .= '<a class="' . $this->pager_class . '" href="' . $url . 'offset=' . ($this->offset + $this->limit) . 
				    '&count=' . $this->limit . '&orderby=' . $this->orderby . '&order=' . $this->order .
			    	'&search=0">' . $this->html_next . '</a>&nbsp; ';

			$html .= '<a class="' . $this->pager_class . '" href="' . $url . 'offset=' . ($total - $this->limit) . 
				    '&count=' . $this->limit . '&orderby=' . $this->orderby . '&order=' . $this->order .
			    	'&search=0">' . $this->html_last . '</a>';
		}
		else 
		{
			$html .= $this->html_next . '&nbsp; ';
			$html .= $this->html_last . ' ';
		}

		return $html;    
	}
	
    /**
     * getSql() - Gets a custom SQL query based on the current recordset
     *
     * @access      public
     * @return      bool
    */    
    function getSql($where='',$orderby='', $order='ASC', $limit=50, $offset='')
    {
        $sql = '';
        
        if($this->select)
        {
            $select = $this->select;
        }
        else 
        {
            $select = 'SELECT * FROM ' . $this->table;
        }
        
        $this->sql_pre = $this->sql;
        if(!empty($this->criteria))
        {
            $criteria = ' WHERE ';
            
            foreach($this->criteria as $key=>$value)
            {
                if(is_numeric($value['criteria']))
                {
                    $thisval = intval($value['criteria']);
                }
                else 
                {
                    $thisval = '"' . $value['criteria'] . '"';
                }
                $criteria .=  $value['field'] . ' = ' . $thisval . ' AND ';
            }
            // Strip the tailing AND
            $criteria = substr($this->sql,0,-4);
        }
        elseif($where)
        {
            $criteria = ' WHERE ' . $where . ' ';
        }

        if($orderby) { 
            $orderby = ' ORDER BY ' . $orderby . ' ' . $order;
        }
        else 
        {
            $orderby = '';
        }
        if($limit) { 
            $limit .= ' LIMIT ' . $offset . ',' . $limit; 
        }
        else 
        {
            $limit = '';
        }

        return $select . $criteria . $orderby . $limit;
    }
    
    /**
     * getTotal() - Get total number of rows for current recordset ** do this without running query again, or COUNT()!
     *
     * @access      public
     * @return      bool
    */
    function getTotal()
    {
        $sql = $this->params['select'] . $this->params['criteria'];
        $total = 0;
	    if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
	    {
	        $total = $rs->NumRows();
	    }
	    return $total;
    }
    
    /**
     * load() - Loads the object's recordset using specified criteria
     *
     * @access      public
     * @param       string $where       WHERE statement
     * @param       string $orderby     ORDER BY field name
     * @param       string $order       ORDER (ASC or DESC)
     * @param       int $limit          LIMIT value
     * @param       int $offset         OFFSET value
     * @return      bool
    */
    function load($where=0,$orderby=0, $order='ASC', $limit=50, $offset=0)
    {
        global $rs_config;

        // Use the existing select statement if defined, else
        // default to SELECT * from this object's table.
        if($this->select)
        {
            $this->sql = $this->select;
        }
        else 
        {
            $this->sql = 'SELECT * FROM ' . $this->table;
            $this->sql_select = 'SELECT * FROM ' . $this->table;
        }
        
        // Store the select statement
        $this->sql_pre = $this->sql;
        $this->params['select'] = $this->sql;
        $this->params['criteria'] = '';
        
        if(!empty($this->criteria))
        {
            if(strpos($this->sql, 'WHERE') !== false)
            {
                $criteria = ' OR ';
            }
            else 
            {
                $criteria = ' WHERE ';
            }
            foreach($this->criteria as $key=>$value)
            {
                if(is_numeric($value['criteria']))
                {
                    $thisval = intval($value['criteria']);
                }
                else 
                {
                    $thisval = '"' . $value['criteria'] . '"';
                }
                $criteria .=  $key . ' = ' . $thisval . ' AND ';
            }
            // Strip the tailing AND
            $criteria = substr($criteria,0,-4);
            $this->params['criteria'] = $criteria;
            $this->sql .= $criteria;
        }

        if($where)
        {
            if(strpos($this->sql, 'WHERE') !== false)
            {
                $this->sql .= ' AND ' . $where . ' ';
                $this->params['criteria'] .= ' AND ' . $where . ' ';
            }
            else 
            {
                $this->sql .= ' WHERE ' . $where . ' ';
                $this->params['criteria'] .= ' WHERE ' . $where . ' ';
            }
        }
        
        // Add ORDER BY to SQL statement
        if($orderby) { 
            $this->orderby = $orderby;
            $this->order = $order;
            $this->sql .= ' ORDER BY ' . $orderby . ' ' . $order;
        }
        
        // Add LIMIT to SQL statement
        if($limit) { 
            $this->limit= $limit;
            $this->offset = $offset;
            $this->sql .= ' LIMIT ' . $offset . ',' . $limit; 
        }

        // Run SQL
        if(($this->rs = $this->db->execute($this->sql)) && (!$this->rs->EOF))
        {
            $this->message = $this->rs->NumRows() . ' records found';
            $this->tpl->assign('dmo_search_tail', getSearchTail($rs_config));
            
            if(!empty($rs_config['f_search']))
			{
				$this->tpl->assign('dmo_results_message', $this->getTotal() . ' ' . $this->lang[$this->name . '_items'] . ' found');
			}
			else
			{
				$this->tpl->assign('dmo_results_message', $this->getTotal() . ' ' . $this->lang[$this->name . '_items'] . ' total');
			}
            return true;
        }
        else 
        {
            if(!empty($rs_config['f_search']))
			{
				$this->tpl->assign('dmo_results_message', 'No ' . $this->lang[$this->name . '_items'] . ' found');
			}
			else
			{
				$this->tpl->assign('dmo_results_message', 'No ' . $this->lang[$this->name . '_items'] . ' found');
			}
            $this->errors[] = $this->lang['rs_not_found'];
            return false;
        }
    }

    /**
     * lock() - Locks a field 
     *
     * @access      public
     * @param       string $path Absolute path
     * @return      bool
    */
    function lock($fieldname)
    {
        $this->locks[] = $fieldname;
        return true;
    }
    
    /**
     * parse_dropdown - Parse form's dropdown elements
     *
     * @access      public
     * @return      bool
    */
    function parse_dropdown($name, $selected = 0)
    {
        $blockname = $this->prefix . $name;

        if(!$selected) $selected = $this->dropdowns[$name]['default'];
        if(!empty($this->dropdowns[$name]['data']))
        {
            foreach($this->dropdowns[$name]['data'] as $key=>$value)
            {
                if(!empty($value['__list_group']))
                {
                    $dropdowns[$value[$value['__list_group']]][] = $value;
                    $grouped = true;
                    krsort($dropdowns);
                }
                else 
                {
                    $dropdowns[0] = $value;
                }
                $index = (!empty($value['__list_group'])) ? $value['__list_group'] : 0;
                
            }
        }
        
        if(empty($grouped))
        {
            if(!empty($this->dropdowns[$name]['data']))
            {
                $dropdowns = $this->dropdowns[$name]['data'];
    
                foreach($dropdowns as $key=>$value)
                {
                    $this->tpl->assign_d($blockname, 'optgroup_start', '');
                    $this->tpl->assign_d($blockname, 'optgroup_end', '');
                    if($selected === $value[$this->lists->lists[$this->dropdowns[$name]['type']]['valuekey']])
                    {
                        $this->tpl->assign_d($blockname, 'item_selected', 'selected');
                    }
                    else 
                    {
                        $this->tpl->assign_d($blockname, 'item_selected', ' ');
                    }
        
                    foreach($value as $k=>$v)
                    {    
                        $this->tpl->assign_d($blockname, $k, $v);
                    }
                if(!$this->tpl->parse_d($blockname));
                }
            }
        }
        else 
        {
            $has_begun = 0;
            foreach($dropdowns as $key=>$value)
            {
                $group_start = 0;

                foreach($value as $k=>$v)
                {
                    if($group_start == 0)
                    {
                        $optgroup = '<optgroup label="' . $key . '">';
                        if($has_begun == 1) $optgroup = '</option>' . $optgroup;
                        $group_start = 1;
                    }
                    else 
                    {
                        $optgroup = '';
                    }
                    $this->tpl->assign_d($blockname, 'optgroup', $optgroup);

                    if($selected === $v[$this->lists->lists[$this->dropdowns[$name]['type']]['valuekey']])
                    {
                        $this->tpl->assign_d($blockname, 'item_selected', 'selected');
                    }
                    else 
                    {
                        $this->tpl->assign_d($blockname, 'item_selected', ' ');
                    }
                    foreach($v as $item=>$data)
                    {
                        $this->tpl->assign_d($blockname, $item, $data);
                    }

                    $this->tpl->parse_d($blockname);
                }
            }
            
        }

    }
    
    /**
     * parse_form - Renders and parses the object's form using current data
     *
     * @access      public
     * @return      bool
    */
    function parse_form($fields = array(), $errors = 0, $edit=0, $title=0)
    {

        if(!$this->formname)
        {
            $this->formname = $this->name . '_form.tpl';
        }
        $this->tpl->define('dmo' . $this->name, $this->formname);

        $this->tpl->define_d('dmo_form_row', 'dmo' . $this->name);
        $this->tpl->define_d('dmo_data_empty', 'dmo' . $this->name);
    
        $this->parse_subtemplates('dmo' . $this->name);
        $this->tpl->parse('dmo' . $this->name);
                
        if(isset($this->dropdowns))
        {
            foreach($this->dropdowns as $key=>$value)
            {
                $this->tpl->define_d($this->prefix . $key, 'dmo_form_row');
            }
        }

        $dmo_row = 1;
       
        // Read data from the current active recordset into $rs_fields
        if((!empty($this->rs)) && (!$this->rs->EOF))
        {

            while(!$this->rs->EOF)
            {
                $rs_fields[$this->rs->fields[$this->id_col]] = $this->rs->fields;
                $this->rs->MoveNext();
            }
        }
        // Read data from the supplied $fields array (if it exists) to $rs_fields. 
        // $_REQUEST array is usually passed here to preserve form data during
        // validation. $fields will overwrite duplicate keys in $rs_fields.
        if((!empty($fields)) && (is_array($fields)))
        {
            foreach($fields as $key=>$value)
            {
                if((!empty($value)) && (strpos($key,'dmo_') !== false))
                {
                    if(is_array($value))
                    {
                        foreach($value as $k=>$v)
                        {
                                $rs_fields[$k][str_replace('dmo_', '', $key)] = $v;
                        }
                    }
                }
            }
        }

        // Check for data in $rs_fields and render the form appropriately
        if($rs_fields)
        {
            
            // Assign a title to the form
            if(!$title)
            {
                $this->tpl->assign('dmo_form_title', $this->lang['action_editing'] . $this->lang[$this->name . '_items']);        
            }
            else 
            {
                $this->tpl->assign('dmo_form_title', $title);        
            }
            // Fetch the appropriate column names based on how our recordset was
            // created - direct table or custom query.
            if($this->select)
            {
                $colnames = db_getcolnames($this->db, $this->sql_pre, 1);
            }
            else 
            {
                $colnames = db_getcolnames($this->db, $this->table);
            }

            // Loop through $rs_fields and render our form
            foreach($rs_fields as $rs_key=>$rs_field)
            {

                if(!empty($errors[$rs_key]))
                {
                    $this->tpl->assign_d('dmo_form_row', 'dmo_form_row_errors', $errors[$rs_key]);
                }
                
                if(isset($rs_field[$this->name_col]))
                {
                    $this->tpl->assign_d('dmo_form_row', 'dmo_row_title', $rs_field[$this->name_col]);
                }
                else 
                {
                    $this->tpl->assign_d('dmo_form_row', 'dmo_row_title', $this->lang[$this->name . '_item'] . ' ' . $dmo_row);
                }
                
                // Loop through column names and perform some transformations
                foreach($colnames as $key=>$value)
                {
                    if(isset($rs_field[$value]))
                    {
                        $this->tpl->assign_d('dmo_form_row', $this->prefix . $value, htmlspecialchars(stripslashes($rs_field[$value])));
                        $this->tpl->assign_d('dmo_form_row', $this->prefix . 'dq_' . $value, str_replace('"', '\"', stripslashes($rs_field[$value])));
                    }
                    else 
                    {
                        $this->tpl->assign_d('dmo_form_row', $this->prefix . $value, '');
                    }
                    
                    // Check to see if field is locked
                    if(in_array($value, $this->locks))
                    {
                        $this->tpl->assign_d('dmo_form_row', $this->prefix . $value . '_disabled', 'disabled');
                    }
                    else 
                    {
                        $this->tpl->assign_d('dmo_form_row', $this->prefix . $value . '_disabled', '');
                    }

                    // Check for current feild in lookups list
                    if(array_key_exists($value, $this->lookups))
                    {
                        foreach($this->lists->lists[$value]['data'] as $k=>$v)
                        {
                            if($v['valuekey'] == $key)
                                $this->tpl->assign_d('dmo_form_row', $this->prefix . $value, 'wooog!');
                        }
                    }

                }
    
                $dmo_row++;

                // Detect and prepare checkboxes
                foreach($this->checkboxes as $key=>$value)
                {

                    if((intval($rs_field[$key]) == '1') || ($rs_field[$key] == 'on'))
                    {
                        $this->tpl->assign_d('dmo_form_row',$this->prefix . $key . '_checked', 'checked');
                    }
                    else
                    {
                        $this->tpl->assign_d('dmo_form_row',$this->prefix . $key . '_checked', '');
                    }
                }
                
                // Detect and prepare field hooks
                foreach($this->hooks as $k=>$func)
                {
                    if(function_exists($func))
                    {
                        $hookval = call_user_func($func,$rs_field[$k]);
                        $this->tpl->assign_d('dmo_form_row', $this->prefix . $func . '_func', $hookval);
                    }
                } 

                // Detect and customize error rows
                foreach($this->f_errors as $key=>$value)
                {
                    $this->tpl->assign($key . '_rowclass', 'class="fErrorRow"');
                    $this->tpl->assign($key . '_labelclass', 'class="fErrorLabel"');
                    $this->tpl->assign($key . '_fieldclass', 'class="fErrorField"');
                    $this->tpl->assign($key . '_ctlclass', 'class="fErrorCtl"');
                    $this->tpl->assign($key . '_errormsg', '<br />&nbsp;* ' . $value);
                }

                // Detect and prepare radio buttons
                foreach($this->radiobuttons as $key=>$value)
                {

                    $isset = 0;
                    foreach($this->radiobuttons[$key]['values'] as $k=>$v)
                    {
                        $nk = strtolower($k);
                        $nk = str_replace(' ','', $nk);
                        $ns = strtolower($rs_field[$key]);
                        $ns = str_replace(' ','', $ns);

                        if($rs_field[$key] == $v)
                        {
                            $isset = 1;
                            $this->tpl->assign($this->prefix . $key . '_' . $nk, 'checked');
                        }
                        else 
                        {
                            $this->tpl->assign($this->prefix . $key . '_' . $nk, '');
                        }
                    }
                    if(!$isset)
                    {
                        foreach($this->radiobuttons[$key]['values'] as $k=>$v)
                        {
                            if($this->radiobuttons[$key]['default'] == $k)
                            {
                                $nk = strtolower($k);
                                $nk = str_replace(' ','', $nk);

                                $this->tpl->assign($this->prefix . $key . '_' . $nk, 'checked');
                            }
                        }
                    }
                }
                
                $this->tpl->parse_d('dmo_form_row');
                
                // Detect and prepare dropdowns
                foreach($this->dropdowns as $key=>$value)
                {
                    $this->parse_dropdown($key, $rs_field[$key]);
                }

                foreach($this->images as $key=>$value)
                {
                    //if((!empty($rs_field[$key])) && (list($width, $height, $type, $attr) = getimagesize(PATH_IMAGES . $rs_field[$key])))
                    if(!empty($rs_field[$key]))
                    {
                        $this->tpl->assign($this->prefix . $key . '_sm', $rs_field[$key]);
                        $this->tpl->assign($this->prefix . $key . '_sm_h', $this->image_sm_maxheight);
                        $this->tpl->assign($this->prefix . $key . '_sm_w', $this->image_sm_maxwidth);
                    }
                    else 
                    {
                        $this->tpl->assign($this->prefix . $key . '_sm', $this->no_image);
                        $this->tpl->assign($this->prefix . $key . '_sm_h', 0);
                        $this->tpl->assign($this->prefix . $key . '_sm_w', 0);
                    }

                }

            }
        }
        else // No recordset present, continue parsing form
        {
        // Assign a title to the form
            if(!$title)
            {
                $this->tpl->assign('dmo_form_title', $this->lang['action_creating'] . $this->lang[$this->name . '_item']);        
            }
            else 
            {
                $this->tpl->assign('dmo_form_title', $title);        
            }
            if(!$edit)
            {
                $this->tpl->parse_d('dmo_form_row');
                
                foreach($this->checkboxes as $key=>$value)
                {
                    $this->tpl->assign_d('dmo_form_row',$this->prefix . $key . '_checked', '');
                }
                
                foreach($this->radiobuttons as $key=>$value)
                {
                    $isset = 0;

                    foreach($this->radiobuttons[$key]['values'] as $k=>$v)
                    {
                        $this->tpl->assign($this->prefix . $key . '_' . strtolower($k), '');
                    }

                    foreach($this->radiobuttons[$key]['values'] as $k=>$v)
                    {
                        if($this->radiobuttons[$key]['default'] == $k)
                        {
                            $this->tpl->assign($this->prefix . $key . '_' . strtolower($k), 'checked');
                        }
                    }
                }
                foreach($this->dropdowns as $key=>$value)
                {
                    $this->parse_dropdown($key, 0);
                }
            }
            else 
            {
                $this->tpl->parse_d('dmo_data_empty');
            }

        }
    }
    
    /**
     * parse_list - Renders and parses the object's list view of current data
     *
     * @access      public
     * @return      bool
    */
    function parse_list($list = '_list')
    {
        
        $this->tpl->define('dmo' . $this->name, $this->name . $list . '.tpl');
        $this->tpl->define_d('dmo_data_row', 'dmo' . $this->name);
        $this->tpl->define_d('dmo_data_empty', 'dmo' . $this->name);
        $this->parse_subtemplates('dmo' . $this->name);
        $this->tpl->parse('dmo' . $this->name);
        
        if(isset($this->dropdowns))
        {
            foreach($this->dropdowns as $key=>$value)
            {
                $this->tpl->define_d($this->prefix . $key, 'dmo' . $this->name, $this->name . $list . '.tpl');
            }
            foreach($this->dropdowns as $key=>$value)
            {
                $this->parse_dropdown($key, $this->rs->fields[$key]);
            }
        }
        
        foreach($this->dynamics as $key=>$value)
        {
            $this->tpl->define_d('dynamic_' . $key, 'dmo_data_row');
        }
        
        $this->tpl->assign('dmo_offset', $this->offset);
        $this->tpl->assign('dmo_limit', $this->limit);
        
        if($this->order == 'DESC')
        {
            $this->tpl->assign('dmo_order_next', 'ASC');
        }
        else 
        {
            $this->tpl->assign('dmo_order_next', 'DESC');
        }

        $this->tpl->assign('dmo_order', $this->order);
        $this->tpl->assign('dmo_orderby', $this->orderby);
        $this->tpl->assign('dmo_where', $this->where);
        
        global $rs_config;
        
        if((!empty($rs_config['f_search'])) && (!empty($rs_config['f_field'])))
        {
            $this->tpl->assign('dmo_sort_link', '&f_search=' . $rs_config['f_search'] . '&f_field=' . $rs_config['f_field']);
        }
        else 
        {
            $this->tpl->assign('dmo_sort_link', '');
        }
        //$this->tpl->assign('rs_pager', db_pager_get($this->tpl, $this->db,$this->sql_select));
        
        if((!empty($this->rs)) && (!$this->rs->EOF))
        {
            $bgClassNum = 1;
            $rownum = 1;
            while(!$this->rs->EOF)
            {
                foreach($this->rs->fields as $key=>$value)
                {
                    $this->tpl->assign_d('dmo_data_row', 'dmo_' . $key, stripslashes($value));
                }
                $this->rs->fields['rs_row_num'] = $rownum;
                foreach($this->hooks as $k=>$func)
                {
                    if(function_exists($func))
                    {
                        $result = call_user_func($func,$this->rs->fields[$k]);

                        $this->tpl->assign_d('dmo_data_row', $this->prefix . $func . '_func', $result);
                    }
                } 

                foreach($this->dateformats as $k=>$v)
                {
                    $this->tpl->assign_d('dmo_data_row', 'dmo_' . $k, date(DEFAULT_DATE_FORMAT, dbtime_to_unix($this->rs->fields[$k])));
                }
/*
                foreach($this->lookups as $key=>$value)
                {
                    $this->tpl->assign_d('dmo_data_row', 'dmo_' . $key, $this->lists->lists[($this->rs->fields[$key])));
                }
*/
                $this->tpl->assign_d('dmo_data_row', 'dmoColClassOne', 'dmoColOne' . $bgClassNum);
                $this->tpl->assign_d('dmo_data_row', 'dmoColClassTwo', 'dmoColTwo' . $bgClassNum);
                
                $bgClassNum = ($bgClassNum == 1) ? 2 : 1;

                $this->tpl->parse_d('dmo_data_row');
                
                foreach($this->dynamics as $k=>$v)
                {
                    if($this->rs->fields[$k] == $v)
                    {
                        $this->tpl->assign_d('dynamic_' . $k,'doo', 'asd');
                        $this->tpl->parse_d('dynamic_' . $k);
                    }
                }
                $rownum++;
                $this->rs->MoveNext();
            }
        }
        else 
        {
            $this->tpl->parse_d('dmo_data_empty');
        }
    }
    
    /**
     * parse_subtemplates() - Define all subtemplates (shouldn't be called parse!)
     *
     * @access      public
     * @return      bool
    */
    function parse_subtemplates($parent)
    {

        if(is_array($this->subtemplates))
        {
            foreach($this->subtemplates as $key=>$value)
            {
                $this->tpl->define_d($value, $parent);
            }
        }
        return true;
        
    }

    /**
     * save() - Save's the provided field contents to the DB
     *
     * @access      public
     * @return      bool
    */
    function save($fields)
    {
        // Look for the unique record ID in the fields provided to 
        // determine if this is an existing record
        
        if((!empty($fields[$this->prefix . $this->id_col])) && (is_array($fields[$this->prefix . $this->id_col])))
        {

            // Loop through submitted ID column
            foreach($fields[$this->prefix . $this->id_col] as $key=>$value)
            {
                // Loop ID columns for all submitted ID's
                foreach($fields as $k=>$v)
                {

                    if($key !== 0)
                    {
                        $rec[$key][$k] = $v[$value];

                    }                   
                    else
                    {
                        if($k == $this->prefix . $this->id_col)
                            $rec[0][$k] = 0;
                        else
                            $rec[0][$k] = $v[0];
                    }
                }
            }

            foreach($rec as $key=>$value)
            {

                $rec[$key] = $this->strip_field_prefix($rec[$key]);
                
                foreach($this->checkboxes as $k=>$v)
                {
                    if((!empty($rec[$key][$k])) && (($rec[$key][$k] == 1) || ($rec[$key][$k] == 'on')))
                    {
                        $rec[$key][$k] = 1;
                    }
                    else 
                    {
                        $rec[$key][$k] = -1;
                    }
                }
                foreach($this->radiobuttons as $k=>$v)
                {
                    if((!empty($rec[$key][$k])) && ($rec[$key][$k] == 1))
                    {
                        $rec[$key][$k] = 1;
                    }
                    else 
                    {
                        $rec[$key][$k] = -1;
                    }
                }

                // Process file uploads
                
                foreach($this->files as $k=>$v)
                {
                    if(!empty($_FILES[$this->prefix . $k]['name'][$key]))
                    {
                        $filename = basename($_FILES[$this->prefix . $k]['name'][$key]);
                        @move_uploaded_file($_FILES[$this->prefix . $k]['tmp_name'][$key], $v . $filename);
                        $rec[$key][$k] = $filename;
                    }
                }

                // Process image file uploads
                
                foreach($this->images as $k=>$v)
                {
                    if(!empty($_FILES[$this->prefix . $k . '_img']['name'][$key]))
                    {
                        $filename = basename($_FILES[$this->prefix . $k . '_img']['name'][$key]);
                        if(@move_uploaded_file($_FILES[$this->prefix . $k . '_img']['tmp_name'][$key], $v['dest'] . $filename))
                        {
                            $rec[$key][$k] = $filename;
                               
                            if($v['thumb'])
                            {
                                $thumbfile = PATH_THUMB_IMAGES . IMAGE_THUMB_PREFIX . $filename;
                                image_thumb_create($v['dest'] . $filename,$thumbfile,IMAGE_THUMB_WIDTH,IMAGE_THUMB_WIDTH);
                            }
                            if($v['db_insert'])
                            {
                                $im_sql = 'SELECT image_id FROM ' . TBL_IMAGES . ' WHERE img_filename = "' . $filename . '"';
                                if(($im_rs = $this->db->execute($im_sql)) && (!$im_rs->EOF))
                                {

                                }
                                else 
                                {
                                    $im_sql = 'INSERT INTO ' . TBL_IMAGES .  '(distributor_id, asset_id, img_name, img_filename, img_thumb_filename) ' .
                                              'VALUES(' . $this->config->getVar('distributor_id') . ', "' . getAssetId(TBL_IMAGES) . '", ' . $filename . '", "' . $filename . '", ' .
                                              '"' . basename($thumbfile) . '")';
                                    $this->db->execute($im_sql);
                                }
                            }
                        }
                    }
                }
                
                if(empty($this->forceInsert[$rec[$key][$this->id_col]])) $this->forceInsert[$rec[$key][$this->id_col]] = 0;
                
                if((!empty($rec[$key][$this->id_col][0])) && ($this->forceInsert[$rec[$key][$this->id_col]] !== 1))
                {
                    $rec[$key][$this->field_prefix . 'dateupdated'] = unix_to_dbtime(time());
                    if(empty($rec[$key]['dateupdated'])) $rec[$key]['dateupdated'] = unix_to_dbtime(time());

                    if(!empty($this->saveExtraCol))
                    {
                        $saveExtraCol = ' AND ' . $this->saveExtraCol . ' = ';
                        $saveExtraCol .= (!empty($this->saveExtraVal)) ? $this->saveExtraVal : $rec[$key][$this->saveExtraCol];
                    }
                    else 
                    {
                        $saveExtraCol = '';
                    }
                    $this->sql = db_getupdate($this->db, $this->table, $rec[$key],  $this->id_col . ' = ' . $rec[$key][$this->id_col] . $saveExtraCol);

                }
                else 
                {
                    if(empty($rec[$key]['dateupdated'])) {
                        $rec[$key]['datecreated'] = unix_to_dbtime(time());
                        $rec[$key]['dateupdated'] = unix_to_dbtime(time());
                    }
                    $rec[$key]['asset_id'] = getAssetId($this->table);
                    $this->sql = db_getinsert($this->db, $this->table, $rec[$key]);
                }

                if(!$this->db->execute($this->sql))
                {
                    $this->errors[] = $this->lang['action_save_error'];
                }
            }

            if($this->errors) {
                return false;
            }
            else 
            {
                return true;
            }

        }
        else 
        {

            $fields = $this->strip_field_prefix($fields);
            foreach($fields as $key=>$value)
            {
                $fields[$key] = (!empty($value)) ? $value : '';
            }

            $fields['datecreated'] = unix_to_dbtime(time());
            $fields['dateupdated'] = unix_to_dbtime(time());

            $this->sql = db_getinsert($this->db, $this->table, $fields);

            if(!$this->db->execute($this->sql))
            {
                $this->errors[] = $this->lang['action_save_error'];
                return false;
            }
            else 
            {
                return true;
            }

        } 
    }

    /**
     * setSelect() - Sets the SELECT statement for the current DMO
     *
     * @access      public
     * @return      bool
    */
    function setSelect($string)
    {
        $this->select = $string;
        return true;
    }

    /**
     * strip_field_prefix - Strips the DMO prefix from all fields in specified array
     *
     * @access      public
     * @return      bool
    */    
    function strip_field_prefix($fields)
    {

        foreach($fields as $key=>$value)
        {
            if(strpos($key,$this->prefix) !== false)
            {
                $clean_fields[str_replace($this->prefix, '', $key)] = $value;
            }
        }
        return $clean_fields;
    }

    
}
/* end class */
?>