<?php
/**
 * index.php - Default Interface Controller File
 *
 * This file handles all requests to the default interface. It parses the current request URI
 * for the current view and determines the appropriate controller to handle the request.
 *
 * Copyright (c) 2009, Intavant (http://www.intavant.com/)
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
 * IMPORTANT - This is not free software. You must adhere to the terms of the End-User License Agreement
 *             under penalty of law. Read the complete EULA in "license.txt" included with your application.
 * 
 *             This file can be used, modified and distributed under the terms of the End-User License
 *             Agreement. You may edit this file on a licensed Web site and/or for private development.
 *             You must adhere to the Source License Agreement. The latest copy can be found online at:
 * 
 *             http://www.phplinkbid.com/en/source_license
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/kbase/enduser_license.html
 * @author      Gabriel Harper, Intavant <gharper@intavant.com>
 * @version     2.0
 * 
 * 2009-03-19   Version 2.0 major rewrite
 *
 */

require_once('config.php');
require_once(PATH_INCLUDE . 'header.php');

$k_defined_func = get_defined_functions();
$k_user_func = $k_defined_func['user'];
unset($k_defined_func);

foreach($k_user_func as $key=>$value)
{
    if(strpos($value, 'widget_') === 0)
    {
//        $args = func_num_args($value);
        $k_widgets[$value] = $args;
    }
}
//sort($k_widgets);
//print_pre($k_widgets);

/**
 * Process the Request
 */
 
$request_uri = $_SERVER['REQUEST_URI'];
$k_this_view = 'index';

// Parse the request URL if not on the home page
if($request_uri !== DIR_BASE)
{
    // Remove DIR_BASE from request URI
    $request_uri = substr($request_uri, strlen(DIR_BASE));

    // Get registered URL pattern variables
    $k_urlvar_patterns = $kApp->getUrlVars();

    // Get interface views
    if($k_this_views = $kApp->getViews(K_VIEW_INDEX))
    {

        // Loop through views
        foreach($k_this_views as $key => $view)
        {
            // Get the URL pattern for the current view
            $pattern = $view['pattern'];

            // Extract all URL variable names from the view URL pattern
            preg_match_all('/%[A-Za-z0-9_]*%/', $pattern, $k_this_varpatterns);

            // Replace URL variable symbols with patterns in current pattern
            foreach($k_this_varpatterns[0] as $matchkey => $tag)
            {
               $varpattern = (!empty($k_urlvar_patterns[str_replace('%', '', $tag)])) ? $k_urlvar_patterns[str_replace('%', '', $tag)] : $k_var_patterns['safename'];
                $pattern = str_replace($tag, $varpattern, $pattern);
            }
            // Fix slashes
            $pattern = str_replace('/', '\/', $pattern);

            // Check for pattern match in current REQUEST_URI
            if(preg_match('/^' . $pattern . '$/', $request_uri, $matches))
            {

                $k_this_urlvars = $view['vars'];
                $kApp->setCurrentView($key);

                // Loop through each of the matches and assign it to the appropriate URL var
                $m_count = count($matches) - 1;
                $v_count = count($k_this_varpatterns[0]) - 1;
                if($m_count > $v_count)
                {
                    $k_this_urlextra = $matches[$m_count];
                }
                for($m_index=0;$m_index<$m_count;$m_index++)
                {
                    $varname = str_replace('%', '', $k_this_varpatterns[0][$m_index]);
                    if(!empty($varname)) $k_this_urlvars[$varname] = $matches[$m_index+1];
                    unset($varname);
                }
                
                if($kApp->isView(K_VIEW_INDEX, $key))
                {
                    $k_this_view = $key;
                }
            }
        }
    }
}

$k_this_view = $kApp->getView($k_this_view);

if($k_this_view['id'] == 'pages')
{
    if(!empty($k_this_urlvars['pg_safename']))
    {
        $sql = 'SELECT * FROM ' . TBL_PAGES . ' WHERE pg_safename = "' . $k_this_urlvars['pg_safename'] . '" AND pg_status = 2';
        if(($rs = $db->execute($sql)) && (!$rs->EOF))
        {
            $cfg->setVar('page_title', $rs->fields['pg_title']);
            $cfg->setVar('meta_keywords', $rs->fields['pg_meta_keywords']);
            $cfg->setVar('meta_description', $rs->fields['pg_meta_description']);
            if(!empty($rs->fields['pg_layout'])) $cfg->setVar('k_layout', $rs->fields['pg_layout']);
    
            $tpl->assign('k_this_page_id', $rs->fields['page_id']);
            $tpl->assign('k_this_pg_safename', $rs->fields['pg_safename']);
            $tpl->assign('k_this_pg_name', $rs->fields['pg_name']);
            $tpl->assign('k_this_pg_title', $rs->fields['pg_title']);
            $tpl->assign('k_this_pg_contents', $rs->fields['pg_contents']);
    
            if((!empty($rs->fields['pg_script'])) && (file_exists(PATH_CONTENT . $rs->fields['pg_script'] . '.php')))
            {
                include(PATH_CONTENT . $rs->fields['pg_script'] . '.php');
            }
            else
            {
                $tpl->define('page', $rs->fields['pg_contents'], 1);
                $tpl->parse('page');
            }
        }
        elseif(file_exists(PATH_CONTENT . $k_this_urlvars['pg_safename'] . '.html'))
        {
            $fcontents = read_file(PATH_CONTENT . $_REQUEST['cat'] . '/' . $k_this_urlvars['pg_safename'] . '.html');
            $ftitle = get_middle($fcontents, '<h1>', '</h1>');
            if($ftitle)
            {
                $cfg->setVar('page_title', $ftitle);
            }
            else
            {
                $h1 = strpos($fcontents, '<h1>');
        
                if($h1 !== false)
                {
                    $h2 = strpos($fcontents, '<h2>');
                    if($h2 !== false)
                    {
                        $ftitle = substr($h1+4, $h2);
                        $cfg->setVar('page_title', $ftitle);
                    }
                }
            }
            $tpl->define('page', $fcontents, true);
            $tpl->parse('page');
        }
        elseif(file_exists(PATH_CONTENT . $_REQUEST['cat'] . '/' . $k_this_urlvars['pg_safename'] . '.php'))
        { 
            include(PATH_CONTENT . $k_this_urlvars['pg_safename'] . '.php');
        }
        else
        {
            header("HTTP/1.0 404 Not Found");
            $cfg->setVar('page_title', '404 Not Found');
            $tpl->define('page', 'error404.tpl');
            $tpl->parse('page');

        }
    }
    else
    {
    
    }
}
elseif(!empty($k_this_urlvars['m']))
{
    $k_this_mod = $k_this_urlvars['m'];

    define('MOD_TAG', $k_this_mod);
    define('MOD_PATH', PATH_MODULES . MOD_TAG . '/');
    define('MOD_DIR', DIR_BASE . MOD_TAG . '/');

    $tpl->add_path(MOD_PATH . 'tpl/');
    $tpl->assign('mod_tag', MOD_TAG);
    $tpl->assign('mod_dir', MOD_DIR);
    $tpl->assign('mod_dir_tpl', MOD_DIR . 'tpl/');
    $tpl->assign('mod_dir_tpl_images', DIR_BASE . 'modules/' . MOD_TAG . '/tpl/images/');

    include_once(MOD_PATH . 'index.php');
}
else
{
    $sql = 'SELECT * FROM ' . TBL_PAGES . ' WHERE pg_safename = "home" AND pg_status = 2';
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        $tpl->define('page', $rs->fields['pg_contents'], 1);
        $tpl->parse('page');
        $cfg->setVar('page_title', $rs->fields['pg_title']);
        $cfg->setVar('meta_keywords', $rs->fields['pg_meta_keywords']);
        $cfg->setVar('meta_description', $rs->fields['pg_meta_description']);
        if((!empty($rs->fields['pg_layout'])) && (empty($_REQUEST['l']))) 
        {
            $cfg->setVar('k_layout', $rs->fields['pg_layout']);
        }
    }
}

require_once(PATH_INCLUDE . 'footer.php');

?>
