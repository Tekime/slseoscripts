<?php
/**
 * class.kSitemap.php - Kytoo Sitemap Component
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.0
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
 * 2009-04-23 (1.0) - Released with Kytoo 2.0  
 *
*/
 
class kSitemap extends kBase
{

    /**
     * @access  private
     * @var     string      $xml_header
    */
    var $xml_header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    var $urlset_params = " xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"";
    var $urlset_extended_params = "\n xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\n http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\"";
    
    var $pings = array(
        'google' => 'http://www.google.com/webmasters/tools/ping?sitemap=%s',
        'yahoo' => 'http://search.yahooapis.com/SiteExplorerService/V1/ping?sitemap=%s',
        'ask' => 'http://submissions.ask.com/ping?sitemap=%s'
    );
    
    var $changefreq = array('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never');
    var $def_changefreq = 'always';
    var $def_lastmod = false;
    var $def_priority = .5;
    var $links = array();
    
    function kSitemap()
    {
        $this->def_lastmod = date('Y-m-d');
        return true;   
    }
    
    function getSitemap()
    {
        if(count($this->links) == 0) return false;
        
        $xml_sitemap = $this->xml_header .
                       "<urlset" . $this->urlset_params . ">\n";

        foreach($this->links as $key => $value)
        {
            $xml_sitemap .= $this->get_xml_url($value);
        }

        $xml_sitemap .= "</urlset>\n";
        
        return $xml_sitemap;
    }
    
    function addLink($loc, $changefreq = false, $lastmod = false, $priority = false)
    {
        if($changefreq === false) $changefreq = $this->def_changefreq;
        if($lastmod === false) $lastmod = $this->def_lastmod;
        if($priority === false) $priority = $this->def_priority;
        
        $this->links[] = array('loc' => $loc, 'lastmod' => $lastmod, 'changefreq' => $changefreq, 'priority' => $priority);
        return true;
    }
    
    function addLinks($links, $changefreq = false, $lastmod = false, $priority = false)
    {
        foreach($links as $key => $loc)
        {
            $this->addLink($loc, $changefreq, $lastmod, $priority);
        }
        return true;
    }

    function get_xml_url($link)
    {
        if(!empty($link['loc']))
        {
            $xml_url = "<url>\n";
            $xml_url .= "<loc>" . $link['loc'] . "</loc>\n";
            if(!empty($link['lastmod'])) $xml_url .= "<lastmod>" . $link['lastmod'] . "</lastmod>\n";
            if(!empty($link['changefreq'])) $xml_url .= "<changefreq>" . $link['changefreq'] . "</changefreq>\n";
            if(!empty($link['changefreq'])) $xml_url .= "<priority>" . $link['priority'] . "</priority>\n";
            $xml_url .= "</url>\n";
            
            return $xml_url;
        }
        else
        {
            return false;
        }
    }
    
    
    
}