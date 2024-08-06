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

$kForm->addText('Title', 'f_title', '', 50, 255);
$kForm->addTextarea('Description', 'f_description', '', 2, 50);
$kForm->addText('Keywords', 'f_keywords', '', 50, 255);
$kForm->addText('Author', 'f_author', '', 50, 255);
$kForm->addText('Owner', 'f_owner', '', 50, 255);
$kForm->addText('Copyright', 'f_copyright', '(c) ' . date('Y'), 50, 255);
//$kForm->addText('Robots', 'f_robots', 'all', 50, 255);
//$kForm->addText('Generator', 'f_generator', '', 50, 255);

if($this_tool['tool_captcha'] == 1)
{
    $kForm->addCaptcha(DIR_BASE . 'captcha.php');
    $kForm->addRule('captcha', 'captcha');
}

$kForm->addHidden('a');
$kForm->addSubmit('Continue >>');

if((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'submit'))
{
    $text = '';
    if(!$kForm->validate($_REQUEST))
    {
    }
    else
    {
        $meta_string = '<meta name="%s" content="%s" />';
        
        $text .= (!empty($_REQUEST['f_title'])) ? sprintf($meta_string, 'title', stripslashes($_REQUEST['f_title'])) . "\n" : "";
        $text .= (!empty($_REQUEST['f_description'])) ? sprintf($meta_string, 'description', stripslashes($_REQUEST['f_description'])) . "\n" : "";
        $text .= (!empty($_REQUEST['f_keywords'])) ? sprintf($meta_string, 'keywords', stripslashes($_REQUEST['f_keywords'])) . "\n" : "";
        $text .= (!empty($_REQUEST['f_author'])) ? sprintf($meta_string, 'author', stripslashes($_REQUEST['f_author'])) . "\n" : "";
        $text .= (!empty($_REQUEST['f_owner'])) ? sprintf($meta_string, 'owner', stripslashes($_REQUEST['f_owner'])) . "\n" : "";
        $text .= (!empty($_REQUEST['f_copyright'])) ? sprintf($meta_string, 'copyright', stripslashes($_REQUEST['f_copyright'])) . "\n" : "";
        $text .= (!empty($_REQUEST['f_robots'])) ? sprintf($meta_string, 'robots', stripslashes($_REQUEST['f_robots'])) . "\n" : "";
        $text .= (!empty($_REQUEST['f_generator'])) ? sprintf($meta_string, 'generator', stripslashes($_REQUEST['f_generator'])) . "\n" : "";
        
        $tool_results_msg = '<textarea class="sl-tool-code">' . $text . '</textarea>';
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
