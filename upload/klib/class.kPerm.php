<?php
/**
 * class.kPerm.php - Kytoo Permissions Component
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
 * 2009-05-01 (1.2) - Updated for Kytoo 2.0  
 *
*/

class kPerm extends kBase
{
    var $rights = array();
    var $perms = array();
    
    function kPerm(&$db)
    {
        $this->db =& $db;
        
        // Define rights map for rights keys
        // The key is the code to be used during method calls, and the value for that key
        // corresponds to the enum field in the DB used to check/set that field.
        
        $this->perms = array('c' => 'pcreate', 
                             'r' => 'pread', 
                             'u' => 'pupdate', 
                             'd' => 'pdelete');
    }
    
    /*
     * bool check_password(string username, string password)
     *
     * Validates a password for a specific username/password combination
     */
    function check_password($username, $password)
    {
        // check if user has permission
        
        $sql = 'SELECT user_id FROM ' . TBL_USERS . ' WHERE usr_username = "' . $username;
        $sql .= '" AND usr_password = "' . $this->make_password($password) . '"';

        if($rs = $this->db->execute($sql))
        {
            if(!empty($rs->fields['user_id']))
            {
                return $rs->fields['user_id'];
            }
            else
            {
                $this->errors[] = 'Invalid password or username';
                return false;
            }
        }
        else
        {
            $this->errors[] = 'Invalid password or username';
            return false;
        }
        
    }

    /*
     * bool check_login(string username, string password)
     *
     * Validates a password for a specific username
     */
    function check_login($username, $password)
    {
        // validate username and pass
        
        $sql = 'SELECT user_id, usr_password FROM ' . TBL_USERS . ' WHERE usr_username = "' . $username.'"';
        if($rs = $this->db->execute($sql))
        {
            if(!empty($rs->fields['user_id']))
            {
                if($rs->fields['usr_password'] == $this->make_password($password))
                {
                    return $rs->fields['user_id'];
                }
                else 
                {
                    $this->errors[] = 'Password is invalid';
                    return false;
                }
            }
            else
            {
                $this->errors[] = 'Username not found.';
                return false;
            }
        }
        else
        {
            
            $this->errors[] = 'Database connection error. Please contact your administrator and report this error.';
            return false;
        }
        
    }
 
    
    /*
     * string make_password(string string)
     *
     * Creates a one-way encrypted password from a text string and returns the value
     */
    function make_password($string)
    {
        // Standard MD5 
        // Add valid text check 
        
        return trim(md5($string));
    }

    /*
     * bool set_password(int user_id, string password, int update_id)
     *
     * Validates a password for a specific username/password combination
     */
    function set_password($user_id, $password, $update_id)
    {

        
        $sql = 'Update '. TBL_USERS .' set usr_password="'. $this->make_password($password).'", usr_dateupdated=now(), usr_updatedby='.$update_id.' where user_id='.$user_id;
        if(!$this->db->execute($sql))
        {
            $this->errors[] = 'Database error. Password was not changed';
            return false;
        }
        else
            return true;
    }
    

    /*
     * check_user() - Checks permissions for a user
     *
     * This function accepts a unique user and component ID, and checks to see if they
     * have permissions for that component.
     *
     * 1. Check if the user is a superuser
     * 2. Check distributor rights and return false on failure, continue on success
     * 3. Check user rights and return true on success, continue on failure
     * 4. Check group rights and return true on success, false on failure
     *
     */    
    function check_user($user_id, $com_tag, $perm)
    {
        global $user;
        
        /* Check if the user is a superuser */
        
        $sql = 'SELECT usr_superuser FROM '. TBL_USERS .' where user_id = ' . $user_id;
        if($rs = $this->db->execute($sql))
        {
           if(intval($rs->fields['usr_superuser']) === 1)
           {
               return true;
           }
        }

        /* Check distributor rights and return false on failure, continue on success */
/*        
        $sql = 'SELECT pcreate, pread, pupdate, pdelete, com_tag FROM ' . TBL_DISTRIBUTOR_PERMISSIONS . ' ' .
               'INNER JOIN ' . TBL_COMPONENTS . ' ON ' . TBL_COMPONENTS . '.component_id = ' .
               TBL_DISTRIBUTOR_PERMISSIONS . '.component_id WHERE distributor_id = ' . $user->distributor_id . ' ' .
               'AND com_tag = "' . $com_tag . '"';

        if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
        {
            // If distributor doesn't have permissions, 
            // neither can group or user. Bye bye.
            if(intval($rs->fields[$this->perms[$perm]]) !== 1)
            {
                return false;
            }
        }        
*/
        /* Check user rights and return false on failure, continue on success */
        
        $sql = 'SELECT pcreate, pread, pupdate, pdelete, com_tag FROM ' . TBL_USER_PERMISSIONS . ' ' .
               'INNER JOIN ' . TBL_COMPONENTS . ' ON ' . TBL_COMPONENTS . '.component_id = ' .
               TBL_USER_PERMISSIONS . '.component_id WHERE user_id = ' . $user->user_id . ' ' .
               'AND com_tag = "' . $com_tag . '"';

        if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
        {
            // If user has permissions, it automatically overrides group.
            if(intval($rs->fields[$this->perms[$perm]]) == 1)
            {
                return true;
            }
        }
        
        /* Loop through user groups and do a final check for permissions on this component */

        $sql = 'SELECT group_id FROM ' . TBL_USERS_GROUPS . ' WHERE user_id = ' . $user->user_id;

        if(($rsGrp = $this->db->execute($sql)) && (!$rsGrp->EOF))
        {
            while(!$rsGrp->EOF)
            {
                $sql = 'SELECT pcreate, pread, pupdate, pdelete, com_tag FROM ' . TBL_GROUP_PERMISSIONS . ' ' .
                       'INNER JOIN ' . TBL_COMPONENTS . ' ON ' . TBL_COMPONENTS . '.component_id = ' .
                       TBL_GROUP_PERMISSIONS . '.component_id WHERE group_id = ' . $rsGrp->fields['group_id'] . ' ' .
                       'AND com_tag = "' . $com_tag . '"';
                if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
                {
                    // If group has permissions, we return success
                    if(intval($rs->fields[$this->perms[$perm]]) == 1)
                    {
                        return true;
                    }
                }
                $rsGrp->MoveNext();
            }
        }
        
        /* No permissions for this component and user anywhere in the database. Bye! */
        return false;

    }
    
    
    /*
     * bool check_group_rights(int group_id, int component_id, string rights)
     *
     * This function accepts a unique group and component ID, and checks to see if they
     * have $rights available.
     *
     */        
    function check_perms($user_id, $component_id, $rights)
    {
        // Get distributor perms
        // Get group perms
        // Get user perms
        
        $sql = 'SELECT gr_create, gr_read, gr_delete, gr_update, gr_archive ' .
               'FROM '.TBL_GROUPS_RIGHTS.' tgr '.
               'WHERE tgr.component_id = ' . $component_id . ' ' .
               'AND tgr.group_id = ' . $group_id;
        $rs = $this->db->execute($sql);
        if($rs)
        {
            if($rs->fields[$this->rights[$rights]] == 'true')
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
            $this->errors[] = 'Query failed.';
            return false;
        }
    }
    
    /*
     * bool check_group_rights(int group_id, int component_id, string rights)
     *
     * This function accepts a unique group and component ID, and checks to see if they
     * have $rights available.
     *
     */        
    function check_group_rights($group_id , $component_id, $rights)
    {
        $sql = 'SELECT gr_create, gr_read, gr_delete, gr_update, gr_archive ' .
               'FROM '.TBL_GROUPS_RIGHTS.' tgr '.
               'WHERE tgr.component_id = ' . $component_id . ' ' .
               'AND tgr.group_id = ' . $group_id;
        $rs = $this->db->execute($sql);
        if($rs)
        {
            if($rs->fields[$this->rights[$rights]] == 'true')
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
            $this->errors[] = 'Query failed.';
            return false;
        }
 

    }
   
    
    /**
     * get_keys()
     */
    function get_keys()
    {
    	$sql = 'SELECT component_id, com_tag FROM ' . TBL_COMPONENTS;

        if($rs = $this->db->execute($sql))
        {
        	$keys = array();
        	while(!$rs->EOF)
        	{
        		$keys[$rs->fields['com_tag']] = $rs->fields['component_id'];	
        		$rs->MoveNext();
        	}
			return $keys;
        }
        else 
        {
        	$this->errors[] = 'Error fetching component keys from DB';
        	return false;
        }
    	
    }
            
}

?>
