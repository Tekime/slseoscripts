<?php
/**
 * config.php - Kytoo System Configuration File
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
 * 1.1 - Changed error_reporting for production site
 * 1.0 - Original version
 *
*/

// Set error reporting levels (E_ERROR for production site, E_ALL for development)
error_reporting(E_ERROR);

/* 
 * MANUAL PATH CONFIGURATION
 * If auto-detection fails, or you are getting an error such as "Failed opening required...",
 * then enter the full server path and base directory here. Otherwise,
 * do not edit these settings unless you know what you're doing.
 */
 
$k_svr_path_base = '';      // Base path - example: '/home/mysite/public_html/'
$k_svr_dir_base = '';       // Base directory - example: '/' or '/mysite/'
$k_svr_path_lib = '';        // Kytoo library - Full path to system libraries, for system use

/*
 * END MANUAL PATH CONFIGURATION -- DO NOT EDIT PAST THIS LINE
 */


$chk_pathbase = false;
$chk_dirbase = false;

// Auto-detect the directory name relative to the document root
if(empty($k_svr_dir_base))
{
    $chk_dirbase = (!empty($_SERVER['PHP_SELF'])) ? dirname($_SERVER['PHP_SELF']) : false;
    if(($chk_dirbase !== false) && (substr($chk_dirbase, -1) !== '/')) $chk_dirbase .= '/';
    
    if($chk_dirbase !== false)
    {
        $chk_dirbase = preg_replace('/install\/$/', '', $chk_dirbase);
        $chk_dirbase = preg_replace('/admin\/$/', '', $chk_dirbase);
    }
    else 
    {
        die('Unable to determine application directory. Please set path settings manually in config.php.');
    }
}
else 
{
    $chk_dirbase = $k_svr_dir_base;
}

// Auto-detect the absolute path to the application directory
if(empty($k_svr_path_base))
{
    // Try to get document root from $_SERVER variable
    $chk_pathbase = (empty($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT'] : false;
    if(($chk_pathbase !== false) && (substr($chk_pathbase, -1) == '/')) $chk_pathbase = substr($chk_pathbase, 0, -1);
    if($chk_pathbase !== false) $chk_pathbase .= $chk_dirbase;
    if(($chk_pathbase !== false) && (!file_exists($chk_pathbase . 'config.php'))) $chk_pathbase = false;
    
    // Try to get document root from __FILE__ constant if necessary
    if($chk_pathbase === false)
    {
        $chk_pathbase = str_replace(basename(__FILE__), '', realpath(__FILE__));
        if(($chk_pathbase !== false) && (substr($chk_pathbase, -1) !== '/')) $chk_pathbase .= '/';
        if(($chk_pathbase !== false) && (!file_exists($chk_pathbase . 'config.php'))) $chk_pathbase = false;
    }
    
    if($chk_pathbase !== false)
    {
        $chk_pathbase = preg_replace('/install\/$/', '', $chk_pathbase);
        $chk_pathbase = preg_replace('/admin\/$/', '', $chk_pathbase);
    }
    else 
    {
        die('Unable to determine application path. Please set path settings manually in config.php.');
    }
}
else 
{
    $chk_pathbase = $k_svr_path_base;
}

define('DIR_BASE', $chk_dirbase);
define('PATH_BASE', $chk_pathbase);
define('PATH_INCLUDE', $chk_pathbase . 'inc/');

include(PATH_INCLUDE . 'sql_config.php');
?>