<?php
/**
 * footer.php - Sitewide footer file
 *
 * Copyright (c) 2006, Scriptalicious (www.scriptalicious.com)
 * All rights reserved.
 *
 * @version     1.0.0
*/

/**
 * Check and assign some template variables
*/

// Convert config URL variables
foreach($cfg->fields as $mod=>$field)
{
    foreach($field as $k=>$v)
    {
        // Convert URL variables
        preg_match_all('/%([A-Za-z0-9_]{2,32})%/', $v, $v_matches);
        if(!empty($v_matches[1][0]))
        {
            foreach($v_matches[1] as $key => $value)
            {
                $v = str_replace('%' . $value . '%', '{' . $value . '}', $v);
                $cfg->setVar($k, $v, $mod);
            }
        }
    }
}

// Set the title displayed in a browser
$ptitle = $cfg->getVar('page_title');
if(empty($ptitle)) $cfg->setVar('page_title', $cfg->getVar('site_title'));
if($cfg->getVar('page_title') !== $cfg->getVar('site_title'))
{
    $tpl->assign('head_title', $cfg->getVar('page_title') . $cfg->getVar('site_title_tail'));
}
else
{
    $tpl->assign('head_title', $cfg->getVar('site_title'));
}

// Parse config vars to template
foreach($cfg->fields as $mod=>$field)
{
    foreach($field as $k=>$v)
    {
        $tpl->assign($k, $v);
    }
}

// Assign all $lang fields to template
foreach($lang as $k=>$v)
{
    $tpl->assign('lang_' . $k, $v);
}

// Assign system path and directory variables
$tpl->assign('dir_base', DIR_BASE);
$tpl->assign('dir_tpl', DIR_BASE . 'tpl/' . $cfg->getVar('template') . '/');
$tpl->assign('dir_tpl_images', DIR_BASE . 'tpl/' . $cfg->getVar('template') . '/images/');
$tpl->assign('dir_images', DIR_BASE . 'images/');


if($session->user_id)
{
    $user = new kUser($db, $session->user_id);
    $tpl->assign('session_msg', 'Logged in as ' . $user->username);
    $tpl->assign('usr_username', $user->username);
    $tpl->assign('usr_email', $user->email);
    $tpl->assign('session_login', '<a href="' . DIR_BASE . 'logout.php">Log Out</a>');
}
else 
{
    $tpl->assign('session_msg', 'Not logged in');
    $tpl->assign('session_login', '<a href="' . DIR_BASE . 'login.php">Log In</a>');
}

$tpl->assign('block_usermenu', blockGetContents('usermenu'));
$tpl->assign('block_loginbox', blockGetContents('loginbox'));
$tpl->assign('block_menu_user', blockGetContents('menu_user'));


// Run auto jobs
if($cfg->getVar('jobs_autorun'))
{
    if($cfg->getVar('expire_lastrun') < (time() - ($cfg->getVar('jobs_autorun') * 60)))
    {
        runEmailJobs();
        $sql = 'UPDATE ' . TBL_CONFIG . ' SET cfg_value = "' . time() . '" WHERE cfg_field = "expire_lastrun"';
        $db->execute($sql);
    }   
}

$k_runtime_end = microtime();
$k_runtime = $k_runtime_start - $k_runtime_end;

$tpl->assign('k_runtime', $k_runtime);


// Render final output
$k_this_content = $tpl->render_all(1);
$k_layout = ($cfg->getVar('k_layout')) ? 'layout_' . $cfg->getVar('k_layout') . '.tpl' : 'layout_default.tpl';

$ktpl = new kTemplate();
$ktpl->paths = $tpl->paths;
$ktpl->vars = $tpl->vars;
$ktpl->define('out', $k_layout);
$ktpl->parse('out');
$ktpl->assign('k_content', $k_this_content);
$ktpl->render_all();


?>
