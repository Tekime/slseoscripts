<?php
/**
 * class.kDecaptcha.php - Kytoo CAPTCHA Decoder
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
 * 2009-05-01 (1.1) - Class now extends kBase. Updated for Kytoo 2.0.  
 *
*/

class kDecaptcha extends kBase
{
    
    function kDecaptcha()
    {
        return true;  
    }

    function decode($url)
    {
        $charset = 'abcdef0123456789';
        $ch_fonts = array(4 => -1, 5 => 0);
        $ch_box = 10;
        $path_tmp = PATH_TMP;
        $tmp_img = $path_tmp . 'cr' . $c_id . '.png';
        
        $cr_img = imagecreatefrompng($url);  
        imagepng($cr_img, $tmp_img);
        
        // Get image details
        list($w, $h, $type, $attr) = @getimagesize($tmp_img);
        // convert to 32-bit and copy image
        $c_img = imagecreatetruecolor($w,$h);
        // copy remote image contents to source
        imagecopy($c_img,$cr_img,0,0,0,0,$w,$h);
        imagedestroy($cr_img);
        
        
        // generate charset letter maps
        $ch_img_tmp = imagecreatetruecolor($ch_box,$ch_box);
        $black = imagecolorallocate($ch_img_tmp, 0, 0, 0);
        $white = imagecolorallocate($ch_img_tmp, 255, 255, 255);
        
        // character index id
        $ci = 0;
        foreach($ch_fonts as $ch_size=>$ch_left)
        {
            for($i=0;$i<strlen($charset);$i++)
            {
                // add new character to character index
                $chars[$ci]['char'] = $charset[$i];
        
                imagefilledrectangle($ch_img_tmp, 0, 0, $ch_box, $ch_box, $white);
                imagestring($ch_img_tmp, $ch_size, 0, -3, $charset[$i], $black);
        
                $atrow = false;
                for($x=0;$x<$ch_box;$x++)
                {
                    $atchar = false;
                    for($y=0;$y<$ch_box;$y++)
                    {
                        $rgb = imagecolorat($ch_img_tmp, $x, $y);
                        $r = ($rgb >> 16) & 0xFF;
                        $g = ($rgb >> 8) & 0xFF;
                        $b = $rgb & 0xFF;
                
                        if(($r == 0) && ($g == 0) && ($b == 0))
                        {
                            // plot paint to character map
                            $chars[$ci]['ycoords'][$y][$x] = 1;
                            $atchar = true;
                        }
                        else
                        {
                            // plot blank to character map
                            $chars[$ci]['ycoords'][$y][$x] = 0;
                        }
                    }
                }
        
                // Trim empty rows
                $haspaint = false;
                $ycoords = array();
                $xcoords = array();
        
                foreach($chars[$ci]['ycoords'] as $y=>$xk)
                {
                    if((array_search(1, $xk)) !== false) $haspaint = true;
        
                    if($haspaint == true)
                    {
                        $bits = array();
                        foreach($xk as $x=>$bit)
                        {
                            $bits[] = $bit;
                        }
                        $ycoords[] = $bits; 
                    }
                }
                $chars[$ci]['ycoords'] = $ycoords;
        
                // Set xcoords
                foreach($ycoords as $y=>$xk)
                {
                    foreach($xk as $x=>$bit)
                    {
                        $chars[$ci]['xcoords'][$x][$y] = $bit;
                    }
                }
                
                // Trim empty columns
                $haspaint = false;
                $ycoords = array();
        
                foreach($chars[$ci]['xcoords'] as $x=>$yk)
                {
                    if((array_search(1, $yk)) !== false) $haspaint = true;
        
                    if($haspaint == true)
                    {
                        $bits = array();
                        foreach($yk as $y=>$bit)
                        {
                            $bits[] = $bit;
                        }
                        $xcoords[] = $bits; 
                    }
                }
                $chars[$ci]['xcoords'] = $xcoords;
        
                // Set ycoords *again*
                foreach($xcoords as $x=>$yk)
                {
                    foreach($yk as $y=>$bit)
                    {
                        $chars[$ci]['ycoords'][$y][$x] = $bit;
                    }
                }
                $ci++;
            }
            
        }
        
        
        // create formatted image
        $f_img = imagecreatetruecolor($w, $h);
        // allocate new image colors
        $white = imagecolorallocate($f_img, 255, 255, 255);
        $black = imagecolorallocate($f_img, 0, 0, 0);
        
        // scan and clean the image
        for($x=0;$x<$w;$x++)
        {
            $colpaint = false;
            for($y=0;$y<$h;$y++)
            {
                $rgb = imagecolorat($c_img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
        
                if(($r == 0) && ($g == 0) && ($b == 0))
                {
                    $c_map[$x][$y] = 1;
        
                    $c_cols[$x] = 1;
                    
                    imagesetpixel($f_img, $x, $y, $black);
                }
                else
                {
                    $c_map[$x][$y] = 0;
        
                    imagesetpixel($f_img, $x, $y, $white);
                }       
            }
            
            
        }
        
        // Split objects by column
        $objects = array();
        $obj = 0;
        foreach($c_map as $x=>$col)
        {
            if(($c_cols[$x] === 1) && ($c_cols[$x-1] !== 1))
            {
                // start object
                $objects[$obj]['x1'] = $x;
            }
            elseif(($c_cols[$x] === 1) && ($c_cols[$x+1] !== 1))
            {
                // end object
                $objects[$obj]['x2'] = $x;
                $obj++;
            }
        }
        
        foreach($objects as $k=>$obj)
        {
            for($y=0;$y<$h;$y++)
            {
                for($x=$obj['x1'];$x<=$obj['x2'];$x++)
                {
                    if($c_map[$x][$y] == 1)
                    {
                        $c_rows[$k][$y] = 1;
                    }
                }
            }
        }
        
        foreach($objects as $k=>$obj)
        {
            for($y=0;$y<$h;$y++)
            {
                if(($c_rows[$k][$y] === 1) && ($c_rows[$k][$y-1] !== 1))
                {
                    // start row
                    $objects[$k]['y1'] = $y;
                }
                elseif(($c_rows[$k][$y] === 1) && ($c_rows[$k][$y+1] !== 1))
                {
                    // end row
                    $objects[$k]['y2'] = $y;
                    $obj++;
                }
            }
        }
        
        foreach($objects as $k=>$obj)
        {
            $objects[$k]['w'] = ($obj['x2'] - $obj['x1']) + 1;
            $objects[$k]['h'] = ($obj['y2'] - $obj['y1']) + 1;
        
            for($xi=0;$xi<$objects[$k]['w'];$xi++)
            {
                for($yi=0;$yi<$objects[$k]['h'];$yi++)
                {
                    $objects[$k]['xmap'][$xi][$yi] = $c_map[$obj['x1']+$xi][$obj['y1']+$yi];
                    $objects[$k]['ymap'][$yi][$xi] = $c_map[$obj['x1']+$xi][$obj['y1']+$yi];
                }
            }
        }
        
        foreach($objects as $k=>$obj)
        {
            $guess = 0;
            
            // loop through each letter/character
            $lowest = 999;
            $highest = 0;
            foreach($chars as $cid=>$char)
            {
                $errors = 0;
                $thisx = 0;
                
                foreach($obj['ymap'] as $y=>$yv)
                {
                    foreach($yv as $x=>$bit)
                    {
                        if($chars[$cid]['ycoords'][$y][$x] !== $bit)
                        {
                            $errors++;
                        }
                    }      
                }
                
                if($errors < $lowest) {
                    $lowest = $errors;
                    $guess = $cid;
                }
                elseif($errors > $highest)
                {
                    $highest = $errors;
                }
            }
            $objects[$k]['guess_cid'] = $guess;
            $objects[$k]['guess'] = $chars[$guess]['char'];
            $objects[$k]['errors'] = $lowest;
            $objects[$k]['cid_errors'][$cid] = $errors;
            $objects[$k]['highest'] = $highest;
            $objects[$k]['accuracy'] = 100 - $objects[$k]['errors'] . '%';
        }
        
        $return = '';    
        foreach($objects as $k=>$obj)
        {
            $return .= $obj['guess'];
        }
        
        return  $return;
    }    
    
}

?>
