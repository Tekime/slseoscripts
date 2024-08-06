<?php
/**
 * kytoo_module_config_admin.php - Template for module configuration admin in Kytoo 2.0 
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.0
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
 * 2009-05-06 (1.0) - First release
 *
*/

$k_list_yesno = array(0 => 'No', 1 => 'Yes');

// Blog Settings: seoscripts_name, seoscripts_title, seoscripts_urlformat_post, seoscripts_urlformat_cat, seoscripts_urlformat_tag, seoscripts_feed_count, seoscripts_pingurls

// Set up configuration
$mod_tag = 'seoscripts';
$lang_main = 'SEO Scripts Configuration';
$this_file = 'main.php';
$this_url = DIR_ADMIN . $this_file . '?m=seoscripts&v=config';
$this_desc = 'Configure common settings for your SEO tools.';

// Fetch config fields and data
$sql = 'SELECT * FROM ' . TBL_CONFIG . ' WHERE mod_tag = "' . $mod_tag . '"';
if(($rs = $db->execute($sql)) && (!$rs->EOF))
{
    while(!$rs->EOF)
    {
        $config_data[$rs->fields['cfg_field']] = $rs->fields['cfg_value'];
        $rs->MoveNext();
    }
}
$seoscripts_menu_options = array('cat' => 'Display categories', 'tools' => 'Display tools', 'both' => 'Display tools & categories', 'none' => 'Hide the tool menu');

$nav->add_link($k_mod_name, $k_mod_url);
$nav->add_link($lang_main, $this_url);
$nav->set_current($lang_main);

$kForm = new kForm(DIR_ADMIN . $this_file, 'post');
$kForm->title = $lang_main;

//$kForm->addSubheading('heading1', 'Display Options');
//$kForm->addSelect('Sidebar Menu Options', 'seoscripts_menu', $seoscripts_menu_options, 'cat');
//$kForm->addDesc('seoscripts_menu', 'Select how you would like the tools menu on your site to appear.'); 
//$kForm->addText('Tool Display Count', 'seoscripts_toolcount', 99, 3, 3);
//$kForm->addDesc('seoscripts_toolcount', 'The max. # of tools to display for each category on the Tools Page. Set to 0 to display only the categories. Set to a high number (99+) to display all tools.');

$kForm->addSubheading('heading2', 'Basic Settings');

$k_list_captcha = array(1 => 'Enabled - Require for all tools', 0 => 'Disabled - Only require if enabled for a tool');
$kForm->addSelect('Require CAPTCHA?', 'seoscripts_captcha', $k_list_captcha);
$kForm->addDesc('seoscripts_captcha', 'Enable/disable CAPTCHA human image verification for all tools.');
 
$kForm->addText('IP Request Limit', 'seoscripts_maxrequests', '', 5, 10);
$kForm->addDesc('seoscripts_maxrequests', 'The maximum # of tool requests allowed from a single IP for the timeframe specified below. Exceeding this limit displays a friendly denial message to the user.');
$kForm->addText('IP Cache (hours)', 'seoscripts_ipcache', '', 3, 3);
$kForm->addDesc('seoscripts_ipcache', 'How long to cache IP addresses. This determines how long denied users need to wait until using the tools again.');
$kForm->addText('Default Max URL Count', 'seoscripts_urlmax', '', 3, 3);
$kForm->addDesc('seoscripts_urlmax', 'The default maximum # of URLs allowed for tools with multiple URL checking. Can be customized for each tool and overrides this setting.');

$kForm->addText('Description Length', 'seoscripts_tool_desc_length', '', 5, 10);
$kForm->addDesc('seoscripts_tool_desc_length', 'The cutoff length in characters for tool descriptions on the homepage, tools page, and categories.');


$kForm->addSubheading('seo', 'SEO Settings');
$kForm->addText('Tools Page URL Format', 'seoscripts_urlformat', '', 80, 255);
$kForm->addDesc('seoscripts_urlformat', 'The URL format for the primary, full tool list.');
$kForm->addText('Tool Page URL Format', 'seoscripts_urlformat_tool', '', 80, 255);
$kForm->addDesc('seoscripts_urlformat_tool', 'The URL format for individual tools. Available fields: %seoscripts_tool_safename%');
$kForm->addText('Category URL Format', 'seoscripts_urlformat_cat', '', 80, 255);
$kForm->addDesc('seoscripts_urlformat_cat', 'The URL format for tool categories. Available fields: %seoscripts_cat_safename%');
$kForm->addText('Short URL Format', 'seoscripts_urlformat_shorturl', '', 80, 255);
$kForm->addDesc('seoscripts_urlformat_shorturl', 'The URL format for short URLs generated by the URL shortener. Available fields: %seoscripts_shorturl%');

$kForm->addHidden('a');
$kForm->addHidden('m');
$kForm->addHidden('v');

$kForm->addRule('seoscripts_urlformat', 'required');
$kForm->addRule('seoscripts_urlformat_tool', 'required');
$kForm->addRule('seoscripts_urlformat_cat', 'required');

$kForm->addSubmit('Save Configuration >>');

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
            if(isset($_REQUEST[$field]))
            {
                $cfg_data['cfg_value'] = $_REQUEST[$field];
                $cfg_data['mod_tag'] = $mod_tag;
    
                $sql = db_getupdate($db, TBL_CONFIG, $cfg_data, 'cfg_field = "' . $field . '"');
                $db->execute($sql);
            }
        }
        if(sizeof($f_errors) > 0)
        {
            header('Location:' . $this_url . '&k_msgerror=' . urlencode('Settings have been updated (with errors).'));
        }
        else
        {
            header('Location:' . $this_url . '&k_msgok=' . urlencode('Settings have been updated successfully.'));
        }
    }
}
else
{       
    $config_data['a'] = 'save';
    $config_data['v'] = 'config';
    $config_data['m'] = 'seoscripts';
    $kForm->renderForm($config_data);    
}


?>
