<?php
/**
 * class.kConfig.php - Kytoo Configuration Componenent
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.6
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
 * 2009-05-01 (1.6) - Class now extends kBase. Added storeVar(), saveVars(). 
 *
*/

class kConfig extends kBase
{

    /**
     * @access  public
     * @var     array $errors            List of errors
    */
    var $errors = array();
    /**
     * @access  public
     * @var     array $fields            List of variables
    */
    var $fields = array();
    
    var $db;
    
    function kConfig(&$db)
    {
        $this->db =& $db;
        
        $sql = 'SELECT * FROM ' . TBL_CONFIG;
        
        if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
        {
            while(!$rs->EOF)
            {
                $this->fields[$rs->fields['mod_tag']][$rs->fields['cfg_field']] = $rs->fields['cfg_value'];
                $rs->MoveNext();
            }
        }
        return true;  
    }

    /**
     * getVar() - Get a variable's value
     *
     * Get the value of a variable in $fields. 
     *
     * @access      public
     * @param       string $field    Variable field name
     * @return      string
    */
    function getVar($field, $module_id = 0)
    {
        foreach($this->fields as $mod_tag=>$fields)
        {
            if(isset($fields[$field]))
            {
                return $fields[$field];
            }
        }
        return false;   
    }
    
    /**
     * setVar() - Set a variable's value
     *
     * Set the value of a variable in $fields.
     *
     * @access      public
     * @param       string $name        Variable name
     * @param       string $value       Variable value
     * @return      string
    */
    function setVar($name, $value, $module_id = 0)
    {
        $this->fields[$module_id][$name] = $value;
        return true;
    }
    
    function getVars($module_id = 0)
    {
        foreach($this->fields as $mod=>$field)
        {
            foreach($field as $fk=>$fv)
            {
                $fieldlist[$fk] = $fv;
            }
        }
        return $fieldlist;
        
        // Old routine returns only specified module fields
        if($this->fields[$module_id])
        {
            return $this->fields[$module_id];
        }
        else 
        {
            return false;
        }
    }
    
    /**
     * storeVar() - Store a variable to DB
     *
     * Store a variable to the supplied database connection 
     *
     * @access      public
     * @param       string $field    Variable field name
     * @return      string
    */
    function storeVar($field, $value, $mod_tag = false)
    {
        $sql = 'SELECT config_id FROM ' . TBL_CONFIG . ' WHERE cfg_field = "' . $field . '"';
        if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
        {
            $sql = 'UPDATE ' . TBL_CONFIG . ' SET cfg_value = ' . $value . ' WHERE cfg_field = "' . $field . '"';
            if($this->db->execute($sql))
            {
                return true;
            }
        }
        else
        {   
            $mod_tag = ($mod_tag == false) ? 0 : $mod_tag;
            $sql = 'INSERT INTO ' . TBL_CONFIG . ' (cfg_field, cfg_value, mod_tag) VALUES ("' . $field . '", "' . $value . '", "' . $mod_tag . '")';
            if($this->db->execute($sql))
            {
                return true;
            }
        }
        return false;   
    }
    
    /**
     * saveVars() - Saves all variables to DB
     *
     * Stores all variables to the db 
     *
     * @access      public
     * @return      bool
    */
    function saveVars()
    {
        foreach($this->fields as $mod_tag => $var)
        {
            foreach($var as $field => $value)
            {
                if(!$this->storeVar($field, $value, $mod_tag)) $this->addError('Unable to save <' . $field . '>', get_class($this));
            }
        }
        if(!$this->hasErrors())
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
