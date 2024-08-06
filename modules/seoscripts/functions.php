<?php
/**
 * functions.php - Kytoo Module Functions
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
*/

function seoscripts_prepare_tool_fields($fields)
{
    global $cfg;
    
    $fields['tool_furl'] = str_replace('%seoscripts_tool_safename%', $fields['tool_safename'], $cfg->getVar('seoscripts_urlformat_tool'));
    $fields['tool_furl'] = $cfg->getVar('site_url') . str_replace('{seoscripts_tool_safename}', $fields['tool_safename'], $fields['tool_furl']);
    $fields['tool_flink'] = '<a href="' . $fields['tool_furl'] . '" title="' . $fields['tool_title'] . '">' . $fields['tool_name'] . '</a>';
    return $fields;
}

function seoscripts_format_shorturl($short_url, $full = true)
{
    global $cfg;
    $pre = ($full) ? $cfg->getVar('site_url') : '';
    return $pre . str_replace('%seoscripts_shorturl%', $short_url, $cfg->getVar('seoscripts_urlformat_shorturl'));
}
function seoscripts_format_shortlink($short_url, $full = true, $target = '_blank')
{
    global $cfg;
    $url = seoscripts_format_shorturl($short_url, $full);
    $target = ($target) ? ' target="' . $target . '"' : '';
    return '<a href="' . $url . '"' . $target . '">' . $url . '</a>';
}
function seoscripts_prepare_cat_fields($fields)
{
    global $cfg;
    
    $fields['cat_furl'] = str_replace('%seoscripts_cat_safename%', $fields['cat_safename'], $cfg->getVar('seoscripts_urlformat_cat'));
    $fields['cat_furl'] = $cfg->getVar('site_url') . str_replace('{seoscripts_cat_safename}', $fields['cat_safename'], $fields['cat_furl']);
    $fields['cat_flink'] = '<a href="' . $fields['cat_furl'] . '" title="' . $fields['cat_title'] . '">' . $fields['cat_name'] . '</a>';
    return $fields;
}

function widget_seoscriptsToolMenu($category_id = 0, $show_cat = true, $summary = 125, $strip = true, $toolstyle = '')
{
    global $cfg, $db;
    $tpl = k_clone_tpl();
    
    $tpl->define('seoscripts_tools', 'seoscripts_widget_tools.tpl');
    $tpl->define_d('tool_category_row', 'seoscripts_tools');
    $tpl->define_d('tool_row', 'tool_category_row');
    $tpl->parse('seoscripts_tools');

    $tpl->assign('seoscripts_toolstyle', $toolstyle);
    
    if($show_cat)
    {
        $cat_criteria = (empty($category_id)) ? '' : ' AND category_id = ' . $category_id; 
        $tool_cols = $cfg->getVar('seoscripts_toolcols');
        $sql = 'SELECT * FROM ' . TBL_SEOTOOLS_CATEGORIES . ' WHERE cat_status = 1' . $cat_criteria . ' ORDER BY cat_sort ASC, cat_title ASC';
        if(($rs = $db->execute($sql)) && (!$rs->EOF))
        {
            $c_index = 0;
            while(!$rs->EOF)
            {
                $c_index++;
                if((!empty($tool_cols)) && ($c_index >= $tool_cols))
                {
                    $c_index = 0;
                    $tpl->assign_d('tool_category_row', 'tool_item_break', '<br clear="all" />');
                }
                else
                {
                    $tpl->assign_d('tool_category_row', 'tool_item_break', '');
                }

                $tpl->assign_array_d('tool_category_row', $rs->fields);
                $tpl->parse_d('tool_category_row');
                
                $sql = 'SELECT * FROM ' . TBL_SEOTOOLS . ' WHERE tool_status = 1 AND category_id = ' . $rs->fields['category_id'] . ' ORDER BY tool_name ASC';
                if(($rs2 = $db->execute($sql)) && (!$rs2->EOF))
                {
                    while(!$rs2->EOF)
                    {
                        $rs2->fields = seoscripts_prepare_tool_fields($rs2->fields);
                        if($strip) $rs2->fields['tool_description'] = k_text_striphtml($rs2->fields['tool_description'], true, true, false, false, false);
                        if($summary) $rs2->fields['tool_description'] = getSummary($rs2->fields['tool_description'], $summary); 
                        $tpl->assign_array_d('tool_row', $rs2->fields);
                        $tpl->parse_d('tool_row');
                        $rs2->MoveNext();
                    }
                }
                $rs->MoveNext();
            }
        }
    }
    else
    {
        $sql = 'SELECT * FROM ' . TBL_SEOTOOLS . ' WHERE tool_status = 1 ORDER BY tool_sort ASC';
        if(($rs2 = $db->execute($sql)) && (!$rs2->EOF))
        {
            while(!$rs2->EOF)
            {
                $rs2->fields = seoscripts_prepare_tool_fields($rs2->fields);
                $tpl->assign_array_d('tool_row', $rs2->fields);
                $tpl->parse_d('tool_row');
                $rs2->MoveNext();
            }
        }

    }
    return $tpl->render_all(1);
}

function widget_seoscriptsCatMenu()
{
    global $cfg, $db;
    $tpl = k_clone_tpl();
    
    $tpl->define('seoscripts_tools', 'seoscripts_widget_catmenu.tpl');
    $tpl->define_d('tool_category_row', 'seoscripts_tools');
    $tpl->parse('seoscripts_tools');
    
    $sql = 'SELECT * FROM ' . TBL_SEOTOOLS_CATEGORIES . ' WHERE cat_status = 1 ORDER BY cat_sort ASC, cat_title ASC';
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        while(!$rs->EOF)
        {
            $rs->fields = seoscripts_prepare_cat_fields($rs->fields);

            $tpl->assign_array_d('tool_category_row', $rs->fields);
            $tpl->parse_d('tool_category_row');
            $rs->MoveNext();
        }
    }
    return $tpl->render_all(1);
}


function seoscripts_shorten_url($url, $length, $target_base, $custom_url = '')
{
    global $db;
    $url_short = '';
    
    $sql = 'SELECT url_short FROM ' . TBL_SHORTURLS . ' WHERE url_full = "' . $url . '"';
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        return $target_base . $rs->fields['url_short'];
    }
    else
    {

        if((!empty($custom_url)) && (preg_match('/^[A-Za-z0-9]{2,16}$/', $custom_url)))
        {
            $url_short = $custom_url;
        }
        else
        {
            $url_short = uniqueId(8, TBL_SHORTURLS, 'url_short');
        }

        if(!empty($url_short))
        {
            $sql = 'INSERT INTO ' . TBL_SHORTURLS . ' (url_full,url_short,url_ip,datecreated) VALUES ("' . $url . '", "' . $url_short . '", "' . netGetIp() . '", "' . unix_to_dbtime(time()) . '")';

            if(($rs = $db->execute($sql)) && (!$rs->EOF))
            {
                return $target_base . $url_short;
            }        
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }    
    
}

function seoscripts_usage_stats()
{
    global $db;
    
    $day = seoscripts_usage_time(86400);
    $hour = seoscripts_usage_time(3600);
    
    $sql = 'SELECT DISTINCT COUNT(log_ip) AS ip_count FROM ' . TBL_SEOTOOLS_IPLOG;
    $ips = (($rs = $db->execute($sql)) && (!$rs->EOF) && ($rs->fields['ip_count'] > 0)) ? $rs->fields['ip_count'] : 0;
    
    $stats = array('day' => $day, 'hour' => $hour, 'ipcount' => $ips);
    return $stats;
}

function seoscripts_usage_time($sec)
{
    global $db;
    $sql = 'SELECT SUM(log_requests) AS sum_requests FROM ' . TBL_SEOTOOLS_IPLOG . ' WHERE datecreated > "' . unix_to_dbtime(time() - $sec) . '"';
    $count = (($rs = $db->execute($sql)) && (!$rs->EOF) && ($rs->fields['sum_requests'] > 0)) ? $rs->fields['sum_requests'] : 0;
    return $count;
}

function widget_seoscriptsUsageStats($msg = false)
{
    global $db;
    
    if(!$msg) $msg = '<p><b>%s</b> tool requests in the last day, and <b>%s</b> requests in the last hour.</p><p><b>%s</b> total IP addresses in cache</p>';

    $stats = seoscripts_usage_stats();

    $html = sprintf($msg, $stats['day'], $stats['hour'], $stats['ipcount']);
    return $html;   
}

?>
