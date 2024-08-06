<?php
/**
 * index.php - SEO Scripts Installer
 *
 * Copying and distribution of this file are strictly forbidden. This file is part of
 * the Kytoo architecture and is protected by copyright law.
 *
 * Copyright (c) 2011, Scriptalicious - http://www.scriptalicious.com/
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
 * 1.1 - SEO Scripts 1.0.6 installer
 *
 */

error_reporting(E_ERROR);

$i_versionkey = 'slseopro-2.0.9';
$app_name = 'SEO Scripts Pro';
$app_logo = 'logo.gif';

// Identify the base relative path of the directory containing the `install` folder
$dir_base = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['PHP_SELF']));
$dir_base = str_replace('install', '', $dir_base);
$path_base = $_SERVER['DOCUMENT_ROOT'] . $dir_base;

define('DIR_BASE', $dir_base);
define('PATH_BASE', $path_base);

define('PATH_INCLUDE', PATH_BASE . 'inc/');
define('PATH_LIB', PATH_BASE . 'klib/');
define('PATH_LANG', PATH_INCLUDE . 'lang/');
define('PATH_INSTALL', PATH_BASE . 'install/');
define('DIR_INSTALL', DIR_BASE . 'install/');

require_once(PATH_LANG . 'lang_en.php');
require_once(PATH_LIB . 'class.kBase.php');
require_once(PATH_LIB . 'class.kDb.php');
require_once(PATH_LIB . 'class.kTemplate.php');
require_once(PATH_LIB . 'class.kForm.php');
require_once(PATH_LIB . 'class.kValidate.php');
require_once(PATH_LIB . 'kFunctions.php');

require_once(PATH_INSTALL . 'config_' . $i_versionkey . '.php');

$tpl = new kTemplate();
$validate = new kValidate();

$kytoo_version = file_get_contents(PATH_INCLUDE . 'version.txt');

// Clean up HTTP_REQUEST variables
clean_request_vars();


// Begin Installer

$tpl->add_path(PATH_INSTALL);

$tpl->define('header', 'header.tpl');
$tpl->parse('header');

$kForm = new kForm(DIR_INSTALL, 'post');

$kForm->addText('Site URL <span style="font-weight:normal;">(Full URL with trailing slash, e.g. http://www.yourdomain.com/)</span>', 'site_url', 'http://', 50, 255);

$kForm->addText('Database Hostname', 'db_host', 'localhost', 24, 100);
$kForm->addText('Database Name', 'db_name', '', 24, 100);
$kForm->addText('Database Username', 'db_username', '', 24, 100);
$kForm->addText('Database Password', 'db_password', '', 24, 100);

$kForm->addHidden('step');

$kForm->addRule('site_url', 'url');

$kForm->addRule('db_host', 'required');
$kForm->addRule('db_name', 'required');
$kForm->addRule('db_username', 'required');
$kForm->addRule('db_password', 'required');

$kForm->addSubmit('Continue >>');

if((!empty($_REQUEST['step'])) && (intval($_REQUEST['step']) == 3))
{
        // Try to get a database connection
        include(PATH_INCLUDE . 'sql_config.php');
        $db = new kDb(K_DB_HOST, K_DB_USERNAME, K_DB_PASSWORD, K_DB_NAME);
        
        if($db->connect())
        {
            $html = '<h1>Save Configuration</h1>';

            $sql = file_get_contents(PATH_INSTALL . $i_versionkey . '.sql');
            if(!empty($sql))
            {
                $query_error = array();
                $error_flag = false;
                $query_count = 0;
    
                $queries = preg_split('/;\n/', $sql);   
                
                foreach($queries as $key=>$value)
                {
                    $value = trim($value);
                    if(!empty($value))
                    {
                        if(!$db->execute($value)) {
                            $query_error[] = 'Error in query: ' . $value . '(' . mysql_error() . ')';
                            $error_flag = true;
                        }
                        else 
                        {
                            $query_count++;
                        }
                    }
                }
                if(!empty($_REQUEST['site_url']))
                {
                    $site_url = $_REQUEST['site_url'];
                    $sql = 'UPDATE tbl_config SET cfg_value = "' . $_REQUEST['site_url'] . '" WHERE cfg_field = "site_url"';
                    if(!$db->execute($sql))
                    {
                        $query_error[] = 'Error in query: ' . $sql . '(' . mysql_error() . ')';
                        $error_flag = true;
                    }
                }
                
                if($error_flag === false)
                {
                    $html .= '<p><b>Database successfully created...</b></p>' .
                             '<p>Your database has been created and you can now <a href="' . $_REQUEST['site_url'] . 'admin/">log in to your Admin CP</a> ' .
                             'with the default username of <b>admin</b> and default password of <b>admin</b>. You should log in and change your password right away.</p>' .
                             '<p>Once you log in successfully you must also delete the <b>install/</b> folder before your site will work.</p>';
                }
                else 
                {
                    $html .= '<p><em>Database Error:</em> One or more errors were encountered during database setup.</p><code>';
                    foreach($query_error as $key=>$error)
                    {
                        $html .= $error . '<br />';
                    }
                    $html .= '</code>';
                    $html .= '<p>Please confirm that all files have been properly uploaded to your server and that all database settings are correct. Use the back button on your browser to retry from the last step, or <a href="' . DIR_INSTALL . '">start over</a> after checking your installation files and database details are complete.</p>';
                }
            }
            else
            {
                die('Error loading required SQL for installation.');
            }

            $tpl->define('body', $html, 1);
            $tpl->parse('body');
            
        }
        else 
        {
            $kForm->title = 'Step One: Server Setup';
            $kForm->heading = '<div class="msg">Unable to connect to a database with the information provided.</div>';
            $kForm->renderForm($_REQUEST);            
        }
}
elseif((!empty($_REQUEST['step'])) && (intval($_REQUEST['step']) == 2))
{
    if($kForm->validate($_REQUEST))
    {
        // Try to get a database connection
        $db = new kDb($_REQUEST['db_host'], $_REQUEST['db_username'], $_REQUEST['db_password'], $_REQUEST['db_name']);
        
        if($db->connect())
        {
            $config_ok = false;
            
            $cForm = new kForm(DIR_INSTALL, 'post');
            $cForm->addHidden('site_url');
            $cForm->addHidden('db_host');
            $cForm->addHidden('db_username');
            $cForm->addHidden('db_name');
            $cForm->addHidden('db_password');
            $cForm->addHidden('step');
            $cForm->addSubmit('Continue >>');
            
            $cForm->title = 'Step Two: Save Configuration';

            if(is_file(PATH_INCLUDE . 'sql_config.php'))
            {
                include(PATH_INCLUDE . 'sql_config.php');
                if((defined('K_DB_USERNAME')) && (defined('K_DB_HOST')) && (defined('K_DB_NAME')) && (defined('K_DB_PASSWORD')))
                {
                    $cForm->heading = '<div class="msg">Configuration found in <b>inc/sql_config.php</b>.</div>';
                    $config_ok = true;
                }
            }

            if($config_ok !== true)
            {
                $sql_config = "<?php\n" .
                               "define('K_DB_HOST', '" . $_REQUEST['db_host'] . "');\n" .
                               "define('K_DB_NAME', '" . $_REQUEST['db_name'] . "');\n" .
                               "define('K_DB_USERNAME', '" . $_REQUEST['db_username'] . "');\n" .
                               "define('K_DB_PASSWORD', '" . $_REQUEST['db_password'] . "')\n" .
                               "?>";
                $handle = fopen(PATH_INCLUDE . 'sql_config.php', 'w');
                if($handle)
                {
                    if(fwrite($handle, $sql_config))
                    {
                        $cForm->heading = '<div class="msg">Configuration saved to <b>inc/sql_config.php</b>. You can now remove write permissions on <b>inc/sql_config.php</b> for the best security.</div>';
                        $config_ok = true;
                    }
                }
            }
            
            if($config_ok === true)
            {
                $_REQUEST['step'] = 3;
            }
            else 
            {
                $cForm->heading = '<div class="msg">The installer was unable to save <b>inc/sql_config.php</b>. Make this file writeable and try again, or save the code below to this file manually and continue.</div>' .
                                  '<textarea style="width:400px;height:100px;font-size:10px;font-family:courier new,sans-serif;">' . $sql_config . '</textarea>';
                $_REQUEST['step'] = 2;
            }
            
            $cForm->renderForm($_REQUEST);
            
            // $tpl->define('body', $html, 1);
            // $tpl->parse('body');
            
        }
        else 
        {
            $kForm->title = 'Step One: Server Setup';
            $kForm->heading = '<div class="msg">Unable to connect to a database with the information provided.</div>';
            $kForm->renderForm($_REQUEST);            
        }
        
    }
    else 
    {
        $kForm->title = 'Step One: Server Setup';
        $kForm->heading = '<div class="msg">Please fix any errors and submit again</div>';
        $kForm->renderForm($_REQUEST);
    }
    
    
}
else 
{
    // Attempt to get base path and base directory

    $kForm->title = 'Step One: Server Setup';
    $kForm->heading = '<div class="msg"><b>Welcome to ' . $app_name . ' installation</b>. The next few steps will collect information about your server and configure your new ' . $app_name . ' Web site. Please enter your site URL and database information below.</div>';
    $kForm->renderForm(array('step' => 2));
    
    // Obtain DB connection info
    
    // Continue to step two
}

$tpl->define('footer', 'footer.tpl');
$tpl->parse('footer');

$tpl->render_all();

?>
