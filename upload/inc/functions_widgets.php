<?php
/**
 * functions_widgets.php - Kytoo Widget Functions
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
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
 * 1.1 - Fixed widget_getFooter() 
 * 1.0 - Added in Kytoo 2.0
 *
*/

function widget_displayPage($name)
{
    global $db;
    $tpl = k_clone_tpl();
    
    $sql = 'SELECT * FROM ' . TBL_PAGES . ' WHERE pg_safename = "' . $name . '"';
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        $tpl->define('page', $rs->fields['pg_contents'], 1);
        $tpl->parse('page');
    }
    return $tpl->render_all(1);
}

function widget_getHeader()
{
    global $cfg, $modules, $k_theme_styles;
    $tpl = k_clone_tpl();

    // Parse the template header
    $tpl->define('header', 'header.tpl');
    $tpl->define_d('head_styles', 'header');
    $tpl->parse('header');
    
    if(($cfg->getVar('themestyle') == '') || ($cfg->getVar('themestyle') == 'Default'))
    {
        $tpl->assign_d('head_styles', 'css_filename', '{dir_tpl}style.css');
        $tpl->parse_d('head_styles');
    } 
    elseif($cfg->getVar('themestyle'))
    {
        $tpl->assign_d('head_styles', 'css_filename', '{dir_tpl}styles/' . $cfg->getVar('themestyle') . '/style.css');
        $tpl->parse_d('head_styles');
    }

    // Load theme styles
    foreach($k_theme_styles as $key => $value)
    {
        
    }
    // Parse auto template CSS files
    if($css_files = getMatchingFiles(PATH_TPL . $cfg->getVar('template') . '/', '/style_([A-Za-z0-9]{2,16}).css/'))
    {
        foreach($css_files as $key => $css_filename)
        {
            $tpl->assign_d('head_styles', 'css_filename', '{dir_tpl}' . $css_filename);
            $tpl->parse_d('head_styles');
        }
    }
    // Parse modules CSS files
    foreach($modules->mod as $key=>$module)
    {
        if($css_files = getMatchingFiles(PATH_MODULES . $module['mod_tag'] . '/tpl/', '/([A-Za-z0-9]{2,32}.css)/'))
        {
            foreach($css_files as $key => $css_filename)
            {
                $tpl->assign_d('head_styles', 'css_filename', DIR_MODULES . $module['mod_tag'] .'/tpl/' . $css_filename);
                $tpl->parse_d('head_styles');
            }
        }
    }
    return $tpl->render_all(1);
}

function widget_getFooter()
{
    $tpl = k_clone_tpl();
    $tpl->define('footer', 'footer.tpl');
    $tpl->parse('footer');
    return $tpl->render_all(1);
}

function widget_rssFeed($url, $count = 5, $length = 0)
{
    $tpl = k_clone_tpl();
    $kFeed = new kFeed();
    $kFeed->getFeed($url, $count);

    if(is_array($kFeed->feed))
    {
        $tpl->define('rss_feed', 'widget_newsfeed.tpl');
        $tpl->define_d('feed_item_row', 'rss_feed');
        $tpl->parse('rss_feed');
        
        foreach($kFeed->feed as $item=>$link)
        {
            $link['description'] = (empty($link['DESCRIPTION'])) ? '' : trim(strip_tags($link['DESCRIPTION']));
            $link['pubdate_short'] = (empty($link['PUBDATE'])) ? '' : date('M d', strtotime($link['PUBDATE']));
            $link['pubdate_med'] = (empty($link['PUBDATE'])) ? '' : date('F jS, Y', strtotime($link['PUBDATE']));
            $link['description_short'] = (empty($link['DESCRIPTION'])) ? '' : getSummary($link['DESCRIPTION'], 50);
            $link['description_med'] = (empty($link['DESCRIPTION'])) ? '' : getSummary($link['DESCRIPTION'], 125);
            if($length) $link['description'] = (empty($link['DESCRIPTION'])) ? '' : getSummary($link['DESCRIPTION'], $length);
            $tpl->assign_array_d('feed_item_row', $link);
            $tpl->parse_d('feed_item_row');            
        }
        return $tpl->render_all(1);
    }
    return false;
}

function widget_date($format, $date = 0)
{
    $date = (empty($date)) ? time() : $date;
    if(!is_numeric($date)) $date = strtotime($date);
    return date($format, $date);    
} 

function widget_showPanels($interface = 'main')
{
    global $kApp;
    $tpl = k_clone_tpl();

    if($panels = $kApp->getPanels($interface))
    {

        $tpl->define('panel', 'panel.tpl');
        $tpl->define_d('panel_outer', 'panel');
        $tpl->define_d('panel_item', 'panel_outer');
        $tpl->parse('panel');


        foreach($panels as $key=>$panel)
        {
            $tpl->assign_d('panel_outer', 'panel_title', $panel['title']);
            $tpl->parse_d('panel_outer');
            
            foreach($panel['items'] as $key2=>$item)
            {
                $tpl->assign_d('panel_item', 'panel_item_url', $item['url']);
                $tpl->assign_d('panel_item', 'panel_item_title', $item['title']);
                $tpl->assign_d('panel_item', 'panel_item_text', $item['text']);
                $tpl->assign_d('panel_item', 'panel_item_name', $item['name']);
                $tpl->parse_d('panel_item');
            }
        }
        return $tpl->render_all(1);
    }
    else
    {
        return false;
    }

}


/**
 * widget_showMenus() - Displays app/module menus
 *
 * Currently used only in admin to render sidebar.
 *
 * @version     0.1
 **/
function widget_showMenus($interface = 'main')
{
    global $kApp;
    $tpl = k_clone_tpl();

    if($menus = $kApp->getMenus($interface))
    {

        $tpl->define('menu', 'menu.tpl');
        $tpl->define_d('menu_outer', 'menu');
        $tpl->define_d('menu_item', 'menu_outer');
        $tpl->parse('menu');

        foreach($menus as $key=>$menu)
        {
            foreach($menu as $mkey=>$mitem)
            {
                if((!empty($mitem)) && (!is_array($mitem)))
                {
                    $tpl->assign_d('menu_outer', 'menu_' . $mkey, $mitem);
                }
            }
            $tpl->parse_d('menu_outer');
            foreach($menu['items'] as $key2=>$item)
            {
                foreach($item as $key3=>$menu_item)
                {
                    $tpl->assign_d('menu_item', 'menu_item_' . $key3, $menu_item);
                }
                $tpl->parse_d('menu_item');
            }
        }
        return $tpl->render_all(1);
    }
    else
    {
        return false;
    }

}

function widget_kAppMessages()
{
    global $kApp;
    $tpl = k_clone_tpl();

    $msg = $type = $class = '';

    if(sizeof($kApp->messages) > 0)
    {
        foreach($kApp->messages as $key => $value)
        {
            $msg = $value['msg'];
            if($value['type'] == 'error')
            {
                $class = 'kMsgAdminError';
            }
            elseif($value['type'] == 'ok')
            {
                $class = 'kMsgAdminOk';
            }
            elseif($value['type'] == 'alert')
            {
                $class = 'kMsgAdminAlert';
            }
            else
            {
                $class = 'kMsgAdminNotice';
            }
            $msghtml .= '<div class="' . $class . '">' . $msg . '</div>';
//            $messages[] = array('msg' => $msg, 'class' => $class);
        }
    }
    else
    {
        if(!empty($_REQUEST['k_msgok']))
        {
            $msg = urldecode(stripslashes($_REQUEST['k_msgok']));
            $class = 'kMsgAdminOk';
        }
        elseif(!empty($_REQUEST['k_msgerror']))
        {
            $msg = urldecode(stripslashes($_REQUEST['k_msgerror']));
            $class = 'kMsgAdminError';
        }
        elseif(!empty($_REQUEST['k_msg']))
        {
            $msg = urldecode(stripslashes($_REQUEST['k_msg']));
            $class = 'kMsgAdminNotice';
        }
        $msghtml .= '<div class="' . $class . '">' . $msg . '</div>';
    }

    $tpl->define('k_appmessage', $msghtml, 1);
    $tpl->parse('k_appmessage');
    
    return $tpl->render_all(1);
}

function widget_kMenu($parent = 0, $limit = false, $hidden = false, $linkhtml = false, $menuhtml = false)
{
    global $kApp,$db;
    $tpl = k_clone_tpl();

    if(!$linkhtml) $linkhtml = '<li><a href="%s" onClick="javascript:this.blur();">%s</a></li>';
    if(!$menuhtml) $menuhtml = '<ul>%s</ul>';
    $status = ($hidden === true) ? 'pg_status > 0 AND ' : 'pg_status = 2 AND '; 
    if(!is_numeric($parent))
    {
        $sql = 'SELECT page_id FROM ' . TBL_PAGES . ' WHERE pg_safename = "' . $parent . '"';
        if(($rs = $db->execute($sql)) && (!$rs->EOF))
        {
            $parent = $rs->fields['page_id'];
        }
    }

    $sql = 'SELECT * FROM ' . TBL_PAGES . ' WHERE ' . $status . 'parent_id = ' . $parent . ' ORDER BY pg_sort ASC';
    if($limit !== false) $sql .= ' LIMIT ' . $limit;

    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        $links = '';
        while(!$rs->EOF)
        {
            if($rs->fields['pg_safename'] == 'home')
            {
                $links .= sprintf($linkhtml, DIR_BASE, $rs->fields['pg_name']);
            }
            else
            {
                $format = $kApp->getUrl('pages', $rs->fields);
                $links .= sprintf($linkhtml, $format, $rs->fields['pg_name']);
            }
            $rs->MoveNext();
        }
        $output = sprintf($menuhtml, $links);
    }

    
    $tpl->define('kmenu', $output, 1);
    $tpl->parse('kmenu');

    return $tpl->render_all(1);
}


function widget_kSiteLinks($limit = false, $linkhtml = false, $menuhtml = false)
{
    global $kApp,$db;
    $tpl = k_clone_tpl();

    if(!$linkhtml) $linkhtml = '<li><a href="%s" onClick="javascript:this.blur();">%s</a></li>';
    if(!$menuhtml) $menuhtml = '<ul>%s</ul>';

    $sql = 'SELECT * FROM ' . TBL_SITELINKS . ' WHERE lnk_status = 1 ORDER BY lnk_title ASC';
    if($limit !== false) $sql .= ' LIMIT ' . $limit;
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        $links = '';
        while(!$rs->EOF)
        {
            $links .= sprintf($linkhtml, $rs->fields['lnk_url'], $rs->fields['lnk_title']);
            $rs->MoveNext();
        }
        $output = sprintf($menuhtml, $links);
    }
    
    $tpl->define('kmenu', $output, 1);
    $tpl->parse('kmenu');

    return $tpl->render_all(1);
}

?>