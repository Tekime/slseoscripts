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
        $url = k_filter_url($_REQUEST['f_url']);
        $timeout = 10;

        $ports = array(
            array('port' => '20', 'name' => 'FTP data (File Transfer Protocol)'),
            array('port' => '21', 'name' => 'FTP (File Transfer Protocol)'),
            array('port' => '22', 'name' => 'SSH (Secure Shell)'),
            array('port' => '23', 'name' => 'Telnet'),
            array('port' => '25', 'name' => 'SMTP (Send Mail Transfer Protocol)'),
            array('port' => '43', 'name' => 'whois'),
            array('port' => '53', 'name' => 'DNS (Domain Name Service)'),
            array('port' => '68', 'name' => 'DHCP (Dynamic Host Control Protocol)'),
            array('port' => '79', 'name' => 'Finger'),
            array('port' => '80', 'name' => 'HTTP (HyperText Transfer Protocol)'),
            array('port' => '110', 'name' => 'POP3 (Post Office Protocol, version 3)'),
            array('port' => '115', 'name' => 'SFTP (Secure File Transfer Protocol)'),
            array('port' => '119', 'name' => 'NNTP (Network New Transfer Protocol)'),
            array('port' => '123', 'name' => 'NTP (Network Time Protocol)'),
            array('port' => '137', 'name' => 'NetBIOS-ns'),
            array('port' => '138', 'name' => 'NetBIOS-dgm'),
            array('port' => '139', 'name' => 'NetBIOS'),
            array('port' => '143', 'name' => 'IMAP (Internet Message Access Protocol)'),
            array('port' => '161', 'name' => 'SNMP (Simple Network Management Protocol)'),
            array('port' => '194', 'name' => 'IRC (Internet Relay Chat)'),
            array('port' => '220', 'name' => 'IMAP3 (Internet Message Access Protocol 3)'),
            array('port' => '389', 'name' => 'LDAP (Lightweight Directory Access Protocol)'),
            array('port' => '443', 'name' => 'SSL (Secure Socket Layer)'),
            array('port' => '445', 'name' => 'SMB (NetBIOS over TCP)'),
            array('port' => '666', 'name' => 'Doom'),
            array('port' => '993', 'name' => 'SIMAP (Secure Internet Message Access Protocol)'),
            array('port' => '995', 'name' => 'SPOP (Secure Post Office Protocol)')
        );

        foreach($ports as $key => $value)
        {
            if(k_net_port_status($url, $value['port'], $timeout))
            {
                $text .= "OK - Port " . $value['port'] . " - " . $value['name'] . "\n";
            }
            else
            {
                $text .= "FAIL - Port " . $value['port'] . " - " . $value['name'] . "\n";
            }
        }

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
