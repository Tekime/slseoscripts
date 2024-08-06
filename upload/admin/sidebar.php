<?php
/**
 * ADMINSTRATION - Index
 *
 * @author      Teknowledgery <gharper@teknowledgery.com>
 * @copyright   ©2004 Teknowledgery
 * @version     1.0.0
 * @access      public
*/

require_once('../config.php');
define('TPL_EXT', '_clean');
require_once(PATH_INCLUDE . 'admin_header.php');

define('SIDEBAR', true);

$tpl->define('sidebar', 'sidebar.tpl');
$tpl->parse('sidebar');

require_once(PATH_INCLUDE . 'admin_footer.php');
$tpl->render_all();

?>