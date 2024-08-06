<?php
/**
 * config.php - Kytoo Site Configuration
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.6
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
 * 2009-05-04 (1.6) - Updated for Kytoo 2.0
 * (1.2) - Updated for phpLinkBid 1.4 support 
 *
*/
require_once('../config.php');
require_once(PATH_INCLUDE . 'admin_header.php');

// Check for active user
if(!empty($session->user_id))
{
    // Set up configuration
    $mod_tag = '0';
    $lang_main = 'Site Configuration';
    $this_file = 'config.php';
    $this_desc = 'Configure common settings for your Web site, like site title, URL, templates, etc.';

    $lang_list = getLanguages(PATH_LANG, 'kytoo-lang-2.0');

    foreach($k_theme_styles as $key => $value)
    {
        $k_theme_styles_list[$value] = $value;
    }
    
    // Load existing themes
    $d = dir(PATH_TPL);
    while (false !== ($entry = $d->read()))
    {
        if(is_dir(PATH_TPL . $entry))
        {
            if(($entry !== '.') && ($entry !== '..'))
            {
                $template_names[$entry] = $entry;
            }
        }
    }
    $d->close();
    
    // Fetch config fields and data
    $sql = 'SELECT * FROM ' . TBL_CONFIG . ' WHERE mod_tag = ' . $mod_tag;
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        while(!$rs->EOF)
        {
            $config_data[$rs->fields['cfg_field']] = $rs->fields['cfg_value'];
            $rs->MoveNext();
        }
    }
    
    $nav->add_link($lang['admin_name'], DIR_ADMIN . 'main.php');
    $nav->add_link($lang_main, DIR_ADMIN . $this_file);
    $nav->set_current($lang_main);

    $kForm = new kForm(DIR_ADMIN . $this_file, 'post');
    $kForm->title = $lang_main;
    $kForm->addSubheading('heading1', 'Basic Settings');
    $kForm->addText('Site URL', 'site_url', '', 50, 255);
    $kForm->addDesc('site_url', 'Full URL including trailing slash, e.g. http://www.example.com/');
    $kForm->addText('Site Name', 'site_name', '', 30);
    $kForm->addDesc('site_name', 'The short name for your site. e.g. "Website Name"');
    $kForm->addText('Site Title', 'site_title', '', 50, 255);
    $kForm->addDesc('site_title', 'The full title of your site. e.g. "Amazing Gadgets and Widgets by Website Name"');
    $kForm->addText('Admin Email Address', 'site_email', '', 30, 127);
    $kForm->addDesc('site_email', 'An email address to send site notifications and contact emails from visitors.');
    $kForm->addText('Welcome Message', 'site_welcome_msg', '', 80, 255);
    $kForm->addDesc('site_welcome_msg', 'A brief welcome message to show visitors at the top of your site.');

    $kForm->addSubheading('themes', 'Theme Settings');
    $kForm->addSelect('Active Theme', 'template', $template_names, '', ' onChange="javascript:document.getElementById(\'themestyle\').disabled = true;"');
    $kForm->addDesc('template', 'The active theme for your site. Add new themes to your `tpl` folder to see them here.');
    $kForm->addSelect('Active Style', 'themestyle', $k_theme_styles_list);
    $kForm->addDesc('themestyle', 'Optional styles, located in the `styles` subfolder of your theme.');

    $kForm->addSubheading('heading3', 'SEO Settings');
    $kForm->addText('Site Page URL Format', 'urlformat_page', '', 32, 255);
    $kForm->addDesc('urlformat_page', 'URL format for site pages. Available fields: %pg_safename%');
    $kForm->addText('Search URL Format', 'urlformat_search', '', 32, 255);
    $kForm->addDesc('urlformat_page', 'URL format for search pages. Available fields: %search_term%');
    $kForm->addText('Default Separator', 'list_sep','', 5, 50);
    $kForm->addDesc('list_sep', 'The default separator character(s) for page titles and navigation.');
    $kForm->addText('Title Tail', 'site_title_tail', '', 50, 255);
    $kForm->addDesc('site_title_tail', 'Default text to append to the browser title of each page. Site Name used if not set. Can be toggled on/off for each page.');
    $kForm->addTextarea('META Keywords', 'meta_keywords', '', 2, 60);
    $kForm->addDesc('meta_keywords', 'Default META tag keywords to use for the site. Enter a comma-separated list of values. Less than 12 words recommended. e.g. "widgets, gadgets, trinkets, treasures"');
    $kForm->addTextarea('META Description', 'meta_description', '', 2, 60);
    $kForm->addDesc('meta_description', 'Default META tag description to use for the site. Enter a short description. Less than 150 characters is recommended.');

    $kForm->addSubheading('othersettings', 'Other Settings');
    $kForm->addSelect('Active Language', 'lang', $lang_list);
    $kForm->addDesc('lang', 'The currently active language file to use for all public text.');
    $kForm->addSelect('HTML Editor', 'admin_editor', array(1 => 'Full - WYSIWYG editor', 0 => 'Basic - Textarea only'));
    $kForm->addDesc('admin_editor', 'Type of editor to use for editing HTML pages.');

    
    $kForm->addHidden('a');
    
    $kForm->addRule('site_url', 'url');
    $kForm->addRule('site_email', 'email');
    $kForm->addRule('site_name', 'required');
    $kForm->addRule('site_title', 'required');

    $kForm->addSubmit('Save Configuration >>');

    $nav->set_current('Site Configuration');

    if((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'save'))
    {
        if(!$kForm->validate($_REQUEST))
        {
            $kForm->heading = 'Please fix any errors and submit again.';
            $kForm->renderForm($_REQUEST);    
        }
        else
        {
            $f_errors = array();
            foreach($config_data as $field=>$value)
            {
                if(!empty($_REQUEST[$field]))
                {
                    $cfg_data['cfg_value'] = $_REQUEST[$field];
                    $cfg_data['updatedby'] = $session->user_id;
                    $cfg_data['dateupdated'] = unix_to_dbtime(time());
    
                    $sql = db_getupdate($db, TBL_CONFIG, $cfg_data, 'cfg_field = "' . $field . '"');
                    if(!$db->execute($sql))
                    {
                        $f_errors[] = 'Error updating "' . $field . '"';
                    }
                }
            }
            if(!$f_errors)
            {
                header('Location:' . DIR_ADMIN_BASE . 'config.php?k_msgok=' . urlencode('Site settings have been updated successfully.'));
            }
            else
            {
                header('Location:' . DIR_ADMIN_BASE . 'config.php?k_msgerror=' . urlencode('Site settings have been updated (with errors).'));
            }
        }
    }
    else
    {       
        $config_data['a'] = 'save';
        $kForm->renderForm($config_data);
        
        $htaccess_html = "<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteBase /\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule ^(.*) index.php\n</IfModule>";
        $htForm = new kForm(DIR_ADMIN . $this_file, 'post');
        $htForm->name = 'htForm';
        $htForm->addHtml('hthtml', '<div style="margin:20px 0px 10px 0px;border:solid 1px #eee;background-color:#fafafa;padding:10px;width:500px;">In order for tidy URLs to work properly, make sure you have uploaded/created a file named <b>.htaccess</b> in your base folder with the following contents:</div>');
        $htForm->addTextarea('', 'htaccesscode', $htaccess_html, 6, 80);
        $htForm->renderForm();    
    }
}
else 
{
	dialog_box('You must be logged in to access this page. Please click Ok to log in.', DIR_ADMIN_BASE . 'login.php', null, 'Please Log In');
}


require_once(PATH_INCLUDE . 'admin_footer.php');
$tpl->render_all();

?>