<?php
/**
 * class.kDb.php - Kytoo Database Component
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
 * 2009-05-01 (1.1) - Updated for Kytoo 2.0  
 *
*/
class kDb extends kBase
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
    
    var $link;
    
    function kDb($host, $username, $password, $name, $type = 'mysql')
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->name = $name;
        $this->type = $type;
        return true;  
    }

    function connect()
    {
        if($this->link = mysql_connect($this->host, $this->username, $this->password))
        {
            if(mysql_select_db($this->name))
            {
                return true;
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
    
    function ErrorMsg()
    {
        return mysql_error($this->link);
    }
    
    /**
     * execute() - Run the SQL code
     *
     * @access      public
     * @param       string $sql     SQL Query
     * @return      object
    */
    function execute($sql, $nors=0)
    {
        // Check for link and load with verify if needed
        if(!$this->link) { if(!$this->connect()) return false; }
        
        $result = mysql_query($sql, $this->link);

        if($result === true)
        {
            return true;
        }
        elseif($result === false)
        {
            return false;
        }
        elseif(is_resource($result))
        {
            if($nors)
            {
                return $result;
            }
            else
            {
                $rs = new kDbRecordset($result);
                return $rs;
            }
        }
        else 
        {
            return false;
        }
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
    function setVar($name, $value)
    {
        $this->fields[$name] = $value;
        return true;
    }
    
    /**
     * getFields() - Get field names from a table
     *
     * Get all column names from a table
     *
     * @access      public
     * @param       string $table       Table
     * @return      mixed
    */
    function getFields($table)
    {
        // Check for link and load with verify if needed
        if(!$this->link) { if(!$this->connect()) return false; }
        
        $sql = 'SHOW COLUMNS FROM ' . $table;

        if(($rs = $this->execute($sql)) && (!$rs->EOF))
        {        
            while(!$rs->EOF)
            {
                $fields[] = $rs->fields['Field'];
                $rs->MoveNext();
            }
            return $fields;  
        }
        else
        {
            return false;
        }
    }
}

class kDbRecordset
{
    var $result;
    var $EOF = true;
    var $fields = array();
    
    function kDbRecordset(&$result)
    {
        $this->result =& $result;
        
        if($this->NumRows())
        {
            $this->EOF = false;
            
            $this->fields = mysql_fetch_array($this->result);
        }
        
        return true;
    }
    
    function MoveNext()
    {
         if($this->fields = mysql_fetch_array($this->result))
         {
             return true;
         }
         else 
         {
             $this->EOF = true;
             $this->fields = array();
             return false;
         }
    }
    
    function NumRows()
    {
        return mysql_num_rows($this->result);
    }
    
    
}

?>
