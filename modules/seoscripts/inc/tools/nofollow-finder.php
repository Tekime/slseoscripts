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
//        $url = 'http://' . k_filter_url($_REQUEST['f_url']) . '/';
        $url = k_filter_url($_REQUEST['f_url'], true, true);
        
        $text = 'Loading...';
        $outgoing_links = $stats->get_outgoing_links($url);
        $inner_links = $stats->get_inner_links($url);
        
        $outgoing_links_count = count($outgoing_links);
        $inner_links_count = count($inner_links);
        
//        $tool_results_msg = '<textarea class="sl-tool-codesm">' . $text . '</textarea>';
        
        $ctpl = k_clone_tpl();
        $ctpl->define('nofollow_finder', 'seoscripts_nofollow_finder.tpl');
        $ctpl->define_d('nofollow_outgoing_links', 'nofollow_finder');
        $ctpl->parse('nofollow_finder');
        
        $ctpl->assign('nofollow_outgoing_count', $outgoing_links_count);
        $row_count = 0;
        foreach($outgoing_links as $key => $value)
        {
            $row_count++;
            $nofollow = ($value['nofollow']) ? 'Yes' : 'No';
            $nofollowstyle = ($value['nofollow']) ? 'background-color:#fffbd2;font-weight:bold;border:solid 1px #d80707;' : '';
            $ctpl->assign_d('nofollow_outgoing_links', 'link_href', $value['href']);
            $ctpl->assign_d('nofollow_outgoing_links', 'link_title', $value['title']);
            $ctpl->assign_d('nofollow_outgoing_links', 'link_anchor', $value['anchor']);
            $ctpl->assign_d('nofollow_outgoing_links', 'link_anchor_safe', $value['anchor_safe']);
            $ctpl->assign_d('nofollow_outgoing_links', 'link_rel', $value['rel']);
            $ctpl->assign_d('nofollow_outgoing_links', 'link_nofollow', $nofollow);
            $ctpl->assign_d('nofollow_outgoing_links', 'nofollowstyle', $nofollowstyle);
            $ctpl->assign_d('nofollow_outgoing_links', 'row_count', $row_count);
            $ctpl->parse_d('nofollow_outgoing_links');
        }
           
        $tool_results_msg = $ctpl->render_all(1);
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
