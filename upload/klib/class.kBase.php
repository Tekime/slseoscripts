<?php
/**
 * class.kBase.php - Kytoo Base Class
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
 * 2009-05-01 (1.0) - Initial release of Kytoo 2.0 Base Class  
 *
*/

class kBase
{

    /**
     * @access  public
     * @var     array $errors   Stores object errors
    */
    var $errors = array();

    /**
     * @access  public
     * @var     array $views    Stores application views
    */
    var $views = array();
    var $db;
    
    function kBase(&$db)
    {
        $this->db =& $db;
        echo '...';
        return true;
    }
    
    function addError($error, $component = false)
    {
        $cp_error = ($component) ? ' [' . $component . ']' : '';
        if(is_array($error))
        {
            foreach($error as $key=>$value)
            {
                $this->errors[] = 'Error' . $cp_error . ': ' . $value;
            }        
        }
        else
        {
            $this->errors[] = 'Error' . $cp_error . ': ' . $error;
        }
    }
    function hasErrors()
    {
        if(count($this->errors) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}