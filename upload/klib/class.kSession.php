<?php
/**
 * class.kSession.php - Kytoo Application Component Object
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
 * 2009-05-01 (1.0) - Updated for Kytoo 2.0  
 *
*/

class kSession extends kBase
{
    
    /**
     * @access  public
     * @var     array $errors       Array of error messages
    */
    var $errors = array();
    /**
     * @access  public
     * @var     int $ttl            Time To Live (TTL) for the session
    */
    var $ttl = 259200;
    /**
     * @access  public
     * @var     string $ip          Client IP address
    */
    var $ip = '';
    /**
     * @access  public
     * @var     int $user_agent     Client browser's user agent identifier
    */
    var $user_agent = '';
    /**
     * @access  public
     * @var     string $session_id  Unique session ID
    */
    var $session_id = '';
    /**
     * @access  public
     * @var     int $uid            Client's unique ID value
    */
    var $uid = 0;
    /**
     * @access  public
     * @var     string $uidTable    Name of the table client's unique ID value is stored
    */
    var $uidTable = 'tbl_users';
    /**
     * @access  public
     * @var     string $uidField    Name of the field client's unique ID value is stored
    */
    var $uidField = 'user_id';
    /**
     * @access  public
     * @var     string $type        Type of session (usually determines the unique ID)
    */
    var $type;
    /**
     * @access  public
     * @var     string $cookieName  Name to assign to the session cookie
    */
    var $cookieName;
    /**
     * @access  public
     * @var     string $cookieName  Name to assign to the session cookie
    */
    var $user_id = 0;
    /**
     * @access  public
     * @var     string $cookieName  Name to assign to the session cookie
    */
    
    var $updated = 0;
    var $created = 0;
    
    function kSession(&$db, $uidTable = TBL_USERS, $uidField = 'user_id', $cookieName = 'kytoocms')
    {        
        $this->db =& $db;
        
        $this->uidTable = $uidTable;
        $this->uidField = $uidField;
        $this->start();
        
        return true;
    }

    /*
     * bool start()
     *
     * This function controls the initial startup and session checking process.
     *
    */
    function start()
    {
        session_start();        

        $this->session_id = session_id();
        $this->server_name = $_SERVER['SERVER_NAME'];
        $this->agent = $_SERVER['HTTP_USER_AGENT'];
        $this->get_ip();
        
        // Attempt to get an existing session from the database,
        // otherwise create a new one
        
        $sql = 'SELECT * FROM ' . TBL_SESSIONS . ' WHERE session_id = "' . $this->session_id . '"';
        if(($rs = $this->db->execute($sql)) && (!$rs->EOF))
        {
            $this->user_id = $rs->fields['sess_user_id'];

   			$this->created = dbtime_to_unix($rs->fields['datecreated']);
        	$this->update();
        }
        else 
        {
        	$this->create();
        }
    }
    
    /*
     * bool update_session()
     *
     * This function updates session information in the session table, and
     * deletes all expired entries. Only use this function once the session
     * is verified.
     *
    */
    function update()
    {

        $this->updated = time();
        /* Update the current session table */
        $sql = 'UPDATE ' . TBL_SESSIONS . ' SET dateupdated = "' . unix_to_dbtime($this->updated) . '", ' .
               'sess_user_id = ' . $this->user_id . ', ' .
               'datecreated = "' . unix_to_dbtime($this->created) . '", ' .
               'sess_expires = "' . unix_to_dbtime(time() + $this->ttl) . '" ' .
               'WHERE session_id = "' . $this->session_id . '"';

		$this->flush_expired();

        if($this->db->execute($sql))
        {
			return true;
        }
        else 
        {
            $this->errors[] = 'Error saving session information to the database.';
            return false;
        }
    
    } // End function update_session()


    function flush_expired()
    {
        // Delete any sessions that have expired
        $expired = time();
        
        $sql = 'DELETE FROM ' . TBL_SESSIONS . ' WHERE sess_expires < "' . unix_to_dbtime($expired) . '"';

        if($rs = $this->db->execute($sql))
        {
        	return true;
        }
        else 
        {    
            $this->errors[] = "Error deleting expired sessions";
            return false;
        }
    }    	
    
    
    /*
     * bool create_session()
     *
     * This function creates a new session entry in the session table.
    */
    function create($uid = 0)
    {

        $sql = 'INSERT INTO ' . TBL_SESSIONS . ' (session_id, sess_user_id, sess_ip, sess_expires, dateupdated, datecreated, sess_useragent) VALUES("';
        $sql .= $this->session_id . '", ' . $this->user_id. ', "';
        $sql .= $this->ip . '", "';
        $sql .= unix_to_dbtime(time() + $this->ttl) . '", "';
        $sql .= unix_to_dbtime(time()) . '", "';
        $sql .= unix_to_dbtime(time()) . '", "';
        $sql .= $this->user_agent . '"';
        $sql .= ')';

        if($rs = $this->db->execute($sql))
        {
			return true;
        }
        else
        {
            $this->errors[] = $this->db->ErrorMsg();
            return false;
        }

    } // End function create_session() 
    

    
    /*
     * get_ip()
     *
     * This function returns the current client IP address
    */        
    function get_ip()
    {
        if(isset($_SERVER))
        {           
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            elseif(isset($_SERVER['HTTP_CLIENT_IP']))
            {
                $this->ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            else
            {
                $this->ip = $_SERVER['REMOTE_ADDR'];
            }    
        }
        else
        {
            if(getenv('HTTP_X_FORWARDED_FOR'))
            {
                $this->ip = getenv('HTTP_X_FORWARDED_FOR');
            }
            elseif(getenv('HTTP_CLIENT_IP'))
            {
                $this->ip = getenv('HTTP_CLIENT_IP');
            }
            else
            {
                $this->ip = getenv('REMOTE_ADDR');
            }
        }

    } // End function get_ip()

    
    function delete()
    {
        setcookie($this->cookieName,"",0,"/");
            
        $sql = 'DELETE FROM ' . TBL_SESSIONS . ' WHERE session_id = "' . $this->session_id . '"';
        
        if($this->db->execute($sql))
        {
            return true;
        }
        else 
        {
        	return false;
        }
        	
        // Unset all of the session variables.
        session_unset();
        // Finally, destroy the session.
        session_destroy();
        
    }

    /**
     * variant login(string $username, string $password)
     *
     * login() accepts a username/password combo and attempts authenticate the user.
     * If the user is authenticated, a new $user object is created and returned.
     * If the authentication fails, false is returned.
    */    
    function login($username, $password)
    {
    	
    	// FIRST, CHECK FOR A SESSION IN DB WITH A USERID
    	// IF ONE EXISTS, FORCE TO LOGOUT FIRST
    	// ELSE, CREATE NEW SESSION, ADD USERID AND UPDATE DB
    	
    	// Validate username
    	if($this->validate->is_username($username) === false)
    	{
    		$this->errors[] = 'Invalid username. Must be 4-12 characters, and contain only letters, numbers, and underscores.';
    	}
    	// validate password
    	if($this->validate->is_password($password,'Invalid password') === false)
    	{
    		$this->errors[] = 'Invalid password. Must be 5-16 characters, and contain only letters, numbers, and underscores.';
    	}
    	
    	// Check for validation errors
    	if(!$this->errors)
    	{
    		// Check the username/password combo
    		if($user_id = $this->perm->check_password($username, $password))
    		{
  				$user->created = time();
   				$user->updated = time();
   				$this->user_id = $user_id;

   				if($this->update_session())
   				{
					return true;
   				}   				
   				else 
   				{
   					$this->errors[] = 'Database access error, please try again later.';
   					return false;
   				}
    		}
    		else 
    		{
    			$this->errors = $this->perm->errors;
    			return false;
    		}
    			
    	}
    	else 
    	{
			$this->errors[] = 'Database access error, please try again later.';    		
    		return false;
    	}
    	
    }
    
    
    function set($name, $value)
    {
        $_SESSION[$name] = $value;   
    }
    
    function clear($name)
    {
        $_SESSION[$name] = '';
    }
}

?>
