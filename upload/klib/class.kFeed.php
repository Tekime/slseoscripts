<?php
/**
 * class.kFeed.php - Kytoo RSS & Atom Feeds Component 
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
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
 * 2009-05-01 (1.2) - Updated for Kytoo 2.0  
 *
*/
 
class kFeed extends kBase
{

    var $item = array(); 
    var $depth = array(); 
    var $tags = array("dummy"); 
    var $index = 0;
    var $max = 100;
    var $data;
    var $cache = 300;
    
    function kFeed()
    {
        return true;
    }
    
    function init()
    {
        $this->item = array ();
    }
    
    function startElement($parser, $name, $attributes)
    {
        if ($name == "ITEM") $this->init();
        $this->depth[$parser] = (empty($this->depth[$parser])) ? 1 :$this->depth[$parser] + 1; 
        array_push($this->tags, $name);
    }
    function endElement($parser, $name)
    {
        array_pop($this->tags);
        $this->depth[$parser]--;
        if(($name == 'ITEM') && ($this->max > $this->index))
        {
            $this->feed[] = $this->item;
            $this->init();
            $this->index++;
        }    
    }
    function parseData($parser, $text)
    {
        $whitespace = preg_replace ("/\s/", "", $text);
        if ($whitespace) {
//            $text = preg_replace ("/^\s+/", "", $text);
            if (!empty($this->item[$this->tags[$this->depth[$parser]]])) {
                $this->item[$this->tags[$this->depth[$parser]]] .= $text;
            } else {
                $this->item[$this->tags[$this->depth[$parser]]] = $text; 
            }
        }    
    }
    function getFeed($rssfile, $max = 0)
    {
        if(($this->cache > 0) && defined('TBL_FEEDCACHE'))
        {
            $cache_id = 0;
            
            global $db;
            $sql = 'SELECT * FROM ' . TBL_FEEDCACHE . ' WHERE feed_url = "' . $rssfile . '" LIMIT 1';
            if(($rs = $db->execute($sql)) && (!$rs->EOF))
            {
                if((dbtime_to_unix($rs->fields['dateupdated'])) > (time() - $this->cache))
                {
                    // Use cached copy
                    $this->feed = unserialize($rs->fields['feed_contents']);
                    return true;                    
                }
                else
                {
                    // Cache is out of date
                    $cache_id = $rs->fields['feedcache_id'];
                }
            }
        }

        if($max) $this->max = $max;
        $xml_parser = xml_parser_create();
        
        $this->init();
        
        xml_set_object ($xml_parser, $this);
        xml_set_element_handler($xml_parser, "startElement", "endElement");
        xml_set_character_data_handler($xml_parser, "parseData");
        
        if($fp = fopen ($rssfile, "r"))
        {
            while ($this->data = fread ($fp, 4096))
            {
                if (!xml_parse($xml_parser, $this->data, feof($fp)))
                {
                    sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code ($xml_parser)), xml_get_current_line_number($xml_parser)); 
                }
            }
            fclose($fp);
            xml_parser_free($xml_parser);
            
            if(($this->cache > 0) && defined('TBL_FEEDCACHE'))
            {
                $feed_serial = serialize($this->feed);
                $feed_serial = mysql_real_escape_string($feed_serial);

                if($cache_id > 0)
                {
                    $sql = 'UPDATE ' . TBL_FEEDCACHE . ' SET feed_contents = "' . $feed_serial . '", dateupdated = "' . unix_to_dbtime(time()) . '" WHERE feedcache_id = ' . $cache_id;
                    $db->execute($sql);
                }
                else
                {
                    $sql = 'INSERT INTO ' . TBL_FEEDCACHE . ' (feed_url, feed_contents, dateupdated) VALUES("' . $rssfile . '", "' . $feed_serial . '", "' . unix_to_dbtime(time()) . '")';
                    $db->execute($sql);
                }
            }
        }
        else
        {
            return false;
        }    
    }
}
?>