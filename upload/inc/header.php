<?php
/**
 * header.php - Sitewide header file
 *
 * Copyright (c) 2006, Scriptalicious (www.scriptalicious.com)
 * All rights reserved.
 *
 * @version     1.0.0
*/

$k_runtime_start = microtime();

require_once(PATH_BASE . 'inc/bootstrap.php');

if(is_dir(PATH_BASE . 'install'))
{
    die('The `install` directory must be removed before this site can be viewed.');
}

// Set the active template directory
$tpl->add_path(PATH_TPL . $cfg->getVar('template') . '/');

if(!empty($_REQUEST['l'])) 
{
    $cfg->setVar('k_layout', $_REQUEST['l']);
}

?>
