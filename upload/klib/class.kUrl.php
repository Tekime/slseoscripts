<?php
/**
 * class.kUrl.php - URL Utility Component
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

class kUrl extends kBase 
{

    /**
     * @access  public
     * @var     array $errors            List of errors
    */
    var $errors = array();
    
    /**
     * @access  public
     * @var     string $root        URL root prefix
    */
    var $root = array();
    
    /**
     * @access  public
     * @var     string $request     URL request string
    */
    var $request = array();
    
    function kUrl($request)
    {
        // Strip any dynamic vars from the request
        $pos = strpos($request, '?');
        if($pos !== false)
        {
            $request = substr($request, 0, $pos);
        }
        // Strip the starting slash
        if(substr($request, 0,1) == '/')
        {
            $request = substr_replace($request, '', 0, 1);
        }
        // Strip the ending slash
        if(substr($request, -1,1) == '/')
        {
            $request = substr_replace($request, '', -1, 1);
        }        
       
        $parts = explode('/', $request);

        if($parts)
        {
            // Check for a URL root prefix in position 0
            if($parts)
            {
                $this->root = array_shift($parts);
                
                // Check for a module name in new position 0
                if($parts)
                {
                    $this->module = array_shift($parts);
                }
                // Check for remaining request in new position 0
                if($parts)
                {
                    $this->request = $parts;
                }
            }
        }
        
        return true;  
    }

}


?>
