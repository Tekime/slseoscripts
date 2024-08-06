<?php
/**
 * k_functions_text.php - Kytoo Utility Functions for Text & String Manipulation
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
 * 2009-05-17 (1.0) - Released for Kytoo 2.0
 *
*/

function k_text_get_words($string, $min = 3, $ignore = false)
{
    if($ignore)
    {
        $ignore_list = explode("\n", $ignore);
    }
    else
    {
        $ignore_list = array(
            'a', 'all', 'am', 'an', 'and', 'any', 'are', 'as', 'at', 'be', 'but', 'can', 'did', 'do',
            'does', 'for', 'from', 'had', 'has', 'have', 'here', 'how', 'i', 'if', 'in', 'is', 'it',
            'no', 'not', 'of', 'on', 'or', 'so', 'that', 'the', 'then', 'there', 'this', 'to', 'too',
            'up', 'use', 'what', 'when', 'where', 'who', 'why', 'you', 'will', 'with', 'was',
            'your', 'youre', 'our', 'their', 'we', 'now', 'ever', 'way', 'th'
        );
    }
    
    $text = k_text_striphtml($string);
    foreach($ignore_list as $key => $value)
    {
        $text = str_replace(' ' . $value . ' ', ' ', $text);
    }

    // Get 1-word matches
    preg_match_all('/ ([a-z]*?) /', $text, $matches);
    foreach($matches[0] as $key=>$value)
    {
        $these_words = explode(' ', $value);
        $these_words = k_text_list_clean($these_words, true, false, $min);
        if(count($these_words) !== 1) $matches[0][$key] = '';
    }
    $words[1] = k_text_list_clean($matches[0], true, false);
    foreach($words[1] as $key=>$value)
    {
        $clean_words1[$value] = $clean_words1[$value] + 1;
    }
    
    // Get 2-word matches
    preg_match_all('/ ([a-z]*?) ([a-z]*?) /', $text, $matches);
    foreach($matches[0] as $key=>$value)
    {
        $these_words = explode(' ', $value);
        $these_words = k_text_list_clean($these_words, true, false, $min);
        if(count($these_words) !== 2) $matches[0][$key] = '';
    }
    $words[2] = k_text_list_clean($matches[0], true, false);
    foreach($words[2] as $key=>$value)
    {
        $clean_words2[$value] = $clean_words2[$value] + 1;
    }
    
    // Get 3-word matches
    preg_match_all('/ ([a-z]*?) ([a-z]*?) ([a-z]*?) /', $text, $matches);
    foreach($matches[0] as $key=>$value)
    {
        $these_words = explode(' ', $value);
        $these_words = k_text_list_clean($these_words, true, false, $min);
        if(count($these_words) !== 3) $matches[0][$key] = '';
    }
    $words[3] = k_text_list_clean($matches[0], true, false);
    foreach($words[3] as $key=>$value)
    {
        $clean_words3[$value] = $clean_words3[$value] + 1;
    }

    krsort($clean_words1);
    krsort($clean_words2);
    krsort($clean_words3);

    arsort($clean_words1);
    arsort($clean_words2);
    arsort($clean_words3);

    return array(1 => $clean_words1, 2 => $clean_words2, 3 => $clean_words3);    
}

function k_text_striphtml($html, $spaces = true, $newlines = true, $lower = true, $special = true, $num = true)
{
    $text = $html;
    
    // Get everything from body first
    $bodystart = strpos($text, '<body') + 5;
    $bodyend = strpos($text, '</body');
    
    if($bodystart && $bodyend)
    {
        $text = substr($text, $bodystart, ($bodyend-$bodystart));
    }
    
    $text = html_entity_decode($text);
    $text = strip_tags($text);
    
    if($newlines)
    {
        $text = str_replace("\n", " ", $text);
        $text = str_replace("\r", " ", $text);
    }
    if($spaces)
    {
        $text = ereg_replace("[ ]{2,}", " ", $text);
    }
    if($special)
    {
        $text = preg_replace('/[^A-Za-z0-9 ]/', '', $text);
    }
    if($num)
    {
        $text = preg_replace('/[0-9]/', '', $text);
    }
    if($lower)
    {
        $text = strtolower($text);
    }
    return $text;
}

function k_text_list_clean($list, $empty = true, $duplicate = true, $min = false)
{

    if($duplicate)
    {
        $list = array_unique($list);
    }
    
    if($empty)
    {
        foreach($list as $key => $value)
        {
            if((!$min) || (($min) && (strlen($value) >= $min)))
            {
                $value = trim($value);
                $value = ereg_replace("[ ]{2,}", " ", $value);
                if(!empty($value))
                {
                    $clean_list[] = $value;
                }
            }
        }
        return $clean_list;
    }
    else
    {
        return $list;
    }
    
}


function k_text_encrypt($s, $sep = '.')
{
    // Shift the character code up 1
    $s = strtolower($s);
    for($i=0;$i<strlen($s);$i++)
    {
        $r[] = chr(ord($s[$i]) + 1);
    }
    $chrstr = implode('', $r);
    $newstr = $chrstr;
    return $newstr;
}
 
function k_text_decrypt($s, $sep = '.')
{
//    $s = explode($sep, $s);
    for($i=0;$i<strlen($s);$i++)
    {
        $s[$i] = chr(ord($s[$i]) - 1);
    }
    return $s;
}



?>
