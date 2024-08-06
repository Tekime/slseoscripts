<?php
/**
 * k_functions_http.php - HTTP Functions
 *
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2012 Scriptalicious, All Rights Reserved
 * @author      Gabriel Harper - http://gabrielharper.com/
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
 * 1.2 - Added k_strip_www()
 * 1.1 - Added k_weblog_ping()
 * 1.0 - First release
 *
*/

require_once(PATH_LIB . 'class.kIXR.php');

function k_http_request($url, $settings = false, $data = false)
{
    $s = array(
        CURLOPT_COOKIEJAR => '',
        CURLOPT_COOKIEFILE => '',
        CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
        CURLOPT_TIMEOUT => 40,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_PROXY => false,
        CURLOPT_URL => $url,
        CURLOPT_HEADER => false,
        CURLOPT_REFERER => '',
        CURLOPT_FOLLOWLOCATION => true
    );
    
    if(is_array($settings))
    {
        foreach($settings as $opt => $val)
        {
            $s[$opt] = $val;
        }
    }

    // Make sure cookie datafile exists and is writeable
    if(!empty($s[CURLOPT_COOKIEFILE])) {

        $fp = fopen(PATH_BASE . $s[CURLOPT_COOKIEFILE], "a");
        fclose($fp);
    }

    // Init CURL session
    $ch = curl_init();
    
    // Set CURL options
    foreach($s as $opt => $val)
    {
        curl_setopt($ch, $opt, $val);
    }

    if ($s[CURLOPT_PROXY]) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
        curl_setopt($ch, CURLOPT_PROXY, $s[CURLOPT_PROXY]);
    }
    
    ob_start();      // prevent any output
    $response = curl_exec($ch);
    ob_end_clean();  // stop preventing output
    curl_close($ch);
    unset($ch);
    
    return $response;
}

function k_http_get($url, $settings = false, $data = false)
{
    return k_http_request($url, $settings, $data);
}

function k_http_post($url, $settings = false, $data = false)
{
    $settings[CURLOPT_POST] = true;
    $settings[CURLOPT_POSTFIELDS] = $data;
    $settings[CURLOPT_FRESH_CONNECT] = true;
    return k_http_request($url, $settings, $data);
}
function k_http_head($url, $settings = false, $data = false)
{
    $settings[CURLOPT_HEADER] = true;
    $settings[CURLOPT_NOBODY] = true;
    $settings[CURLOPT_FRESH_CONNECT] = true;
    return k_http_request($url, $settings, $data);
}
function k_http_meta($url)
{
    return get_meta_tags($url);
}
function k_http_sitespeed($url)
{
    $s = array(
        CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
        CURLOPT_TIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url,
        CURLOPT_HEADER => false,
        CURLOPT_REFERER => '',
        CURLOPT_FOLLOWLOCATION => true
    );

    // Init CURL session
    $ch = curl_init();

    // Set CURL options
    foreach($s as $opt => $val)
    {
        curl_setopt($ch, $opt, $val);
    }

    ob_start();      // prevent any output
    $response = curl_exec($ch);

    $times['total'] = ($curlinfo = curl_getinfo($ch, CURLINFO_TOTAL_TIME)) ? round($curlinfo,2) : 'Unknown';
    $times['namelookup'] = ($curlinfo = curl_getinfo($ch, CURLINFO_NAMELOOKUP_TIME)) ? round($curlinfo,2) : 'Unknown';
    $times['connect'] = ($curlinfo = curl_getinfo($ch, CURLINFO_CONNECT_TIME)) ? round($curlinfo,2) : 'Unknown';
    $times['pretransfer'] = ($curlinfo = curl_getinfo($ch, CURLINFO_PRETRANSFER_TIME)) ? round($curlinfo,2) : 'Unknown';
    $times['starttransfer'] = ($curlinfo = curl_getinfo($ch, CURLINFO_STARTTRANSFER_TIME)) ? round($curlinfo,2) : 'Unknown';
    $times['redirect'] = ($curlinfo = curl_getinfo($ch, CURLINFO_REDIRECT_TIME)) ? round($curlinfo,2) : 'Unknown';
    ob_end_clean();  // stop preventing output
    curl_close($ch);
    unset($ch);

    return $times;
}

// A preliminary cleanup filter 
function k_filter_url($url, $http = false, $full = false)
{
    $url = trim($url);

    if($http == false)
    {
        // Strip http://
        if ((eregi( "^http://", $url)))
        {
            $url = substr($url, 7);
        }
        elseif((eregi( "^https://", $url)))
        {
            $url = substr($url, 8);
        }
    }
    else
    {
        if(strpos($url, 'http://') !== 0)
        {
            $url = 'http://' . $url;
        }
    }
    
    if($full == false)
    {   
        $fs = strpos($url, '/');
        if($fs !== false)
        {
            $url = substr($url, 0, $fs);
        }
    }
    
    // If it's a plain domain or IP strip / on the end 
//    if (eregi( "^https?://.+/", $url)) $url = substr($url, 0, strlen($url)); 

    return $url;
}

function k_weblog_ping($server, $title, $url, $feed_url = '', $path = '')
{
	$client = new IXR_Client($server, ((!strlen(trim($path)) || ('/' == $path)) ? false : $path));
	$client->timeout = 3;
	$client->useragent .= ' -- Kytoo/2.0';

	$client->debug = false;

	if((empty($feed_url)) || (!$client->query('weblogUpdates.extendedPing', $title, $url, $feed_url)))
    {
		$client->query('weblogUpdates.ping', $title, $url);
    }
    $response = $client->getResponse();

    if((!empty($response['flerror'])) && ($response['flerror'] == 1))
    {
        if(empty($response['message'])) $response['message'] = 'no response';
    }
    else
    {
        if(empty($response['message'])) $response['message'] = 'no response';
    }
    
    return $response['message'];
}

function k_get_whois($url)
{
    $url = k_filter_url($url);
    $urlparts = explode('.', $url);
    while(sizeof($urlparts) > 2)
    {
        array_shift($urlparts);
    }
    $dn = implode('.', $urlparts);
    $whoisserver = "whois.crsnic.net";
    $query   = $dn . "@" . $whoisserver;
    $result  = "";
    $ns = @fsockopen($whoisserver,43);
    @fputs($ns, "$dn\r\n");
    while(!feof($ns)) $result .= @fgets($ns,128); fclose($ns);
    return $result;
}

function k_parse_domain($url, $max = 2)
{
    $urlparts = explode('.', $url);
    if(($urlparts > $max))
    {
        while(count($urlparts) > $max)
        {
            array_shift($urlparts);
        }
    }
    $url = implode('.', $urlparts);
    return $url;
}

function k_strip_www($url)
{
    if(strpos($url, 'www.') === 0)
    {
        $url = substr($url, 4);
    }
    return $url;
}
?>