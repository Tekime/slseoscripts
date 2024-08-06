<?php
/**
 * kytoo_system_admin.php - Template for system admin script in Kytoo 2.0
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
 * 2009-05-06 (1.0) - First release
 *
*/
require_once('../config.php');
require_once(PATH_INCLUDE . 'admin_header.php');

// Check for active user
if(!empty($session->user_id))
{

/* Module admin variables: $k_mod_id, $k_mod_name, $k_mod_title, $k_mod_url */

// Required: Configure module admin settings
$this_item = 'Link';
$this_items = 'Links';
$this_title = 'Site Links';
$this_table = TBL_SITELINKS;
$this_id = 'sitelink_id';
$this_view = '';
$this_edit_return = false;
$this_sort = 'lnk_title';
$this_order = 'asc';
$this_parent = false;
$this_parent_name = false;
$this_table_parent = false;

// Optional: Set up lists
$yesno_select = array(1 => 'Yes', 0 => 'No');
$k_list_sitelink_status = array(0 => 'Disabled', 1 => 'Active');

// Optional: Configure unique fields
// $this_unique_cols = array('pg_safename');

// Optional: Configure safename JS fields
// $this_safename_cols = array('pg_name' => 'pg_safename', 'pg_safename' => 'pg_safename');

// Optional: Configure search & filter options
// $this_search = array('page_title' => 'Page title');
$this_filter = array('name' => 'Status', 'field' => 'lnk_status', 'values' => $k_list_sitelink_status);

// System: Configure default module admin settings
$this_file = 'sitelinks.php';
$this_url = DIR_ADMIN . $this_file;
$this_iteml = strtolower($this_item);
$this_itemsl = strtolower($this_items);
$this_submit = DIR_ADMIN . $this_file;
$lang_create = $lang['create'] . ' ' . $this_item;
$lang_save = $lang['save'] . ' ' . $this_item;
$lang_edit = $lang['edit'] . ' ' . $this_item;
$lang_main = $this_title;

// Optional: Buttons and extended setup
$this_buttons = array(
    array('url' => $this_url . '?a=create', 'title' => $lang['create'] . ' New ' . $this_item)
);

// Required: Configure data columns
$this_cols = array(
    'sitelink_id' => array(
        'field' => $this_id,
        'sortable' => false,
        'width' => '16px',
        'type' => 'checkbox'
    ),
    'lnk_title' => array(
        'title' => 'Link Name',
        'href' => DIR_ADMIN_BASE . $this_file . '?a=edit&' . $this_id . '={' . $this_id . '}',
        'classext' => 'Pad'
    ),
    'lnk_url' => array(
        'title' => 'Link URL',
        'classext' => 'Pad'
    ),
    'lnk_status' => array(
        'title' => 'Status',
        'lookup' => $k_list_sitelink_status
    ),
    'delete' => array(
        'sortable' => false,
        'href' => DIR_ADMIN_BASE . $this_file . '?a=delete&' . $this_id . '={' . $this_id . '}',
        'text' => 'Delete'
    )
);

// Add default navigation for module
$nav->add_link($lang['admin_name'], DIR_ADMIN . 'main.php');
$nav->add_link($lang_main, $this_url);
$nav->set_current($lang_main);

// Initialize form as needed
if((!empty($_REQUEST['a'])) && (($_REQUEST['a'] == 'create') | ($_REQUEST['a'] == 'edit') || ($_REQUEST['a'] == 'save')))
{
    $kForm = new kForm(DIR_ADMIN . $this_file, 'post');
    
    $kForm->addText('Link Title', 'lnk_title', '', 60, 255, 'http://');
    $kForm->addDesc('lnk_title', 'The anchor text to display for this link.');
    $kForm->addText('Link URL', 'lnk_url', '', 60, 255, 'http://');
    $kForm->addDesc('lnk_url', 'The full URL for this link, including `http://`.');

    $kForm->addTextarea('Link Summary', 'lnk_summary', '', 2,60);
    $kForm->addDesc('lnk_summary', 'A description or text summary to display below the link.');
    $kForm->addSelect('Status', 'lnk_status', $k_list_sitelink_status, 1);
    $kForm->addDesc('lnk_status', 'Disable/enable this link in menus.');

    $kForm->addCheckbox('Open in New Window?', 'lnk_openwin', array(1 => ''), 0);
    $kForm->addDesc('lnk_openwin', 'Check this box to open the link in a new browser window/tab.');

    $kForm->addCheckbox('Add nofollow?', 'lnk_nofollow', array(1 => ''), 0);
    $kForm->addDesc('lnk_nofollow', 'Check this box to add the rel="nofollow" attribute to this link.');

    $kForm->addHtml('whatisnofollow', ' &nbsp; &nbsp; <small><a href="http://www.gabrielharper.com/2009/06/what-is-relnofollow-and-should-i-use-it/" target="_blank">What is nofollow?</a></small>');

    $kForm->addHidden($this_id);
    $kForm->addHidden('a');

    $kForm->addRule('lnk_url', 'required');
    $kForm->addRule('lnk_url', 'url');
    $kForm->addRule('lnk_title', 'required');

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
                    $url = $this_url . '?a=edit&' . $this_id . '=' . intval(mysql_insert_id($db->link)) . '&';
                    $msg = 'Your ' . $this_iteml . ' has been created successfully. <a href="' . $this_url . '">Return to ' . $this_itemsl . '</a> or continue editing below.';
                }
                else
                {
                    $url = $this_url . '?';
                    $msg = 'Your ' . $this_iteml . ' has been created successfully.';                
                }
                header('Location:' . $url . 'k_msgok=' . urlencode($msg));
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
                header('Location:' . $this_url . '?a=edit&category_id=' . intval($_REQUEST[$this_id]) . '&k_msgok=' . urlencode($msg));
                
                if($this_edit_return)
                {
                    $url = $this_url . '?a=edit&' . $this_id . '=' . intval($_REQUEST[$this_id]) . '&';
                    $msg = 'Your ' . $this_iteml . ' was saved successfully. <a href="' . $this_url . '">Return to ' . $this_itemsl . '</a> or continue editing below.';
                }
                else
                {
                    $url = $this_url . '?';
                    $msg = 'Your ' . $this_iteml . ' was saved successfully.';                
                }
                header('Location:' . $url . 'k_msgok=' . urlencode($msg));
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
                
                header('Location:' . $this_url . '?k_msg=' . urlencode($msg));
            }
            else
            {
                die('Unable to delete.');
            }
        }
        else
        {
            dialog_box('Are you sure you want to delete this record?', $this_url . '?a=delete&' . $this_id . '=' . intval($_REQUEST[$this_id]) . '&confirm=1', $this_url, 'Confirm Delete', false);
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
    header('Location:' . $this_url . '?k_msg=' . urlencode('New page sorting applied.'));
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
            header('Location:' . $this_url . '?k_msgok=' . urlencode($msg));
        }
        else
        {
            $_REQUEST['confirm'] = 1;
            dialog_form('Are you sure you want to permanently delete <b>' . count($_REQUEST[$this_id]) . '</b> ' . $this_itemsl . '?', $this_submit, $this_url, 'Confirm Delete', false, '', $_REQUEST);
        }
    }
    else
    {
        header('Location:' . $this_url . '?k_msg=' . urlencode('No ' . $this_itemsl . ' selected.'));
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

    if($this_parent)
    {
    	$grid = new kDataGrid($db, $this_url . '?', $this_id, $this_parent, $this_parent_name);    
    }
    else
    {
    	$grid = new kDataGrid($db, $this_url . '?');
    }
    if($this_search) $grid->searchform = $this_search;
    if($this_filter) $grid->filter = $this_filter;
    
	$grid->sql = 'SELECT * FROM ' . $this_table;
    $grid->addColumns($this_cols);
    $grid_sort = (empty($_REQUEST['sort'])) ? $this_sort : $_REQUEST['sort'];
    $grid_order = (empty($_REQUEST['order'])) ? $this_order : $_REQUEST['order'];
    $grid->page = (empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
    $grid->primary_col = $this_id;    
    $grid->addFormOption('a', 'bulkdelete', 'Delete');
    $grid->addSubmit('Submit');

    $grid_html = $grid->display(1, $grid_sort, $grid_order, 0, true);
    $tpl->define('grid', $grid_html, 1);
    $tpl->parse('grid');
}	


}
else 
{
    dialog_box('You must be logged in to access this page. Please click Ok to log in.', DIR_ADMIN_BASE . 'login.php', null, 'Please Log In');
}

require_once(PATH_INCLUDE . 'admin_footer.php');
$tpl->render_all();

?>
