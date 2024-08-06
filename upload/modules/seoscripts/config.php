<?php
/**
 * config.php - Kytoo Module Configuration File
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
*/

$kApp->registerPanel('admin', 'seoscripts', 'SEO Scripts', 5);
$kApp->registerPanelItem('admin', 'seoscripts', 'seoscripts', 'Dashboard', DIR_ADMIN . 'main.php?m=seoscripts', 'SEO Scripts Dashboard'); 
$kApp->registerPanelItem('admin', 'seoscripts', 'seoscripts_cats', 'Categories', DIR_ADMIN . 'main.php?m=seoscripts&v=categories', 'Create, edit and manage categories for organizing your SEO tools.'); 
$kApp->registerPanelItem('admin', 'seoscripts', 'seoscripts_shorturls', 'Short URLs', DIR_ADMIN . 'main.php?m=seoscripts&v=shorturls', 'View, edit, and delete short URLs created with the URL shortener tool.'); 
$kApp->registerPanelItem('admin', 'seoscripts', 'seoscripts_config', 'Configuration', DIR_ADMIN . 'main.php?m=seoscripts&v=config', 'Configure all settings and options for your SEO tools.'); 

$kApp->registerUrlVar('seoscripts_tool_safename', $k_var_patterns['safename']);
$kApp->registerUrlVar('seoscripts_cat_safename', $k_var_patterns['safename']);
$kApp->registerUrlVar('seoscripts_shorturl', $k_var_patterns['safename']);
$kApp->registerUrlVar('seoscripts_email', $k_var_patterns['email']);
$kApp->registerUrlVar('seoscripts_email_size', '([12345])');

$kApp->registerView('seoscripts', $cfg->getVar('seoscripts_urlformat'), array('m' => 'seoscripts'), 1);
$kApp->registerView('seoscripts_tool', $cfg->getVar('seoscripts_urlformat_tool'), array('m' => 'seoscripts', 'v' => 'tool'), 1);
$kApp->registerView('seoscripts_cat', $cfg->getVar('seoscripts_urlformat_cat'), array('m' => 'seoscripts', 'v' => 'cat'), 1);  
$kApp->registerView('seoscripts_shorturl', $cfg->getVar('seoscripts_urlformat_shorturl'), array('m' => 'seoscripts', 'v' => 'shorturl'), 1);  

// email2image URL formats - 1=small, 2=medium, 3=large
$kApp->registerView('seoscripts_email2image', $cfg->getVar('seoscripts_urlformat_tool') . '%seoscripts_email_size%/%seoscripts_email%.jpg', array('m' => 'seoscripts', 'v' => 'tool', 'es' => 1), 1);  
$kApp->registerView('seoscripts_email2imagenoise', $cfg->getVar('seoscripts_urlformat_tool') . '%seoscripts_email_size%/noise/%seoscripts_email%.jpg', array('m' => 'seoscripts', 'v' => 'tool', 'noise' => 1), 1);  

$kApp->registerMenu('admin', 'seoscripts', 'SEO Scripts', '', 5);
$kApp->registerMenuItem('admin', 'seoscripts', 'seoscripts', 'Edit Tools', DIR_ADMIN . 'main.php?m=seoscripts', 1, 'main'); 
$kApp->registerMenuItem('admin', 'seoscripts', 'seoscripts_cats', 'Categories', DIR_ADMIN . 'main.php?m=seoscripts&v=categories', 2, 'main'); 
$kApp->registerMenuItem('admin', 'seoscripts', 'seoscripts_shorturls', 'Short URLs', DIR_ADMIN . 'main.php?m=seoscripts&v=shorturls', 3, 'main'); 

$kApp->registerMenuItem('admin', 'settings', 'settings_seoscripts', 'SEO Tools', DIR_ADMIN . 'main.php?m=seoscripts&v=config', 4, 'main'); 


$sql = 'SELECT COUNT(tool_id) AS toolcount FROM ' . TBL_SEOTOOLS . ' WHERE tool_status = 1';
if(($rs = $db->execute($sql)) && (!$rs->EOF))
{
    $seoscripts_toolcount = ($rs->fields['toolcount'] > 0) ? $rs->fields['toolcount'] : 0;
    $cfg->setVar('seoscripts_info_toolcount', $seoscripts_toolcount);
    $tpl->assign('seoscripts_info_toolcount', $seoscripts_toolcount);
}

$sql = 'SELECT COUNT(category_id) AS count FROM ' . TBL_SEOTOOLS_CATEGORIES . ' WHERE cat_status = 1';
if(($rs = $db->execute($sql)) && (!$rs->EOF))
{
    $seoscripts_catcount = ($rs->fields['count'] > 0) ? $rs->fields['count'] : 0;
    $cfg->setVar('seoscripts_info_catcount', $seoscripts_catcount);
}

?>
