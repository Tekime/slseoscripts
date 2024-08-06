<?php
/**
 * md5.php - Scriptalicious SEO Scripts Tool
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
 * 1.0 - First release
 *
*/

$kForm = new kForm($_SERVER['REQUEST_URI'], 'post');

//$kForm->addText('Enter URL to check', 'f_url', '', 50, 255);
//$kForm->addTextarea('Enter text to encrypt', 'f_text', '', 5, 60);
if($this_tool['tool_captcha'] == 1)
{
    $kForm->addCaptcha(DIR_BASE . 'captcha.php');
    $kForm->addRule('captcha', 'captcha');
}

$kForm->addHidden('a');
$kForm->addSubmit('Get Browser Details >>');

if((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'submit'))
{
    foreach($_REQUEST as $key => $value)
    {
        $_REQUEST[$key] = stripslashes($value);
    }
    $tool_form = $kForm->renderForm($_REQUEST, 1);
        
    $browser = k_client_browser_detection("browser");
    $bnum = k_client_browser_detection("number");
    $biever = k_client_browser_detection("ie_version");
    $bmozver = k_client_browser_detection("moz_version");
    $operatingsystem = k_client_browser_detection("os");
    $onum = k_client_browser_detection("os_number");
    
    if ($bmozver[0]) $browser = "$bmozver[0] $bmozver[1]";
    else $browser .= " $bnum";
    $operatingsystem .= " $onum";
    
    $browser = trim($browser);
    $operatingsystem = trim($operatingsystem);
    $operatingsystem = str_replace("win","Windows",$operatingsystem);
    $operatingsystem = str_replace("nt","Windows NT",$operatingsystem);
    $operatingsystem = str_replace("me","ME",$operatingsystem);
    $operatingsystem = str_replace("xp","Windows XP",$operatingsystem);
    $operatingsystem = str_replace("vist","Windows Vista",$operatingsystem);
    $operatingsystem = str_replace("mac","Mac",$operatingsystem);
    $operatingsystem = str_replace("lin","Linux",$operatingsystem);
    $operatingsystem = str_replace("linux","Linux",$operatingsystem);
    $operatingsystem = str_replace("NT 5.1","XP SP2",$operatingsystem);
    $operatingsystem = str_replace("NT 5","XP",$operatingsystem);
    
    $browser = str_replace("saf","Safari",$browser);
    $browser = str_replace("op","Opera",$browser);
    $browser = str_replace("omni","OmniWeb",$browser);
    $browser = str_replace("ie","MSIE",$browser);
    $browser = str_replace("konq","Konqueror",$browser);
    $browser = str_replace("moz","Gecko/Moz",$browser);
    $browser = str_replace("netp","NetPositive",$browser);
    $browser = str_replace("lynx","Lynx",$browser);
    $browser = str_replace("webtv","WebTV",$browser);
    $browser = str_replace("firefox","FireFox",$browser);
    $browser = str_replace("netscape","Netscape",$browser);
    
    $ipaddress = netGetIp();
    $hostname = gethostbyaddr($ipaddress);
    $countrylanguage = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    
    $cookie = (!empty($HTTP_COOKIE_VARS['cookiecheck'])) ? 'Enabled' : 'Disabled';
    
    $ctpl = k_clone_tpl();
    $ctpl->define('browser_details', 'seoscripts_browser_details.tpl');
    $ctpl->parse('browser_details');
    $ctpl->assign('client_ip', $ipaddress);
    $ctpl->assign('client_hostname', $hostname);
    $ctpl->assign('client_os', $operatingsystem);
    $ctpl->assign('client_browser', $browser);
    $ctpl->assign('client_lang', $countrylanguage);
    $ctpl->assign('client_cookies', $cookie);
    $ctpl->assign('client_time', date("D, M d, Y h:i:s A T"));

    $tool_results_msg = $ctpl->render_all(1);
}
else
{
	setcookie("cookiecheck", 1);
    $tool_form = $kForm->renderForm(array('a' => 'submit'), 1);
}
$f_instructions = '<ol>';
if(!empty($this_tool['tool_instructions']))
{
    $instructions = explode("\n", $this_tool['tool_instructions']);
    foreach($instructions as $key => $value)
    {
        $f_instructions .= '<li>' . $value . '</li>';
    }
}
if($this_tool['tool_captcha'] == 1) $f_instructions .= '<li>Enter the text shown in the image.</li>';
$f_instructions .= '<li>Click Continue to get your results.</li></ol>';


$tpl->define('seoscripts_tool', 'seoscripts_tool.tpl');
$tpl->parse('seoscripts_tool');
$tpl->assign('tool_form', $tool_form);
$tpl->assign('tool_title', $this_tool['tool_title']);
$tpl->assign('tool_name', $this_tool['tool_name']);
$tpl->assign('tool_description', $this_tool['tool_description']);
$tpl->assign('tool_help_contents', $f_instructions);

if($tool_results_msg)
{
    $tpl->define('tool_results', 'seoscripts_tool_results.tpl');
    $tpl->parse('tool_results');
    $tpl->assign('tool_results_msg', $tool_results_msg);
}

?>
