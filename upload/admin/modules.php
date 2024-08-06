<?php
/**
 * modules.php - Kytoo Application Modules Manager
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
 * 2009-05-20 (1.1) - Updated for Kytoo 2.0, added module generator
 *
*/

require_once('../config.php');
require_once(PATH_INCLUDE . 'admin_header.php');

$k_script_header = "<?php\n";
$k_script_header .= <<<KSCRIPTLAYOUT
/**
 * [fileinfo]
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
KSCRIPTLAYOUT;

$k_script_mod_config = <<<KSCRIPTMODCONFIG
\$kApp->registerPanel('admin', '[mod_tag]', '[mod_name]', 5);
\$kApp->registerPanelItem('admin', '[mod_tag]', '[mod_tag]', 'Dashboard', DIR_ADMIN . 'main.php?m=[mod_tag]', '[mod_name] Dashboard'); 
\$kApp->registerUrlVar('[mod_tag]_tag_safename', \$k_var_patterns['safename']);
// \$kApp->registerView('[mod_tag]', \$cfg->getVar('[mod_tag]_urlformat'), array('m' => '[mod_tag]'), 1);  
\$kApp->registerMenu('admin', '[mod_tag]', '[mod_name]', '', 5);
\$kApp->registerMenuItem('admin', '[mod_tag]', '[mod_tag]', 'Dashboard', DIR_ADMIN . 'main.php?m=[mod_tag]', 1, 'main'); 
KSCRIPTMODCONFIG;

$k_index_tpl_file = "<h1>[mod_title]</h1>\n\n<p>[mod_description]</p>\n\n";
$k_admin_index_tpl_file = "<h1>[mod_title] Dashboard</h1>\n\n<p>[mod_description]</p>\n\n";

// Check for active user
if(!empty($session->user_id))
{
    
    $lang_create = 'Create Module';
    $lang_edit = 'Edit Module';
    $lang_delete = 'Delete Module';
    $lang_save = 'Save Module';
    $lang_main = 'Modules';
    $this_table = TBL_MODULES;
    $this_file = 'modules.php';
    $this_id = 'module_id';
    $this_tag = 'modules';
    $this_desc = 'Modules are collections of script and entire applications that can be installed to run on your Kytoo Web site.';
    
    $nav->add_link($lang['admin_name'], DIR_ADMIN . 'main.php');
    $nav->add_link('System Tools', DIR_ADMIN . 'system.php');
    $nav->add_link($lang_main, DIR_ADMIN . $this_file);
    $nav->set_current($lang_main);


    
    if((!empty($_REQUEST['a'])) && ($_REQUEST['a']) == 'install')
    {
        $umod_list = get_umodules();
        
        if(isset($umod_list[$_REQUEST['mod_tag']]))
        {
            $tmod = $umod_list[$_REQUEST['mod_tag']];
            
            $sql = 'INSERT INTO ' . $this_table . ' (mod_tag, mod_name, mod_title, mod_description) ' .
                   'VALUES("' . $tmod['mod_tag'] . '", "' . $tmod['mod_name'] . '", "' . $tmod['mod_title'] . '", "' . $tmod['mod_description'] . '")';
            $db->execute($sql);
        
            $sql = file_get_contents(PATH_MODULES . $tmod['mod_tag'] . '/' . $tmod['mod_tag'] . '.sql');
            if(!empty($sql))
            {
                // $sql = str_replace("\n", '', $sql);
                $queries = explode(';', $sql);

                $query_error = false;
                foreach($queries as $key=>$value)
                {
                    $value = trim($value);
                    if(!empty($value))
                    {
                        if(!$db->execute($value)) $query_error = true;
                    }
                }
            }
            
            header('Location:' . DIR_ADMIN_BASE . 'modules.php?refresh=1');
        }
    }
    elseif((!empty($_REQUEST['a'])) && ($_REQUEST['a']) == 'save')
    {
        if(empty($_POST[$this_id]))
        {
            $nav->set_current($lang_create);
            $kForm->title = $lang_create;
        }
        else
        {
            $nav->set_current($lang_edit);
            $kForm->title = $lang_edit;
        }

        $kForm->addSubmit($lang_save . ' >>');

        if(!$kForm->validate($_POST))
        {
            $kForm->heading = 'Please fix any errors and submit again.';
            $kForm->renderForm($_POST);    
        }
        else
        {
            if(empty($_POST[$this_id]))
            {
                $_POST['createdby'] = $session->user_id;
                
                $sql = db_getinsert($db, $this_table, $_POST);
                if($db->execute($sql))
                {
                    header('Location:' . DIR_ADMIN_BASE . $this_file . '?refresh=1');
                }
                else
                {
                    die('Error creating new record');
                }
            }
            else
            {
                $_POST['updatedby'] = $session->user_id;
                
                $sql = db_getupdate($db, $this_table, $_POST, $this_id . ' = ' . intval($_POST[$this_id]));

                if($db->execute($sql))
                {
                    header('Location:' . DIR_ADMIN_BASE . $this_file . '?refresh=1');
                }
                else
                {
                    die('Error editing record');
                }
            } 
        }
    }
    elseif((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'delete'))
    {
        if((!empty($_REQUEST[$this_id])) && (is_numeric($_REQUEST[$this_id])))
        {
            if((!empty($_REQUEST['confirm'])) && ($_REQUEST['confirm'] == 1))
            {
                $sql = 'DELETE FROM ' . $this_table . ' WHERE ' . $this_id . ' = ' . intval($_REQUEST[$this_id]);
                
                if($db->execute($sql))
                {
                    header('Location:' . DIR_ADMIN_BASE . $this_file . '?refresh=1');
                }
                else
                {
                    die('Unable to delete.');
                }
            
            }
            else
            {
                dialog_box('Are you sure you want to delete this record?', DIR_ADMIN_BASE . $this_file . '?a=delete&' . $this_id . '=' . intval($_REQUEST[$this_id]) . '&confirm=1', DIR_ADMIN_BASE . $this_file, 'Confirm Delete', false);
            }
        }

    }
    elseif((!empty($_REQUEST['a'])) && ($_REQUEST['a']) == 'edit')
    {
        if(!empty($_REQUEST[$this_id]))
        {
            $sql = 'SELECT * FROM ' . $this_table . ' WHERE ' . $this_id . ' = ' . $_REQUEST[$this_id];
            if(($rs = $db->execute($sql)) && (!$rs->EOF))
            {
                $nav->set_current($lang_edit);
            
                $kForm->addSubmit($lang_save . ' >>');
                $kForm->title = $lang_edit;
                $kForm->heading = '';
                $rs->fields['a'] = 'save';
                $kForm->renderForm($rs->fields);
            }
        }
    }
    elseif((!empty($_REQUEST['v'])) && ($_REQUEST['v'] == 'gen'))
    {

        $kForm = new kForm(DIR_ADMIN . 'modules.php', 'post');
    
        $kForm->addText('Module Tag', 'mod_tag','',32,32);
        $kForm->addText('Name', 'mod_name', '', 50, 255);
        $kForm->addText('Title', 'mod_title', '', 50, 255);
        $kForm->addTextarea('Description', 'mod_description', '', 2, 80);

        $kForm->addHidden('a');
        $kForm->addHidden('v');
        
        $kForm->title = 'Generate New Module';
        $kForm->addRule('mod_tag', 'required');
        $kForm->addRule('mod_name', 'required');
        $kForm->addRule('mod_title', 'required');
        $kForm->addRule('mod_description', 'required');
        $kForm->addSubmit('Generate Module');
        
        if((!empty($_REQUEST['a'])) && ($_REQUEST['a'] == 'gen'))
        {
            if($kForm->validate($_REQUEST))
            {
                $g_mod_tag = $_REQUEST['mod_tag'];
                $g_mod_name = $_REQUEST['mod_name'];
                $g_mod_title = $_REQUEST['mod_title'];
                $g_mod_desc = $_REQUEST['mod_description'];
                
                $g_mod_path = PATH_MODULES . $_REQUEST['mod_tag'];
                
                $g_cfg_filename = $g_mod_path . '/' . $g_mod_tag . '.cfg';
                $g_cfg_file = "mod_tag $g_mod_tag\nmod_name $g_mod_name\nmod_title $g_mod_title\nmod_description $g_mod_desc\n";

                $g_config_filename = $g_mod_path . '/config.php';
                $g_config_file = str_replace('[fileinfo]', 'config.php - Kytoo Module Configuration File', $k_script_header);
                $k_script_mod_config = str_replace('[mod_tag]', $g_mod_tag, $k_script_mod_config);
                $k_script_mod_config = str_replace('[mod_name]', $g_mod_name, $k_script_mod_config);
                $k_script_mod_config = str_replace('[mod_title]', $g_mod_title, $k_script_mod_config);
                $g_config_file .= "\n\n" . $k_script_mod_config . "\n\n?>\n";

                $g_functions_filename = $g_mod_path . '/functions.php';
                $g_functions_file = str_replace('[fileinfo]', 'functions.php - Kytoo Module Functions', $k_script_header);
                $g_functions_file .= "\n\n?>\n";

                $g_admin_filename = $g_mod_path . '/admin/index.php';
                $g_admin_file = str_replace('[fileinfo]', 'index.php - Kytoo Module Admin Index', $k_script_header);
                $g_admin_file .= "\n\n?>\n";

                $g_index_filename = $g_mod_path . '/index.php';
                $g_index_file = str_replace('[fileinfo]', 'index.php - Kytoo Module Index', $k_script_header);
                $g_index_file .= "\n\n?>\n";

                $g_index_tpl_filename = $g_mod_path . '/tpl/' . $g_mod_tag . '_index.tpl';
                $k_index_tpl_file = str_replace('[mod_tag]', $g_mod_tag, $k_index_tpl_file);
                $k_index_tpl_file = str_replace('[mod_name]', $g_mod_name, $k_index_tpl_file);
                $k_index_tpl_file = str_replace('[mod_title]', $g_mod_title, $k_index_tpl_file);
                $g_index_tpl_file .= "\n\n?>\n";

                $g_admin_index_tpl_filename = $g_mod_path . '/admin/tpl/' . $g_mod_tag . '_admin_index.tpl';
                $k_admin_index_tpl_file = str_replace('[mod_tag]', $g_mod_tag, $k_admin_index_tpl_file);
                $k_admin_index_tpl_file = str_replace('[mod_name]', $g_mod_name, $k_admin_index_tpl_file);
                $k_admin_index_tpl_file = str_replace('[mod_title]', $g_mod_title, $k_admin_index_tpl_file);
                $g_admin_index_tpl_file .= "\n\n?>\n";
                
                mkdir($g_mod_path);
                mkdir($g_mod_path . '/admin', 0777);
                mkdir($g_mod_path . '/admin/tpl', 0777);
                mkdir($g_mod_path . '/admin/tpl/images', 0777);
                mkdir($g_mod_path . '/tpl', 0777);
                mkdir($g_mod_path . '/tpl/images', 0777);
                mkdir($g_mod_path . '/inc', 0777);
                
                $fp = fopen($g_cfg_filename, 'w');
                fwrite($fp, $g_cfg_file);
                fclose($fp);
                
                $fp = fopen($g_config_filename, 'w');
                fwrite($fp, $g_config_file);
                fclose($fp);

                $fp = fopen($g_functions_filename, 'w');
                fwrite($fp, $g_functions_file);
                fclose($fp);

                $fp = fopen($g_admin_filename, 'w');
                fwrite($fp, $g_admin_file);
                fclose($fp);

                $fp = fopen($g_index_filename, 'w');
                fwrite($fp, $g_index_file);
                fclose($fp);
                
                if(!file_exists($g_mod_path))
                {
                }
                else
                {
                }
            }
            else
            {
                $kForm->renderForm($_REQUEST);            
            }
        }
        else
        {
            $kForm->renderForm(array('v' => 'gen', 'a' => 'gen'));
        }

    }
    else
    {
        // Show the default 'home' page
    	$tpl->define('admin_body', '<h1>' . $lang_main . '</h1><div class="btn"><a href="{dir_admin_base}modules.php?v=gen">Generate New Module</a></div><p>The following modules are currently installed.</p>', 1);	
    	$tpl->parse('admin_body');

    	$grid = new kDataGrid($db, DIR_ADMIN_BASE . $this_file . '?');
        $grid->primary_col = 'module_id';
    	
    	$grid->sql = 'SELECT * FROM ' . $this_table;

        $grid->addColumns(array(
            'module_id' => array(
                'field' => $this_id,
                'sortable' => false,
                'width' => '16px',
                'type' => 'checkbox'
            ),
            'mod_tag' => array(
                'title' => 'Module Tag',
                'href' => DIR_ADMIN_BASE . $this_file . '?a=edit&' . $this_id . '={' . $this_id . '}'
            ),
            'mod_name' => array(
                'title' => 'Name'
            ),
            'mod_title' => array(
                'title' => 'Title'
            ),
            'mod_description' => array(
                'title' => 'Description'
            ),
            'delete' => array(
                'sortable' => false,
                'href' => DIR_ADMIN_BASE . $this_file . '?a=delete&' . $this_id . '={' . $this_id . '}',
                'text' => 'Delete'
            )
        ));
        
        
        $grid_sort = 'module_id';
        $grid_order = 'asc';
        if(!empty($_REQUEST['sort'])) $grid_sort = $_REQUEST['sort'];
        if(!empty($_REQUEST['order'])) $grid_order = $_REQUEST['order'];
        
        $grid_html = $grid->display(1, $grid_sort, $grid_order);
        $tpl->define('grid', $grid_html, 1);
        $tpl->parse('grid');
        $tpl->define('modules_body', 'modules.tpl');
        $tpl->define_d('imod_row', 'modules_body');
        $tpl->define_d('imod_norow', 'modules_body');
        $tpl->parse('modules_body');

        $umod_list = get_umodules();
        
        foreach($umod_list as $key=>$mod)
        {
            $tpl->assign_array_d('imod_row', $mod);
            $tpl->parse_d('imod_row');
        }
        if(empty($umod_list))
        {
            $tpl->parse_d('imod_norow');
        }
        
        if(!empty($_REQUEST['refresh']))
        {
            $tpl->define('refresh_frame', '<script type="text/javascript">parent.frames[\'sidebar\'].location.reload();</script>', 1);
            $tpl->parse('refresh_frame');
        }
    }	
}
else 
{
    dialog_box('You must be logged in to access this page. Please click Ok to log in.', DIR_ADMIN_BASE . 'login.php', null, 'Please Log In');
}

require_once(PATH_INCLUDE . 'admin_footer.php');
$tpl->render_all();

?>