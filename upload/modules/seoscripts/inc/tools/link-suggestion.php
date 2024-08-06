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

$type_list = array('title' => 'In Title', 'quotes' => 'In Quotes', 'normal' => 'Normal Search');
$search_list = array(
    1 => 'Add a link', 2 => 'Add site', 3 => 'Add a site', 4 => 'Add URL', 5 => 'Add a URL',
    6 => 'Ads', 7 => 'Advertiser Testimonials', 8 => 'Article', 9 => 'Articles', 10 => 'Blog',
    11 => 'Blogs', 12 => 'Buy Links', 13 => 'Directory', 14 => 'Directories', 15 => 'Donate',
    16 => 'Donation', 17 => 'Donations', 18 => 'Ezine', 19 => 'Ezine Ads', 20 => 'Links',
    21 => 'Merchants', 22 => 'Newsletter', 23 => 'Newsletter Ads', 24 => 'Rent Links',
    25 => 'Resources', 26 => 'Related sites', 27 => 'Related urls', 28 => 'Sites',
    29 => 'Sponsors', 30 => 'Sponsorship', 31 => 'Submit a link', 32 => 'Submit a site',
    33 => 'Submit URL', 34 => 'Submit an URL', 35 => 'Suggest a link', 36 => 'Suggest a site',
    37 => 'Suggest URL', 38 => 'Suggest an URL', 39 => 'Thanks to Our Sponsors', 40 => 'Tips',
    41 => 'Vendors', 42 => 'Weblog', 43 => 'Websites'
);

$kForm = new kForm($_SERVER['REQUEST_URI'], 'post');

$kForm->addText('Keyword', 'f_keyword', '', 50, 255);
$kForm->addSelect('Search Type', 'f_type', $type_list, 'title');
$kForm->addSelect('Search Text', 'f_search', $search_list, 0); 
//$kForm->addTextarea('Enter text to encrypt', 'f_text', '', 5, 60);
if($this_tool['tool_captcha'] == 1)
{
    $kForm->addCaptcha(DIR_BASE . 'captcha.php');
    $kForm->addRule('captcha', 'captcha');
}

$kForm->addRule('f_keyword', 'required');
$kForm->addRule('f_type', 'required');
$kForm->addRule('f_search', 'required');
$kForm->addHidden('a');
$kForm->addSubmit('Continue >>');

if((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'submit'))
{
    if(!$kForm->validate($_REQUEST))
    {
    }
    else
    {
        $keyword = k_text_striphtml($_REQUEST['f_keyword']);
        
        if($_REQUEST['f_type'] == 'title')
        {
            $query = 'intitle:' . $keyword;
        }
        elseif($_REQUEST['f_type'] == 'quotes')
        {
            $query = '"' . $keyword . '"';
        }
        else
        {
            $query = $keyword;
        }
        
        $query .= ' "' . strtolower($search_list[$_REQUEST['f_search']]) . '"';
        $g_links = $kRank->getResults($query, 'google', 50);
        $y_links = $kRank->getResults($query, 'yahoo', 50);

        foreach($g_links as $key=>$value)
        {
            $links[] = $value['url'];
        }
        foreach($y_links as $key=>$value)
        {
            $links[] = $value['url'];
        }
        $links = k_text_list_clean($links);
        
        $html = '<strong>' . count($links) . '</strong> total links found for <strong>`' . $query . '`</strong><br /><br />' .
                '<table width="100%" cellpadding="0px" cellspacing="1px" border="0px" class="grid">' .
                '<tr><th>Site URL</th></tr>';
        $list = '';
        foreach($links as $key=>$value)
        {
            if($value !== 'URL missing')
            {
                $html .= '<tr><td><a target="_blank" href="' . $value . '">' . $value . '</a></td></tr>';
                $list .= $value . "\n";
            }
        }        
        $html .= '</table>';
        $html .= '<br /><textarea class="sl-tool-code">' . $list . '</textarea><br />';
        
        $tool_results_msg = $html;
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
