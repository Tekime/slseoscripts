<?php
/**
 * class.kModules.php - Kytoo Module Utility Component
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.2
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
 * 2009-05-01 (1.2) - Class now extends kBase. Updated for Kytoo 2.0.  
 *
*/

class kModules extends kBase
{

    /**
     * @access  public
     * @var     array $errors            List of errors
    */
    var $errors = array();
    var $mod = array();
    
    function kModules(&$db)
    {
        $this->db =& $db;
        $this->mod = $this->getModules();
        
        return true;
    }

    function getModules()
    {
        $sql = 'SELECT * FROM ' . TBL_MODULES;
        
        if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
        {
            while(!$rs->EOF)
            {
                $modules[$rs->fields['mod_tag']] = $rs->fields;
                $rs->MoveNext();
            }
            return $modules;
        }
        else 
        {
            return array();
        }
    }
    
    function isModule($module)
    {
        if(!empty($this->mod[$module]))
        {
            return true;
        }
        else 
        {
            return false;
        }
    }
    
}


?>
