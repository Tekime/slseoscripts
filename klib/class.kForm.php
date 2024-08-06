<?php
/**
 * class.kForm.php - Kytoo HTML Forms Component
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     3.1
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
 * 3.1 - Removed CAPTCHA session clearing - moved to captcha.php
 * 3.0 - Class now extends kBase. Updated for Kytoo 2.0.  
 * (2.9) - Added addHtml().
 * (2.8.7) - Fixed checkox selections.
 * (2.8.6) - Added file uploads, multi select and CAPTCHA.
 *
*/

class kForm extends kBase
{

    var $action = '';
    var $method = '';

    var $title='';
    var $heading='';
    
    var $name = 'kForm';
    var $class = 'kForm';
    
    var $prefix = 'frm_';

    var $rules = array();
    var $types = array();
    var $controls = array();
    var $counters = array();
    
    var $html = '';
    var $enctype = 'application/x-www-form-urlencoded';
    var $required_display = false;
    var $form_template = false;
    var $use_template = true;
    
    function kForm($action, $method)
    {
        $this->action = $action;
        $this->method = $method;
        
        // First field of all input strings should be extra input attributes
        $this->types = array(
            'inputText' => '<input %s type="text" name="%s" id="%s" value="%s" size="%s" maxlength="%s" %s />',
            'inputCheckbox' => '<table cellpadding="1px" cellspacing="0px" border="0px"><tr><td><input %s class="kFormRichElement" type="checkbox" name="%s" id="%s" value="%s" %s /></td><td>%s</td></tr></table>',
            'inputPassword' => '<input %s type="password" name="%s" id="%s" size="%s" maxlength="%s" />',
            'inputHidden' => '<input %s type="hidden" name="%s" id="%s" value="%s" style="margin:0px;" />',
            'inputRadio' => '<input %s class="radio" type="radio" name="%s" id="%s" value="%s" %s /><div class="radioText">%s</div><br />',
            'inputSubmit' => '<input %s type="submit" value="%s" class="submit" />',
            'inputCurrency' => '<b>{cur_symbol}</b><input style="display:inline;" %s type="text" name="%s" id="%s" value="%s" size="%s" maxlength="%s" %s />',
            'inputFile' => '<input %s type="file" name="%s" id="%s" size="%s" %s /><input type="hidden" name="MAX_FILE_SIZE" value="%s" />',
            'counter' => '<input type="text" id="%s" name="%s" class="counter" size="3" value="0" disabled />',
            'select' => '<select %s name="%s" id="%s" %s>%s</select>',
            'multi' => '<tr><td><input %s type="checkbox" name="%s[%s]" id="%s" value="%s" %s /></td><td>%s</td></tr>',
            'textarea' => '<textarea %s name="%s" id="%s" rows="%s" cols="%s" %s>%s</textarea>',
            'captcha' => '<table cellpadding="1px" cellspacing="0px" border="0px"><tr><td valign="middle"><img src="%s" /></td><td valign="middle"><input type="text" name="%s" id="%s" value="" size="5" maxlength="5" /></td></tr></table>'
            );
    }
    
    function addControl($type, $label='', $name='', $id='', $default='', $size='', $maxlength='', $rows='', $cols='', $fields = array(), $extra = '') 
    {
        $this->controls[$name] = array(
            'type' => $type,
            'label' => $label,
            'name' => $name,
            'id' => $id,
            'default' => $default,
            'size' => $size,
            'maxlength' => $maxlength,
            'rows' => $rows,
            'cols' => $cols,
            'fields' => $fields,
            'rules' => array(),
            'errors' => array(),
            'extra' => $extra
            );

        return true;
    }
    function addDesc($name, $desc)
    {
        $this->controls[$name]['desc'] = $desc;
        return true;
    }
    
    function addRule($name, $rule, $minlength=0, $maxlength=0, $error = '')
    {       
        $this->controls[$name]['rules'][] = array(
            'rule' => $rule,
            'minlength' => $minlength,
            'maxlength' => $maxlength,
            'error' => $error
            );

        return true;
    }
    function deleteRules($name)
    {
        if($this->controls[$name])
        {
            unset($this->controls[$name]);
            return true;
        }
        else
        {
            return false;
        }
    }
    function addError($control, $error)
    {
        $this->controls[$control]['errors'][] = $error;
        return true;
    }
    function addSubheading($control, $label)
    {
        $this->controls[$control]['type'] = 'subheading';
        $this->controls[$control]['label'] = $label;
    }
    function addHtml($control, $html)
    {
        $this->controls[$control]['type'] = 'html';
        $this->controls[$control]['label'] = $html;
    }
    function addCounter($control, $label, $max = 0)
    {
        $this->counters[$control]['label'] = $label;
        $this->counters[$control]['max'] = $max;
        
        // Format counter input field
        $this->counters[$control]['field'] = sprintf($this->types['counter'], 'counter_' . $control, 'counter_' . $control);
    }
    
    function addPassword($label, $name, $id='', $size=32, $maxlength=100)
    {
        $this->addControl('inputPassword', $label, $name, $name, '', $size, $maxlength);
        return true;      
    }
    function addText($label, $name, $default='', $size=32, $maxlength=100, $extra = '')
    {
        $this->addControl('inputText', $label, $name, $name, $default, $size, $maxlength, '', '', '', $extra);
        return true;      
    }
    function addCaptcha($default, $label = 'Enter the text from the image below:', $name = 'captcha', $length = 5)
    {
        $this->addControl('captcha', $label, $name, $name, $default, $length, '', '', '', '', $extra);
        return true;      
    }
    function addCurrency($label, $name, $default='', $size=32, $maxlength=100, $extra = '')
    {
        $this->addControl('inputCurrency', $label, $name, $name, $default, $size, $maxlength, '', '', '', $extra);
        return true;      
    }
    function addCheckbox($label, $name, $fields, $default = '', $extra = '')
    {
        $this->addControl('inputCheckbox', $label, $name, $name, $default, '', '', '', '', $fields, $extra);
    }
    function addMultiCheck($label, $name, $fields, $default='', $extra = '')
    {
        $this->addControl('multi', $label, $name, $name, $default, '', '', '', '', $fields, $extra);

        return true;
    }
    function addRadio($label, $name, $fields, $default='', $extra = '')
    {
        $this->addControl('inputRadio', $label, $name, $name, $default, '', '', '', '', $fields, $extra);
        return true;      
    }
    function addSubmit($label='Submit Form')
    {
        $this->addControl('inputSubmit', $label, 'submit');
        return true;
    }
    
    function addSelect($label, $name, $fields, $default='', $extra = '')
    {
        $this->addControl('select', $label, $name, $name, $default, '', '', '', '', $fields, $extra);

        return true;
    }
    function addHidden($name)
    {
        $this->addControl('inputHidden', '', $name, $name);
        return true;
    }
    function addTextarea($label, $name, $default='', $rows=5, $cols=50, $extra = '')
    {
        $this->addControl('textarea', $label, $name, $name, $default, '', '', $rows, $cols, '', $extra);
        return true;
    }
    function addFile($label, $name, $max_file_size = 10000000)
    {
        $this->enctype = 'multipart/form-data';
        $this->addControl('inputFile', $label, $name, $name, '', $size, $max_file_size);
    }
    function loadTemplate($template)
    {
        if($this->form_template = file_get_contents($template))
        {
            $this->use_template = true;
        }
        else
        {
            $this->use_template = false;
            return false;
        }
    }
    
    function renderForm($data = 0, $return_html = false)
    {
        global $tpl;
        
        $hsep = '<div class="frmRowSep"> </div>';

        if($this->use_template && $this->form_template)
        {
            $html = $this->form_template;
            $html = str_replace('{k_form_hsep}', $hsep, $html);
            if(!empty($this->title)) $html = str_replace('{k_form_title}', $this->title, $html);
            if(!empty($this->heading)) $html = str_replace('{k_form_heading}', $this->heading, $html);
        }
        else
        {
            $html = '{frmControlRequiredMsg}';
            if($this->title) $html .= '<h1>' . $this->title . '</h1>';
            if($this->heading) $html .= '<p>' . $this->heading . '</p>';
        }
        
        $html_formstart .= '<form action="' . $this->action . '" method="' . $this->method . '" name="' . $this->name . '" id="' . $this->name . '" class="' . $this->class . '" enctype = "' . $this->enctype . '">';
        
        // Loop through controls and render form        
        foreach($this->controls as $name => $control)
        {
            // Initialize some variables
//            $value = empty($data[$name]) ? $control['default'] : $data[$name];
            
            $value = (isset($data[$name])) ? $data[$name] : $control['default'];
            
            $hc = $hcc = $hl = $hlc = $hcr = $hcrm = $he = '';
            
            // Check for errors and set up error attributes
            $he = '';
            if(isset($control['errors']))
            {
                foreach($control['errors'] as $k=>$error)
                {
                    $he .= $error . '<br />';
                }
            }
            if(!empty($he))
            {
                $he = '<div class="' . $this->class . 'Error">' . $he . '</div>';
                $input_attr = 'class="error"';
                $hef = 'Error';
            }
            else 
            {
                $input_attr = '';
                $hef = '';
            }
            
            // Add extra code for counter control
            if(is_array($this->counters[$name]))
            {
                $control['extra'] = $control['extra'] .= 'onKeyUp="fCounter(\'' . $control['id'] . '\', \'counter_' . $control['id'] . '\', ' . $this->counters[$name]['max'] . ');lpUpdate();"';
            }
            
            // Set up control required messages
            if($this->required_display && $control['rules'])
            {
                foreach($control['rules'] as $k=>$rule)
                {
                    if($rule['rule'] == 'required')
                    {
                        $hcrm = ' <span class="frmRequired"><strong>*</strong> - Indicates required field</span>';
                        $hcr = ' <span class="frmRequired"><strong>*</strong></span>';
                    }
                }
            }
            
            if(!empty($control['label']))
            {
                if(!empty($control['desc']))
                {
                    $descHover = ' onMouseOver="javascript:setVis(this, \'frmCtlDesc_' . $control['name'] . '\',true);" onMouseOut="javascript:setVis(this, \'frmCtlDesc_' . $control['name'] . '\',false);"';
                    $hl = '<table cellpadding="0px" cellspacing="0px" border="0px"><tr><td nowrap><label>' . $control['label'] . $hcr . '</label></td><td width="10px"><img class="frmCtlInfoImg" id="frmCtlInfoImg_' . $control['name'] . '" src="{dir_tpl_images}ico_sminfo.gif" alt="" ' . $descHover . ' /></td><td><div class="frmCtlDesc" id="frmCtlDesc_' . $control['name'] . '">' . $control['desc'] . '</div></td></tr></table>';
                }
                else
                {
                    $hl = '<label>' . $control['label'] . $hcr . '</label>';
                }
                $hlc = '<div class="frmLabelContainer">' . $hl . '</div>';
            }
            
            switch($control['type'])
            {
                case 'inputText':
                    $hc = sprintf($this->types['inputText'], $input_attr, $control['name'], $control['id'], $value, $control['size'], $control['maxlength'], $control['extra']);
                    break;
                    
                case 'inputCurrency':
                    $hc = sprintf($this->types['inputCurrency'], $input_attr, $control['name'], $control['id'], currencyFormat($value, 0, 1, 0), $control['size'], $control['maxlength'], $control['extra']);
                    break;
                    
                case 'inputHidden':
                    $hc = sprintf($this->types['inputHidden'], $input_attr, $control['name'], $control['id'], $value);
                    $hch .= $hc;             
                    break;

                case 'inputPassword':
                    $hc = sprintf($this->types['inputPassword'], $input_attr, $control['name'], $control['id'], $control['size'], $control['maxlength']);                    
                    break;

                case 'inputSubmit':
                    $hc = sprintf($this->types['inputSubmit'], $input_attr, $control['label']);                    
                    break;
                    
                case 'textarea':
                    $hc = sprintf($this->types['textarea'], $input_attr, $control['name'], $control['id'], $control['rows'], $control['cols'], $control['extra'], $value);
                    break;
                    
                case 'captcha':
                    $hc = sprintf($this->types['captcha'], $control['default'], $control['name'], $control['id']);
                    break;
                    
                case 'inputFile':
                    $hc = sprintf($this->types['inputFile'], $input_attr, $control['name'], $control['id'], $control['size'], $control['extra'], $control['maxlength']);
                    break;
                    
                case 'select':
                
                    $options = '';
                    foreach($control['fields'] as $field_id => $field_value)
                    {
                        if($field_id == $value) {
                            $selected = ' selected';
                        } else {
                            $selected = '';
                        }
                        $options .= '<option value="' . $field_id . '"' . $selected . '>' . $field_value . '</option>';
                    }

                    $hc = sprintf($this->types['select'], $input_attr, $control['name'], $control['id'], $control['extra'], $options);
                    break;
                    
                case 'multi':
                
                    $checks = '';

                    foreach($control['fields'] as $field_id => $field_value)
                    {
                        if($field_id == $value[$field_id]) {
                            $selected = ' checked';
                        } else {
                            $selected = '';
                        }
                        //$checks .= '<option value="' . $field_id . '"' . $selected . '>' . $field_value . '</option>';
                        $checks .= sprintf($this->types['multi'], $input_attr, $control['name'], $field_id, $control['id'], $field_id, $selected, $field_value);
                    }

                    $hc = '<table cellpadding="1px" cellspacing="0px" border="0px">' . $checks . '</table>';
                    break;
                    
                case 'subheading':
                    $hc = '<h2>' . $control['label'] . '</h2>';
                    break;
                    
                case 'html':
                    $hc = $control['label'];
                    break;

                case 'inputRadio':

                    $radios = '';
                    
                    foreach($control['fields'] as $field_id => $field_value)
                    {
                        if($field_id == $value) {
                            $selected = ' checked';
                        } else {
                            $selected = '';
                        }
                        $radios .= sprintf($this->types['inputRadio'], $input_attr, $control['name'], $control['id'], $field_id, $control['extra'], $field_value);
                    }

                    $hc = $radios;
                    break;
                    
                case 'inputCheckbox':
                    
                    $checks = '';
                    if(is_array($control['fields']))
                    {

                        foreach($control['fields'] as $field_id => $field_value)
                        {
                            if($field_id == $value) {
                                $selected = ' checked';
                            } else {
                                $selected = '';
                            }
                            $checks .= sprintf($this->types['inputCheckbox'], $input_attr, $control['name'], $control['id'], $field_id, $selected . $control['extra'], $field_value);
                        }
                    }

                    $hc = $checks;
                    break;

                default:
                    $hc = 'Control HTML error';
                    break;
            }
            
            if(is_array($this->counters[$name]))
            {
                $hcounter .= sprintf($this->counters[$name]['label'], $this->counters[$name]['field'], $this->counters[$name]['max']);
            }
            
            $hcc = '<div class="frmInputContainer">' . $hc . $he . '</div>';
            
            // Check for form template if defined
            if($this->use_template && $this->form_template)
            {
                // Replace control fields in form template HTML
                $html = str_replace('{k_form_l_' . $control['name'] . '}', $hl, $html);
                $html = str_replace('{k_form_lc_' . $control['name'] . '}', $hlc, $html);
                $html = str_replace('{k_form_c_' . $control['name'] . '}', $hc, $html);
                $html = str_replace('{k_form_cc_' . $control['name'] . '}', $hcc, $html);
                $html = str_replace('{k_form_hef_' . $control['name'] . '}', $hef, $html);
                
                $html = str_replace('{k_form_ctl_id_' . $control['name'] . '}', $control['id'], $html);
                $html = str_replace('{k_form_ctl_name_' . $control['name'] . '}', $control['name'], $html);
                $html = str_replace('{k_form_ctl_selected_' . $control['name'] . '}', $selected, $html);
                $html = str_replace('{k_form_ctl_extra_' . $control['name'] . '}', $control['extra'], $html);
                $html = str_replace('{k_form_ctl_value_' . $control['name'] . '}', $field_value, $html);
                $html = str_replace('{k_form_ctl_label_' . $control['name'] . '}', $control['label'], $html);
                $html = str_replace('{k_form_ctl_desc_' . $control['name'] . '}', $control['desc'], $html);
                
            }
            else
            {
                // Add the control HTML to the current form html
                if(($control['type'] == 'subheading') || ($control['type'] == 'html') || ($control['type'] == 'inputHidden') || ($control['type'] == 'inputSubmit'))
                {
                    $html .= $hc . $hsep;
                }
                else
                {
                    $html .= $hlc . $hcc . $hsep;
                }            
            }
        }
        
        $html_formend .= '</form>';
        
        if($this->use_template && $this->form_template)
        {
            $html = $html_formstart . $html . $hch . $html_formend;
        }
        else
        {
            $html = $html_formstart . $html . $html_formend;        
        }
        
        if($return_html)
        {
            return $html;        
        }
        else
        {
            $tpl->define($this->name . 'Html', $html, 1);
            $tpl->assign('frmControlRequiredMsg', $hcrm);
            $tpl->parse($this->name . 'Html');
        }
    }
    
    function validate($data = array())
    {
        $f_error = false;
        global $validate, $cfg;
        foreach($this->controls as $name=>$control)
        {
            if($control['type'] == 'inputCheckbox')
            {
                if(empty($data[$name]))
                {
                    $data[$name] = 0;
                    $_REQUEST[$name] = 0;
                }
            }
            if($control['rules'])
            {
                foreach($control['rules'] as $k=>$rule)
                {
                    switch($rule['rule'])
                    {
                        case 'required':
                            if((!isset($data[$name])) || (empty($data[$name])))
                                $this->controls[$name]['errors'][] = 'Required field';                        
                            break;
                        case 'email':
                            if(!$validate->is_email($data[$name]))
                                $this->controls[$name]['errors'][] = 'Invalid email address';
                            break;
                        case 'url':
                            if(!empty($data[$name]))
                            {
                                if(!$validate->is_url($data[$name]))
                                    $this->controls[$name]['errors'][] = 'Invalid URL';
                            }
                            break;
                        case 'password':
                            if(!$validate->is_password($data[$name]))
                                $this->controls[$name]['errors'][] = 'Invalid password';
                            break;
                        case 'title':
                            if(!$validate->is_title($data[$name]))
                                $this->controls[$name]['errors'][] = 'Invalid title';
                            break;
                        case 'desc':
                            if(!$validate->is_desc($data[$name], 1, $cfg->getVar('link_desc_max')))
                                $this->controls[$name]['errors'][] = 'Invalid description';
                            break;
                        case 'bid':
                            if((!$validate->is_currency($data[$name])) || ($data[$name] < $cfg->getVar('minimum_bid')))
                                $this->controls[$name]['errors'][] = 'Invalid bid';
                            break;
                        case 'bidup':
                            if((!$validate->is_currency($data[$name])) || ($data[$name] < $cfg->getVar('minimum_bidup')))
                                $this->controls[$name]['errors'][] = 'Invalid bid';
                            break;
                        case 'captcha':
                            if(!empty($_SESSION['captcha']))
                            {
                                if(strtolower($data[$name]) !== strtolower($_SESSION['captcha']))
                                {
                                    $this->controls[$name]['errors'][] = 'Invalid CAPTCHA code';
                                }
                            }
                            else
                            {
                                $this->controls[$name]['errors'][] = 'Missing CAPTCHA code';                            
                            }
                            break;
                        case 'agree':
                            if($data[$name] !== 'agree')
                                $this->controls[$name]['errors'][] = 'You must agree to the Terms of Service';
                            break;
                        case 'deeplink_title':
                            if((!empty($data[$name])) && (!$validate->is_title($data[$name])))
                                $this->controls[$name]['errors'][] = 'Invalid link title';
                            break;
                        case 'deeplink_url':
                            if(!empty($data[$name]))
                            {
                                if(!$validate->is_url($data[$name]))
                                {
                                    $this->controls[$name]['errors'][] = 'Invalid link URL';
                                }
                                else 
                                {
                                    $url_parts = parse_url($data['link_url']);
                                    $deeplink_parts = parse_url($data[$name]);

                                    if(($url_parts !== false) && ($deeplink_parts !== false))
                                    {
                                        $url_host = explode('.', $url_parts['host']);
                                        $deeplink_host = explode('.', $deeplink_parts['host']);
                                        
                                        if(($url_host[0] !== $deeplink_host[0]) && ($cfg->getVar('link_deeplinks_subdomains') != 1))
                                        {
                                            $this->controls[$name]['errors'][] = 'Subdomains not allowed for deep links';
                                        }
                                    }
                                    else 
                                    {
                                        $this->controls[$name]['errors'][] = 'Invalid link URL';
                                    }
                                }
                            }
                            break;

                        default:
                            break;
                    }
                    
                    if(count($this->controls[$name]['errors']) > 0) $f_error = true;
                }
            }
        }
        if($f_error)
        {
            return false;
        }
        else 
        {
            return true;
        }
    }
    
}