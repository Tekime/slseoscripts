<?php
/**
 * class.kPageRank.php 
 *
 * This file is part of the Kytoo architecture (www.kytoo.com). It is a Google PageRank interface
 * class, providing functions for getting and displaying Google PR's. Checksum functions are based
 * on public domain code created by Alex Stapleton, Andy Doctorow, Tarakan, Bill Zeller, and
 * Vijay "Cyberax" Bhatter. Many thanks!
 *
 * Copyright (c) 2008 phpLinkBid (http://www.phplinkbid.com/), All Rights Reserved
 * An Intavant Creation (http://www.intavant.com/)
 * 
 * IMPORTANT - This is not free software. You must adhere to the terms of the End-User License Agreement
 *             under penalty of law. Read the complete EULA in "license.txt" included with your application.
 * 
 *             This file can be used, modified and distributed under the terms of the Source License
 *             Agreement. You may edit this file on a licensed Web site and/or for private development.
 *             You must adhere to the Source License Agreement. The latest copy can be found online at:
 * 
 *             http://www.phplinkbid.com/en/source_license
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
 * @copyright   Copyright (c) 2008 phpLinkBid, All Rights Reserved
 * @author      Gabriel Harper, Intavant <gharper@intavant.com>
 * @version     1.8
 *
 * 1.8 - Fixed PageRank toolbar URL
 * 1.7 - Fixed checkFake() failing on Google redirections
 * 1.6 - Fixed checkFake()
 * 1.5 - Updated kPageRank() for external DCs, fixed checkFake()
 * 1.4 - Added checkFake()
 * 1.3 - Removed several bad datacenter IPs
 * 1.2 - Replaced checksum algorithms for PHP 4/5 compatibility
 *
 */

define('GOOGLE_MAGIC', 0xE6359A60);

class kPageRank
{
    /**
     * @access  public
     * @var     string $pr          PageRank
    */
    var $pr;
    
    /**
     * @access  public
     * @var     string $timeout     Timeout in seconds for datacenter queries
    */
    var $timeout;
    
    /**
     * @access  public
     * @var     array $hosts        Array of Google servers to check (DN or URL)
    */
    var $hosts = array('toolbarqueries.google.com');

    function kPageRank($path_images, $url_images, $hosts = 0, $timeout = 2)
    {
        $this->path_images = $path_images;
        $this->url_images = $url_images;
        $this->timeout = $timeout;
        if(!empty($hosts))
        {
            $this->hosts = $hosts;
        }
        return true;
    }


    /**
     * getImage() - Loads a PR image and returns the image resource
     *
     * This function loads a PR image from the ranking and style, then returns the image resource. 
     *
     * @access      public
     * @param       string $style   Name of the image style
     * @return      mixed
    */
    function getImage($pr = '0', $style)
    {

        $imagefile = $this->path_images . $style . '/pr' . $pr . '.gif';
        $im = @imagecreatefromgif($imagefile);
        
        if($im)
        {
            return $im;
        }
        else
        {
            return false;
        }
    }
    

    /**
     * getImages() - Gets all PR images for the specified style in an array
     *
     * This function gets all of the PageRank images for the specified style and
     * returns the URL as an array, by PR. 
     *
     * @access      public
     * @param       string $style   Name of the image style
     * @return      mixed
    */
    function getImages($style)
    {
        $styles = $this->getStyles();
        if(in_array($style, $styles))
        {
            for($i=0;$i<=10;$i++)
            {
                $images[$i] = $this->url_images . $style . '/pr' . $i . '.gif';
            }
            return $images;
        }
        else
        {
            return false;
        }
    
    }

    /**
     * getStyles() - Gets all PR Styles in an array
     *
     * This function gets all of the PageRank Styles and returns the names in an array
     *
     * @access      public
     * @return      mixed
    */
    function getStyles()
    {

        // Open the PR base image directory
        if ($handle = opendir($this->path_images))
        {
            while (false !== ($file = readdir($handle)))
            {
                if((is_dir($this->path_images . $file)) && ($file !== '..') && ($file !== '.'))
                {
                    $styles[] = $file;
                }
             }
         closedir($handle);
        }
        return $styles;
    }
      
    /**
     * getPageRank() - Connects to the specified host and extracts PR from the response
     *
     * This function gets a checksum for an URL, attempts a connection to the specified Google host, 
     * searches the response for PageRank, and returns said result. 
     *
     * @access      public
     * @param       string $url     URL of the domain to check
     * @param       string $host    IP or domain name of the Google host to query
     * @return      integer
    */
    function getPageRank($url, $host = 'toolbarqueries.google.com')
    {
        // Open domain socket connection to specified host
        $fp = @fsockopen($host, 80, $errno, $errstr, $this->timeout);
                
        if($fp)
        {
            // Get URL checksum
            $hash = $this->getHash($url);
            $ch = $this->getCh($hash);

            // Build the domain info request
            $out = "GET /tbr?client=navclient-auto&ch=" . $ch .  "&features=Rank&q=info:" . $url . " HTTP/1.1\r\n" ;
            $out .= "Host: " . $host . "\r\n";
            $out .= "Connection: Close\r\n\r\n";
            
            // Write the request to our connection 
            fwrite($fp, $out);
            
            while (!feof($fp))
            {
                // Check the response for a ranking
                $data = fgets($fp, 128);
                $pos = strpos($data, "Rank_");
                if($pos !== false)
                {
                    // Get the rank from our response and return
                    $pagerank = intval(substr($data, $pos + 9));
                    return $pagerank;
                }
            }
            
            // Close the connection
            fclose($fp);
        }
        else
        {
            // Couldn't connect to the host
            $this->errors[] = $errstr . ' (' . $errno . ')';
            return false;
        }
    
    }

    /**
     * strToInt() - Convert string to a 32-bit integer
    */
    function strToInt($string, $check, $gmagic)
    {
        $integer32 = 4294967296;
    
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++)
        {
            $check *= $gmagic; 	
            if ($check >= $integer32)
            {
                $check = ($check - $integer32 * (int) ($check / $integer32));
                $check = ($check < -2147483648) ? ($check + $integer32) : $check;
            }
            $check += ord($string{$i}); 
        }
        return $check;
    }
    
    /**
     * getHash() - Generate an URL hash to feed the checksum
    */
    function getHash($url)
    {
        $check1 = $this->strToInt($url, 0x1505, 0x21);
        $check2 = $this->strToInt($url, 0, 0x1003F);
    
        $check1 >>= 2; 	
        $check1 = (($check1 >> 4) & 0x3FFFFC0 ) | ($check1 & 0x3F);
        $check1 = (($check1 >> 4) & 0x3FFC00 ) | ($check1 & 0x3FF);
        $check1 = (($check1 >> 4) & 0x3C000 ) | ($check1 & 0x3FFF);	
    	
        $t1 = (((($check1 & 0x3C0) << 4) | ($check1 & 0x3C)) <<2 ) | ($check2 & 0xF0F );
        $t2 = (((($check1 & 0xFFFFC000) << 4) | ($check1 & 0x3C00)) << 0xA) | ($check2 & 0xF0F0000 );
    	
        return ($t1 | $t2);
    }

    /**
     * getCh() - Get a Google checksum for provided hash
    */
    function getCh($hash)
    {
        $checkByte = 0;
        $flag = 0;
    
        $string = sprintf('%u', $hash) ;
        $length = strlen($string);
    	
        for ($i = $length - 1;  $i >= 0;  $i --) {
            $Re = $string{$i};
            if (1 === ($flag % 2)) {              
                $Re += $Re;     
                $Re = (int)($Re / 10) + ($Re % 10);
            }
            $checkByte += $Re;
            $flag ++;	
        }
    
        $checkByte %= 10;
        if (0 !== $checkByte) {
            $checkByte = 10 - $checkByte;
            if (1 === ($flag % 2) ) {
                if (1 === ($checkByte % 2)) {
                    $checkByte += 9;
                }
                $checkByte >>= 1;
            }
        }
    
        return '7'.$checkByte.$string;
    }
    
    /**
     * Fake Rank check looks for cached copy in Google
     */
    function checkFake($url)
    {
        $user_agent = "Mozilla/4.0";
        $site_url = "http://www.google.com/search?hl=en&lr=&q=cache:".$url;
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $site_url);
        curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt ($ch, CURLOPT_HEADER, 1);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
        $code = curl_exec ($ch);
        curl_close($ch);

        $reg = '/cache of <a href=\"(.*?)\">/';
        $rmatch = preg_match($reg, $code, $match);

        if(!empty($match[0])) $code1 = $match[0];
        $pattern = "did not match any documents";

        if(strpos($code1,$url) != false)
        {
        	return 1;
        }
        else if (eregi($pattern,$code))
        {
        	return 2;
        }
        else
        {
        	return 3;
        }
    
    }

}

?>