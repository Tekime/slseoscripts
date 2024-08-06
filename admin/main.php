<?php
/**
 * main.php - Kytoo CMS main admin page
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
    $sql = 'SELECT user_id, usr_admin FROM ' . TBL_USERS . ' WHERE user_id = ' . $user->user_id;
    if(($rs = $db->execute($sql)) && (!$rs->EOF) && ($rs->fields['usr_admin'] == 1))
    {
    
        if((!empty($_REQUEST['m'])) && ($modules->isModule($_REQUEST['m'])))
        {
            $mod_tag = $_REQUEST['m'];
            
            $nav->add_link($lang['admin_name'], DIR_ADMIN . 'main.php');
            $nav->set_current($modules->mod[$mod_tag]['mod_title']);
        
            if(!empty($_REQUEST['v']))
            {
                $mod_view = $_REQUEST['v'];
            }
            else 
            {
                $mod_view = 'index.php';
            }
            
            if(substr($mod_view, -4) !== '.php') $mod_view .= '.php';
            $mod_file = PATH_MODULES . $mod_tag . '/admin/' . $mod_view;
            
            $k_mod_id = $mod_tag;
            $k_mod_name = $modules->mod[$mod_tag]['mod_name'];
            $k_mod_title = $modules->mod[$mod_tag]['mod_title'];
            $k_mod_url = DIR_ADMIN . 'main.php?m=' . $k_mod_id;
            
            if(file_exists($mod_file))
            {
                $tpl->add_path(PATH_MODULES . $mod_tag . '/admin/tpl/');
                include($mod_file);
            }
            else 
            {
                $tpl->define('main', 'Module error - view not available', 1);
                $tpl->parse('main');
            }
            
        }
        else 
        {
            // Show the default 'home' page
        	$tpl->define('main', 'main.tpl');	
        	$tpl->parse('main');

            $tpl->assign('stats_phpversion', phpversion());
            $stats_serversoftware = ($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'unknown';
            $tpl->assign('stats_serversoftware', $stats_serversoftware);
            $tpl->assign('stats_loadedextensions', implode(', ', get_loaded_extensions()));
            $tpl->assign('stats_mysqlinfo', mysql_get_server_info($db->link));
            $tpl->assign('stats_mysqlclientinfo', mysql_get_client_info());
            $tpl->assign('stats_mysqlhostinfo', mysql_get_host_info());

            if(function_exists('memory_get_usage'))
            {            
                $tpl->assign('stats_memusage', memory_get_usage());
            }
            
            $sql = 'SELECT COUNT(*) AS count FROM ' . TBL_MAILQUEUE;
            if(($rs = $db->execute($sql)) && (!$rs->EOF))
            {
                $tpl->assign('mailqueue_count', $rs->fields['count']);
            }
            
            $sql = 'SELECT user_id FROM ' . TBL_USERS . ' WHERE usr_username="admin" AND usr_password="21232f297a57a5a743894a0e4a801fc3"';
            if(($rs = $db->execute($sql)) && (!$rs->EOF))
            {
                $kApp->addMessage('<b>Alert:</b> You should <a href="' . DIR_ADMIN . 'users.php?a=edit&user_id=' . $rs->fields['user_id'] . '">set your password</a> immediately to avoid security risks.', 'alert');                
            }
            
            if($cfg->getVar('site_name') == 'Scriptalicious SEO Scripts')
            {
                $kApp->addMessage('<b>Reminder:</b> You should <a href="' . DIR_ADMIN . 'config.php">configure your site</a> soon and set a unique title, name and site email.');   
            }
            
        }
    }
    else 
    {
	   dialog_box('You are not authorized to view this page.', DIR_BASE, null, 'Not Authorized');
    }
}
else 
{
	// dialog_box('You must be logged in to access this page. Please click Ok to log in.', DIR_ADMIN_BASE . 'login.php', null, 'Please Log In');
}

require_once(PATH_INCLUDE . 'admin_footer.php');
$tpl->render_all();

?>