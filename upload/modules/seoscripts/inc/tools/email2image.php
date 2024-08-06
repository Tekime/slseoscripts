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
if(!empty($k_this_urlvars['seoscripts_email']))
{
// ([A-Za-z0-9_!]|\\-|\\.)+@(([A-Za-z0-9_]|\\-)+\\.)+[A-Za-z]{2,4}
//    $k_this_urlvars['seoscripts_email'] = urldecode($k_this_urlvars['seoscripts_email']);
    $k_this_urlvars['seoscripts_email'] = k_text_decrypt($k_this_urlvars['seoscripts_email']);
        // Construct email image
        $font_name = 'monofont.ttf';
        $font = PATH_BASE . 'inc/' . $font_name;

        $font_sizes = array(1 => 11, 2 => 14, 3 => 18, 4 => 22, 5 => 28);
        $font_size = (!empty($k_this_urlvars['seoscripts_email_size'])) ? $font_sizes[$k_this_urlvars['seoscripts_email_size']] : $font_sizes[1];
        $noise = (!empty($k_this_urlvars['noise'])) ? true : false;
        
        $str_len = strlen($k_this_urlvars['seoscripts_email']);
        $width = round($str_len * ($font_size * .58));
        $height = $font_size * 2;
        $imgtext = $k_this_urlvars['seoscripts_email'];
        
        $image = @imagecreate($width, $height) or die('imagecreate() failed');
          
        $bg_color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $noise_color = imagecolorallocate($image, 225, 225, 225);
        
        // Random dots + lines

        if($noise)
        {
            for($i=0;$i<($width*$height)/3;$i++)
            {
            	imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noise_color);
            }
        }

        // Create text
        $textbox = imagettfbbox($font_size, 0, $font, $imgtext) or die('imagettfbbox() failed');
        $x = ($width - $textbox[4])/2;
//        $x = 5;
        $y = ($height - $textbox[5])/2;
//        $angle = mt_rand(-4,4);
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $imgtext) or die('imagettftext() failed');
        
        header('Content-Type: image/jpeg');
        imagejpeg($image);
        imagedestroy($image);
        
        // Determine char length
        
        // Create image
        
        // Add text
        
        // Output image
        
}
else
{
    $kForm = new kForm($_SERVER['REQUEST_URI'], 'post');
    
    $kForm->addText('Enter an email address', 'f_email', '', 50, 255);
    $kForm->addSelect('Select a font size', 'f_size', array(1 => 'Small', 2 => 'Medium', 3 => 'Large', 4 => 'Extra Large', 5 => 'Huge!'), 2);
    $kForm->addCheckbox('', 'f_noise', array(1 => 'Add image noise to deter harvesting'), 1);
    if($this_tool['tool_captcha'] == 1)
    {
        $kForm->addCaptcha(DIR_BASE . 'captcha.php');
        $kForm->addRule('captcha', 'captcha');
    }
    
    $kForm->addRule('f_email', 'email');
    $kForm->addHidden('a');
    $kForm->addSubmit('Continue >>');
    
    if((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'submit'))
    {
        if(!$kForm->validate($_REQUEST))
        {
        }
        else
        {
            $noise = ($_REQUEST['f_noise'] == 1) ? 'noise/' : '';
            $size = (!empty($k_this_urlvars['seoscripts_email_size'])) ? $k_this_urlvars['seoscripts_email_size'] : 2;
            $size = (!empty($_REQUEST['f_size'])) ? $_REQUEST['f_size'] : 2;
            
            $email = $_REQUEST['f_email'];
//            $email = urlencode($email);
            
            $tool_fields = seoscripts_prepare_tool_fields($this_tool);
            $img_furl = $tool_fields['tool_furl'] . $_REQUEST['f_size'] . '/' . $noise . k_text_encrypt($email) . '.jpg';            
            $tpl->assign('email2image_furl', $img_furl);
            
            $ctpl = k_clone_tpl();
            $ctpl->define('email2image', 'seoscripts_email2image.tpl');
            $ctpl->parse('email2image');
            $tool_results_msg .= $ctpl->render_all(1);
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
}

?>
