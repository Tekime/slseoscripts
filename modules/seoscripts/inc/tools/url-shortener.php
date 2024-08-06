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

$kForm->addText('Enter full URL to shorten', 'f_url', '', 50, 255);
$kForm->addText('Enter custom URL text (optional)', 'f_custom', '', 16, 32);
if($this_tool['tool_captcha'] == 1)
{
    $kForm->addCaptcha(DIR_BASE . 'captcha.php');
    $kForm->addRule('captcha', 'captcha');
}

$kForm->addRule('f_url', 'required');
$kForm->addHidden('a');
$kForm->addSubmit('Continue >>');

if((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'submit'))
{
    $f_error = false;
    if(!empty($_REQUEST['f_custom']))
    {
        if(preg_match('/^[A-Za-z0-9]{2,16}$/', $_REQUEST['f_custom']))
        {
            $shorturl_custom = $_REQUEST['f_custom'];
        }
        else
        {
            $f_error = true;
            $kForm->addError('f_custom', 'Invalid custom URL');        
        }
    }
    if((!$kForm->validate($_REQUEST)) || ($f_error == true))
    {
    }
    else
    {
        $url = 'http://' . k_filter_url($_REQUEST['f_url']) . '/';
        $contents = k_http_get($url);

        $sql = 'SELECT * FROM ' . TBL_SHORTURLS . ' WHERE url_full = "' . $_REQUEST['f_url'] . '"';
        $sql2 = 'SELECT * FROM ' . TBL_SHORTURLS . ' WHERE url_short = "' . $_REQUEST['f_custom'] . '"';
        if(($rs = $db->execute($sql)) && (!$rs->EOF))
        {
            $short_url = $rs->fields['url_short'];
        }
        elseif((!empty($_REQUEST['f_custom'])) && (($rs = $db->execute($sql2)) && (!$rs->EOF)))
        {
            $short_url = false;
            $kForm->addError('f_custom', 'Custom URL already taken.');
        }
        else
        {
            if($short_url = seoscripts_shorten_url($_REQUEST['f_url'], $url_length, '', $shorturl_custom))
            {
            
            }
            else
            {
                $short_url = false;
                $kForm->addError('f_url', 'Error creating short URL.');
            }
        }
        
        $ctpl = k_clone_tpl();
        $ctpl->define('url_shortener', 'seoscripts_url_shortener.tpl');
        $ctpl->parse('url_shortener');
        
        if($short_url)
        { 
            $short_furl = seoscripts_format_shorturl($short_url);
            $long_url = htmlspecialchars(stripslashes($_REQUEST['f_url']));
            $ctpl->assign('shorturl_url', $short_furl);
            $ctpl->assign('shorturl_longurl', $long_url);
            $tool_results_msg = $ctpl->render_all(1);
        }
        else
        {
            $tool_results_msg = 'Error generating short URL. Please check the form for details.';
        }

    }
    
    foreach($_REQUEST as $key => $value)
    {
        $_REQUEST[$key] = stripslashes($value);
    }
    $tool_form = $kForm->renderForm($_REQUEST, 1);

}
else
{
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
