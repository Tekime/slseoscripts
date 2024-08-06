<?php
/**
 * system.php - Kytoo System Tools
 *
 * Simple interface for system tools and back-end stuff that should usually be left alone.
 *
 * This script is part of Kytoo CMS (www.kytoo.com).
 *
 * Copyright (c) 2009, Kytoo (www.kytoo.com)
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
        $nav->add_link($lang['admin_name'], DIR_ADMIN . 'main.php');
        $nav->add_link('System Tools', DIR_ADMIN . 'system.php');
        $nav->set_current('System Tools');
        
        // Show the default 'home' page
    	$tpl->define('system', 'system.tpl');	
    	$tpl->parse('system');
        

        $tpl->assign('stats_phpversion', phpversion());
        $stats_serversoftware = ($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'unknown';
        $tpl->assign('stats_serversoftware', $stats_serversoftware);
        $tpl->assign('stats_loadedextensions', implode(', ', get_loaded_extensions()));
        $tpl->assign('stats_mysqlinfo', mysql_get_server_info($db->link));
        $tpl->assign('stats_mysqlclientinfo', mysql_get_client_info());
        $tpl->assign('stats_mysqlhostinfo', mysql_get_host_info());
        
        $tpl->assign('stats_memusage', memory_get_usage());
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