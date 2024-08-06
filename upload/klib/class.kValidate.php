<?php
/**
 * class.kValidate.php - Kytoo Data Validation Component
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.3
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
 * 2009-04-23 (1.3) - Released with Kytoo 2.0  
 *
 * // is_valid($string, $min_length, $max_length, $regex)
 * // is_alpha($string, $min_length = 0, $max_length = 0) 
 * // is_number($string, $min_length = 0, $max_length = 0)
 * // is_alphanumeric($string, $min_length = 0, $max_length = 0)
 * // is_clean_text($string, $min_length = 0, $max_length = 0)
 * // is_written_text($string, $min_length = 0, $max_length = 0) 
 * // is_username($value, $min=4, $max=16)
 * // is_location($value, $min=0, $max=16)
 * // is_password($value, $msg="") 
 * // is_email($string) 
 * // is_url($urladdr)
 * // contains_bad_words($string)
 * // is_numeric_big($string=0) 
 * // is_zip_code($string = "")
 * // is_currency($string, $dollar_sign=0)
 * // is_phone_number($string, $msg="")
 * // is_real_name($string, $min_length=0, $max_length=0)
 * // is_cc_type($cc_type, $msg="")
 * // is_cc_num($cc_num, $cc_type="", $cc_date="", $msg="")
 * // is_credit_card($cc_num, $cc_type, $cc_name, $cc_date, $msg="")
 *
*/

class kValidate extends kBase
{

    function kValidate()
    {            
        // Set common regexp's
        $this->friendly_text = '(^[-A-Za-z0-9 ]*$)';
        $this->friendly_url = '(^[-A-Za-z0-9]*$)';
        $this->errorlist = array();      
    }
        
    function is_valid($string, $min_length, $max_length, $regex)
    {
        // Check if the string is empty
        $str = trim($string);
        if(empty($str))
        {
            return false;
        }
    
        // Regex the string
        if(!eregi("^$regex$", $string))
        {
            return false;
        }
        
        // Optionally check the length
        $strlen = strlen($string);
        if(($min_length != 0 && $strlen < $min_length) || ($max_length != 0 && $strlen > $max_length))
        {
            return false;
        }
    
        // Passed all tests
        return true;
    }
    
    /*
     *      bool is_alpha(string string[, int min_length[, int max_length]])
     *      Check if a string consists of alphabetic characters only. Optionally
     *      check if it has a minimum length of min_length characters and/or a
     *      maximum length of max_length characters.
     */
    function is_alpha($string, $min_length = 0, $max_length = 0) 
    {
        $return = $this->is_valid($string, $min_length, $max_length, "[[:alpha:]]+");
        return($return);
    }
    
    /*
     *      bool is_number(string string[, int min_length[, int max_length]])
     *      Check if a string consists of digits only. Optionally
     *      check if it has a minimum length of min_length characters and/or a
     *      maximum length of max_length characters. 
     */
    function is_number($string, $min_length = 0, $max_length = 0)
    {
    
        $return = $this->is_valid($string, $min_length, $max_length, "[[:digit:]]+");
    
        return($return);
    }
    
    /*
     *      bool is_alphanumeric(string string[, int min_length[, int max_length]])
     *      Check if a string consists of alphanumeric characters only. Optionally
     *      check if it has a minimum length of min_length characters and/or a
     *      maximum length of max_length characters.
     */
    function is_alphanumeric($string, $min_length = 0, $max_length = 0)
    {
    
        $return = $this->is_valid($string, $min_length, $max_length, "[[:alnum:]]+");
    
        return($return);
    }
    
    
    
    /*
     *      bool is_clean_text(string string[, int min_length[, int max_length]])
     *      Check if a string contains only a subset of alphanumerics characters
     *      allowed in the Western alphabets. Useful for validation of names.
     *      Optionally check if it has a minimum length of min_length characters and/or a
     *      maximum length of max_length characters.
     */
    function is_clean_text($string, $min_length = 0, $max_length = 0)
    {
    
        $return = $this->is_valid($string, $min_length, $max_length, "[a-zA-Z0-9[:space:].ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþ`´# '-]+");
    
        return($return);    
    }

    function is_written_text($string, $min_length = 0, $max_length = 0) 
    {
    
        $return = $this->is_valid($string, $min_length, $max_length, "[a-zA-Z0-9[:space:].,ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþ`´# '-]+");
    
        return($return);    
    }
    
    /*
     *      bool is_username($value, $min_length=4, $max_length=16)
     *      check if a string is valid alphanumeric location and is the correct optional length 
    */
    function is_username($value, $min_length=3, $max_length=16)
    {   
        // Validates a username
        //  - Length must be between 4-16
        //  - Valid characters a-z, 0-9 and _
        
        if(preg_match("!^[a-z0-9_]{".$min_length.",".$max_length."}$!i", $value))
        {
            return true;
        }
        else
        {
       		$this->errors[] = 'Invalid username';        	
            return false;
        }
                     
    }
    
    /*
     *      bool is_location($value, $min_length=5, $max_length=16)
     *      check if a string is valid alphanumeric location and is the correct optional length 
    */
    function is_location($value, $min_length=5, $max_length=16)
    {   
        // Validates a location
        //  - Length must be between 5-16
        //  - Valid characters a-z, 0-9 and -,_,',',' '    
        if(preg_match("!^[-_, a-z0-9]{".$min_length.",".$max_length."}$!i", $value))
        {
            return true;
        }
        else
        {
            return false;
        }
                     
    }
      
    /*
     *      bool is_password(string $value, string $msg="", int $min_length=4, int $max_length=16) 
     *      check if a string is valid alphanumeric password and is the correct optional length 
    */
    function is_password($value, $msg="", $min_length=4, $max_length=16) 
    {
        // Validates a username
        //  - Length must be between 5-16
        //  - Valid characters a-z, 0-9 and _    
          
        if(eregi("[^a-z0-9]", $value) || strlen($value) < $min_length || strlen($value) > $max_length) 
        {
            $addinfo="";
            $this->errorlist[] = array("value" => $value, "msg" => $msg, "addinfo" => $addinfo);
            return false;
        } 

        return true;
                     
    }
    
    /*
     *      bool is_email(string string)
     *      check if a string is a syntactically valid mail address.
    */
    function is_email($string) 
    {
      
        $string = trim($string);        
        $result = ereg('^([A-Za-z0-9_!]|\\-|\\.)+'.
                      '@'.
                      '(([A-Za-z0-9_]|\\-)+\\.)+'.
                      '[A-Za-z]{2,4}$', $string);
          
        return($result);
    }
    
    /*
     *      bool is_url(string $urladdr)
     *      check if a string is a syntactically valid url.
     */
    function is_url($urladdr)
    { 
        $regexp = "^(https?://)";  // http:// 
        $regexp .= "?(([0-9a-z_!~*'().&=+$%-]+:)?[0-9a-z_!~*'().&=+$%-]+@)?";  // username:password@ 
        $regexp .= "(([0-9]{1,3}\.){3}[0-9]{1,3}";  // IP- 199.194.52.184 
        $regexp .= "|";  // allows either IP or domain 
        $regexp .= "([0-9a-z_!~*'()-]+\.)*"; // tertiary domain(s)- www. 
        $regexp .= "([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\."; // second level domain 
        $regexp .= "[a-z]{2,6})"; // first level domain- .com or .museum 
        $regexp .= "(:[0-9]{1,4})?"; // port number- :80 
        $regexp .= "((/?)|"; // a slash isn't required if there is no file name 
        $regexp .= "(/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+/?)$"; // filename/queries/anchors 
        
        //qualified domain 
        if (eregi( $regexp, $urladdr ))
        { 
            // No http:// at the front? lets add it. 
            if (!eregi( "^https?://", $urladdr )) $urladdr = "http://" . $urladdr; 
        
            // If it's a plain domain or IP there should be a / on the end 
            if (!eregi( "^https?://.+/", $urladdr )) $urladdr .= "/"; 
        
            // If it's a directory on the end we should add the proper slash 
            // We should first make sure it isn't a file, query, or fragment 
            if ((eregi( "/[0-9a-z~_-]+$", $urladdr)) && (!eregi( "[\?;&=+\$,#]", $urladdr))) $urladdr .= "/"; 
            return ($urladdr); 
        } 
        else 
            return false; // The domain didn't pass the expression 
    
    }
    
    /*
     *      bool contains_bad_words(string string)
     *      check if a string contains bad words
     */
    function contains_bad_words($string)
    {
        $bad_words = array('cunt','dildo','fuck');
        
        // Check for bad words
        for($i=0; $i<count($bad_words); $i++)
        {
            if(strstr(strtoupper($string), strtoupper($bad_words[$i])))
            {
                return true;
            }
        }
        
        return false;
    }
    /*
     *      bool returns if string contains all digits including large number >real
     *         needed because ::digits:: regex fails after 24 digits
     */
    function is_numeric_big($string=0) 
    {
        return preg_match('/^-?\d+$/', $string);
    } 


    /*
     *      bool is_zip_code($string = "")
     *      check if in zip and zip +4 formats 
     */

    function is_zip_code($string = "")
    {
        return $this->is_valid($string,0,0,'^[0-9]{5}([-]{1}[0-9]{4})?$');
    }



    /*
     *      bool is_currency(string $string, bool $bool) 
     *      check if the string contains a currency format
     *      format allows for white_space, optional $ sign, commas, 0 or 2 numbers after decimal
     *      used http://p2p.wrox.com/topic.asp?TOPIC_ID=3099 as reference
     */
    function is_currency($string, $dollar_sign=0)
    {
        if($dollar_sign)
            $patt = '/^\s*[$]?\s*((\d+)|(\d{1,3}(\,\d{3})+))(\.\d{2})?\s*$/'; //include  $
        else 
            $patt = '/^\s*((\d+)|(\d{1,3}(\,\d{3})+))(\.\d{2})?\s*$/';

        if(!preg_match($patt, $string, $matches))
            return false;
        return true;
    }

    /*
     *      bool is_phone_number(string string)
     *      Check if a string contains a phone number (any 13+-digit sequence,
     *      optionally separated by "(", ")", " ", "-", "/", ".", "x", "ext").
     *
     *      was if(ereg("[[:digit:]]{3,10}[\. /\)\(-]*[[:digit:]]{6,10}", $string))
     *      but this was failing with multiple issues
     */
    function is_phone_number($string, $msg="")
    {
        // Check for phone number 
        if(empty($string))
        {
            $addinfo = "No Phone Number submitted";
            $this->errorlist[] = array("value" => $string, "msg" => $msg, "addinfo" => $addinfo);
            return false;
        }

        $num = $string;
        $num = ereg_replace("([ ]+)","",$num); //remove spaces
        $num = eregi_replace("(\(|\)|\-|\+|\/|\.|\x|\ext)","",$num); //remove "(,),-,+,/,.,x,ext" characters
        if(!$this->is_numeric_big($num))
        {
            $bad_chars = eregi_replace("[0-9]","", $num);
            $addinfo = "Bad characters in Phone number: (\"".$bad_chars."\").";
            $this->errorlist[] = array("value" => $string, "msg" => $msg, "addinfo" => $addinfo);
            return false;
        }

        if ( (strlen($num)) < 7)
        {
            $addinfo = "Phone number is too short.";
            $this->errorlist[] = array("value" => $string, "msg" => $msg, "addinfo" => $addinfo);
            return false;
        }

        // 000 000 000 0000
        // CC  AC  PRE SUFX = max 13 digits
        if( (strlen($num)) > 13)
        {
            $addinfo = "Phone number is too long.";
            $this->errorlist[] = array("value" => $string, "msg" => $msg, "addinfo" => $addinfo);
            return false;
        }

        return true;
    }

    /*
     *      bool is_real_name(string $string, int $min_length=0, int $max_length=0)
     *      checks if name contains any bad characters according to the is_clean_text function
     */
    function is_real_name($string, $min_length=0, $max_length=0)
    {
       return $this->is_clean_text($string, $min_length, $max_length);
    }

   
    /*
     *      bool is_cc_num(string $cc_type, string $msg="")
     *      checks to see if the credit card type is a valid type accepted
     */
    function is_cc_type($cc_type, $msg="")
    {
        switch(strtoupper($cc_type))
        {
            case "MC":
            case "VISA":
            case "AMEX":
            case "DISC":
                return true;
                break; 
            default:
                $addinfo = "Credit Card Type is invalid";
                $this->errorlist[] = array("value" => $string, "msg" => $msg, "addinfo" => $addinfo);
                return false;    
                break; 
        }

    }
    
    function is_db_fieldname($string)
    {
        if(preg_match("/^[A-Za-z0-9]/", $string))
        {
            return true;
        }
        else
        {
       		$this->errors[] = 'Invalid MySQL field name';        	
            return false;
        }    	
    }
    
    function is_title($string, $min_length = 0, $max_length = 0)
    {
    
        $return = $this->is_valid($string, $min_length, $max_length, "[a-zA-Z0-9[:space:].ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþ`´$&# '-]+");
    
        return($return);    
    }

    function is_desc($string, $min_length = 0, $max_length = 0)
    {
    
        $return = $this->is_valid($string, $min_length, $max_length, "[a-zA-Z0-9[:space:].ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþ`´,$&# '-]+");
    
        return($return);    
    }

    /*
     *      bool is_cc_num(string $cc_num, string $cc_type)
     */
    function is_cc_num($cc_num, $cc_type)
    {
    
        //Prepare the cc number by removing all extraneous characters
        $num = ereg_replace("[^0-9]", "", $cc_num);
        switch(strtoupper($cc_type))
        {
            case "MASTERCARD":
                $cc_regex = "^5[1-5][0-9]{14}$";
                break; 
            case "VISA":
                $cc_regex = "^4[0-9]{12}$|^4[0-9]{15}$";
                break; 
            case "AMEX":
                $cc_regex = "^3[4|7][0-9]{13}$";
                break; 
            case "BANKCARD":
                $cc_regex = "^56[0-9]{14}$";
                break; 
            default:
                return false;    
                break; 
        }

        if(!$this->is_valid($num,0,0,$cc_regex))
        {
            return false;    
        }
        else 
        {
            // Perform MOD 10 validation algorithm
            $cc_c = strrev($num);
            $numSum = 0;
            
            for($i = 0; $i < strlen($cc_c); $i++)
            {
              $c_num = substr($cc_c, $i, 1);
            
            // Double every second digit
            if($i % 2 == 1)
            {
              $c_num *= 2;
            }
            // Add digits of 2 digit numbers together
            if($c_num > 9)
            {
              $firstNum = $c_num % 10;
              $secondNum = ($c_num - $firstNum) / 10;
              $c_num = $firstNum + $secondNum;
            }
            
            $numSum += $c_num;
            }
            $pass = ($numSum % 10 == 0);
            if($pass)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
}

?>
