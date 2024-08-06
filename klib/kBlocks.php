<?php
/**
 * kBlocks - Template blocks
 *
 * This class is part of the Kytoo architecture (www.kytoo.com).
 *
 * Copyright (c) 2006, Kytoo (www.kytoo.com)
 * All rights reserved.
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
 * @version     0.1
 *
 */

define('BLOCK_FUNC_PREFIX', 'blockHdl_');

function blockGetContents($blockname)
{
    global $cfg;
    
    $tpl = new kTemplate();
    $tpl->add_path(PATH_TPL . $cfg->getVar('template') . '/blocks/');

    $tpl->auto_parse = 0;
    if(function_exists(BLOCK_FUNC_PREFIX . $blockname))
    {
        call_user_func(BLOCK_FUNC_PREFIX . $blockname, &$tpl);
    }
    
    return $tpl->render_all(1);
    
}

function blockHdl_usermenu(&$tpl)
{
    global $session;
    
    if(!empty($session->user_id))
    {
        $tpl->define('usermenu', 'usermenu.tpl');
        $tpl->parse('usermenu');
    }
    else
    {
        return false;
    }
}
function blockHdl_menu_user(&$tpl)
{
    global $session;
    
    if(!empty($session->user_id))
    {
        $tpl->define('menu_user', 'menu_user.tpl');
        $tpl->parse('menu_user');
    }
    else
    {
        $tpl->define('menu_guest', 'menu_guest.tpl');
        $tpl->parse('menu_guest');
    }
    return true;
}

function blockHdl_loginbox(&$tpl)
{
    global $session;
    
    if(empty($session->user_id))
    {
        $tpl->define('loginbox', 'loginbox.tpl');
        $tpl->parse('loginbox');
    }
    else
    {
        return false;
    }
}
?>