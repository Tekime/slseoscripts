<?php
/**
 * categories.php - Scriptalicious SEO Scripts Category Admin
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.2
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
 * 2009-05-04 (1.2) - Updated for Kytoo 2.0. Based on module admin template 2.0
 *
*/

/* Module admin variables: $k_mod_id, $k_mod_name, $k_mod_title, $k_mod_url */

// Required: Configure module admin settings
$this_item = 'Category';
$this_items = 'Categories';
$this_title = 'Tool Categories';
$this_table = TBL_SEOTOOLS_CATEGORIES;
$this_id = 'category_id';
$this_view = 'categories';
$this_edit_return = false;
$this_sort = 'cat_sort';
$this_order = 'asc';
$this_parent = false;
$this_table_parent = false;

// Optional: Set up lists
$k_list_yesno = array(1 => 'Yes', 0 => 'No');
$k_list_status = array(1 => 'Active', 0 => 'Disabled');
// $k_list = k_db_makelist($db, TBL_BLAH, 'field', 'x = 1');
// $k_list = k_db_makelistr($db, TBL_BLAH, 'field', 'parentcol', 'x = 1', skipid, parent_id, sep, top, sort, order);

// Optional: Configure unique fields
$this_unique_cols = array('cat_safename');

// Optional: Configure safename JS fields
$this_safename_cols = array('cat_name' => 'cat_safename');

// Optional: Configure search & filter options
$this_search = array('cat_name' => 'Post name', 'cat_title' => 'Post title');
$this_filter = array('name' => 'Status', 'field' => 'cat_status', 'values' => $k_list_status);

// System: Configure default module admin settings
$this_file = 'main.php';
$this_url = $k_mod_url . '&v=' . $this_view;
$this_mod = $k_mod_id;
$this_iteml = strtolower($this_item);
$this_itemsl = strtolower($this_items);
$this_submit = DIR_ADMIN . $this_file;
$lang_create = $lang['create'] . ' ' . $this_item;
$lang_save = $lang['save'] . ' ' . $this_item;
$lang_edit = $lang['edit'] . ' ' . $this_item;
$lang_main = $this_title;


// Optional: Buttons and extended setup
$this_buttons = array(
    array('url' => $this_url . '&a=create', 'title' => $lang['create'] . ' ' . $this_item)
);

// Required: Configure data columns
$this_cols = array(
    'category_id' => array(
        'sortable' => false,
        'type' => 'checkbox',
        'width' => '16px'
    ),
    'cat_name' => array(
        'title' => 'Category Name',
        'href' => $this_url . '&a=edit&' . $this_id . '={' . $this_id . '}',
        'text' => '{cat_name}',
        'classext' => 'Pad'
    ),
    'cat_status' => array(
        'title' => 'Status',
        'lookup' => $k_list_status
    ),
    'cat_sort' => array(
        'title' => 'Sort',
        'text' => '<a href="' . $this_url . '&a=sortUp&' . $this_id . '={' . $this_id . '}"><img src="{dir_tpl_images}ico_up.gif" border="0px" alt="Up"></a><a href="' . $this_url . '&a=sortDown&' . $this_id . '={' . $this_id . '}"><img src="{dir_tpl_images}ico_down.gif" border="0px" alt="Down"></a>'
    ),
    'delete' => array(
        'href' => $this_url . '&a=delete&' . $this_id . '={' . $this_id . '}',
        'text' => $lang['delete'],
        'sortable' => false
    )
);

// Add default navigation for module
$nav->add_link($k_mod_name, $k_mod_url);
$nav->add_link($lang_main, $this_url);
$nav->set_current($lang_main);

// Initialize form as needed
if((!empty($_REQUEST['a'])) && (($_REQUEST['a'] == 'create') || ($_REQUEST['a'] == 'edit') || ($_REQUEST['a'] == 'save')))
{
    // Create the form
    $kForm = new kForm(DIR_ADMIN . $this_file, 'category');
    $kForm->addText('Title', 'cat_title', '', 30, 255);
    $kForm->addDesc('cat_title', 'The full title of the category, used in category pages and browser titles.');
    $kForm->addText('Name', 'cat_name', '', 30, 255);
    $kForm->addDesc('cat_name', 'The short name for the category, used in menus.');
    $kForm->addText('Safe Name', 'cat_safename', '', 30, 255);
    $kForm->addDesc('cat_safename', 'The URL friendly name for the category.');
    $kForm->addTextarea('Description', 'cat_description', '', 2, 50);
    $kForm->addDesc('cat_description', 'A description of the category, optionally displayed on category pages.');
    $kForm->addSelect('Status', 'cat_status', $k_list_status, 1);
    $kForm->addDesc('cat_status', 'Disable this category to hide it from blog menus.');
    $kForm->addRule('cat_name', 'required');
    $kForm->addRule('cat_title', 'required');
    $kForm->addRule('cat_safename', 'required');
    $kForm->addHidden($this_id);
    $kForm->addHidden('a');
    $kForm->addHidden('m');
    $kForm->addHidden('v');

    // Add JS to safename columns    
    if(($_REQUEST['a'] == 'create') && (sizeof($this_safename_cols) > 0))
    {
        foreach($this_safename_cols as $key => $value)
        {
            $kForm->controls[$key]['extra'] = ' onKeyUp="javascript:format_safeurl(\'' . $key . '\', \'' . $value . '\');return false;"';
        }
    }
}

if((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'create'))
{
    /**
     * Create Record
    */
    $nav->set_current($lang_create);
    $kForm->title = $lang_create;
    $kForm->addSubmit($lang_save . ' >>');
    $kForm->renderForm(array('a' => 'save', 'm' => $this_mod, 'v' => $this_view));
}
elseif((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'save'))
{
    /**
     * Save Record
    */

    // Check for unique column names as needed
    if((is_array($this_unique_cols)) && (count($this_unique_cols) > 0))
    {
        foreach($this_unique_cols as $key => $value)
        {
            if(!empty($_REQUEST[$value]))
            {
                $sql = 'SELECT ' . $value . ' FROM ' . $this_tbl . ' WHERE ' . $value . ' = "' . $_REQUEST[$value] . '"';
                if(($rs = $db->execute($sql)) && (!$rs->EOF)) $kForm->addError($value, 'Must be unique. A record with this value already exists.');
            }
        }
    }

    // Set current nav and form details
    if(empty($_REQUEST[$this_id]))
    {
        $nav->set_current($lang_create);
        $kForm->title = $lang_create;
    }
    else
    {
        $nav->set_current($lang_edit);
        $kForm->title = $lang_edit;
    }
    $kForm->addSubmit($lang_save . ' >>');

    if(!$kForm->validate($_REQUEST))
    {
        $kApp->addMessage('There was a problem saving your ' . $this_iteml . '. Please fix the errors highlighted below.', 'error');
        
        // Clean up slashes from previous request
        foreach($_REQUEST as $key => $value)
        {
            $_REQUEST[$key] = stripslashes($value);
        }
        $kForm->renderForm($_REQUEST);    
    }
    else
    {
        if(empty($_REQUEST[$this_id]))
        {
            // Set default record user/date info
            $_REQUEST['createdby'] = $session->user_id;
            $_REQUEST['updatedby'] = $session->user_id;
            $_REQUEST['datecreated'] = unix_to_dbtime(time());
            $_REQUEST['dateupdated'] = unix_to_dbtime(time()); 

            $sql = db_getinsert($db, $this_table, $_REQUEST);
            if($db->execute($sql))
            {
                if($this_edit_return)
                {
                    $url = $this_url . '&a=edit&' . $this_id . '=' . intval(mysql_insert_id($db->link));
                    $msg = 'Your ' . $this_iteml . ' has been created successfully. <a href="' . $this_url . '">Return to ' . $this_itemsl . '</a> or continue editing below.';
                }
                else
                {
                    $url = $this_url;
                    $msg = 'Your ' . $this_iteml . ' has been created successfully.';                
                }
                header('Location:' . $url . '&k_msgok=' . urlencode($msg));
            }
            else
            {
                $kApp->addMessage('There was an error creating your ' . $this_iteml . '. Please check your form and try again.', 'error');
                // Clean up slashes from previous request
                foreach($_REQUEST as $key => $value)
                {
                    $_REQUEST[$key] = stripslashes($value);
                }
                $kForm->renderForm($_REQUEST);    
            }
        }
        else
        {
            // Set updated record user/date info
            $_REQUEST['updatedby'] = $session->user_id;
            $_REQUEST['dateupdated'] = unix_to_dbtime(time()); 

            $sql = db_getupdate($db, $this_table, $_REQUEST, $this_id . ' = ' . intval($_REQUEST[$this_id]));
            if($db->execute($sql))
            {
                $msg = 'Your ' . $this_iteml . ' was saved successfully. <a href="' . $this_url . '">Return to ' . $this_itemsl . '</a> or continue editing below.';
                header('Location:' . $this_url . '&a=edit&category_id=' . intval($_REQUEST[$this_id]) . '&k_msgok=' . urlencode($msg));
                
                if($this_edit_return)
                {
                    $url = $this_url . '&a=edit&' . $this_id . '=' . intval($_REQUEST[$this_id]);
                    $msg = 'Your ' . $this_iteml . ' was saved successfully. <a href="' . $this_url . '">Return to ' . $this_itemsl . '</a> or continue editing below.';
                }
                else
                {
                    $url = $this_url;
                    $msg = 'Your ' . $this_iteml . ' was saved successfully.';                
                }
                header('Location:' . $url . '&k_msgok=' . urlencode($msg));
            }
            else
            {
                $kApp->addMessage('There was an error updating your ' . $this_iteml . '. Please check your form and try again.', 'error');
                $kForm->renderForm($_REQUEST);    
            }
        } 
    }
}
elseif((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'delete'))
{
    /**
     * Delete Record
    */

    if((!empty($_REQUEST[$this_id])) && (is_numeric($_REQUEST[$this_id])))
    {
        if((!empty($_REQUEST['confirm'])) && ($_REQUEST['confirm'] == 1))
        {
            $sql = 'DELETE FROM ' . $this_table . ' WHERE ' . $this_id . ' = ' . intval($_REQUEST[$this_id]);
            if($db->execute($sql))
            {
                $dc = 1;
                $dcp = ($dc>1) ? 's' : '';
                $msg = $dc . ' record' . $dcp . ' was permanenty deleted.';
                
                header('Location:' . $this_url . '&k_msg=' . urlencode($msg));
            }
            else
            {
                die('Unable to delete.');
            }
        }
        else
        {
            dialog_box('Are you sure you want to delete this record?', $this_url . '&a=delete&' . $this_id . '=' . intval($_REQUEST[$this_id]) . '&confirm=1', $this_url, 'Confirm Delete', false);
        }
    }
}
elseif((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'edit'))
{
    /**
     * Edit Record
    */

    $nav->set_current($lang_edit);
    if(!empty($_REQUEST[$this_id]))
    {
        $sql = 'SELECT * FROM ' . $this_table . ' WHERE ' . $this_id . ' = ' . $_REQUEST[$this_id];
        if(($rs = $db->execute($sql)) && (!$rs->EOF))
        {
            $kForm->addSubmit($lang_save . ' >>');
            $kForm->title = $lang_edit;
            $kForm->heading = '';
            $rs->fields['a'] = 'save';
            $rs->fields['m'] = $this_mod;
            $rs->fields['v'] = $this_view;
            $kForm->renderForm($rs->fields);
        }
    }
}
elseif((!empty($_REQUEST['a'])) && (($_REQUEST['a'] == 'sortUp') || ($_REQUEST['a'] == 'sortDown'))) 
{
    /**
     * Sort Record
    */

    // Get the details of this record first
    $sql = 'SELECT * FROM ' . $this_table . ' WHERE ' . $this_id . ' = ' . intval($_REQUEST[$this_id]);
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        // Set up SQL and fetch all records with same parent below this sort level
        $sql_op = ($_REQUEST['a'] == 'sortUp') ? ' < ' : ' <= ';
        $sql_parent = ($this_parent) ? ' AND ' . $this_parent . ' = ' . $rs->fields[$this_parent] : '';
        $sql = 'SELECT * FROM ' . $this_table . ' WHERE ' . $this_sort . $sql_op . 
               $rs->fields[$this_sort] . ' AND ' . $this_id . ' != ' . $rs->fields[$this_id] .
               $sql_parent . ' ORDER BY ' . $this_sort . ' ASC';
        if(($rs2 = $db->execute($sql)) && (!$rs2->EOF))
        {
            while(!$rs2->EOF)
            {
                $newsort[] = $rs2->fields[$this_id];
                $newsortp[] = $rs2->fields[$this_id];
                $rs2->MoveNext();
            }
        }
        if($_REQUEST['a'] == 'sortUp')
        {
            // Stick the current page in directly before the last one
            if(sizeof($newsort) > 0)
            {
                $oldend = array_pop($newsort);
                $newsort[] = $rs->fields[$this_id];
                $newsort[] = $oldend;
            }
            else
            {
                $newsort[] = $rs->fields[$this_id];
            }
        }
        
        // Set up SQL and fetch all records with same parent above this sort level
        $sql_op = ($_REQUEST['a'] == 'sortUp') ? ' >= ' : ' > ';
        $sql_parent = ($this_parent) ? ' AND ' . $this_parent . ' = ' . $rs->fields[$this_parent] : '';
        $sql = 'SELECT * FROM ' . $this_table . ' WHERE ' . $this_sort . $sql_op . 
               $rs->fields[$this_sort] . ' AND ' . $this_id . ' != ' . $rs->fields[$this_id] .
               $sql_parent . ' ORDER BY ' . $this_sort . ' ASC';
        if(($rs2 = $db->execute($sql)) && (!$rs2->EOF))
        {
            while(!$rs2->EOF)
            {
                $newsort[] = $rs2->fields[$this_id];
                $rs2->MoveNext();
            }
        }
        
        if($_REQUEST['a'] == 'sortDown')
        {
            // Stick the current page in directly before the last one
            if(sizeof($newsort) > 0)
            {
                $oldone = array_shift($newsort);
                array_unshift($newsort, $rs->fields[$this_id]);
                array_unshift($newsort, $oldone);
            }
            else
            {
                $newsort[] = $rs->fields[$this_id];
            }
            foreach($newsort as $key => $value)
            {
                $newsortp[] = $value;
            }
            $newsort = $newsortp;
        }
        
        foreach($newsort as $key=>$value)
        {
            $sql = 'UPDATE ' . $this_table . ' SET ' . $this_sort . ' = ' . ($key+1) . ' WHERE ' . $this_id . ' = ' . $value;
            $db->execute($sql);
        }
    }
    header('Location:' . $this_url . '&k_msg=' . urlencode('New page sorting applied.'));
}
elseif((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'bulkdelete'))
{
    /**
     * Delete Multiple Records
    */
    if((!empty($_REQUEST[$this_id])) && (is_array($_REQUEST[$this_id])))
    {
        if((!empty($_REQUEST['confirm'])) && ($_REQUEST['confirm'] == 1))
        {
            $count = 0;
            foreach($_REQUEST[$this_id] as $key => $value)
            {
                $sql = 'DELETE FROM ' . $this_table . ' WHERE ' . $this_id . ' = ' . $value;
                $db->execute($sql);                
            }
            $msg = '<b>' . $count . '</b> ' . $this_itemsl . ' deleted successfully.';
            header('Location:' . $this_url . '&k_msgok=' . urlencode($msg));
        }
        else
        {
            $_REQUEST['confirm'] = 1;
            dialog_form('Are you sure you want to permanently delete <b>' . count($_REQUEST[$this_id]) . '</b> ' . $this_itemsl . '?', $this_submit, $this_url, 'Confirm Delete', false, '', $_REQUEST);
        }
    }
    else
    {
        header('Location:' . $this_url . '&k_msg=' . urlencode('No ' . $this_itemsl . ' selected.'));
    }
}
else
{
    /**
     * Default View
    */

    // Make button HTML
    if(is_array($this_buttons))
    {
        $this_buttons_html = '';
        foreach($this_buttons as $key => $value)
        {
            $this_buttons_html .= '<a href="' . $value['url'] . '">' . $value['title'] . '</a>'; 
        }
    }

    // Show heading and buttons
    $this_html_top = '<h1>' . $lang_main . '</h1>';
    if($this_desc) $this_html_top .= '<p>' . $this_desc . '</p>';
    $this_html_top .= '<div class="btn">' . $this_buttons_html . '</div>';
	$tpl->define('mod_admin_top', $this_html_top, 1);	
	$tpl->parse('mod_admin_top');
    
    // Show list of records
	$grid = new kDataGrid($db, $this_url . '&');
    if($this_search) $grid->searchform = $this_search;
    if($this_filter) $grid->filter = $this_filter;
    
	$grid->sql = 'SELECT * FROM ' . $this_table;
    $grid->addColumns($this_cols);
    $grid_sort = (empty($_REQUEST['sort'])) ? $this_sort : $_REQUEST['sort'];
    $grid_order = (empty($_REQUEST['order'])) ? $this_order : $_REQUEST['order'];
    $grid->primary_col = $this_id;
    $grid->addFormOption('a', 'bulkdelete', 'Delete');
    $grid->addSubmit('Submit');

    $grid_html = $grid->display(1, $grid_sort, $grid_order, false, true);
    $tpl->define('grid', $grid_html, 1);
    $tpl->parse('grid');
}	

?>
