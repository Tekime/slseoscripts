<?php
/**
 * admin_header.php - Admin Header
 *
 * @author      Gabriel Harper <gharper@teknowledgery.com>
 * @copyright   Copyright 2003-2005; Gabriel Harper, All Rights Reserved
 * @version     1.0.0
 * @access      public
*/


/**
 *******************************************************************************
 * Section I: System Resource Initialization
 *******************************************************************************
*/

require_once(PATH_INCLUDE . 'bootstrap.php');

define('DIR_ADMIN_BASE', DIR_BASE . 'admin/');
define('DIR_ADMIN_TPL', DIR_ADMIN_BASE . 'tpl/');
define('PATH_ADMIN', PATH_BASE . 'admin/');

/**
 *******************************************************************************
 * Section IV: Authentication Initialization
 ******************************************************************************* 
*/

// Load component keys to set up permissions
$c_keys = array();
if($c_keys = $perm->get_keys())
{
	if(isset($c_keys['ADMIN']))
	{
		define('C_ADMIN', $c_keys['ADMIN']);
	}
}


/**
 *******************************************************************************
 * Section V: Session Intialization
 *******************************************************************************
*/

if(!empty($session->user_id))
{
	if($user = new kUser($db, $session->user_id))
	{
        $sql = 'SELECT user_id, usr_admin FROM ' . TBL_USERS . ' WHERE user_id = ' . $user->user_id;
        if(($rs = $db->execute($sql)) && (!$rs->EOF) && ($rs->fields['usr_admin'] == 1))
        {
        }
        else 
        {
    	   die('You are not authorized to view this page.');
        }
        
		$tpl->assign('u_username', $user->username);
		$tpl->assign('u_user_id', $user->user_id);
        $tpl->assign('session_msg', 'Logged in as ' . $user->username);
        $tpl->assign('session_login', '<a href="' . DIR_ADMIN_LOGOUT . '">Log Out</a>');
	}
    else
    {
		die($lang['error_user_load']);    
    }
}
else
{
    $tpl->assign('session_msg', 'Not logged in');
    $tpl->assign('session_login', '<a href="' . DIR_ADMIN_LOGIN . '">Log In</a>');
}
$tpl->add_path(PATH_ADMIN . 'tpl/');

if(defined('TPL_EXT'))
{
    $tpl->define('body_header', 'body_header' . TPL_EXT . '.tpl');
    $tpl->parse('body_header');    
}
else 
{
    $tpl->define('body_header', 'body_header.tpl');
    $tpl->parse('body_header');    
}


/**
 * Register Admin Menus and Panels
*/

$kApp->registerMenu('admin', 'site', 'Site Admin', '', 0);
$kApp->registerMenuItem('admin', 'site', 'site_createpage', 'New Page', DIR_ADMIN . 'pages.php?a=create', 1, 'main');
$kApp->registerMenuItem('admin', 'site', 'site_editpages', 'Edit Pages', DIR_ADMIN . 'pages.php', 2, 'main');
$kApp->registerMenuItem('admin', 'site', 'site_links', 'Site Links', DIR_ADMIN . 'sitelinks.php', 3, 'main');
$kApp->registerMenuItem('admin', 'site', 'site_users', 'Users', DIR_ADMIN . 'users.php', 4, 'main');

$kApp->registerMenu('admin', 'help', 'Help & Support', '', 999);
$kApp->registerMenuItem('admin', 'help', 'help_docs', 'Documentation', $lang['app_url_docs'], 2, '_blank');
$kApp->registerMenuItem('admin', 'help', 'help_forums', 'Community Forums', $lang['app_url_forums'], 2, '_blank');
$kApp->registerMenuItem('admin', 'help', 'help_blog', 'Scriptalicious Blog', $lang['app_blog_url'], 2, '_blank');
$kApp->registerMenuItem('admin', 'help', 'help_support', 'Technical Support', $lang['app_url_support'], 3, '_blank');

$kApp->registerPanel('admin', 'siteadmin', 'Site Administration', 0);
$kApp->registerPanelItem('admin', 'siteadmin', 'pages', 'Site Pages', DIR_ADMIN . 'pages.php', 'Pages let you add and manage content on your site. Pages are listed in the main menu and can contain HTML, CSS and JavaScript code.'); 
$kApp->registerPanelItem('admin', 'siteadmin', 'config', 'Site Configuration', DIR_ADMIN . 'config.php', 'Configure common settings for your Web site. Edit your site title, URL, META description and keywords, admin email and active template.');
$kApp->registerPanelItem('admin', 'siteadmin', 'users', 'User Accounts', DIR_ADMIN . 'users.php', 'Manage and update user accounts for site administration. You should change your password and username after installation.'); 
$kApp->registerPanelItem('admin', 'siteadmin', 'messages', 'Messages', DIR_ADMIN . 'messages.php', 'Messages are preset messages with dynamic fields used for automatic emails, system errors, and other text displayed to users.'); 

$kApp->registerMenu('admin', 'settings', 'Settings', '', 98);
$kApp->registerMenuItem('admin', 'settings', 'site_config', 'Site Config', DIR_ADMIN . 'config.php', 1, 'main');
$kApp->registerMenuItem('admin', 'settings', 'site_messages', 'Edit Messages', DIR_ADMIN . 'messages.php', 99, 'main');

?>
