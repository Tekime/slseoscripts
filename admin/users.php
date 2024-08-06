<?php
/**
 * users.php - User Administration
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

    $this_file = 'users.php';
    $this_desc = 'Manage and update user accounts for site administration. You should change your password and username after installation.';
    $lang_main = 'User Accounts';
    $lang_create = 'Create User';
    $this_id = 'user_id';
    
    $k_list_usr_admin = array(0 => 'No', 1 => 'Yes');
    
    $nav->add_link($lang['admin_name'], DIR_ADMIN . 'main.php');
    $nav->add_link('User Accounts', DIR_ADMIN . 'users.php');
    $nav->set_current('User Accounts');

    $kForm = new kForm(DIR_ADMIN . 'users.php', 'post');

    $kForm->addText('Username', 'usr_username','',32,32);
    $kForm->addDesc('usr_username', 'A unique username used to log in.');
    $kForm->addText('Email Address', 'usr_email', '', 50, 255);
    $kForm->addDesc('usr_email', 'A valid email address for user notifications.');
    $kForm->addPassword('Password', 'usr_password1','',18,16);
    $kForm->addPassword('Confirm Password', 'usr_password2','',18,16);
//    $kForm->addCheckbox('Administrator?', 'usr_admin', array(1 => ''), 0);
//    $kForm->addDesc('usr_admin', 'Only administrator accounts can access the Admin CP.');
    $kForm->addHidden('user_id');
    $kForm->addHidden('a');

    $kForm->title = $lang_main;
    $kForm->addRule('usr_username', 'required');
    $kForm->addRule('usr_email', 'required');

    if((!empty($_REQUEST['a'])) && ($_REQUEST['a']) == 'create')
    {
        $nav->set_current('Create User');
        $kForm->title = 'Create User';
        $kForm->addSubmit('Save >>');
        $kForm->renderForm(array('a' => 'save'));
    }
    elseif((!empty($_REQUEST['a'])) && ($_REQUEST['a']) == 'save')
    {
        $f_error = false;
        $kForm->addSubmit('Save >>');

        if(empty($_POST['user_id']))
        {
            $nav->set_current('Create User');
            $kForm->title = 'Create User';
            if(empty($_POST['usr_password1']) || empty($_POST['usr_password2']))
            {
                $f_error = true;
                $kForm->addError('usr_password1', 'Password required');
                $kForm->addError('usr_password2', 'Password required');
            }
            $sql = 'SELECT user_id FROM ' . TBL_USERS . ' WHERE usr_username = "' . $_POST['usr_username'] . '"';
            if(($rs = $db->execute($sql)) && (!$rs->EOF))
            {
                $f_error = true;
                $kForm->addError('usr_username', 'Username already taken.');
            }
        }
        else
        {
            $nav->set_current('Edit User');
            $kForm->title = 'Edit User';
        }

        if($validate->is_username($_POST['usr_username']) === false)
        {
            $f_error = true;
            $kForm->addError('usr_username', 'Invalid username. Must be 3-12 characters, and contain only letters, numbers, and underscores.');
        }

        if(!empty($_POST['usr_password1']) || !empty($_POST['usr_password2']))
        {
            if($validate->is_password($_POST['usr_password1']) === false)
            {
                $f_error = true;
                $kForm->addError('usr_password1', 'Invalid password. Must be 5-16 characters, and contain only letters, numbers, and underscores.');
            }
            else
            {
                if($_POST['usr_password1'] !== $_POST['usr_password2'])
                {
                    $f_error = true;
                    $kForm->addError('usr_password1', 'Passwords don\'t match');
                    $kForm->addError('usr_password2', 'Passwords don\'t match');
                }
            }
        }
            
        if((!$kForm->validate($_POST)) || ($f_error === true))
        {
            $kForm->heading = 'Please fix any errors and submit again.';
            $kForm->renderForm($_POST);    
        }
        else
        {
            $_REQUEST['usr_admin'] = 1;
            $_POST['usr_password'] = md5($_POST['usr_password1']);
            
            if(empty($_POST['user_id']))
            {
                $_POST['createdby'] = $session->user_id;
                
                $sql = db_getinsert($db, TBL_USERS, $_POST);
                if($db->execute($sql))
                {
                    header('Location:' . DIR_ADMIN_BASE . 'users.php?k_msgok=' . urlencode('The user <b>' . $_POST['usr_username'] . '</b> has been created successfully.'));
                }
                else
                {
                    die('Error creating new site record');
                }
            }
            else
            {
                $_POST['updatedby'] = $session->user_id;
                
                $sql = db_getupdate($db, TBL_USERS, $_POST, 'user_id = ' . intval($_POST['user_id']));

                if($db->execute($sql))
                {
                    header('Location:' . DIR_ADMIN_BASE . 'users.php?k_msgok=' . urlencode('The user <b>' . $_POST['usr_username'] . '</b> has been updated.'));
                }
                else
                {
                    die('Error editing site record');
                }
            } 
        }
    }
    elseif((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'delete'))
    {
        if((!empty($_REQUEST['user_id'])) && (is_numeric($_REQUEST['user_id'])))
        {
            if((!empty($_REQUEST['confirm'])) && ($_REQUEST['confirm'] == 1))
            {
                $sql = 'DELETE FROM ' . TBL_USERS . ' WHERE user_id = ' . intval($_REQUEST['user_id']);
                
                if($db->execute($sql))
                {
                    dialog_box('User permanently deleted.', DIR_ADMIN_BASE . 'users.php', 0);                
                }
                else
                {
                    die('Unable to delete user.');
                }
            
            }
            else
            {
                dialog_box('Are you sure you want to delete this user?', DIR_ADMIN_BASE . 'users.php?a=delete&user_id=' . intval($_REQUEST['user_id']) . '&confirm=1', DIR_ADMIN_BASE . 'users.php', 'Confirm Delete', false);            
            }
        }

    }
    elseif((!empty($_REQUEST['a'])) && ($_REQUEST['a']) == 'edit')
    {
        if(!empty($_REQUEST['user_id']))
        {
            $sql = 'SELECT * FROM ' . TBL_USERS . ' WHERE user_id = ' . $_REQUEST['user_id'];
            if(($rs = $db->execute($sql)) && (!$rs->EOF))
            {
                $nav->set_current('Editing User');
            
                $kForm->addSubmit('Save >>');
                $kForm->title = 'Edit User';
                $kForm->heading = '';
                $rs->fields['a'] = 'save';
                $kForm->renderForm($rs->fields);
            }
        }
    }
    else
    {
    	$tpl->define('page_top', '<h1>' . $lang_main . '</h1><div class="btn"><a href="' . DIR_ADMIN_BASE . $this_file . '?a=create">Create User</a></div>', 1);	
    	$tpl->parse('page_top');

    	$grid = new kDataGrid($db, DIR_ADMIN_BASE . 'users.php?');
    	
    	$grid->sql = 'SELECT * FROM ' . TBL_USERS;
        
        $grid->addColumns(array(
            'user_id' => array(
                'field' => $this_id,
                'sortable' => false,
                'width' => '16px',
                'type' => 'checkbox'
            ),
            'usr_username' => array(
                'field' => 'usr_username',
                'title' => 'Username',
                'href' => DIR_ADMIN_BASE . $this_file . '?a=edit&' . $this_id . '={' . $this_id . '}',
                'classext' => 'Pad'
            ),
            'usr_email' => array(
                'field' => 'usr_email',
                'title' => 'Email Address',
                'classext' => 'Pad'
            ),
            'usr_admin' => array(
                'field' => 'usr_admin',
                'title' => 'Admin',
                'lookup' => $k_list_usr_admin
            ),
            'delete' => array(
                'sortable' => false,
                'href' => DIR_ADMIN_BASE . $this_file . '?a=delete&' . $this_id . '={' . $this_id . '}',
                'text' => 'Delete'
            ),
            'created' => array(
                'field' => 'datecreated',
                'title' => 'Added',
                'filter' => 'shortdate'
            )
        ));
        
        
        $grid_sort = 'user_id';
        $grid_order = 'asc';
        if(!empty($_REQUEST['sort'])) $grid_sort = $_REQUEST['sort'];
        if(!empty($_REQUEST['order'])) $grid_order = $_REQUEST['order'];
        $grid->primary_col = 'user_id';
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