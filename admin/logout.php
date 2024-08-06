<?php
/**
 * Logout Script
 *
 * @author      Teknowledgery <gharper@teknowledgery.com>
 * @copyright   Teknowledgery
 * @version     1.0.0
 * @access      public
*/
define('_APP_IN', 'true');

include('../config.php');
include(PATH_INCLUDE . 'admin_header.php');


if($session->user_id)
{

	$session->user_id = 0;
	$session->update();
	dialog_box('You have been successfully logged out.', DIR_ADMIN_BASE . '" target="_top');
}
else 
{

	dialog_box('You must be logged in to access this page.', DIR_ADMIN_BASE . 'login.php', '',3500);
}


include(PATH_INCLUDE . 'admin_footer.php');
$tpl->render_all();

?>
