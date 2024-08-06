<?php
/**
 * kFunctions.php - Common functions
 *
 * IMPORTANT - This is not free software. You must adhere to the terms of the End-User License Agreement
 *             under penalty of law. Read the complete EULA in "license.txt" included with your application.
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
 * @copyright   Copyright (c) 2012 Gabriel Harper, All Rights Reserved
 * @author      Gabriel Harper, http://gabrielharper.com/
 * @version     2.3
 *
 * 2.3 - Added json_decode() for PHP < 5.2
 * 2.2 (2009-05-23) - Added createUniqueCode()
 * 2.1 (2009-05-20) - Updated for Kytoo 2.0, added k_clone_tpl()
 * 2.0 - Updated: getMatchingFiles()
 * 1.9 - Added replaceMsgVars(), update getMessage with subjects
 * 1.8 - Merged changes from disparate 1.6 release and 1.7
 * 1.7 - Added captchaImageDisplay()
 * 1.6 - Added getMessage(), replaceCommonVars(), getSummary(), getLanguages(), systemError(), urlReplaceVars(),
 *             sendEmail(), queueEmail(), processEmail(), deliverEmail(), runEmailJobs(), uniqueId(), netGetIp()
 * 1.5 - Added dircopy(), recursive_remove_directory(), do_post_request()
 * 1.4 - Added getUrlBase()
 * 1.3 - Added db_getinsert(), db_getupdate(), print_pre(), getUrl()
 * 1.2 - Added error_msg(), unix_to_dbtime(), dbtime_to_unix(), dialog_box(), get_middle()
 * 1.1 - Added format_friendly_url(), unformat_friendly_url(), read_file()
 * 
 */

/**
 * Checks for magic quotes and addslashes() all $_REQUEST vars.
 *
 * @param   bool $post
 * @param   bool $get
 * @param   bool $cookie
 * @return  mixed
 */
function clean_request_vars()
{
    // Check for magic quotes
    if(!get_magic_quotes_gpc())
    {
        foreach($_REQUEST as $key=>$value)
        {
            if(!empty($value))
            	$_REQUEST[$key] = trim(addslashes($value));
        }
    }
}

// Removes slashes from all text REQUEST variables
function strip_request_vars()
{
    foreach($_REQUEST as $key => $value)
    {
        if((!empty($value)) && (!is_array($value)) && (!is_numeric($value)))
        {
            $_REQUEST[$key] = stripslashes($value);
        }
    }
}

/**
 * format_friendly_url() - Make a string URL friendly
 *
 * Replaces all characters except letters, numbers, dashes, spaces and underscores
 * with a single dash (-), reduces mulitiple unfriendly characters to one dash
 * 
 * @param string $string
 * @return string
 */
function format_friendly_url($string)
{
    if(isset($string))
    {
        $regex = "^[-_ A-Za-z0-9]*$";
        
        if(ereg($regex, $string))
        {
            $string = ereg_replace(" +", "-", $string);
            return $string;    
        }
        else
        {
            return $string;
        }
    }
}

/**
 * unformat_friendly_url() - Convert friendly URL to string
 * 
 * Converts an URL friendly string back into a regular string by replacing
 * one or more dashes into spaces.
 *
 * @param string $string
 * @return string
 */
function unformat_friendly_url($string)
{
    if(isset($string))
    {
        $regex = "^[-_ A-Za-z0-9]*$";
        
        if(ereg($regex, $string))
        {
            $string = ereg_replace("-+", " ", $string);
            return $string;    
        }
        else
        {
            return false;
        }
        
    }   

}

/**
 * read_file() - Read file contents into a string
 *
 * Read the contents of the provided filename and return as a string.
 * 
 * @param string $filename
 * @return string
 */
function read_file($filename) {
    if(file_exists($filename))
    {
        $fhandle = fopen($filename, "r");
        if(filesize($filename) > 0)
            $fcontent = fread ($fhandle, filesize($filename));
        fclose($fhandle);

        return $fcontent;    
    }
    else
    {
        return false;
    }
}

/**
 * error_msg() - Output standard error message
 * 
 * Parse a standard error message to the primary template. Accepts
 * a string or array of error messages and handles appropriately.
 *
 * @param mixed $errors
 * @return bool
 */
function error_msg($errors)
{
    global $tpl;
    $tpl->define('error_msg', 'error_msg.tpl');
    $tpl->define_d('error_row', 'error_msg');
    $tpl->parse('error_msg');
    
    if(is_array($errors))
    {
        foreach($errors as $error)
        {
            $tpl->assign_d('error_row', 'error_msg', $error);
            $tpl->parse_d('error_row');
        }
    }
    else 
    {
        $tpl->assign_d('error_row', 'error_msg', $errors);
        $tpl->parse_d('error_row');
    }
    
    return true;
}

/**
 * dbtime_to_unix() - Convert DATETIME to UNIX timestamp
 * 
 * Converts a MySQL DATETIME value to a UNIX timestamp.
 *
 * @param string $datetime
 * @return string
 */
function dbtime_to_unix($datetime)
{
	return strtotime($datetime);
}

/**
 * unix_to_dbtime() - Convert UNIX timestamp to DATETIME
 * 
 * Converts a MySQL DATETIME value to a UNIX timestamp.
 *
 * @param string $datetime
 * @return string
 */
function unix_to_dbtime($timestamp)
{
	return date('Y-m-d H:i:s', $timestamp);
}

/**
 * dialog_box() - Display a standard dialog box
 *
 * Displays a common dialog box to the primary template. Accepts 
 * optional OK and Cancel buttons, title and redirect.
 * 
 * @param string $msg
 * @param string $ok_url
 * @param string $cancel_url
 * @param string $title
 * @param string $redirect
 * @param string $tplext
 */
function dialog_box($msg, $ok_url = 0, $cancel_url = 0, $title = 'Confirmation', $redirect = 4000, $tplext = '')
{
	global $tpl, $cfg;

	if(is_object($tpl))
	{

	    $tpl->define('dialog_box', 'dialog_box' . $tplext . '.tpl');
		
		$tpl->parse('dialog_box');
				
		$tpl->define_d('dialog_ok', 'dialog_box');
		$tpl->define_d('dialog_cancel', 'dialog_box');
		$tpl->define_d('js_redirect', 'dialog_box');
		
		if(!empty($ok_url))
		{
			$tpl->assign_d('dialog_ok', 'ok_url', $ok_url);
			$tpl->parse_d('dialog_ok');
		}
		if(!empty($cancel_url))
		{
			$tpl->assign_d('dialog_cancel', 'cancel_url', $cancel_url);
			$tpl->parse_d('dialog_cancel');
		}
		if($redirect !== false)
		{
			$tpl->assign_d('js_redirect', 'js_redirect_url', $ok_url);
			$tpl->assign_d('js_redirect', 'js_redirect_time', $redirect);
			$tpl->parse_d('js_redirect');

		}
		
		$tpl->assign('dialog_title', $title);
		$cfg->setVar('page_title', $title);
		$tpl->assign('dialog_body', $msg);		
		
	}
	else
	{
		app_error();
	}
	
}

/**
 * dialog_box() - Display a standard dialog box
 *
 * Displays a common dialog box to the primary template. Accepts 
 * optional OK and Cancel buttons, title and redirect.
 * 
 * @param string $msg
 * @param string $ok_url
 * @param string $cancel_url
 * @param string $title
 * @param string $redirect
 * @param string $tplext
 */
function dialog_form($msg, $ok_url = 0, $cancel_url = 0, $title = 'Confirmation', $redirect = 4000, $tplext = '', $vals = false, $dbtable = false, $dblook = false)
{
	global $tpl, $cfg;

	if(!empty($idfield))
	{

	    $tpl->define('dialog_box', 'dialog_box' . $tplext . '.tpl');
		
		$tpl->parse('dialog_box');
				
		$tpl->define_d('dialog_ok', 'dialog_box');
		$tpl->define_d('dialog_cancel', 'dialog_box');
		$tpl->define_d('js_redirect', 'dialog_box');
		
		if(!empty($ok_url))
		{
			$tpl->assign_d('dialog_ok', 'ok_url', $ok_url);
			$tpl->parse_d('dialog_ok');
		}
		if(!empty($cancel_url))
		{
			$tpl->assign_d('dialog_cancel', 'cancel_url', $cancel_url);
			$tpl->parse_d('dialog_cancel');
		}
		if($redirect !== false)
		{
			$tpl->assign_d('js_redirect', 'js_redirect_url', $ok_url);
			$tpl->assign_d('js_redirect', 'js_redirect_time', $redirect);
			$tpl->parse_d('js_redirect');

		}
		
		$tpl->assign('dialog_title', $title);
		$cfg->setVar('page_title', $title);
		$tpl->assign('dialog_body', $msg);		
    }
    else
    {
	    $tpl->define('dialog_box', 'dialog_form.tpl');	
		$tpl->parse('dialog_box');
				
		$tpl->define_d('dialog_form_fields', 'dialog_box');
		$tpl->define_d('dialog_form_names', 'dialog_box');
		$tpl->define_d('dialog_cancel', 'dialog_box');

        $tpl->assign('dialog_form_action', $ok_url);

        foreach($vals as $key => $value)
        {
            if(is_array($value))
            {
                foreach($value as $key2 => $value2)
                {
                    $tpl->assign_d('dialog_form_fields', 'field_name', $key . '[]');
                    $tpl->assign_d('dialog_form_fields', 'field_value', $value2);
                    $tpl->parse_d('dialog_form_fields');
                }
            }
            else
            {
                $tpl->assign_d('dialog_form_fields', 'field_name', $key);
                $tpl->assign_d('dialog_form_fields', 'field_value', $value);
                $tpl->parse_d('dialog_form_fields');
            }
        }

        if($dbtable && $dblook)
        {
            global $db;
            foreach($vals as $key => $value)
            {
                $sql = 'SELECT ' . $dblook . ' FROM ' . $dbtable . ' WHERE ' . $idcol . ' = ' . $value;
                if(($rs = $db->execute($sql)) && (!$rs->EOF))
                {
                    $tpl->assign_d('dialog_form_names', 'field_name', $rs->fields[$dblook]);
                    $tpl->parse_d('dialog_form_names');
                }
            }
        }
        
		if(!empty($cancel_url))
		{
			$tpl->assign_d('dialog_cancel', 'cancel_url', $cancel_url);
			$tpl->parse_d('dialog_cancel');
		}
		
		$tpl->assign('dialog_title', $title);
		$cfg->setVar('page_title', $title);
		$tpl->assign('dialog_body', $msg);		
    }
	
}
/**
 * get_middle() - Get content between two strings within a string
 * 
 * Searches for and returns the content between the two provided strings.
 *
 * @param string $source
 * @param string $beginning
 * @param string $ending
 * @param int $init_pos
 * @return string
 */
function get_middle($source, $beginning, $ending, $init_pos = 0)
{
   $beginning_pos = strpos($source, $beginning, $init_pos);
   $middle_pos = $beginning_pos + strlen($beginning);
   $ending_pos = strpos($source, $ending, $beginning_pos + 1);
   $middle = substr($source, $middle_pos, $ending_pos - $middle_pos);
   
   return $middle;
}
/**
 * get_middle_all() - Get all occurances of content between two strings within a string
 *
 * Searches for and returns multiple occurances of content between the two provided strings.
 * 
 * @param string $start
 * @param string $end
 * @param string $string
 * @return mixed
 */
function get_middle_all($start, $end, $string)
{
    $startpos = false;
    $endpos = false;
    $matches = array();
    $no_matches = false;
    $startlen = strlen($start)-1;
    $endlen = strlen($end)-1;
    
    while($no_matches === false)
    {
        $startpos = strpos($string, $start);
        if($startpos !== false)
        {
            $string = substr($string, $startpos + $startlen);
            $endpos = strpos($string, $end);
            if($endpos !== false)
            {
                $matches[] = substr($string, 0, $endpos + $endlen);
            }
            $string = substr($string, $endpos + $endlen);
        }
        else 
        {
            $no_matches = true;
        }
    }
    return $matches;
}

/**
 * db_getinsert() - Creates a MySQL INSERT statement
 *
 * Generates a MySQL INSERT statement for the specified table using
 * the supplied data array and matching key names with table field names.
 * 
 * @param mixed $db
 * @param string $table
 * @param string $fields
 * @param bool $addslashes
 * @return string
 */
function db_getinsert(&$db, $table, $fields, $addslashes = 0)
{  
    $dbfields = $db->getFields($table);

    foreach($dbfields as $key=>$dbfield)
    {
        if(@isset($fields[$dbfield]))
        {
            if(is_numeric($fields[$dbfield]))
            {
                $fieldset[] = $dbfield;
                $valueset[] = $fields[$dbfield];            
            }
            elseif((!empty($fields[$dbfield])) || ($fields[$dbfield] === 0))
            {
                $fieldset[] = $dbfield;
                if($addslashes)
                {
                    $valueset[] = '"' . addslashes($fields[$dbfield]) . '"';
                }
                else 
                {
                    $valueset[] = '"' . $fields[$dbfield] . '"';
                }
                
            }
        }
    }
    $sql = 'INSERT INTO ' . $table . ' (' . implode(',', $fieldset) . ') VALUES (' . implode(',', $valueset) . ')';

    return $sql;
}
/**
 * db_getinsert() - Creates a MySQL UPDATE statement
 *
 * Generates a MySQL UPDATE statement for the specified table using
 * the supplied data array and matching key names with table field names.
 * 
 * @param mixed $db
 * @param string $table
 * @param string $fields
 * @param string $criteria
 * @param bool $addslashes
 * @return string
 */
function db_getupdate(&$db, $table, &$fields, $criteria = '0=1', $addslashes = 0)
{
    $dbfields = $db->getFields($table);

    foreach($dbfields as $key=>$dbfield)
    {
        if(@isset($fields[$dbfield]))
        {
            if(is_numeric($fields[$dbfield]))
            {
                $fieldset[] = $dbfield . '=' . $fields[$dbfield];
            }
            elseif(isset($fields[$dbfield]))
            {
                if($addslashes)
                {
                    $fieldset[] = $dbfield . '="' . addslashes($fields[$dbfield]) . '"';
                }
                else 
                {
                    $fieldset[] = $dbfield . '="' . $fields[$dbfield] . '"';
                }
            }
        }
    }
    $sql = 'UPDATE ' . $table . ' SET ' . implode(',', $fieldset) . ' WHERE ' . $criteria;

    return $sql;
}

/**
 * print_pre() - Prints an array between <pre></pre> tags
 *
 * @param array $fields
 */
function print_pre($fields)
{
    print '<pre>';
    print_r($fields);
    print '</pre>';
}

/**
 * getUrl() - Returns URL contents as a string
 *
 * Fetches an URL using CURL and returns as a string.
 * 
 * @param string $url
 * @param int $header
 * @return string
 */
function getUrl($url, $header = 0)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $contents = curl_exec($ch);
    curl_close($ch);
    
    return $contents;
}

/**
 * getUrlBase() - Strips "www." from an URL
 *
 * Strips the "www." from an URL and returns the base URL as a string.
 * 
 * @param string $url
 * @return string
 */
function getUrlBase($url)
{
    $url = str_replace('www.', '', $url);
    return $url;
}

/**
 * getUrlRoot() - Removes subdomain from an URL
 * 
 * Returns the base URL with any subdomain or "www." removed.
 *
 * @param string $url
 * @return string
 */
function getUrlRoot($url)
{
    $parts = split('\.', $url);
    
    $parts = array_reverse($parts);
    
    return $parts[1] . '.' . $parts[0];
}

/**
 * dircopy() - Copies an entire directory
 * 
 * Copies the contents of an entire directory from one directory to another.
 *
 * @param string $srcdir
 * @param string $dstdir
 * @param bool $verbose
 * @return int
 */
function dircopy($srcdir, $dstdir, $verbose = false)
{
  $num = 0;
  if(!is_dir($dstdir)) mkdir($dstdir);
  if($curdir = opendir($srcdir)) {
   while($file = readdir($curdir)) {
       
     if($file != '.' && $file != '..') {
       $srcfile = $srcdir . $file;
       $dstfile = $dstdir . $file;
       echo $srcfile;
       if(is_file($srcfile)) {

         if(is_file($dstfile)) $ow = filemtime($srcfile) - filemtime($dstfile); else $ow = 1;
         if($ow > 0) {
           if($verbose) echo "Copying '$srcfile' to '$dstfile'...";
           if(copy($srcfile, $dstfile)) {
             touch($dstfile, filemtime($srcfile)); $num++;
             if($verbose) echo "OK\n";
           }
           else echo "Error: File '$srcfile' could not be copied!\n";
         }                 
       }
       else if(is_dir($srcfile)) {
         $num += dircopy($srcfile, $dstfile, $verbose);
       }
     }
   }
   closedir($curdir);
  }
  return $num;
}

/**
 * recursive_remove_directory() - Recursively remove directories
 * 
 * Removes or empties a directory and all subdirectories
 *
 * @param string $directory
 * @param bool $empty
 * @return bool
 */
function recursive_remove_directory($directory, $empty = false)
{
    // Remove trailing slash
    if(substr($directory,-1) == '/')
    {
         $directory = substr($directory,0,-1);
    }
    
    // Check for valid, readable directory
    if(!file_exists($directory) || !is_dir($directory))
    {
        return false;
    }
    elseif(!is_readable($directory))
    {
        return false;
    }
    else
    {
        $handle = opendir($directory);
        while (false !== ($item = readdir($handle)))
        {
            if($item != '.' && $item != '..')
            {
                $path = $directory . '/' . $item;
                if(is_dir($path)) 
                {
                    recursive_remove_directory($path);
                }
                else
                {
                    unlink($path);
                }
            }
        }
        closedir($handle);
        if($empty == false)
        {
            if(!rmdir($directory))
            {
                return false;
            }
        }
        return true;
    }
}

/**
 * do_post_request() - Perform an HTTP POST request
 *
 * Perform an HTTP POST request using CURL and supplied data.
 * 
 * @param string $url
 * @param mixed $data
 * @param string $referer
 * @return string
 */
function do_post_request($url, $data, $referer = '')
{
    $urlinfo = parse_url($url);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlinfo['host'] . $urlinfo['path']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

/**
 * getMessage() - Gets a formatted Kytoo message
 *
 * Gets a Kytoo system message from the database and returns formatted with the provided fields.
 * 
 * @param string $name
 * @param mixed $fields
 * @return string
 */
function getMessage($name, $fields = false)
{
    global $db;
    
    $sql = 'SELECT * FROM ' . TBL_MESSAGES . ' WHERE msg_name="' . $name . '"';
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        $message['title'] = $rs->fields['msg_title'];
        $message['subject'] = $rs->fields['msg_subject'];
        $message['text'] = $rs->fields['msg_text'];
        
        $message['subject'] = replaceCommonVars($message['subject']);       
        $message['title'] = replaceCommonVars($message['title']);
        $message['text'] = replaceCommonVars($message['text']);

        if($fields)
        {
            foreach($fields as $field=>$value)
            {
                $message['title'] = str_replace('[[' . $field . ']]', $value, $message['title']);
                $message['subject'] = str_replace('[[' . $field . ']]', $value, $message['subject']);
                $message['text'] = str_replace('[[' . $field . ']]', $value, $message['text']);
            }
        }
        return $message;
    }
    else 
    {
        return false;
    }
}

/**
 * replaceCommonVars() - Replaces common Kytoo config variables in a string
 *
 * Replaces common Kytoo config variables in the provided string and returns the formatted output.
 * 
 * @param string $string
 * @param string $match1
 * @param string $match2
 * @return string
 */
function replaceCommonVars($string, $match1 = '[[', $match2 = ']]')
{
    global $cfg;
    foreach($cfg->fields as $key=>$config)
    {
        foreach($config as $field=>$value)
        {
            $string = str_replace('[[' . $field . ']]', $value, $string);
        }
    }
    return $string;
}

/**
 * replaceMsgVars() - Replace message vars using provided format
 *
 * @param string $string
 * @param string $match1
 * @param string $match2
 * @return string
 */
function replaceMsgVars($string, $fields, $match1 = '[[', $match2 = ']]')
{
    foreach($fields as $field => $value)
    {
        $string = str_replace($match1 . $field . $match2, $value, $string);
    }
    return $string;
}

/**
 * getSummary() - Shortens a string to max length
 *
 * Shortens the provided string to a certain length if max length is 
 * exceeded, optionally adding a tail.
 * 
 * @param string $text
 * @param int $length
 * @param string $tail
 * @return string
 */
function getSummary($text, $length, $tail = '...')
{
    if(strlen($text) > $length)
    {
        $text = substr($text, 0, $length);
        $fpos = strrpos($text, ' ');
        if($fpos !== false)
        {
            $text = substr($text, 0, $fpos);
        }
        return $text . $tail;
    }
    else 
    {
        return $text;
    }
}

/**
 * getLanguages() - Gets all Kytoo compatible languages on disk
 *
 * Searches for and returns details of all Kytoo compatible languages
 * located in the provided source path.
 * 
 * @param unknown_type $path
 * @param unknown_type $version
 * @return unknown
 */
function getLanguages($path, $version)
{
    $d = dir($path);
    
    while (false !== ($entry = $d->read()))
    {
        if(preg_match('/lang_([A-Za-z]{2,10}).php/', $entry, $matches))
        {
            if($matches[1])
            {
                $handle = @fopen($path . 'lang_' . $matches[1] . '.php', 'r');
                
                if ($handle) {
                    while (!feof($handle)) {
                        $buffer = fgets($handle, 4096);
                        
                        if(preg_match('/' . $version  . ';(.*);(.*);/', $buffer, $line))
                        {
                            $languages[$line[1]] = $line[2];
                        }
                    }
                    fclose($handle);
                }
            }
        }
    }
    $d->close();
    return $languages;
}

/**
 * systemError() - Display a pretty system error
 * 
 * Display a `pretty` error page for critical system errors that halt script processing.
 *
 * @param string $type
 * @param string $error_output
 */
function systemError($type = 0, $error_output = 0)
{
    global $cfg, $lang;

    // Create a new template object
    $tpl = new kTemplate();
    $tpl->add_path(PATH_TPL . $cfg->getVar('template') . '/');
    
    // Define the error template page
    $tpl->define('error_page', 'system_error.tpl');
    $tpl->define_d('error_output', 'error_page');
    $tpl->parse('error_page');
    
    // Set an error title and description based on error type
    switch($type)
    {
        // Database error
        case 'db':
            $error_heading = $lang['error_db_title'];
            $error_description = $lang['error_system_db'];
            break;
        default:
            $error_heading = $lang['error_system_title'];
            $error_description = $lang['error_system_description'];
            break;
    }
    
    // Assign error details to the template
    $tpl->assign('error_description', $error_description);
    $tpl->assign('error_title', $error_heading);
    
    // Assign language phrase variables
    foreach($lang as $field => $value)
    {
        $tpl->assign('lang_' . $field, $value);
    }
    // Assign common config variables
    foreach($cfg->fields[0] as $field => $value)
    {
        $tpl->assign($field, $value);
    }
    // Assign common config variables
    foreach($cfg->fields['linkbid'] as $field => $value)
    {
        $tpl->assign($field, $value);
    }
    
    // If error output is passed to this function (DB errors, code, etc.) parse the optional output
    if($error_output)
    {
        $tpl->assign_d('error_output', 'error_output', $error_output);
        $tpl->parse_d('error_output');
    }
    
    // Render the template to the browser
    $tpl->render_all();
    
    // Kill the script!
    die();
}

/**
 * urlReplaceVars() - Replace array of vars in a string
 * 
 * Replace dynamic URL format variable fields with an array of 
 * values. Variables fields are in the format %fieldname%.
 *
 * @param string $format
 * @param mixed $vars
 * @return string
 */
function urlReplaceVars($format, $vars)
{
    foreach($vars as $field=>$value)
    {
        $format = str_replace('%' . $field . '%', $value, $format);
    }
    return $format;
}

/**
 * sendEmail() - Send an email message immediately
 * 
 * Accepts and processes an email messages immediately (bypassing the queue).
 *
 * @param string $to
 * @param string $subject
 * @param string $body
 * @param string $from_addr
 * @param string $from_name
 * @param string $attachments
 * @return bool
 */
function sendEmail($to, $subject, $body, $from_addr, $from_name = '', $attachments = false)
{
    return processEmail($to, $subject, $body, $from_addr, $from_name, $attachments);
}

/**
 * queueEmail() - Adds an email to the email queue
 *
 * Accepts and processes an email to the email queue for scheduled delivery
 * 
 * @param mixed $db
 * @param string $to
 * @param string $subject
 * @param string $body
 * @param string $from_addr
 * @param string $from_name
 * @param string $attachments
 * @return bool
 */
function queueEmail($db, $to, $subject, $body, $from_addr, $from_name, $attachments = false)
{
    return processEmail($to, $subject, $body, $from_addr, $from_name, $attachments, &$db);
}

/**
 * processEmail() - Processes emails for immediate delivery or queue
 * 
 * Processes emails for immediate delivery or optional email queue.
 *
 * @param string $to
 * @param string $subject
 * @param string $body
 * @param string $from_addr
 * @param string $from_name
 * @param string $attachments
 * @param mixed $db
 * @return bool
 */
function processEmail($to, $subject, $body, $from_addr, $from_name = '', $attachments = false, $db = 0)
{
    $eol = "\n";
    $mime_boundary = md5(time());
    
    // Common Headers
    $headers .= "From: " . $from_name . " <" . $from_addr . ">" . $eol;
    $headers .= "Reply-To: " . $from_name . " <" . $from_addr . ">" . $eol;
    $headers .= "Return-Path: " . $from_name . " <" . $from_addr . ">" . $eol;
    $headers .= "Message-ID: <" . time() . "-" . $from_addr . ">" . $eol;
    $headers .= "X-Mailer: PHP v" . phpversion() . $eol;
    
    // Boundary for marking the split & Multitype Headers
    /*
    $headers .= 'MIME-Version: 1.0' . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $mime_boundary . "\"" . $eol . $eol;
    
    // Open the first part of the mail
    $msg = "--" . $mime_boundary . $eol;
    
    $htmlalt_mime_boundary = $mime_boundary."_htmlalt"; //we must define a different MIME boundary for this section
    
    // Setup for text or HTML
    $msg .= "Content-Type: multipart/alternative; boundary=\"" . $htmlalt_mime_boundary . "\"" . $eol . $eol;
    
    // Text Version
    $msg .= "--" . $htmlalt_mime_boundary . $eol;
    $msg .= "Content-Type: text/plain; charset=iso-8859-1" . $eol;
    $msg .= "Content-Transfer-Encoding: 8bit" . $eol . $eol;
    $msg .= $body . $eol . $eol;
    
    // HTML Version
    $msg .= "--" . $htmlalt_mime_boundary . $eol;
    $msg .= "Content-Type: text/html; charset=iso-8859-1" . $eol;
    $msg .= "Content-Transfer-Encoding: 8bit" . $eol . $eol;
    */
    $msg .= $body . $eol . $eol;
    
    // Close the HTML/plain text alternate portion
    // $msg .= "--" . $htmlalt_mime_boundary . "--" . $eol . $eol;
    
    if ($attachments !== false)
    {
        for($i=0; $i < count($attachments); $i++)
        {
            if (is_file($attachments[$i]["file"]))
            {  
                // File for Attachment
                $file_name = substr($attachments[$i]["file"], (strrpos($attachments[$i]["file"], "/")+1));
                
                // Read the attachment file
                $handle = fopen($attachments[$i]["file"], 'rb');
                $f_contents = fread($handle, filesize($attachments[$i]["file"]));
                
                // Encode The Data For Transition using base64_encode();
                $f_contents = chunk_split(base64_encode($f_contents));
                $f_type = filetype($attachments[$i]["file"]);
                fclose($handle);
                
                // Add the attachment
                $msg .= "--" . $mime_boundary . $eol;
                $msg .= "Content-Type: " . $attachments[$i]["content_type"]."; name=\"".$file_name."\"".$eol;  // sometimes i have to send MS Word, use 'msword' instead of 'pdf'
                $msg .= "Content-Transfer-Encoding: base64" . $eol;
                $msg .= "Content-Description: " . $file_name . $eol;
                // Set attachment filename with TWO end-of-lines
                $msg .= "Content-Disposition: attachment; filename=\"" . $file_name . "\"" . $eol . $eol;
                $msg .= $f_contents . $eol . $eol;
            }
        }
    }
    
    // Finish with two EOL's for better security.
    // $msg .= "--" . $mime_boundary . "--" . $eol . $eol;
   
    if($db)
    {
        // Create a reference to the DB obj
        $db =& $db;
        
        // Queue the email
        $sql = 'INSERT INTO ' . TBL_MAILQUEUE . ' (mq_to, mq_subject, mq_message, mq_from, mq_headers) VALUES (' .
               '"' . $to . '", "' . addslashes($subject) . '", "' . addslashes($msg) . '", "' . $from_addr . '", "' . $headers . '")';

        return $db->execute($sql);
    }
    else 
    {
        return deliverEmail($to, $subject, $msg, $from_addr, $headers);
    }
    
}

/**
 * deliverEmail() - Sends an email using the built-in mail system
 * 
 * Sends an email message immediately using PHP's built-in mail system.
 *
 * @param string $to
 * @param string $subject
 * @param string $msg
 * @param string $from_addr
 * @param string $headers
 * @return bool
 */
function deliverEmail($to, $subject, $msg, $from_addr, $headers)
{
    // Set INI lines are force the From Address
    ini_set(sendmail_from, $from_addr);

    // Send the email immediately
    $mail_sent = mail($to, $subject, $msg, $headers);
    
    // Restore INI settings
    ini_restore(sendmail_from);
    
    // Return delivery response
    return $mail_sent;
}

/**
 * netGetIp() - Returns the client IP
 *
 * Attempts to retrieve the client's IP address using several methods and returns the result.
 * 
 * @return string
*/        
function netGetIp()
{
    if(isset($_SERVER))
    {           
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip_addr = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif(isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip_addr = $_SERVER['HTTP_CLIENT_IP'];
        }
        else
        {
            $ip_addr = $_SERVER['REMOTE_ADDR'];
        }    
    }
    else
    {
        if(getenv('HTTP_X_FORWARDED_FOR'))
        {
            $ip_addr = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif(getenv('HTTP_CLIENT_IP'))
        {
            $ip_addr = getenv('HTTP_CLIENT_IP');
        }
        else
        {
            $ip_addr = getenv('REMOTE_ADDR');
        }
    }
    
    return $ip_addr;

}

/**
 * DEPRECATED - Replaced with k_get_dir()
 * get_dir() - Gets directory contents
 * 
 * Reads the contents of a directory and returns an array of filenames
 *
 * @param string $dir
 * @param string $ext
 * @return mixed
 */
function get_dir($dir, $ext = '')
{
    $array = array();
    $d = @dir($dir);
    if($d)
    {
         while (false !== ($entry = $d->read()))
         {
             if($entry != '.' && $entry != '..')
             {
                 $array[] = $entry;
             }
         }
         $d->close();
    }
    return $array;
}

/**
 * k_get_dir() - Gets directory contents
 * 
 * Reads the contents of a directory and returns an array of filenames
 *
 * @param string $dir
 * @param string $ext
 * @return mixed
 */
function k_get_dir($dir, $sd = true, $sf = false)
{
    $array = array();
    $d = @dir($dir);
    if($d)
    {
         while (false !== ($entry = $d->read()))
         {
             if($entry != '.' && $entry != '..')
             {
                if((is_dir($dir . $entry)) && ($sd == true))
                {
                    $array[] = $entry;                
                }
                elseif((!is_dir($dir . $entry)) && ($sf == true))
                {
                    $array[] = $entry;
                }
             }
         }
         $d->close();
    }
    if(count($array) > 0)
    {
        return $array;
    }
    else
    {
        return false;    
    }
}


/**
 * get_umodules() - Gets all Kytoo modules present on disk
 * 
 * Searches for all Kytoo modules present on disk and returns array of module details.
 *
 * @return mixed
 */
function get_umodules()
{
    global $modules;
    
    $mod_list = get_dir(PATH_MODULES);
    $umod_list = array();
    foreach($mod_list as $key=>$mod_tag)
    {
        if(!$modules->isModule($mod_tag))
        {
            if(is_file(PATH_MODULES . $mod_tag . '/' . $mod_tag . '.cfg'))
            {
                $mcfg = file_get_contents(PATH_MODULES . $mod_tag . '/' . $mod_tag . '.cfg');
                if(!empty($mcfg))
                {
                    $lines = explode("\n", $mcfg);
                    foreach($lines as $key=>$line)
                    {
                        $posx = strpos($line, ' ');
                        if($posx !== false)
                        {
                            $field = substr($line, 0, $posx);
                            $value = substr($line, $posx+1);
                            if((!empty($field)) && (!empty($value)))
                            {
                                $umod_list[$mod_tag][$field] = $value;
                            }
                        }
                    }
                }
            }
        }
    }
    return $umod_list;
}

/**
 * unique_id() - Generates a unique ID string
 *
 * @param unknown_type $argLength
 * @return unknown
 */
function unique_id($argLength = 50) { 

    // Seed the random number generator using make_seed().  
    // The function make_seed() is a custom fuction and it can be found in the  
    // PHP Manual when looking up the function srand().  
 
    global $db;

    $unique = false;
    while($unique == false)
    {
        // Get a 32 character id string.  
        $id = uniqid(md5(mt_rand(1,500))); 
    
        // Get $length characters from the ID, starting from a random location in  
        // the ID string.  
    
        $return = substr($id, mt_rand(0,strlen($id)), $argLength); 
    
        // If $return is less than $length, add random characters to the end.  
        if ($stringlength = strlen($return) < $argLength)
        { 
    
            // Calculate the difference  
            $difference = $argLength - $stringlength; 
    
            // Generate a string with random alphanumeric characters pulled from $string.  
            // The length will be equal to $difference.  
    
            $string = "a1A2b3B4c5c6d7D8e9EfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ"; 
            $random = '';
            for($i=0; $i < $difference; $i++)
            { 
                $random .= $string{mt_rand(0,strlen($string)-1)}; 
            } 
    
            // Append the string to our ID.  
            $return .= $random; 
        }
        
        $sql = 'SELECT * FROM  ' . TBL_CART_ORDERS . ' WHERE ord_number = "' . $return . '"';
        if(($rs = $db->execute($sql)) && (!$rs->EOF))
        {
            $unique = false;
        }
        else
        {
            $unique = true;
        }
    }
    return substr($return,0,$argLength); 
}

function createUniqueCode($table, $field, $prefix, $min = 10000)
{
    global $db;
    
    $sql = 'SELECT ' . $field . ' FROM ' . $table . ' ORDER BY ' . $field . ' DESC LIMIT 1';
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        $code = str_replace($prefix, '', $rs->fields[$field]);
        if(is_numeric($code))
        {
            return $prefix . (intval($code) + 1);
        }
        else
        {
            return $prefix . $min;
        }
    }
    else
    {
        return $prefix . $min;
    }
}


function runEmailJobs()
{
    global $db, $cfg;

    /**
     * Process Email Queue - run every time
     */
    
    // Fetch all emails in queue
    $sql = 'SELECT * FROM ' . TBL_MAILQUEUE . ' ORDER BY datecreated ASC LIMIT ' . $cfg->getVar('queue_maxemails');
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        while(!$rs->EOF)
        {
            // Deliver the email
            if(deliverEmail($rs->fields['mq_to'], $rs->fields['mq_subject'], $rs->fields['mq_message'], $rs->fields['mq_from'], $rs->fields['mq_headers']))
            {
                // Delete email from the queue
                $sql = 'DELETE FROM ' . TBL_MAILQUEUE . ' WHERE mailqueue_id = ' . $rs->fields['mailqueue_id'];
                $db->execute($sql);
            }
            $rs->MoveNext();
        }
    }
}

function getMatchingFiles($path, $regexp)
{
    $filenames = false;
    if(!file_exists($path)) return false;
    if($d = dir($path))
    {
        while (false !== ($entry = $d->read()))
        {
            if(preg_match($regexp, $entry, $matches))
            {
                if($matches[1])
                {
                    $filenames[] = $matches[1];
                }
            }
        }
        $d->close();
    
        return $filenames;
    }
    else
    {
        return false;
    }    
}


function uniqueId($argLength, $table = '', $field = '', $prefix = '', $string = "a1A2b3B4c5c6d7D8e9EfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ") { 

    global $db;

    $unique = false;
    while($unique == false)
    {
        // Get a 32 character id string.  
        $id = uniqid(md5(mt_rand(1,500))); 
    
        // Get $length characters from the ID, starting from a random location in the ID string.  
        $return = substr($id, mt_rand(0,strlen($id)), $argLength); 
    
        // If $return is less than $length, add random characters to the end.  
        if ($stringlength = strlen($return) < $argLength)
        { 
            // Calculate the difference  
            $difference = $argLength - $stringlength; 
   
            // Generate a string with random alphanumeric characters pulled from $string.  
            // The length will be equal to $difference.  

            $random = '';
            for($i=0; $i < $difference; $i++)
            { 
                $random .= $string{mt_rand(0,strlen($string)-1)}; 
            }
    
            // Append the string to our ID.  
            $return .= $random; 
        }
        if((!empty($table)) && (!empty($field)))
        {
            $sql = 'SELECT ' . $field . ' FROM  ' . $table . ' WHERE ' . $field . ' = "' . $prefix . substr($return,0,$argLength) . '"';
            if(($rs = $db->execute($sql)) && (!$rs->EOF))
            {
                $unique = false;
            }
            else
            {
                $unique = true;
            }
        }
        else
        {
            $unique = true;
        }
    }
    return $prefix . substr($return,0,$argLength); 
}

function uniqueFilename($argLength, $path, $extension = '', $prefix = '', $string = "a1A2b3B4c5c6d7D8e9EfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ") { 

    global $db;

    $unique = false;
    while($unique == false)
    {
        // Get a 32 character id string.  
        $id = uniqid(md5(mt_rand(1,500))); 
    
        // Get $length characters from the ID, starting from a random location in the ID string.  
        $return = substr($id, mt_rand(0,strlen($id)), $argLength); 
    
        // If $return is less than $length, add random characters to the end.  
        if ($stringlength = strlen($return) < $argLength)
        { 
            // Calculate the difference  
            $difference = $argLength - $stringlength; 
   
            // Generate a string with random alphanumeric characters pulled from $string.  
            // The length will be equal to $difference.  

            $random = '';
            for($i=0; $i < $difference; $i++)
            { 
                $random .= $string{mt_rand(0,strlen($string)-1)}; 
            }
    
            // Append the string to our ID.  
            $return .= $random; 
        }
        
        if(!file_exists($path . $prefix . substr($return,0,$argLength) . $extension))
        {
            $unique = true;
        }
    }
    return $prefix . substr($return,0,$argLength) . $extension; 
}


function captchaImageDisplay($width = 120, $height = 40, $length = 5, $font = 'monofont.ttf', $string = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ')
{
    $code = $random = '';
    
    for($i=0;$i<$length;$i++)
    { 
        $code .= $string{mt_rand(0,strlen($string)-1)}; 
    } 
    
    $font_size = $height * 0.75;
    $image = @imagecreate($width, $height) or die('imagecreate() failed');
      
    $bg_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 50, 60, 90);
    $noise_color = imagecolorallocate($image, 100, 110, 140);
    
    // Random dots + lines
    for($i=0;$i<($width*$height)/3;$i++)
    {
    	imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noise_color);
    }
    for($i=0;$i<($width*$height)/150;$i++)
    {
    	imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noise_color);
    }
    // Create text
    $textbox = imagettfbbox($font_size, 0, $font, $code) or die('imagettfbbox() failed');
    $x = ($width - $textbox[4])/2;
    $y = ($height - $textbox[5])/2;
    $angle = mt_rand(-4,4);
    imagettftext($image, $font_size, $angle, $x, $y, $text_color, $font , $code) or die('imagettftext() failed');
    
    header('Content-Type: image/jpeg');
    imagejpeg($image);
    imagedestroy($image);
    $_SESSION['captcha'] = $code;
}

/**
 * widgetLoader() - Generic loader for widgets
 *
*/
function widgetLoad($widget_name, $widget_params)
{
    $widget_return = call_user_func_array(WIDGET_PREFIX . $widget_name, $widget_params);
    if(!empty($widget_return))
    {
        return $widget_return;
    }
    else
    {
        return '';
    }
}

/**
 * explode_paramstr()
 *
 * Performs the same function as explode, except ignoring instances of separator within
 * quote marks. E.g. "Hello, world", 5.3, "Text here"
 *
*/
function k_explode_paramstr($sep, $str)
{
    $str = trim($str);
    $len = strlen($str);
    $index = 0;
    $pos = 0;
    $params = array();
    $param = '';
        
    for($pos=0;$pos<$len;$pos++)
    {
        if(($str[$pos] == '"') || ($str[$pos] == "'"))
        {
            if((!empty($str[$pos-1])) && ($str[$pos-1] == '\\'))
            {
                $param = substr($param, 0, -1);
                $param .= $str[$pos];
            }
            else
            {
                $delim = $str[$pos];            
            }
        }
        elseif(($str[$pos] == $sep) && (!empty($delim)) && ($delim !== $sep))
        {
            $param .= $str[$pos];
        }
        elseif(empty($delim))
        {
            $delim = $sep;
            $param .= $str[$pos];
        }
        else
        {
            $param .= $str[$pos];
        }

        // Reached end of current param
        if(($delim == $str[$pos]) || ($pos == ($len-1)))
        {
            if(($delim == $str[$pos]) && ($delim == $sep))
            {
                    $param = substr($param, 0, -1);
            }
            if(!empty($param))
            {
                $params[] = $param;
                $param = '';
                $delim = '';
            }
        }
    }

    return $params;

}

function array_insert_at($array, $value, $index)
{
    $count = count($array);
    
    if(!isset($array[$index]))
    {
        $array[] = $value;
        return $array;
    }
    else
    {
        foreach($array as $key=>$val)
        {
            if($key == $index)
            {
                $kstart = $key;
            }
        }
    
        $start = array_slice($array, 0, $index); 
        $end = array_slice($array, $index);
        $start[] = $value;
        
        return array_merge($start, $end);
    }
 }

function getPage($page)
{
    global $db;
    
    $sql = 'SELECT * FROM ' . TBL_PAGES . ' WHERE ';   
    $sql .= (is_numeric($page)) ? 'page_id' . ' = ' . $page : 'pg_safename' . ' = "' . $page . '"';
    
    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        return $rs->fields;
    }
    else
    {
        return false;
    }
}

function array_sort_by_field($array, $sort)
{

    $dirtyarray = array();
    foreach($array as $key => $value)
    {
        $dirtyarray[$value[$sort]][] = $value;
    }

    ksort($dirtyarray);
    
    $sortarray = array();
    foreach($dirtyarray as $key=>$value)
    {
        if(is_array($value))
        {
            foreach($value as $dkey=>$dvalue)
            {
                $sortarray[] = $dvalue;
            }
        }
    }

    return $sortarray;
}  

function k_db_makelist(&$db, $tbl, $idcol, $namecol, $where = '')
{
    $sql = 'SELECT ' . $idcol . ', ' . $namecol . ' FROM ' . $tbl;
    if($where) $sql .= ' WHERE ' . $where;
    $sql .= ' ORDER BY ' . $namecol . ' ASC';

    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        while(!$rs->EOF)
        {
            $list[$rs->fields[$idcol]] = $rs->fields[$namecol];
            $rs->MoveNext();
        }
        if(sizeof($list) > 0) return $list;
    }
    return false;
}

function k_db_makelistr(&$db, $tbl, $idcol, $namecol, $parentcol, $where = '', $skipid = false, $id = 0, $sep = '', $showtop = false, $sort = false, $order = 'ASC')
{
    static $cat_list;
    static $crumbs = array();
    static $depth;
    
    if($showtop)
    {
        $cat_list[0] = $showtop;
    }
    $criteria = array();
    $sql = 'SELECT ' . $idcol . ', ' . $namecol . ' FROM ' . $tbl . ' WHERE ' . $parentcol . ' = ' . $id;
    if($where) $criteria[] = $where;
    if($skipid) $criteria[] = $idcol . ' != ' . $skipid;
    if(sizeof($criteria) > 0) $sql .= ' AND ' . implode(' AND ', $criteria);
    if($sort)
    {
        $sql .= ' ORDER BY ' . $sort . ' ' . $order;
    }
    else
    {
        $sql .= ' ORDER BY ' . $namecol . ' ' . $order;
    }

    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        // Fetch current field details
        $sql = 'SELECT ' . $namecol . ' FROM ' . $tbl . ' WHERE ' . $idcol . ' = ' . $id;

        if(($rs2 = $db->execute($sql)) && (!$rs2->EOF))
        {
            array_push($crumbs, $rs2->fields[$namecol]);
            $depth = $depth + 1;
        }

        $crumbstr = implode($sep, $crumbs);
        while(!$rs->EOF)
        {
            $cat_list[$rs->fields[$idcol]] = ($depth > 0) ? $crumbstr . $sep . $rs->fields[$namecol] : $rs->fields[$namecol];
            
            k_db_makelistr($db, $tbl, $idcol, $namecol, $parentcol, $where, $skipid, $rs->fields[$idcol], $sep);
                        
            $rs->MoveNext();
        }
        array_pop($crumbs);
        $depth = $depth - 1;
    }

    return $cat_list;
}

function k_db_countr($category_id, $recursive = 1, $private = 0)
{
    global $db;
    static $depth;
    static $count;
    
    $sql = 'SELECT category_id FROM ' . TBL_CATEGORIES . ' WHERE parent_id = ' . $category_id . ' AND cat_active = 1';

    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        $count = $count + $rs->NumRows();
        if($recursive)
        {
            while(!$rs->EOF)
            {
                getCategoryCount($rs->fields['category_id'], $recursive, 1);
                $rs->MoveNext();
            }
        }
    }

    $return = $count;
    if($private == 0) $count = 0;
    return $return;
}

function getCategoryLinkCount($category_id, $recursive = 1, $private = 0)
{
    global $db;
    static $depth;
    static $count;
    if($private == 0) $count = 0;
    
    $sql = 'SELECT COUNT(' . TBL_LINKS . '.link_id) AS link_count FROM ' . TBL_LINKS . ' WHERE link_status = 1 AND category_id = ' . $category_id;

    if(($rsc = $db->execute($sql)) && (!$rsc->EOF))
    {
        $count = $count + intval($rsc->fields['link_count']);
    }

    $sql = 'SELECT category_id FROM ' . TBL_CATEGORIES . ' WHERE parent_id = ' . $category_id . ' AND cat_active = 1';

    if(($rs = $db->execute($sql)) && (!$rs->EOF))
    {
        if($recursive)
        {
            while(!$rs->EOF)
            {
                getCategoryLinkCount($rs->fields['category_id'], $recursive, 1);
                $rs->MoveNext();
            }
        }
    }
    $return = $count;

    return $return;
}

function k_filter_shortdate($date)
{
    $time = strtotime($date);
    return date('Y-m-d', $time);
}

function k_execute_sqlfile(&$db, $sql)
{
    $query_error = array();
    $error_flag = false;
    $query_count = 0;

    $queries = preg_split('/;\n/', $sql);   
    
    foreach($queries as $key=>$value)
    {
        $value = trim($value);
        if(!empty($value))
        {
            if(!$db->execute($value)) {
                $query_error[] = 'Error in query: ' . $value . '(' . mysql_error() . ')';
                $error_flag = true;
            }
            else 
            {
                $query_count++;
            }
        }
    }
}

function k_clone_tpl($s = false)
{
    global $tpl;
    $stpl = new kTemplate();
    $stpl->paths = $tpl->paths;
    $stpl->auto_parse = 0;
    return $stpl;
}

function k_net_port_status($server, $port, $timeout = 10)
{
    return @fsockopen("$server", $port, $errno, $errstr, $timeout);
}

/**
 * json_decode() function requires PHP 5.2+
 * If it doesn't exist, create an alternative
*/
if (!function_exists('json_decode'))
{
    function json_decode($json)
    {
        $comment = false;
        $out = '$x=';
     
        for ($i=0; $i<strlen($json); $i++)
        {
            if (!$comment)
            {
                if (($json[$i] == '{') || ($json[$i] == '['))       $out .= ' array(';
                else if (($json[$i] == '}') || ($json[$i] == ']'))   $out .= ')';
                else if ($json[$i] == ':')    $out .= '=>';
                else                         $out .= $json[$i];         
            }
            else $out .= $json[$i];
            if ($json[$i] == '"' && $json[($i-1)]!="\\")    $comment = !$comment;
        }
        eval($out . ';');
        return $x;
    }
}

?>