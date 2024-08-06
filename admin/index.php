<?php
/**
 * index.php - Kytoo Admin
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
 */
 
require_once('../config.php');
define('TPL_EXT', '_clean');
require_once(PATH_INCLUDE . 'admin_header.php');


// Check for active user
if(!empty($session->user_id))
{
    $sql = 'SELECT user_id, usr_admin FROM ' . TBL_USERS . ' WHERE user_id = ' . $user->user_id;
    if(($rs = $db->execute($sql)) && (!$rs->EOF) && ($rs->fields['usr_admin'] == 1))
    {
        if((!empty($_REQUEST['m'])) && ($modules->isModule($_REQUEST['m'])))
        {
            $mod_tag = $_REQUEST['m'];
            
            $nav->add_link($lang['admin_name'], DIR_ADMIN . 'main.php');
            $nav->add_link($modules->mod[$mod_tag]['mod_name'], DIR_ADMIN . 'main.php?m=' . $mod_tag);
            $nav->set_current($lang_main);
        
        }
        else 
        {
        	// Show the default 'home' page
        	$tpl->define('admin', 'index.tpl');	
        	$tpl->parse('admin');
        }
    }
    else 
    {
	   dialog_box('You are not authorized to view this page.', DIR_BASE, null, 'Not Authorized');
    }
}
else 
{
    header('Location:'.DIR_ADMIN_BASE . 'login.php');
	//dialog_box('You must be logged in to access this page. Please click Ok to log in.', DIR_ADMIN_BASE . 'login.php', null, 'Please Log In');
}

$tpl->assign('head_title', $lang['app_admin_title']);


require_once(PATH_INCLUDE . 'admin_footer.php');
$tpl->render_all();
?>