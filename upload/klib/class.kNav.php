<?php
/**
 * class.kNav.php - Kytoo Site Nav Component
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
 * 2009-05-01 (1.0) - Updated for Kytoo 2.0.
 * 2004-xx-xx (0.1) - Initial release for custom project.
 *
*/

class kNav extends kBase 
{
    
    var $is_active = false;
	var $sep = " <b>&#187;</b> ";
	
    function kNav(&$tpl)
    {
        $this->tpl =& $tpl;    	
        return true;
    }

    function parse()
    {
        
        $this->tpl->define('body_nav', 'body_nav.tpl');
        $this->tpl->define_d('nav_link', 'body_nav');
        $this->tpl->parse('body_nav');    	
        $this->is_active = true;
    }
    
    function add_link($name, $url)
    {
        if($this->is_active === false)
        {
        	$this->parse();
        	$this->tpl->assign_d('nav_link', 'nav_sep', '');
        }
        else 
        {
        	$this->tpl->assign_d('nav_link', 'nav_sep', $this->sep);        	
        }
        
        $this->tpl->assign_d('nav_link', 'link_title', $name);
        $this->tpl->assign_d('nav_link', 'link_url', $url);
        $this->tpl->parse_d('nav_link');
    }
    
    function set_current($name)
    {
        if($this->is_active === false) $this->parse();

        $this->tpl->assign('nav_sep', $this->sep);
        $this->tpl->assign('nav_current', $name);
    }
}    
?>
