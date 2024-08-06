<?php
/**
 * Login Script
 *
 * @author      Teknowledgery <gharper@teknowledgery.com>
 * @copyright   Teknowledgery
 * @version     0.0.1
 * @access      public
*/
define('_APP_IN', 'true');

require_once('../config.php');
require_once(PATH_INCLUDE . 'admin_header.php');

if(empty($session->user_id))
{
    print_pre($user);
    $cfg->setVar('page_title', 'Log In');
	$tpl->define('login_form', 'login_form.tpl');

	// Validate email address and password
	if(!empty($_POST['username']) && !empty($_POST['password']))
	{
		$verrors = array();
		$errors = array();

    	// Validate username
    	if($validate->is_username($_POST['username']) === false)
    	{
    		$verrors[] = 'Invalid username. Must be 4-12 characters, and contain only letters, numbers, and underscores.';
    	}
    	// validate password
    	if($validate->is_password($_POST['password'],'Invalid password') === false)
    	{
    		$verrors[] = 'Invalid password. Must be 5-16 characters, and contain only letters, numbers, and underscores.';
    	}
    	
    	// Check for validation errors
    	if($verrors)
    	{
    	    error_msg($verrors);
    	    $tpl->parse('login_form');
    	    $tpl->assign('f_email', htmlspecialchars($_POST['email']));
    	}
    	else 
    	{
    		$sql = 'SELECT user_id, usr_admin FROM ' . TBL_USERS . ' WHERE usr_username = "' . $_POST['username'] .
    		       '" AND usr_password = "' . $perm->make_password($_POST['password']) . '"';

            if(($rs = $db->execute($sql)) && (!$rs->EOF))
            {
                if($rs->fields['usr_admin'] == 1)
                {
                    if($user = new kUser($db, $rs->fields['user_id']))
                    {
                        $session->user_id = $user->user_id;
          				$user->created = time();
           				$user->updated = time();
                        if($session->update())
                        {
    			             dialog_box('Thank you for logging in, ' . $_POST['username'] . '.', DIR_ADMIN_BASE, null, 'Logged In', 3500);
                        }
                        else 
                        {
                            die('An error was encountered when accessing your user account');
                        }
                    }
                    else 
                    {
                        die('An error was encountered when loading your user account');
                    }
                }
                else 
                {
                    dialog_box('Sorry, you must be an administrator to log in here.', DIR_ADMIN_BASE, null, 'Unauthorized', 5000);
                }
    		}
    		else 
    		{
    			$errors[] = 'The username and password you entered are incorrect';
    			error_msg($errors);
        	    $tpl->parse('login_form');
        	    $tpl->assign('f_username', htmlspecialchars($_POST['username']));
    		}
    	}
	}
	else
	{
		$tpl->parse('login_form');
	}
}
else 
{
	dialog_box('A user has already logged in from this PC.', DIR_ADMIN_BASE, '', 'Login Failure');
}


require_once(PATH_INCLUDE . 'admin_footer.php');
$tpl->render_all();
?>