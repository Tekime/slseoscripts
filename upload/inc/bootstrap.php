<?php
/**
 * bootstrap.php - Kytoo System Initialization Script
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.2
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
 * 2009-05-20 (1.2) - Changed DB_ to K_DB_, Added kBase
 * 2009-05-04 (1.1) - Updated for Kytoo 2.0
 *
*/

// Make sure database config has been created
if((!defined('K_DB_USERNAME')) || (!defined('K_DB_HOST')) || (!defined('K_DB_NAME')) || (!defined('K_DB_PASSWORD')))
{
    die('Database configuration not found. Please run the installer to configure your Web site.');
}

define('PATH_CONTENT', PATH_BASE . 'content/');
define('PATH_LIB', PATH_BASE . 'klib/');
define('PATH_TPL', PATH_BASE . 'tpl/');
define('PATH_TMP', PATH_BASE . 'tmp/');
define('PATH_MODULES', PATH_BASE . 'modules/');
define('PATH_IMAGES', PATH_BASE . 'images/');
define('PATH_LANG', PATH_INCLUDE . 'lang/');
define('DIR_TPL', DIR_BASE . 'tpl/');
define('DIR_IMAGES', DIR_BASE . 'images/');
define('DIR_MODULES', DIR_BASE . 'modules/');
define('DIR_ADMIN', DIR_BASE . 'admin/');
define('PATH_SYSTMP', PATH_BASE . 'systmp/');
define('DIR_SYSTMP', DIR_BASE . 'systmp/');
define('K_VIEW_INDEX', 'index');

require_once(PATH_LANG . 'core.php');
require_once(PATH_LIB . 'class.kBase.php');
require_once(PATH_LIB . 'class.kDb.php');

// Try to get a database connection
$db = new kDb(K_DB_HOST, K_DB_USERNAME, K_DB_PASSWORD, K_DB_NAME);
if(!$db->connect()) die($db->ErrorMsg());

// Define a named constant for each table name
if(($rs = $db->execute('SHOW TABLES')) && (!$rs->EOF))
{
    while(!$rs->EOF)
    {
        define(strtoupper($rs->fields[0]), $rs->fields[0]);
        $rs->MoveNext();
    }
}

/**
 * Include all framework class files. Check for autoloading objects support (PHP 5+) and 
 * use it if we can. Otherwise include all the class files on every load.
*/
if(function_exists('spl_autoload_register'))
{
    function __autoload($class)
    {
        if(file_exists(PATH_LIB . 'class.' . $class . '.php'))
        {
            require_once(PATH_LIB . 'class.' . $class . '.php');
        }
    }
}
else
{
    // Automate by getting class filenames in klib
    require_once(PATH_LIB . 'class.kApplication.php');
    require_once(PATH_LIB . 'class.kConfig.php');
    require_once(PATH_LIB . 'class.kTemplate.php');
    require_once(PATH_LIB . 'class.kDataGrid.php');
    require_once(PATH_LIB . 'class.kPager.php');
    require_once(PATH_LIB . 'class.kValidate.php');
    require_once(PATH_LIB . 'class.kPerm.php');
    require_once(PATH_LIB . 'class.kSession.php');
    require_once(PATH_LIB . 'class.kUrl.php');
    require_once(PATH_LIB . 'class.kForm.php');
    require_once(PATH_LIB . 'class.kNav.php');
    require_once(PATH_LIB . 'class.kUser.php');
    require_once(PATH_LIB . 'class.kModules.php');
    require_once(PATH_LIB . 'class.kWebSearch.php');
    require_once(PATH_LIB . 'class.kWebStats.php');
    require_once(PATH_LIB . 'class.kDecaptcha.php');
    require_once(PATH_LIB . 'class.kFeed.php');
}
require_once(PATH_LIB . 'k_functions_text.php');
require_once(PATH_LIB . 'k_functions_http.php');
require_once(PATH_LIB . 'k_functions_client.php');
require_once(PATH_LIB . 'kFunctions.php');
require_once(PATH_LIB . 'kBlocks.php');



$cfg = new kConfig($db);
if(!$cfg->getVar('lang')) $cfg->setVar('lang', 'en');
require_once(PATH_LANG . 'lang_' . $cfg->getVar('lang') . '.php');

$tpl = new kTemplate();
$session = new kSession($db);
$url = new kUrl($_SERVER['REQUEST_URI']);
$modules = new kModules($db);
$validate = new kValidate();
$perm = new kPerm($db);
$nav = new kNav($tpl);

$kApp = new kApplication();
$kApp->registerInterface('main', 'Main Site');
$kApp->registerInterface('admin', 'Admin CP', '', 'admin');

// Clean up HTTP_REQUEST variables
clean_request_vars();

define('DIR_ADMIN_LOGIN', DIR_ADMIN . 'login.php');
define('DIR_ADMIN_LOGOUT', DIR_ADMIN . 'logout.php');

$cfg->setVar('dir_base', DIR_BASE);
$cfg->setVar('dir_images', DIR_IMAGES);
$cfg->setVar('dir_admin', DIR_ADMIN);

$tpl->assign('dir_admin_login', DIR_ADMIN_LOGIN);
$tpl->assign('dir_admin_logout', DIR_ADMIN_LOGOUT);

$k_var_patterns = array(
    'tag' => '([A-Za-z0-9_-]*)',
    'tags' => '([/A-Za-z0-9_-]*)',
    'wildcard' => '(.*)',
    'number' => '([0-9]*)',
    'title' => '([A-Za-z0-9_-]*)',
    'page_num' => '([0-9]{1,5})',
    'email' => '(.*)',
    'letter' => '([A-Za-z]|09)',
    'safename' => '([A-Za-z0-9_-]*?)'
    );

/* Discover and register page layouts from current theme */
if($filenames = getMatchingFiles(PATH_TPL . $cfg->getVar('template') . '/', '/^layout_([A-Za-z0-9-_.]{2,50}).tpl$/'))
{
    foreach($filenames as $key=>$value)
    {
        $value = trim($value);
        if(file_exists(PATH_TPL . $cfg->getVar('template') . '/layout_' . $value . '.tpl'))
        {
            $kApp->registerLayout($value);
        }
    }
}

/* Set up theme styles */
$k_theme_styles = array();
// Check for custom styles and create list
if($k_theme_styles = k_get_dir(PATH_TPL . $cfg->getVar('template') . '/styles/', true, false))
{
    asort($k_theme_styles);
    if(file_exists(PATH_TPL . $cfg->getVar('template') . '/style.css'))
    {
        array_unshift($k_theme_styles, 'Default');
    }
}
else
{
    $k_theme_styles[] = 'Default';
}
// Add default as first option, even if the file doesn't exist - we'll check later.

/* Register Application URL Variables */
$kApp->registerUrlVar('search_term', $k_var_patterns['wildcard']);
$kApp->registerUrlVar('pg_safename', $k_var_patterns['safename']);

/* Register Application Views */
$kApp->registerView('pages', $cfg->getVar('urlformat_page'), array('v' => 'pages'), 1, 'Pages');  
$kApp->registerView('search', $cfg->getVar('urlformat_search'), array('v' => 'search', 'a' => 'search'), 2, 'Search');  

/* Load Modules */
foreach($modules->mod as $key=>$module)
{
    // Add module template path
    $tpl->add_path(PATH_MODULES . $module['mod_tag'] . '/tpl/');

    // Dynamically load module includes
    if($filenames = getMatchingFiles(PATH_MODULES . $module['mod_tag'] . '/inc/', '/^([A-Za-z0-9-_.]{2,50}.php)$/'))
    {
        foreach($filenames as $key=>$filename)
        {
            $filename = trim($filename);
            if(file_exists(PATH_MODULES . $module['mod_tag'] . '/inc/' . $filename))
            {
                include(PATH_MODULES . $module['mod_tag'] . '/inc/' . $filename);
            }
        }
    }
    
    // Dynamically load module layout files
    if($filenames = getMatchingFiles(PATH_MODULES . $module['mod_tag'] . '/tpl/', '/^layout_([A-Za-z0-9-_.]{2,50}).tpl$/'))
    {
        foreach($filenames as $key=>$value)
        {
            $value = trim($value);
            if(file_exists(PATH_MODULES . $module['mod_tag'] . '/tpl/layout_' . $value . '.tpl'))
            {
                $kApp->registerLayout($value);
            }
        }
    }
    // Load module config
    $mod_config = PATH_MODULES . $module['mod_tag'] . '/config.php';
    if(file_exists($mod_config))
    {
        include_once($mod_config);
    }
    
    // Load module functions file
    $mod_functions = PATH_MODULES . $module['mod_tag'] . '/functions.php';
    if(file_exists($mod_functions))
    {
        include_once($mod_functions);
    }
    // Load moduel global file -- here for backward compatibility
    // Use of global.php file after Kytoo 2.0 deprecated
    $mod_global = PATH_MODULES . $module['mod_tag'] . '/global.php';
    if(file_exists($mod_global))
    {
        include_once($mod_global);
    }

}
unset($mod_global);
unset($mod_functions);

if($cfg->getVar('cpanel_path'))
{   
    require_once($cfg->getVar('cpanel_path'));
}

// Locate additional includes in PATH_INCLUDE
if($filenames = getMatchingFiles(PATH_INCLUDE, '/^functions_([A-Za-z0-9]{2,16}).php$/'))
{
    foreach($filenames as $key=>$filename)
    {
        include(PATH_INCLUDE . 'functions_' . $filename . '.php');
    }
}

define('WIDGET_PREFIX', 'widget_');

$cfg->setVar('k_layout', 'default');

?>