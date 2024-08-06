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

$kForm->addText('Enter URL to check', 'f_url', '', 50, 255);
//$kForm->addTextarea('Enter text to encrypt', 'f_text', '', 5, 60);
if($this_tool['tool_captcha'] == 1)
{
    $kForm->addCaptcha(DIR_BASE . 'captcha.php');
    $kForm->addRule('captcha', 'captcha');
}

$kForm->addRule('f_url', 'required');
$kForm->addRule('f_url', 'url');
$kForm->addHidden('a');
$kForm->addSubmit('Continue >>');

if((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'submit'))
{
    if(!$kForm->validate($_REQUEST))
    {
    }
    else
    {
        $url = 'http://' . k_filter_url($_REQUEST['f_url']) . '/';
        $contents = k_http_get($url);
        $text = $contents;
        $text = strip_tags($text);
        $text = htmlspecialchars($text);
        $text = str_replace("\n", " ", $text);
        $text = str_replace("\r", " ", $text);
        $text = ereg_replace("[ ]{2,}", " ", $text);

        preg_match('/<title>(.*?)</', $contents, $matches);
        $title = (!empty($matches[1])) ? $matches[1] : 'empty'; 
        $extract['title'] = array('name' => 'Title', 'value' => $title);
        
        $meta_tags = get_meta_tags($url);
        foreach($meta_tags as $key=>$value)
        {
            $tag_name = ucfirst($key);
            $extract[$key] = array('name' => $tag_name, 'value' => $value);
        }

        
        $extract['length'] = array('name' => 'Character Length', 'value' => strlen($text));
        $extract['source'] = array('name' => 'Source Code', 'value' => $text);
        
        $tool_results_msg = '<textarea class="sl-tool-code">' . $text . '</textarea>';
        
        $tool_results_msg = '<table width="100%" class="kGrid" cellpadding="3px" cellspacing="0px"><tr><th colspan="2">Sites Details for ' . $url . '</th></tr>';

        foreach($extract as $key => $value)
        {
            if(strlen($value['value']) > 255)
            {
                $this_value = '<textarea class="sl-tool-codesmm">' . $value['value'] . '</textarea>';
            }
            else
            {
                $this_value = $value['value'];
            }
            $tool_results_msg .= '<tr><td>' . $value['name'] . '</td><td>' . $this_value . '</td></tr>';
        }
        $tool_results_msg .= '</table>';
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
