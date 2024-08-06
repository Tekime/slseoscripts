<?php
/**
 * admin_footer.php - Admin Footer
 *
 * @author      Gabriel Harper <gharper@teknowledgery.com>
 * @copyright   Copyright 2003-2005; Gabriel Harper, All Rights Reserved
 * @version     1.0.0
 * @access      public
*/

if(defined('TPL_EXT'))
{
    $tpl->define('body_footer', 'body_footer' . TPL_EXT . '.tpl');
    $tpl->parse('body_footer');    
}
else 
{
    $tpl->define('body_footer', 'body_footer.tpl');
    $tpl->parse('body_footer');    
}

// Assign common values to template variables

$tpl->assign('dir_base', DIR_BASE);
$tpl->assign('dir_admin_base', DIR_ADMIN_BASE);
$tpl->assign('dir_tpl', DIR_ADMIN_TPL);
$tpl->assign('dir_tpl_images', DIR_ADMIN_TPL . 'images/');
$tpl->assign('body_onload', '');
$tpl->assign('php_self', $_SERVER['PHP_SELF']);
$tpl->assign('dir_images', DIR_IMAGES);
$tpl->assign('path_images', PATH_IMAGES);

// Assign all $cfg fields to template
foreach($cfg->getVars() as $k=>$v)
{
    $tpl->assign($k, $v);
}

// Assign all $lang fields to template
foreach($lang as $k=>$v)
{
    $tpl->assign('lang_' . $k, $v);
}

?>
