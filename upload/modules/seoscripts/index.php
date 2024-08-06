<?php
/**
 * index.php - SEO Scripts Module Index
 *
 * Copyright (c) 2011 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2011 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.1
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
 * 1.1 - Added META keyword & description fields to tools
 *
*/

$stats = new kWebStats();

require_once(PATH_LIB . 'class.kPageRank.php');
$kPr = new kPageRank(0, 0);
require_once(PATH_LIB . 'class.kWebRankSimple.php');
$kRank = new kWebRankSimple(0, 0);
require_once(PATH_LIB . 'class.kWebSearch.php');
$kSearch = new kWebSearch();

if((!empty($k_this_urlvars['v'])) && ($k_this_urlvars['v'] == 'api'))
{
    $sql = 'SELECT * FROM ' . TBL_SEOTOOLS . ' WHERE tool_safename = "' . $k_this_urlvars['seoscripts_tool_safename'] . '" AND tool_status = 1';
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {

    }
}
elseif(!empty($k_this_urlvars['seoscripts_shorturl']))
{
    $sql = 'SELECT * FROM ' . TBL_SHORTURLS . ' WHERE url_short = "' . $k_this_urlvars['seoscripts_shorturl'] . '"';
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        $sql = 'UPDATE ' . TBL_SHORTURLS . ' SET url_hits = ' . ($rs->fields['url_hits'] + 1) . ' WHERE shorturl_id = ' . $rs->fields['shorturl_id'];
        $db->execute($sql);
        header('Location:' . $rs->fields['url_full']);
    }
}
elseif(!empty($k_this_urlvars['seoscripts_tool_safename']))
{
    $sql = 'SELECT * FROM ' . TBL_SEOTOOLS . ' WHERE tool_safename = "' . $k_this_urlvars['seoscripts_tool_safename'] . '" AND tool_status = 1';
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        $this_tool = $rs->fields;
        $this_tool['tool_urlmax'] = (is_numeric($this_tool['tool_urlmax'])) ? intval($this_tool['tool_urlmax']) : 0;
        
        if(!empty($rs->fields['tool_meta_description']))
        {
            $cfg->setVar('meta_description', $rs->fields['tool_meta_description']);
        }
        if(!empty($rs->fields['tool_meta_keywords']))
        {
            $cfg->setVar('meta_keywords', $rs->fields['tool_meta_keywords']);
        }
        
        $cfg->setVar('page_title', $this_tool['tool_title']);
        $nav->add_link('Home', $cfg->getVar('site_url'));
        $nav->add_link('SEO Tools', $cfg->getVar('site_url') . $cfg->getVar('seoscripts_urlformat'));
        $nav->set_current($this_tool['tool_name']);

        if((!empty($rs->fields['tool_filename'])) && (file_exists(MOD_PATH . 'inc/tools/' . $rs->fields['tool_filename'])))
        {
            include(MOD_PATH . 'inc/tools/' . $rs->fields['tool_filename']);
        }
        elseif(true == false)
        {

        }
        else
        {
            $tool_form = 'Error loading tool form.';
            $tool_exists = false;
            
            $tool_stats = explode(',', $this_tool['tool_stats']);
            
            // Check for tool max & create form 
            if($this_tool['tool_urlmax'] > 0)
            {
                /**
                 * Step One: Create the tool form
                */
                $kForm = new kForm($_SERVER['REQUEST_URI'], 'post');

                // Create instructions
                $f_instructions = '<ol>';
                $f_instructions .= ($this_tool['tool_urlmax'] > 1) ? '<li>Enter up to ' . $this_tool['tool_urlmax'] . ' URLs you want to check, one per line.</li>' : '<li>Enter the URL you want to check.</li>';
                if(($cfg->getVar('seoscripts_captcha') == 1) || ($this_tool['tool_captcha'] == 1)) $f_instructions .= '<li>Enter the text shown in the image.</li>';
                if(!empty($this_tool['tool_instructions']))
                {
                    $instructions = explode("\n", $this_tool['tool_instructions']);
                    foreach($instructions as $key => $value) $f_instructions .= (!empty($value)) ? '<li>' . $value . '</li>' : '';
                }
                $f_instructions .= '<li>Click Continue to get your results.</li></ol>';
                
                // Check for URL limit and create URL input field(s)
                if($this_tool['tool_urlmax'] == 1)
                {
                    $kForm->addText('Enter Your URL', 'f_url', '', 60, 255);
                    $kForm->addRule('f_url', 'required');
                }
                else
                {
                    $kForm->addTextarea('Enter URL List (Max ' . $this_tool['tool_urlmax'] . ')', 'f_urls', '', 5, 60);
                    $kForm->addRule('f_urls', 'required');
                }
                
                // Loop through all stat fields defined for this tool
                foreach($tool_stats as $key => $tool_stat)
                {
                    // Check for additional input fields for this tool
                    if(!empty($stats->stats[$tool_stat]['input']))
                    {
                        // Create list of additional input fields
                        $tool_input = $stats->stats[$tool_stat]['input'];
                        
                        if($this_tool['tool_datamax'] == 1)
                        {
                            $kForm->addText($tool_input['title'], $tool_input['id'], '', $tool_input['length'], $tool_input['maxlength']);
                            $kForm->addRule($tool_input['id'], 'required');
                        }
                        else
                        {
                            $kForm->addTextarea($tool_input['title'], $tool_input['id'], '', 5, 60);
                            $kForm->addRule($tool_input['id'], 'required');
                        }
                    }  
                }

                if(($cfg->getVar('seoscripts_captcha') == 1) || ($this_tool['tool_captcha'] == 1))
                {
                    $kForm->addCaptcha(DIR_BASE . 'captcha.php');
                    $kForm->addRule('captcha', 'captcha');
                }
                $kForm->addHidden('a');
                $kForm->addSubmit('Continue >>');

                // Render form to var
                $tool_form = $kForm->renderForm($_REQUEST, 1);

                /**
                 * Step Two: Process submitted form
                */
                if((!empty($_REQUEST['a']) && ($_REQUEST['a'] == 'submit')))
                {
                    $f_error = $f_url = $f_urls = false;

                    if($kForm->validate($_REQUEST))
                    {
                        // Check for input in REQUEST for cleanup, save processing later
                        if(($this_tool['tool_datamax'] == 1) && (!empty($_REQUEST[$tool_input['id']])))
                        {
                            $f_data[] = trim(stripslashes($_REQUEST[$tool_input['id']]));
                        }
                        elseif(!empty($_REQUEST[$tool_input['id']]))
                        // Check for input in REQUEST for cleanup, save processing later
                        {
                            $f_datarows = explode("\n", $_REQUEST[$tool_input['id']]);
                            $f_datarows = k_text_list_clean($f_datarows, true, false);
                            foreach($f_datarows as $fdkey => $fdvalue)
                            {
                                $f_data[] = trim(stripslashes($fdvalue));
                            }
                        }
                        
                        if(!empty($_REQUEST['f_urls']))
                        {
                            // Parse and validate multiple URLs to create URL list
                            $url_list = explode("\n", $_REQUEST['f_urls']);
                            if(count($url_list) > 0)
                            {
                                $url_list = k_text_list_clean($url_list, true, false);
                                foreach($url_list as $key => $value)
                                {
                                    $value = trim($value);
                                    if(!$validate->is_url($value)) $f_error = 'Invalid URL in list';
                                }
                            }
                            else
                            {
                                $f_error = 'Invalid URL list';
                            }
                            if($f_error)
                            {
                                $kForm->addError('f_urls', $f_error);
                            }
                            else
                            {
                                $f_urls = $url_list;
                            }
                        }
                        elseif(!empty($_REQUEST['f_url']))
                        {
                            // Add single URL to URL list
                            $f_urls[] = $_REQUEST['f_url'];
                        }

                        // Clean up REQUEST vars
                        strip_request_vars();
                    }
                    else
                    {
                        $f_error = true;
                        $kForm->heading = '<div class="msg1">Please fix any errors and submit again.</div>' . $kForm->heading;
                    }
                    if($cfg->getVar('seoscripts_maxrequests') > 0)
                    {
                        $ipinc = count($f_urls);
                        $ipcache = $cfg->getVar('seoscripts_ipcache');
                        $ipcache = (time() - ($ipcache * 3600));

                        $sql = 'DELETE FROM ' . TBL_SEOTOOLS_IPLOG . ' WHERE datecreated <= "' . unix_to_dbtime($ipcache) . '"';
                        $db->execute($sql);

                        // Check, add, and update IP log
                        $sql = 'SELECT * FROM ' . TBL_SEOTOOLS_IPLOG . ' WHERE log_ip = "' . netGetIp() . '"';
                        if(($rs = $db->execute($sql)) && (!$rs->EOF))
                        {
                            if($rs->fields['log_requests'] >= $cfg->getVar('seoscripts_maxrequests'))
                            {
                                $f_error = 'Request limit reached';
                                $kForm->heading = '<div class="msg1">Request limit of ' . $cfg->getVar('seoscripts_maxrequests') . ' reached. Please try again in ' . $cfg->getVar('seoscripts_ipcache') . ' hours.</div>' . $kForm->heading;
                                $kForm->addError('f_url', $f_error);
                            }
                            else
                            {
                                $sql = 'UPDATE ' . TBL_SEOTOOLS_IPLOG . ' SET log_requests = ' . intval($rs->fields['log_requests'] + $ipinc) . ', dateupdated = "' . unix_to_dbtime(time()) . '" WHERE log_id = ' . $rs->fields['log_id'];
                                $db->execute($sql); 
                            }
                        }
                        else
                        {
                            $sql = 'INSERT INTO ' . TBL_SEOTOOLS_IPLOG . ' (log_ip,log_requests,datecreated,dateupdated) VALUES ("' . netGetIp() . '",1,"' . unix_to_dbtime(time()) . '","' . unix_to_dbtime(time()) . '")'; 
                            $db->execute($sql);
                        }
                    }

                    /**
                     * Step Three: Process form request
                    */
                    if(!$f_error)
                    {
                        // Form validates, start processing request
                        $count = $index = 0;
                        $t_cols = $t_rows = array();
                        
                        $data = (!empty($f_data[0])) ? array($f_data[0]) : false;
                        
                        // Multiple input data
                        if((count($f_data) > 1) && (!empty($f_urls[0])))
                        {
                            $f_url = $f_urls[0];
                            foreach($f_data as $key => $input)
                            {
                                $index++;
                                $t_rows[$index][] = $input;
                                foreach($tool_stats as $key2 => $tool_stat)
                                {
                                    if($result = $stats->get_stats($tool_stat, $f_url, $input))
                                    {
                                        $t_rows[$index][] = $result['rdata']; 
                                    }
                                }
                            }
                            $t_cols[] = $tool_input['title'];
                        }
                        elseif(count($f_urls) > 1)
                        {
                            // if multiple urls, go by that
                            foreach($f_urls as $key => $f_url)
                            {
                                $index++;
                                $t_rows[$index][] = $f_url;
                                foreach($tool_stats as $key2 => $tool_stat)
                                {
                                    if($result = $stats->get_stats($tool_stat, $f_url, $f_data[0]))
                                    {
                                        $t_rows[$index][] = $result['rdata']; 
                                    }
                                }
                            }
                            $t_cols[] = 'URL';
                        }
                        elseif(!empty($f_urls[0]))
                        {
                            // if both single, single result
                            $f_url = $f_urls[0];
                            $t_rows[$index][] = $f_url;
                            foreach($tool_stats as $key2 => $tool_stat)
                            {
                                if($result = $stats->get_stats($tool_stat, $f_url, $f_data[0]))
                                {
                                    $t_rows[$index][] = $result['rdata'];
                                }
                            }
                            $t_cols[] = 'URL';
                        }

                        foreach($tool_stats as $key2 => $tool_stat)
                        {
                            $t_cols[] = $stats->stats[$tool_stat]['name'];
                        }

                        if($this_tool['tool_type'] == 'list')
                        {
                            $align = '';
                            $html = '<table width="100%" cellpadding="0px" cellspacing="1px" border="0px" class="grid">';
                            $html .= '<tr>';

                            $align = '';
                            foreach($t_cols as $key => $value)
                            {
                                if($colindex > 0) $align = ' align="center"';
                                $html .= '<th' . $align . '>' . $value . '</th>';
                                $align = (empty($align)) ? ' align="center"' : $align;
                            }
                            $html .= '</tr>';

                            foreach($t_rows as $index => $t_cols)
                            {
                                $align = '';
                                $html .= '<tr>';
                                foreach($t_cols as $index => $t_col)
                                {
                                    $t_col = ((empty($t_col)) && ($t_col !== 0)) ? 'n/a' : ((is_numeric($t_col)) ? number_format($t_col) : $t_col); 
                                    $html .= '<td' . $align . '>' . $t_col . '</td>';
                                    $align = (empty($align)) ? ' align="center"' : $align;
                                }
                                $html .= '</tr>';
                            }
                            $html .= '</table>';
                            
                            $tool_results_msg = $html;
                        }
                        elseif($this_tool['tool_type'] == 'code')
                        {
                            $row_data = '';
                            foreach($t_rows as $index => $t_cols)
                            {
                                $row_data .= $t_col . "\n";
                            }
                            $tool_results_msg = '<textarea style="width:600px;height:300px;">' . $row_data . '</textarea>';
                        }
                        elseif(($this_tool['tool_type'] == 'text') || (empty($this_tool['tool_type'])))
                        {
                            $tool_results_msg .= (!empty($result['fmsg'])) ? $result['fmsg'] : ((!empty($result['ftitle'])) ? $result['ftitle'] : $result['fdata']);
                        }
                    }
                    else
                    {
                        $tool_form = $kForm->renderForm($_REQUEST, 1);                    
                    }
                }
                else
                {
                    $tool_form = $kForm->renderForm(array('a' => 'submit'), 1);
                }
            }

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
    }
}
elseif(!empty($k_this_urlvars['seoscripts_cat_safename']))
{
    $sql = 'SELECT * FROM ' . TBL_SEOTOOLS_CATEGORIES . ' WHERE cat_safename = "' . $k_this_urlvars['seoscripts_cat_safename'] . '"';
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        $cfg->setVar('page_title', $rs->fields['cat_title']);
        $tpl->define('seoscripts_cat', 'seoscripts_cat.tpl');
        $tpl->assign_array($rs->fields);
        $tpl->parse('seoscripts_cat');
    }
}
else
{
    $tpl->define('seoscripts_tools', 'seoscripts_tools.tpl');
    $tpl->parse('seoscripts_tools');

}

//$cfg->setVar('k_layout', 'clean');

?>
