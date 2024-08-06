<?php
/**
 * bids.php - LinkBid Bids Admin
 *
 * This script is part of Kytoo CMS (www.kytoo.com).
 *
 * Copyright (c) 2007, Kytoo (www.kytoo.com)
 * All rights reserved.
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
 * @version     1.0
 * 
*/

require_once('../config.php');
require_once(PATH_INCLUDE . 'admin_header.php');


// Check for active user
if(!empty($session->user_id))
{
    
    $lang_create = 'Create Message';
    $lang_edit = 'Edit Message';
    $lang_delete = 'Delete Message';
    $lang_save = 'Save Message';
    $lang_main = 'Messages';
    $this_table = TBL_MESSAGES;
    $this_file = 'messages.php';
    $this_id = 'message_id';
    $this_tag = 'messages';
    $this_desc = 'Messages are preset messages with dynamic fields used for automatic emails, system errors, and other text displayed to users.';
    
    $nav->add_link($lang['admin_name'], DIR_ADMIN . 'main.php');
    $nav->add_link($lang_main, DIR_ADMIN . $this_file);
    $nav->set_current($lang_main);

    $kForm = new kForm(DIR_ADMIN . $this_file, 'post');

    $kForm->addText('Message Title', 'msg_title', '', 50, 255);
    $kForm->addText('Message Subject', 'msg_subject', '', 50, 255);
    $kForm->addTextarea('Message Text', 'msg_text', '',10,80);
    $kForm->addText('Message Name <span style="font-weight:normal;">(Do NOT edit unless you know exactly what you are doing)</span>', 'msg_name','',24,50);

    $kForm->addHidden($this_id);
    $kForm->addHidden('a');
    
//    $kForm->addRule('msg_name', 'required');
    $kForm->addRule('msg_title', 'required');
    $kForm->addRule('msg_text', 'required');
    $kForm->title = $lang_main;
    
    if((!empty($_REQUEST['a'])) && ($_REQUEST['a']) == 'create')
    {
        $nav->set_current($lang_create);
        $kForm->title = $lang_create;
        $kForm->addSubmit($lang_save . ' >>');
        $kForm->renderForm(array('a' => 'save'));
    }
    elseif((!empty($_REQUEST['a'])) && ($_REQUEST['a']) == 'save')
    {
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
            $kForm->heading = 'Please fix any errors and submit again.';
            $kForm->renderForm($_REQUEST);    
        }
        else
        {
            if(!empty($_REQUEST[$this_id]))
            {
                $_REQUEST['updatedby'] = $session->user_id;
                
                $sql = db_getupdate($db, $this_table, $_REQUEST, $this_id . ' = ' . intval($_REQUEST[$this_id]));

                if($db->execute($sql))
                {
                    header('Location:' . DIR_ADMIN_BASE . $this_file . '?k_msgok=' . urlencode('Message saved successfully.'));
                }
                else
                {
                    header('Location:' . DIR_ADMIN_BASE . $this_file . '?k_msgerror=' . urlencode('Error updating message.'));
                }
            } 
        }
    }
    elseif((!empty($_REQUEST['a'])) && ($_REQUEST['a']) == 'edit')
    {
        if(!empty($_REQUEST[$this_id]))
        {
            $sql = 'SELECT * FROM ' . $this_table . ' WHERE ' . $this_id . ' = ' . $_REQUEST[$this_id];
            if(($rs = $db->execute($sql)) && (!$rs->EOF))
            {
                $nav->set_current($lang_edit);
            
                $kForm->addSubmit($lang_save . ' >>');
                $kForm->title = $lang_edit;
                $kForm->heading = '';
                $rs->fields['a'] = 'save';
                $kForm->renderForm($rs->fields);
            }
        }
    }
    else
    {
        // Show the default 'home' page
        $tpl->define('links', '<h1>' . $lang_main . '</h1><p>' . $this_desc . '</p>', 1);	
    	$tpl->parse('links');

    	$grid = new kDataGrid($db, DIR_ADMIN_BASE . $this_file . '?');
    	
    	$grid->sql = 'SELECT * FROM ' . $this_table;
    	
        
        $grid->addColumns(array(
            'msg_name' => array(
                'field' => 'msg_name',
                'title' => 'Message Name',
                'href' => DIR_ADMIN_BASE . $this_file . '?a=edit&' . $this_id . '={' . $this_id . '}',
                'text' => '{msg_name}',
                'classext' => 'Pad'
            ),
            'msg_title' => array(
                'field' => 'msg_title',
                'title' => 'Message Title',
                'text' => '{msg_title}',
                'classext' => 'Pad'
            )
        ));
        
        $grid_sort = $this_id;
        $grid_order = 'asc';
        if(!empty($_REQUEST['sort'])) $grid_sort = $_REQUEST['sort'];
        if(!empty($_REQUEST['order'])) $grid_order = $_REQUEST['order'];
        
        $grid_html = $grid->display(1, $grid_sort, $grid_order);
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