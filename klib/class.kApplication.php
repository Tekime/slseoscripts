<?php
/**
 * class.kApplication.php - Kytoo Application Component Object
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
 * 2009-05-20 (1.1) - Minor update
 * 2009-05-01 (1.0) - Released with Kytoo 2.0  
 *
*/

class kApplication
{

    /**
     * @access  private
     * @var     array $errors   Stores object errors
    */
    var $errors = array();

    /**
     * @access  private
     * @var     array $interface    Registered application interfaces
    */
    var $interfaces = array();
    
    /**
     * @access  private
     * @var     array $views    Registered application views
    */
    var $views = array();
    
    /**
     * @access  private
     * @var     array $views    Registered application panels
    */
    var $panels = array();
    
    /**
     * @access  private
     * @var     array $views    Registered application menus
    */
    var $menus = array();
    
    /**
     * @access  private
     * @var     array $urlVars    Registered application URL variables
    */
    var $urlVars = array();
    
    /**
     * @access  private
     * @var     string $defaultInterface    The default interface for application components
    */
    var $defaultInterface = K_VIEW_INDEX;
    
    /**
     * @access  private
     * @var     string $currentView     The currently active view
    */
    var $currentView = K_VIEW_INDEX;
    
    /**
     * @access  private
     * @var     int $defaultViewOrder       Default setting for view order
    */
    var $defaultViewOrder = 99;
    
    /**
     * @access  private
     * @var     array $messages     Application UI messages
    */
    var $messages = array();
    
    /**
     * @access  private
     * @var     array $layouts      Registered applicaiton layouts
    */
    var $layouts = array();
    
    /**
     * 
     *
     * @param   bool $post
     * @param   bool $get
     * @param   bool $cookie
     * @return  mixed
     */
    function kApplication($interface = false)
    {
        $this->defaultInterface = ($interface) ? $interface : K_VIEW_INDEX;
         
        $this->registerInterface($this->defaultInterface, 'Index');
        
        return true;
    }
    
    /** Utilities ***********************************************/
    function addMessage($message, $type = false)
    {
        $this->messages[] = array('msg' => $message, 'type' => $type);
        return true;
    }
    /** End utilities ***********************************************/

    /** Interfaces ***************************************************/
    function registerInterface($interface, $title, $text = '', $perm = 0)
    {
        if(!$this->isInterface($interface))
        {
            $this->interfaces[$interface] = array(
                'id' => $interface,
                'name' => $interface,
                'title' => $title,
                'text' => $text,
                'perm' => $perm
            );
        }
        else
        {
            $this->errors[] = 'Interface `' . $interface . '` already exists';
            return false;
        }
    }    
    function isInterface($interface)
    {
        if(!empty($this->interfaces[$interface]['id']))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    /** End Interfaces ************************************************/

    /** Layouts ***********************************************/
    function registerLayout($layout, $interface = false)
    {
        $interface = ($interface) ? $interface : $this->defaultInterface;

        if($this->isInterface($interface))
        {
            $this->layouts[$interface][] = $layout;
            return true;
        }
        else
        {
            return false;
        } 
    }

    function getLayouts($interface = false)
    {
        $interface = ($interface) ? $interface : $this->defaultInterface;

        if(sizeof($this->layouts[$interface] > 0))
        {
            return $this->layouts[$interface];
        }
        else
        {
            $this->errors[] = 'No layouts have been registered to interface `' . $interface . '`.';
            return false;
        }
    }

    function getLayoutsList($interface = false)
    {
        if($layouts = $this->getLayouts($interface))
        {
            foreach($layouts as $key=>$value)
            {
                $f_value = preg_replace('/([A-Z]{1})/', ' \\1', $value);
                $list[$value] = trim($f_value);
            }
            natcasesort($list);
            return $list; 
        }
        else
        {
            return false;
        }
    }
    /** End Layouts ***********************************************/
    
    /** Menus ***********************************************/
    function registerMenu($interface, $name, $title, $url = '', $sort = 99, $status = 1)
    {
        if($this->isInterface($interface))
        {
            $this->menus[$interface][$name]['id'] = $name;
            $this->menus[$interface][$name]['name'] = $name;
            $this->menus[$interface][$name]['title'] = $title;
            $this->menus[$interface][$name]['sort'] = $sort;
            $this->menus[$interface][$name]['url'] = $url;
            $this->menus[$interface][$name]['id'] = $sort;
            $this->menus[$interface][$name]['status'] = $status;
            return true;
        }
        else
        {
            return false;
        }            
    }
    
    function registerMenuItem($interface, $menu, $name, $title, $url = '', $sort = 99, $target = '', $extra = '', $html = '', $status = 1)
    {
        if(empty($text)) $text = '';
        $this->menus[$interface][$menu]['items'][$name] = array(
            'id' => $name,
            'name' => $name,
            'title' => $title,
            'url' => $url,
            'sort' => $sort,
            'text' => $text,
            'status' => $status,
            'target' => $target,
            'extra' => $extra,
            'html' => $html
            );

        return true;
    }
    function isMenu($interface, $menu)
    {
        if(!empty($this->menus[$interface][$menu]['id']))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    function getMenus($interface = K_VIEW_INDEX)
    {
        if(sizeof($this->menus[$interface] > 0))
        {
            $menus = $this->menus[$interface];
            foreach($menus as $key=>$menu)
            {
                $menus[$key]['items'] = array_sort_by_field($menus[$key]['items'], 'sort');
            }

            $sortarray = array_sort_by_field($menus, 'sort', 'items', 'sort');

            return $sortarray;
        }
        else
        {
            $this->errors[] = 'No menus have been registered to interface `' . $interface . '`.';
            return false;
        }
    }
    /** End Menus ***********************************************/

    /** Panels ***********************************************/
    function isPanel($interface, $panel)
    {
        if(!empty($this->panels[$interface][$panel]['id']))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    function getPanels($interface = K_VIEW_INDEX)
    {
        if(sizeof($this->panels[$interface] > 0))
        {
            return array_sort_by_field($this->panels[$interface], 'sort');
        }
        else
        {
            $this->errors[] = 'No panels have been registered to interface `' . $interface . '`.';
            return false;
        }
    }    
    
    function registerPanel($interface, $name, $title, $sort = 99, $status = 1)
    {
        if($this->isInterface($interface))
        {
            $this->panels[$interface][$name] = array(
                'id' => $name,
                'name' => $name,
                'title' => $title,
                'sort' => $sort,
                'status' => $status
                );
                
            return true;
        }
        else
        {
            return false;
        } 
    }
    
    function registerPanelItem($interface, $panel, $name, $title, $url = '', $text = '', $target = '', $status = 1, $extra = '')
    {
        if($this->isPanel($interface, $panel))
        {
            $this->panels[$interface][$panel]['items'][$name] = array(
                'id' => $name,
                'name' => $name,
                'title' => $title,
                'url' => $url,
                'text' => $text,
                'status' => $status,
                'target' => $target,
                'extra' => $extra
                );

            return true;
        }
        else
        {
            $this->errors[] = 'Panel `' . $panel . '` does not exist';
            return false;
        }
    }
    /** End Panels ***********************************************/
    
    /** URL Parsing **************************************************/
    function registerUrlVar($name, $pattern, $interface = '')
    {
        if(empty($interface)) $interface = $this->defaultInterface;
        $this->urlvars[$interface][$name] = $pattern;
        return true;
    }
    
    function getUrlVars($interface = '')
    {
        if(empty($interface)) $interface = $this->defaultInterface;
        if(!empty($this->urlvars[$interface]))
        {
            return $this->urlvars[$interface];
        }
        else
        {
            return false;
        }
    }
    
    function getUrlFormat($view, $interface = false)
    {
        if(!$interface) $interface = $this->defaultInterface;
        global $cfg;
        
        $pattern = $this->views[$interface][$view]['pattern'];
        $bit = '';
        while(strpos($pattern, '%') !== false)
        {
            $bit = ($bit == '{') ? $bit = '}' : $bit = '{';
            $pattern = substr_replace($pattern, $bit, strpos($pattern, '%'), 1);
        }
        return $pattern;
    }
    
    function getUrl($view, $data, $interface = false, $baseurl = false)
    {
        if(!$baseurl) { global $cfg; $baseurl = $cfg->getVar('site_url'); }
        if(!$interface) $interface = $this->defaultInterface;

        $furl = $this->getUrlFormat($view, $interface);

        if(!empty($this->urlvars[$interface]))
        {
            foreach($this->urlvars[$interface] as $key=>$value)
            {
                if(!empty($data[$key])) $furl = str_replace('{' . $key . '}', $data[$key], $furl);
            }
            if($baseurl) $furl = $baseurl . $furl;
            return $furl;
        }
        return false;
    }
    
    /** End URL Parsing ***********************************************/

    /** Views ***********************************************/
    function registerView($view, $pattern, $vars, $sort = '', $name = '', $greedy = true, $interface = '')
    {
        if(empty($interface)) $interface = $this->defaultInterface;
        if(empty($order)) $order = $this->defaultViewOrder;
        
        if($this->isInterface($interface))
        {
            $this->views[$interface][$view] = array(
                'id' => $view,
                'pattern' => $pattern,
                'vars' => $vars,
                'sort' => $sort,
                'name' => $name,
                'greedy' => $greedy
            );
        }        
    }
    function setCurrentView($view)
    {
        $this->currentView = $view;
        return true;
    }
    
    function getCurrentView($view)
    {
        if(!empty($this->currentView))
        {
            return $this->currentView;        
        }
        else
        {
            return false;
        }
    }
    function getView($view, $interface = '')
    {
        if(empty($interface)) $interface = $this->defaultInterface;

        if($this->isView($interface, $view))
        {
            return $this->views[$interface][$view];
        }
        else
        {
            $this->errors[] = 'View `' . $interface . ':' . $view . '` does not exist';
            return false;
        }
    }
    
    function getViews($interface)
    {
        if($this->isInterface($interface))
        {
            if(sizeof($this->views[$interface] > 0))
            {
//                $sortarray = array_sort_by_field($this->views[$interface], 'sort');
                $sortarray = $this->views[$interface];
                return $sortarray;
            }
            else
            {
                $this->errors[] = 'Interface `' . $interface . '` contains no views';
                return false;
            }
        }
        else
        {
            $this->errors[] = 'Interface `' . $interface . '` does not exist';
            return false;
        }

    }
    
    function isView($interface, $view)
    {
        if(!empty($this->views[$interface][$view]['id']))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    /** End Views ***********************************************/
    

    

}