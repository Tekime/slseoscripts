<?php
/**
 * main.php - Kytoo CMS main admin page
 *
 * This script is part of Kytoo CMS (www.kytoo.com).
 *
 * Copyright (c) 2007, Kytoo (www.kytoo.com)
 * All rights reserved.
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
 * @version     1.0
 * 
*/
require_once('../config.php');
require_once(PATH_INCLUDE . 'header.php');

if(!empty($_REQUEST['page_id']))
{
    $sql = 'SELECT pg_contents FROM ' . TBL_PAGES . ' WHERE page_id = ' . intval($_REQUEST['page_id']);
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        $html = $rs->fields['pg_contents'];
    }
}
else
{
    $html = 'document.getElementById(\'pgpreview\').innerHTML=\opener.document.getElementById(\'pg_contents\').value;"></body></html>';
    $html = '<div id="pgpreview">Loading...</div><script language="JavaScript">document.getElementById(\'pgpreview\').innerHTML=opener.document.getElementById(\'pg_contents\').value;</script>';
}

$tpl->define('preview', $html, 1);
$tpl->parse('preview');

require_once(PATH_INCLUDE . 'footer.php');
$tpl->render_all();

?>