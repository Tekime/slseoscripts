<?php
/**
 * class.kUser.php - Kytoo User Component
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
 * 2009-04-23 (1.0) - Released with Kytoo 2.0  
 *
*/

class kUser extends kBase
{
	
	// Define all user attributes
	var $errors = array();
	
	var $user_id = NULL;
	var $username = '';
	var $email = '';
	var $lastlogin = '';
	var $fullname = '';
	
	function kUser(&$db, $user_id = 0)
	{
        $this->db =& $db;
        
	    if($user_id)
		{
			$this->load_user($user_id);
		}
	}
	
	function load_user($user_id)
	{
		
		$sql = 'SELECT * FROM ' . TBL_USERS . ' WHERE user_id = ' . $user_id;

		if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
		{
    		$this->user_id = $user_id;
    		$this->username = $rs->fields['usr_username'];
    		$this->fullname = $rs->fields['usr_fullname'];
    		$this->email = $rs->fields['usr_email'];
    		$this->password = $rs->fields['usr_password'];
    		$this->lastlogin = dbtime_to_unix($rs->fields['usr_lastlogin']);
    		return true;
		}
		else 
		{
		    return false;
		}
	}
	
	function save()
	{
		if($this->errors) unset($this->errors);
		
		if(!$this->validate->is_username($this->username))
			$this->errors[] = 'Invalid Username';
		if(!$this->validate->is_real_name($this->fullname))
			$this->errors[] = 'Invalid Full Name';
		if(!$this->validate->is_email($this->email)) 
			$this->errors[] = 'Invalid EMail Address';
		if(!empty($this->user_id))
		{
			if(!$this->validate->is_number($this->user_id))
			$this->errors[] = 'Invalid User ID requested';
		}

		if(!empty($this->password1))
		{
			if($this->password1 == $this->password2)
			{
				if($this->validate->is_password($this->password1))
				{
					$this->password = $this->perm->make_password($this->password1);
				}
				else 
				{
					$this->errors[] = 'Invalid password format';
				}
			}	
			else 
			{
				$this->errors[] = 'Passwords do not match';	
			}
		}
		
		if(!$this->errors)
		{
			if(!empty($this->user_id))
			{
				$sql = 'UPDATE ' . TBL_USERS . ' SET ' . 
					   'usr_username = "' . $this->username . '", ' . 
					   'usr_firstname = "' . $this->firstname . '", ' .
					   'usr_lastname = "' . $this->lastname . '", ' .
					   'usr_email = "' . $this->email . '", ' .
					   'usr_dateupdated = "' . unix_to_dbtime(time()) . '", ' .
					   'usr_updatedby = ' . $this->session->user_id . ' ';
					   if(!empty($this->password))
					   {
					   		$sql .= ', password = "' . $this->password . '" ';
					   }
					   $sql .= ' WHERE user_id = ' . $this->user_id;

				if($rs = $this->db->execute($sql))
				{
					return true;
				}
			}
			else 
			{
				$sql = 'INSERT INTO ' . TBL_USERS . ' ' .
					   '(usr_username, usr_password, usr_firstname, usr_lastname, usr_email, usr_datecreated, usr_createdby) ' .
					   'VALUES("' . $this->username . '", "' . $this->password . '", "' . $this->firstname . '", "' .
					   $this->lastname . '", "' . $this->email . '", "' . unix_to_dbtime(time()) . '", ' .
					   $this->session->user_id . ')';

				if($rs = $this->db->execute($sql))
				{
					$this->user_id = $this->db->Insert_ID();
					return true;
				}
				else 
				{
					$this->errors[] = 'A database error has occurred. The username is already taken or the database is temporarily offline';
					return false;	
				}
				
			}
			
			
		}
		else 
		{
			return false;
		}
	}
	
	function update_lastlogin($time)
	{
		$sql = 'UPDATE ' . TBL_USERS . ' SET usr_lastlogin = "' . unix_to_dbtime($time) . '" WHERE user_id = ' . $this->user_id;
		if($rs = $this->db->execute($sql))
		{
				return true;		
		}
		else 
		{
			$this->errors[] = 'Database failure updating user login time';			
			return false;
		}
	}
	
	function delete($user_id)
	{
		$sql = 'DELETE FROM ' . TBL_USERS . ' WHERE user_id = ' . $user_id;		
		
		if($rs = $this->db->execute($sql))
		{
			return true;
		}
		else 
		{
			$this->errors[] = 'Error deleting user from database';
			return false;
		}
	}
}