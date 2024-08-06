<?php
/**
 * class.kWebStats.php - Kytoo Web Stats
 *
 * Copyright (c) 2011 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2010 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.5
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
 * 1.7 - Fixed: get_twitter_links (now using REST API)
 * 1.6 - Fixed: Google Indexed Pages, Bing Indexed Pages
 * 1.5 - Fixed: Google backlinks, PageRank, FakeRank, Compete, Delicious, Digg
 * 1.4 - Fixed: Yahoo mangled backlinks (greedy regexp)
 * 1.3 - Fixed: Alexa, Google & Bing pages, Google backlinks
 * 1.2 - Fixed Yahoo regexp f/ digg, delicious, dmoz, gov, edu, other  
 * 1.1 - Fixed Alexa regexp for Alexa site update
 * 1.0 (2009-05-29) - First release based on kWebSearch & kWebTools  
 *
*/

class kWebStats extends kBase
{
    /**
     * @access  private
     * @var     array $ds   Array of data sources for Web statistics
    */
    var $ds = array(
        'google' => array(
            'title' => 'Google',
            'url' => 'http://www.google.com/',
            'search_url' => 'search?q=%s&num=%s&start=%s',
            'links_url' => 'search?q=link:%s',
//            'links_reg' => '/<b>([0-9,]*?)<\/b> linking to/',
            'links_reg' => '/([0-9,]*?) results<\/div>/',
            'pages_url' => 'search?q=site:%s',
//            'pages_reg' => '/of about <b>([0-9,]*?)<\/b> from/',
            'pages_reg' => '/About ([0-9,]*?) results/',
            'search_hosts' => array('www.google.com', '64.233.161.99'),
            'stats_hosts' => array('www.google.com', '64.233.161.99'),
            'backlinks_url' => 'http://www.google.com/search?q=link:%s',
            'backlinks_reg' => '/<h3 class="r"><a href="(.*?)"/'
        ),
        'yahoo' => array(
            // Yahoo, AltaVista, AllTheWeb, HotBot
            // Add support for 'linkdomain:' command
            'title' => 'Yahoo',
            'url' => 'http://search.yahoo.com/',
            'search_url' => 'search?p=%s&n=%s&b=%s',
            'links_url' => 'advsearch?p=%s&bwm=i&bwmo=d&bwmf=u',
            'links_reg' => '/Inlinks \(([0-9,]*)\)/',
            'pages_url' => 'search?p=site:%s',
            'pages_reg' => '/<span id="resultCount">([0-9,]*)?<\/span>/',
            'search_hosts' => array('search.yahoo.com'),
            'stats_hosts' => array('search.yahoo.com'),
            'results_reg' => '/>([0-9,].*)?<\/strong> result/',
            'backlinks_url' => 'http://siteexplorer.search.yahoo.com/advsearch?p=%s&bwm=i&bwmo=d&bwmf=u&b=%s',
            'backlinks_reg' => '/<span class="result"><a href="(.*?)"/',
            'backlinks_export_url' => 'http://siteexplorer.search.yahoo.com/export?p=%s&bwm=i&bwmf=u&bwmo=d&b=%s'
        ),
        'msn' => array(
            'title' => 'MSN',
            'url' => 'search?q=%s',
            'search_url' => 'search?q=%s',
            'pages_url' => 'search?q=site:%s',
            'pages_reg' => '/[-0-9,]* of ([0-9,]*) results/',
            'search_hosts' => array('www.bing.com'),
            'stats_hosts' => array('www.bing.com')
        ),
        'bing' => array(
            'title' => 'Bing',
            'url' => 'search?q=%s',
            'search_url' => 'search?q=%s',
            'pages_url' => 'search?q=site:%s',
//            'pages_reg' => '/of ([0-9,]*?) result/',
            'pages_reg' => '/([0-9,]*?) results/',
            'search_hosts' => array('www.bing.com'),
            'stats_hosts' => array('www.bing.com')
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
            'links_reg' => '/<th>3 month<\/th>\\n<td class=\"avg \">([0-9,]*?)<\/td>/',
            'search_hosts' => array('www.alexa.com'),
            'stats_hosts' => array('www.alexa.com')
        ),
        'aol' => array(
        // AOL Search, Netscape Search
            'title' => 'AOL',
            'url' => 'http://search.aol.com/',
            'search_url' => 'aol/search?query=%s',
            'search_hosts' => array('search.aol.com'),
            'stats_hosts' => array('search.aol.com')
        ),
        'lycos' => array(
            'title' => 'Lycos Search',
            'url' => 'http://search.lycos.com/',
            'search_url' => '?query=',
            'search_hosts' => array('search.lycos.com'),
            'stats_hosts' => array('search.lycos.com')
        ),
        'gigablast' => array(
            'title' => 'Gigablast',
            'url' => 'http://www.gigablast.com/',
            'search_url' => 'search?q=%s',
            'search_hosts' => array('www.gigablast.com'),
            'stats_hosts' => array('www.gigablast.com')
        ),
        'lygo' => array(
        // Visual search - a Lycos thing
            'title' => 'lyGO Beta',
            'url' => 'http://www.lygo.com/',
            'search_url' => '?query=%s',
            'search_hosts' => array('www.lygo.com'),
            'stats_hosts' => array('www.lygo.com')
        ),
        'ask' => array(
        // Teoma engine
            'title' => 'Ask.com',
            'url' => 'http://www.ask.com/',
            'search_url' => 'web?q=%s',
            'search_hosts' => array('www.ask.com'),
            'stats_hosts' => array('www.ask.com')
        ),
        'dogpile' => array(
        // Meta search - Google, Yahoo, Live & Ask
            'title' => 'Dogpile',
            'url' => 'http://www.dogpile.com/',
            'search_url' => 'dogpile/ws/results/Web/%s/1/417/TopNavigation/Relevance/iq=true/zoom=off/_iceUrlFlag=7?_IceUrl=true',
            'search_hosts' => array('www.dogpile.com'),
            'stats_hosts' => array('www.dogpile.com')
        )
    );

    var $inputs = array(
        'stat_phrase' => array('type' => 'text', 'title' => 'Phrase', 'length' => 50, 'maxlength' => 255)
    );
    
    /**
     * @access  private
     * @var     array $stats   Array of Web statistics and data details
    */
    var $stats = array(
        'alexa' => array(
            'name' => 'Alexa',
            'shortname' => 'Alexa',
            'title' => 'Alexa Traffic Rank',
            'fmsg' => 'Alexa Traffic Rank for %s is %s',
            'ftitle' => 'Alexa %s',
            'cache' => 604800,
			'type' => 'rank'
        ),
        'backlinks_dmoz' => array(
            'name' => 'DMOZ Backlinks',
            'shortname' => 'DMOZ',
            'title' => 'DMOZ Backlinks',
            'fmsg' => 'Alexa Traffic Rank for %s is %s',
            'source' => 'k_webstats_get_backlinks_dmoz',
            'cache' => 2419200,
			'type' => 'backlinks'
        ),
        'backlinks_edu' => array(
            'name' => '.edu Backlinks',
            'shortname' => '.edu',
            'title' => '.edu Backlinks',
            'source' => 'k_webstats_get_backlinks_edu',
            'cache' => 2419200,
			'type' => 'backlinks'
        ),
        'backlinks_gov' => array(
            'name' => '.gov Backlinks',
            'shortname' => '.gov',
            'title' => '.gov Backlinks',
            'source' => 'k_webstats_get_backlinks_gov',
            'cache' => 2419200,
			'type' => 'backlinks'
        ),
        'backlinks_google' => array(
            'name' => 'Google Backlinks',
            'shortname' => 'GBL',
            'title' => 'Google Backlinks',
            'ftitle' => '%s Google Backlinks',
            'source' => 'k_webstats_get_backlinks_google',
            'export' => true,
			'type' => 'backlinks'
        ),
        'backlinks_yahoo' => array(
            'name' => 'Yahoo Backlinks',
            'shortname' => 'YBL',
            'title' => 'Yahoo Backlinks',
            'ftitle' => '%s Yahoo Backlinks',
            'source' => 'k_webstats_get_backlinks_yahoo',
            'export' => true,
			'type' => 'backlinks'
        ),
        'backlinks_digg' => array(
            'name' => 'Digg Links',
            'shortname' => 'Digg',
            'title' => 'Digg Links',
            'export' => true,
			'type' => 'backlinks'
        ),
        'backlinks_twitter' => array(
            'name' => 'Twitter Links',
            'shortname' => 'Twitter',
            'title' => 'Twitter Links',
            'export' => true,
			'type' => 'backlinks'
        ),
        'backlinks_delicious' => array(
            'name' => 'Delicious Links',
            'shortname' => 'Delicious',
            'title' => 'Delicious Links',
            'export' => true,
			'type' => 'backlinks'
        ),
        'backlinks_yahoodir' => array(
            'name' => 'Yahoo! Directory Links',
            'shortname' => 'Yahoo! Directory',
            'title' => 'Yahoo! Directory Links',
            'export' => true,
			'type' => 'backlinks'
        ),
        'position_google' => array(
            'name' => 'Google',
            'shortname' => 'GP',
            'title' => 'Google Position',
            'ftitle' => 'Google Position %s',
            'fmsg' => '%s is #<b>%s</b> in Google for &quot;<b>%s</b>&quot;',
            'source' => 'k_webstats_get_backlinks_google',
            'input' => array('id' => 'stat_phrase', 'type' => 'text', 'title' => 'Phrase', 'length' => 50, 'maxlength' => 255)
        ),
        'position_yahoo' => array(
            'name' => 'Yahoo',
            'shortname' => 'YP',
            'title' => 'Yahoo Position',
            'ftitle' => 'Yahoo Position %s',
            'fmsg' => '%s is #<b>%s</b> in Yahoo for &quot;<b>%s</b>&quot;',
            'source' => 'k_webstats_get_backlinks_yahoo',
            'input' => array('id' => 'stat_phrase', 'type' => 'text', 'title' => 'Phrase', 'length' => 50, 'maxlength' => 255)
        ),
        'pagerank' => array(
            'name' => 'PageRank',
            'shortname' => 'PR',
            'title' => 'Google PageRank',
            'ftitle' => 'PageRank %s',
            'fmsg' => 'Google PageRank for %s is PageRank %s',
            'source' => 'k_webstats_get_pagerank',
            'export' => true,
			'type' => 'rank'
        ),
        'fakerank' => array(
            'name' => 'Fake Rank',
            'shortname' => 'Fake',
            'title' => 'Fake PageRank',
            'ftitle' => 'Google PageRank is %s',
            'fmsg' => 'Google PageRank for %s is %s',
            'source' => 'k_webstats_get_fakerank',
            'export' => false
        ),
        'pageheat' => array(
            'name' => 'PageHeat',
            'shortname' => 'Heat',
            'title' => 'PageHeat Rating',
            'ftitle' => 'PageHeat %s',
            'fmsg' => 'PageHeat rating for %s is %s',
			'type' => 'rank'
        ),
        'pages_google' => array(
            'name' => 'Google Indexed Pages',
            'shortname' => 'GIP',
            'title' => 'Google Indexed Pages',
            'ftitle' => '%s Google Indexed Pages',
            'source' => 'k_webstats_get_pages_google',
            'export' => true,
			'type' => 'pages'
        ),
        'pages_yahoo' => array(
            'name' => 'Yahoo Indexed Pages',
            'shortname' => 'YIP',
            'title' => 'Yahoo Indexed Pages',
            'ftitle' => '%s Yahoo Indexed Pages',
            'source' => 'k_webstats_get_pages_yahoo',
            'export' => true,
			'type' => 'pages'
        ),
        'pages_msn' => array(
            'name' => 'MSN Indexed Pages',
            'shortname' => 'MSN',
            'title' => 'MSN Indexed Pages',
            'ftitle' => '%s MSN Indexed Pages',
            'source' => 'k_webstats_get_pages_msn',
            'export' => true,
			'type' => 'pagesold'
        ),
        'pages_bing' => array(
            'name' => 'Bing Indexed Pages',
            'shortname' => 'Bing',
            'title' => 'Bing Indexed Pages',
            'ftitle' => '%s Bing Indexed Pages',
            'source' => 'k_webstats_get_pages_bing',
            'export' => true,
			'type' => 'pages'
        ),
        'age' => array(
            'name' => 'Age',
            'shortname' => 'Age',
            'title' => 'Site Age',
            'ftitle' => '%s Old',
            'source' => 'k_webstats_get_age',
            'export' => true,
			'type' => 'urlinfo'
        ),
        'sitespeed' => array(
            'name' => 'Load Time',
            'shortname' => 'Speed',
            'title' => 'Load Time',
            'ftitle' => 'Load time is %s seconds',
            'fmsg' => 'Load time for <b>%s</b> is <b>%s</b> seconds.',
            'source' => '',
			'type' => 'urlinfo'
        ),
        'getsource' => array(
            'name' => 'Source',
            'shortname' => 'HTML Source',
            'title' => 'HTML Source',
            'ftitle' => 'HTML Source Code',
            'source' => ''
        ),
        'compete_rank' => array(
            'name' => 'Compete',
            'shortname' => 'Compete',
            'title' => 'Compete Rank',
			'type' => 'rank'
        ),
        'compete_visitors' => array(
            'name' => 'Compete Visits',
            'shortname' => 'Compete Visits',
            'title' => 'Compete Visits'
        ),
        'compete_unique' => array(
            'name' => 'Compete Unique Visitors',
            'shortname' => 'Compete Uniques',
            'title' => 'Compete Uniques'
        ),
        'ip_address' => array(
            'name' => 'IP',
            'shortname' => 'IP',
            'title' => 'IP Address',
			'type' => 'urlinfo'
        ),
        'reverse_ip' => array(
            'name' => 'Hostname',
            'shortname' => 'Hostname',
            'title' => 'Reverse IP / Hostname'
        ),
        'reciprocal' => array(
            'name' => 'Reciprocal Links',
            'shortname' => 'RL',
            'title' => 'Reciprocal Links',
            'ftitle' => '%s Reciprocal Links',
            'fmsg' => '%s has <b>%s</b> links to %s',
            'input' => array('id' => 'stat_url', 'type' => 'text', 'title' => 'Reciprocal Site')
        )
        //    'backlinks_technorati' => 'Technorati Backlinks',
    );
    
    var $proxy_table = false;
    var $proxy_urlformat = 'index.php?hl=40&q=%s';

    function kWebStats()
    {
        return true;   
    }
    
    function filter_url($url)
    {
        $url = 'http://' . k_filter_url($url) . '/';
        return $url;
    }
    
    function test_proxy($proxy, $output = false)
    {
        if(is_numeric($proxy))
        {
            global $db;
            $sql = 'SELECT * FROM ' . TBL_WEBSTATS_PROXIES . ' WHERE proxy_id = ' . $proxy;
            if(($rs = $db->execute($sql)) && (!$rs->EOF))
            {
                $url = $rs->fields['prx_url'];
            }
            else
            {
                return false;
            }
        }
        else
        {
            $url = $proxy;
        }
        $referer = $url;
        $url = $url . 'index.php?hl=40&q=' . urlencode('http://www.google.com/');
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $contents = curl_exec($ch);
        curl_close($ch);
        if($output) echo $contents;
        if(strpos($contents, 'Feeling Lucky') !== false)
        {
            return true;
        }
        else
        {
            return false;
        }
    
    }
    
    function get_stats($type, $url, $data = false)
    {
        global $kPr, $kRank, $kSearch;
        
        if(($type !== 'pagerank') && ($type !== 'fakerank'))
        {
            $url = $this->filter_url($url);
        }
        $stats = array();
        switch($type)
        {
            case 'pagerank':
                $rdata = $kPr->getPageRank($url);
                break;
            case 'backlinks_google':
                $rdata = $this->getBacklinks('google', $url);
                break;
            case 'backlinks_yahoo':
                $rdata = $this->getBacklinks('yahoo', $url);
                break;
            case 'backlinks_technorati':
                $rdata = $this->getBacklinks('technorati', $url);
                break;
            case 'alexa':
                $rdata = $this->getBacklinks('alexa', $url);
                break;
            case 'pages_google':
                $rdata = $this->getPages('google', $url);
                break;
            case 'pages_yahoo':
                $rdata = $this->getPages('yahoo', $url);
                break;
            case 'pages_msn':
                $rdata = $this->getPages('msn', $url);
                break;
            case 'pages_bing':
                $rdata = $this->getPages('bing', $url);
                break;
            case 'backlinks_gov':
                $rdata = $this->getGovLinks($url);
                break;
            case 'backlinks_edu':
                $rdata = $this->getEduLinks($url);
                break;
            case 'backlinks_dmoz':
                $rdata = $this->getDmozLinks($url);
                break;
            case 'fakerank':
                $fake_check = $kPr->checkFake($url);
                if($fake_check == 1)
                {
                    $rdata = 'Valid';
                    $pr_valid = '<span style="color:#00bb00;font-weight:bold;">VALID</span>';
                }
                elseif($fake_check == 2)
                {
                    $rdata = 'Unknown';
                    $pr_valid = '<span style="color:#d1b301;font-weight:bold;">UNKNOWN</span>';
                }
                elseif($fake_check == 3)
                {
                    $rdata = 'Not Valid';
                    $pr_valid = '<span style="color:#00ff00;font-weight:bold;">NOT VALID</span>';
                }
                break;
            case 'position_google':
                if(!empty($data))
                {
                    $fields = $kRank->getResults(trim($data), 'google');
                    $rdata = $kRank->getRank($url, $fields);
                }
                break;
            case 'position_yahoo':
                if(!empty($data))
                {
                    $fields = $kRank->getResults(trim($data), 'yahoo');
                    $rdata = $kRank->getRank($url, $fields);
                }
                break;
            case 'sitespeed':
                $times = k_http_sitespeed($url);
                $loadtime = $times['total'];
                $rdata = $loadtime;
                break;
            case 'reciprocal':
                if(!empty($data))
                {
                    $reciprocals = $this->get_reciprocal_links(trim($data), $url);
                    $rdata = $reciprocals;
                }
                break;
            case 'pageheat':
                $rdata = $this->get_pageheat($url);
                break;
            case 'getsource':
                $rdata = $this->getUrl($url);
                break;
            case 'backlinks_digg':
                $rdata = $this->get_digg_links($url);
                break;
            case 'backlinks_delicious':
                $rdata = $this->get_delicious_links($url);
                break;
            case 'compete_rank':
                $rdata = $this->get_compete($url);
                break;
            case 'compete_unique':
                $rdata = $this->get_compete($url, 'unique');
                break;
            case 'compete_visitors':
                $rdata = $this->get_compete($url, 'visitors');
                break;
            case 'ip_address':
                $rdata = $this->get_ip($url);
                break;
            case 'backlinks_twitter':
                $rdata = $this->get_twitter_links($url);
                break;
            case 'reverse_ip':
                $rdata = $this->get_hostname($url);
                break;
            case 'backlinks_yahoodir':
                $rdata = $this->get_yahoodir_links($url);
                break;
            case 'default':
                break;
        }

        $fdata = str_replace(',', '', $rdata);
        if(empty($fdata)) $fdata = 0;
        if(($key == 'alexa') && ($fdata == 0)) $fdata = false;
        if(($key == 'pagerank') && ($rdata == false)) $fdata = 'na';
        $stats = array('rdata' => $rdata, 'fdata' => $fdata);

        if(is_numeric($fdata))
        {
            if(strpos($fdata, '.') === false)
            {
                $fdata = number_format($fdata);
            }
            else
            {
                $fdata = number_format($fdata, 2);
            }
        }
        
        if(($this->stats[$type]['fmsg']) && (!empty($data['stat_phrase'])))
        {
            $stats['fmsg'] = sprintf($this->stats[$type]['fmsg'], $url, $fdata, $data['stat_phrase']);
        }
        elseif(!empty($this->stats[$type]['fmsg']))
        {
            $stats['fmsg'] = sprintf($this->stats[$type]['fmsg'], $url, $fdata);
        }

        if($this->stats[$type]['fname']) $stats['fname'] = sprintf($this->stats[$type]['fname'], $fdata);
        if($this->stats[$type]['ftitle']) $stats['ftitle'] = sprintf($this->stats[$type]['ftitle'], $fdata);
        
        return $stats;
    }
    
    function get_pageheat($url)
    {
        $url = k_filter_url($url);
        // Check the page and validate - return data
        if($contents = $this->getUrl('http://www.pageheat.com/api/' . $url, $s))
        {
            preg_match('/<root>([0-9]{1,2})<\/root>/', $contents, $heat_match);
            if(is_numeric($heat_match[1]))
            {
                return intval($heat_match[1]);
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return 0;
        } 
    }
    
    function get_ip($url)
    {
        $url = k_filter_url($url);
        $text = gethostbyname($url);
        if($text == $url) $text = 'unknown';
        return $text;
    }

    function get_hostname($url)
    {
        $url = k_filter_url($url);
        $text = gethostbyaddr($url);
        if($text == $url) $text = 'unknown';
        return $text;
    }
    function get_compete($url, $stat = 'rank')
    {
        $url = k_filter_url($url);
        
        $urlparts = explode('.', $url);
        while(count($urlparts) > 2)
        {
            array_shift($urlparts);
        }
        $url = implode('.', $urlparts);
        
        $compete_url = 'http://siteanalytics.compete.com/%s/';
        $furl = sprintf($compete_url, $url);

        // Check the page and validate - return data
        if($contents = $this->getUrl($furl, $s))
        {
            preg_match_all('/<h4>([0-9,]*)<\/h4>/', $contents, $matches);

            if(($stat == 'unique') && (!empty($matches[1][0])))
            {
                return str_replace(',', '', ($matches[1][0]));
            }
            elseif(($stat == 'visitors') && (!empty($matches[1][0])))
            {
                return str_replace(',', '', ($matches[1][0]));
            }
            elseif(($stat == 'rank') && (!empty($matches[1][1])))
            {
                return str_replace(',', '', ($matches[1][1]));
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return 0;
        } 
    }
    
    function get_digg_links($url)
    {
        $url = k_filter_url($url);
        $url = k_strip_www($url);
        
        $regex = '/<h1 class="section-title">([0-9,]*?) results<\/h1>/';
        $dsurl = 'http://digg.com/search?q=%s';
        $furl = sprintf($dsurl, $url);
        $contents = $this->getUrl($furl);

        // Check the page and validate - return data
        if($contents)
        {
            preg_match($regex, $contents, $matches);

            if(!empty($matches[1]))
            {
                return $matches[1];            
            }
            else
            {
                return '';
            }
        }
        else
        {
            return '';
        } 
    }

    function get_delicious_links($url)
    {
        $url = k_filter_url($url);
        
        $regex = '/<li>([0-9,]*?) Saves<\/li>/';
        $dsurl = 'http://delicious.com/url/%s';
        
        $furl = sprintf($dsurl, $url);
        $contents = $this->getUrl($furl);

        // Check the page and validate - return data
        if($contents)
        {
            preg_match($regex, $contents, $matches);
            if(!empty($matches[1]))
            {
                return $matches[1];            
            }
            else
            {
                return '';
            }
        }
        else
        {
            return '';
        } 
    }
    
    function get_twitter_links($url)
    {
        $url = k_filter_url($url);
        $url = k_strip_www($url);
        
        $dsurl = 'http://search.twitter.com/search.json?q=' . $url . '&rpp=100';
        $furl = sprintf($dsurl, $url);

        $contents = $this->getUrl($furl);

        // Check the page and validate - return data
        if($contents)
        {
            $linkresults = '';
            $json = json_decode($contents);
            if(is_object($json))
            {
                if(is_array($json->results))
                {
                    $linkresults = count($json->results);
                }
            }
            else
            {
                if(is_array($json['results']))
                {
                    $linkresults = count($json['results']);
                }
            }
            return $linkresults;
        }
        else
        {
            return '';
        }   
    }
    
    function get_yahoodir_links($url)
    {
        $url = k_filter_url($url);
        $dsurl = 'http://search.yahoo.com/search?p=linkdomain:' . $url . '+site:dir.yahoo.com+-site:' . $url;
        $furl = sprintf($dsurl, $url);
        $contents = $this->getUrl($furl);

        if($contents)
        {
            preg_match($this->ds['yahoo']['results_reg'], $contents, $matches);
            if(!empty($matches[1]))
            {
                return $matches[1];            
            }
            else
            {
                return '';
            }
        }
        else
        {
            return '';
        }     
    }
    
    function getBacklinks($eng, $url)
    {
        $eng_url = 'http://' . $this->ds[$eng]['stats_hosts'][0] . '/' . $this->ds[$eng]['links_url'];
        $links_url = sprintf($eng_url, urlencode($url));
        $page = $this->getUrl($links_url);

        if(($eng !== 'google') && ($eng !== 'alexa'))
        {
            $page = strip_tags($page);
        }
     
        preg_match_all($this->ds[$eng]['links_reg'], $page, $matches);

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
    
    function get_reciprocal_links($source, $target)
    {
        $reciprocals = 0;
        if($links = $this->get_outgoing_links($source))
        {
            foreach($links as $key => $value )
            {
                if(strpos($value['href'], $target) !== false)
                {
                    $reciprocals++;
                }
            }
        }
        return $reciprocals;
    }
    
    function get_outgoing_links($url)
    {
        $links = $this->get_site_links($url, array('/^http:\/\//'));
        foreach($links as $key => $value)
        {
            if(strpos($value['href'], $url) === false)
            {
                $clean_links[] = $value;
            }
        }
        return $clean_links;
    }

    function get_inner_links($url)
    {
        $links = $this->get_site_links($url);
        foreach($links as $key => $value)
        {

            if((strpos($value['href'], 'http://') !== 0) || (strpos($value['href'], $url) === 0))
            {
                if(substr($value['href'], 0, 7) !== 'http://')
                {
                    if(substr($value['href'], 0, 1) === '/')
                    {
                        $value['href'] = $url . substr($value['href'], 1);
                    }
                    else
                    {
                        $value['href'] = $url . $value['href'];
                    }
                }
                $clean_links[] = $value;
            }
        }
        return $clean_links;
    }
    function get_site_links($url, $regex = false)
    {
        $contents = $this->getUrl($url);
        preg_match_all('/<a(.*?)<\/a>/', $contents, $matches);

        foreach($matches[0] as $key => $value)
        {
            preg_match('/href=["\']?(.*?)["\' >]./', $value, $matches);
            if(!empty($matches[1]))
            {
                $add_link = true;
                if($regex)
                {
                    $add_link = false;
                    foreach($regex as $key => $expr)
                    {
                        preg_match($expr, $matches[1], $matches2);

                        if(!empty($matches2[0])) $add_link = true;
                    }
                }
                if($add_link)
                {
                    $link['href'] = trim($matches[1]);
                    
                    preg_match('/title=["\']?(.*?)["\'>]./', $value, $matches);
                    if(!empty($matches[1])) $link['title'] = $matches[1];
                    preg_match('/rel=["\']?(.*?)["\'>]./', $value, $matches);
                    if(!empty($matches[1]))
                    {
                        $link['rel'] = $matches[1];
                        $link['nofollow'] = ((strpos($link['rel'], 'nofollow') !== false)) ? true : false;
                    }
                    else
                    {
                        $link['rel'] = '';
                        $link['nofollow'] = false;
                    }
                    preg_match('/<a(.*?)?>(.*?)<\/a>/', $value, $matches);
                    if(!empty($matches[2]))
                    {
                        $link['anchor'] = $matches[2];
                        $link['anchor_safe'] = htmlspecialchars($matches[2]);
                    }
    
                    $links[] = $link;
                }
            }
        }

        if(count($links) > 0)
        {
            return $links;
        }
        else
        {
            return false;
        }
    }
    
    // Get indexed pages count
    function getPages($eng, $url)
    {
        $eng_url = 'http://' . $this->ds[$eng]['stats_hosts'][0] . '/' . $this->ds[$eng]['pages_url'];
        $pages_url = sprintf($eng_url, $url);

        $page = $this->getUrl($pages_url);

        if(($eng !== 'yahoo') && ($eng !== 'google') && ($eng !== 'bing'))
        {
            $page = strip_tags($page);
        }
        
//if($eng == 'yahoo') echo $pages_url;
        preg_match_all($this->ds[$eng]['pages_reg'], $page, $matches);
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
        preg_match_all($this->ds['yahoo']['results_reg'], $page, $matches);

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
        preg_match_all($this->ds['yahoo']['results_reg'], $page, $matches);

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

        $page = $this->getUrl($link_url);
        preg_match_all($this->ds['yahoo']['results_reg'], $page, $matches);

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