<?php
/**
 * class.kWebSearch.php - Search engine utility class
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.9
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
 * 1.9 (2009-05-28) - Added default proxy URL format
 * 1.8 (2009-05-23) - Fixed Alexa ranking regex
 * 1.7 (2009-05-20) - Added proxy support, fixed minor regex bugs
 * 1.6 - Fixed bug with MSN links regexp
 * 1.5 - Fixed bug with Google backlinks, Google pages, and MSN links regexps 
 * 1.4 - Fix Yahoo backlinks, Yahoo pages & Technorati backlinks 
 * 1.2 - Version 1.2: Updates for Google stats regexp's 
 *
*/
 
class kWebSearch
{

    /**
     * @access  private
     * @var     array $engines  Search engines supported by kWebSearch
    */
    var $engines = array(
        'google' => array(
            'title' => 'Google',
            'url' => 'http://www.google.com/',
            'search_url' => 'search?q=%s&num=%s&start=%s',
            'links_url' => 'search?q=link:%s',
            'links_reg' => '/Results .* (of|of about) <b>(.*)<\/b> linking/',
            'pages_url' => 'search?q=site:%s',
            'pages_reg' => '/Results .* (of|of about) <b>(.*)<\/b> from/',
            'search_hosts' => array('www.google.com', '64.233.161.99'),
            'stats_hosts' => array('www.google.com', '64.233.161.99')
        ),
        'yahoo' => array(
            'title' => 'Yahoo',
            'url' => 'http://search.yahoo.com/',
            'search_url' => 'search?p=%s&n=%s&b=%s',
            'links_url' => 'advsearch?p=%s&bwm=i&bwmo=d&bwmf=u',
            'links_reg' => '/Inlinks \(([0-9,].*)\)/',
            'pages_url' => 'advsearch?p=%s&bwm=p&bwmf=s&bwmo=d',
            'pages_reg' => '/Pages \(([0-9,]*)\)/',
            'search_hosts' => array('search.yahoo.com'),
            'stats_hosts' => array('siteexplorer.search.yahoo.com')
        ),
        'msn' => array(
            'title' => 'MSN',
            'url' => '?q=%s',
            'search_url' => '?q=%s',
            'pages_url' => 'results.aspx?q=site:%s&form=QBRE',
            'pages_reg' => '/[-0-9,]* of ([0-9,]*) results/',
            'search_hosts' => array('search.live.com'),
            'stats_hosts' => array('search.live.com')
        ),
        'technorati' => array(
            'title' => 'Technorati',
            'url' => 'http://www.technorati.com/',
            'search_url' => '',
            'links_url' => 'blogs/%s',
            'links_reg' => '/([0-9,]*) blog reaction/',
            'search_hosts' => array('www.technorati.com'),
            'stats_hosts' => array('www.technorati.com')
        ),
        'alexa' => array(
            'title' => 'Alexa',
            'url' => 'http://www.alexa.com/',
            'search_url' => '',
            'links_url' => 'siteinfo/%s',
            'links_reg' => '/div class=\"data (up|down|steady)\">([0-9,]*)/',
            'search_hosts' => array('www.alexa.com'),
            'stats_hosts' => array('www.alexa.com')
        )
    );
    
    var $proxy_table = false;
    var $proxy_urlformat = 'index.php?hl=40&q=%s';

    function kWebSearch()
    {
        return true;   
    }
    
    function getBacklinks($eng, $url)
    {
        $eng_url = 'http://' . $this->engines[$eng]['stats_hosts'][0] . '/' . $this->engines[$eng]['links_url'];
        $links_url = sprintf($eng_url, urlencode($url));
        $page = $this->getUrl($links_url);

        if(($eng !== 'google') && ($eng !== 'alexa'))
        {
            $page = strip_tags($page);
        }
     
        preg_match_all($this->engines[$eng]['links_reg'], $page, $matches);

        $match_idx = count($matches) - 1;
        if(!empty($matches[$match_idx][0]))
        {
            return trim($matches[$match_idx][0]);
        }
        else 
        {
            return false;
        }
    }
    
    // Get indexed pages count
    function getPages($eng, $url)
    {
        $eng_url = 'http://' . $this->engines[$eng]['stats_hosts'][0] . '/' . $this->engines[$eng]['pages_url'];
        $pages_url = sprintf($eng_url, $url);

        $page = $this->getUrl($pages_url);
        if($eng !== 'google')
        {
            $page = strip_tags($page);
        }

        preg_match_all($this->engines[$eng]['pages_reg'], $page, $matches);

        $match_idx = count($matches) - 1;
        if(!empty($matches[$match_idx][0]))
        {
            return trim($matches[$match_idx][0]);
        }
        else 
        {
            return false;
        }
    }
    
    function getGovLinks($url)
    {
        
        $url = str_replace('http://', '', $url);
        $link_url = 'http://search.yahoo.com/search?p=linkdomain:' . $url . '+site:.gov+-site:' . $url;

        $page = $this->getUrl($link_url);
        $page = strip_tags($page);

        preg_match_all('/.* of (.*) from/', $page, $matches);

        if(!empty($matches[1][0]))
        {
            $matches[1][0] = ereg_replace('[^0-9]', '', $matches[1][0]);
            return trim($matches[1][0]);
        }
        else 
        {
            return false;
        }
    }
    function getEduLinks($url)
    {
        
        $url = str_replace('http://', '', $url);
        $link_url = 'http://search.yahoo.com/search?p=linkdomain:' . $url . '+site:.edu+-site:' . $url;

        $page = $this->getUrl($link_url);
        $page = strip_tags($page);

        preg_match_all('/.* of (.*) from/', $page, $matches);

        if(!empty($matches[1][0]))
        {
            $matches[1][0] = ereg_replace('[^0-9]', '', $matches[1][0]);
            return trim($matches[1][0]);
        }
        else 
        {
            return false;
        }
    }
    
    function getDmozLinks($url)
    {
        $url = str_replace('http://', '', $url);
        $link_url = 'http://search.yahoo.com/search?p=linkdomain:' . $url . '+site:dmoz.org';
//        $link_url = $this->engines['yahoo']['search_url']
        $page = $this->getUrl($link_url);
        $page = strip_tags($page);
        preg_match_all('/.* of (.*) from/', $page, $matches);

        if(!empty($matches[1][0]))
        {
            $matches[1][0] = ereg_replace('[^0-9]', '', $matches[1][0]);
            return trim($matches[1][0]);
        }
        else 
        {
            return false;
        }
    }
    
    /**
     * Use CURL to fetch URL contents and return them as a string
     *
     * @param string $url
     * @param string $referer
     * @return string
     */
    function getUrl($url, $referer = 'als')
    {
        if((!empty($this->proxy_table)) && (!empty($this->proxy_urlformat)))
        {
            global $db;
            
            $sql = 'SELECT proxy_id, prx_url, prx_queries FROM ' . $this->proxy_table . ' WHERE prx_status = 1 ORDER BY prx_queries ASC LIMIT 1';
            if(($rs = $db->execute($sql)) && (!$rs->EOF))
            {
                $sql = 'UPDATE ' . $this->proxy_table . ' SET prx_queries = ' . intval($rs->fields['prx_queries'] + 1) . ', dateupdated = "' . unix_to_dbtime(time()) . '" WHERE proxy_id = ' . $rs->fields['proxy_id'];
                $db->execute($sql);
                $alt_curl = $rs->fields['prx_url'];
            }
        }
        if(!empty($alt_curl))
        {
            $referer = $alt_curl;
            $url = sprintf($alt_curl . $this->proxy_urlformat, urlencode($url));
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $contents = curl_exec($ch);
        curl_close($ch);

        $crawl_errors = array('google' => '<title>403 Forbidden</title>', 'yahoo' => 'error 999');
        
        foreach($crawl_errors as $key => $value)
        {
            if(strpos($contents, $value) !== false)
            {
                $this->error = 'Temporarily banned from ' . $key . ' for automated requests from your IP address.<br /><br />' . $url;
                return false;
            }
        }        
        return $contents;
    }
    
    

}