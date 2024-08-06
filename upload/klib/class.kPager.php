<?php
/**
 * class.kPager.php - Kytoo Data Pager Component
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
 * 2009-05-01 [1.0] - Initial release with Kytoo 2.0. 
 *
*/

class kPager extends kBase
{
	
	var $offset = 0;
	var $count = 0;
	var $orderby = '';
	var $order = '';
	var $max;
    
	var $html_first = '|<<';
	var $html_prev = '<<';
	var $html_next = '>>';
	var $html_last = '>>|';
	
	function kPager($base_url, $offset, $count, $max, $orderby, $order, $link_class)
	{
		$this->max = $max;
		$this->offset = $offset;
		$this->count = $count;
		$this->orderby = $orderby;
		$this->order = $order;
		$this->link_class = $link_class;
		
		if(eregi('[?]', $base_url))
		{
			$this->base_url = $base_url . '&';
		}
		else 
		{
			$this->base_url = $base_url . '?';			
		}

	}
	
	
	function get_html()
	{
		if($this->offset > 0)
		{
			$html = '<a class="' . $this->link_class . '" href="' . $this->base_url . 'offset=0&count=' . $this->count .
					'&orderby=' . $this->orderby . '&order=' . $this->order . '&search=0">' .
					$this->html_first . '</a>&nbsp; ';
		}
		else 
		{
			$html = $this->html_first . '&nbsp; ';
		}

		if($this->offset - $this->count > 0)
		{
			$html .= '<a class="' . $this->link_class . '" href="' . $this->base_url . 'offset=' . ($this->offset - $this->count) . 
			    	'&count=' . $this->count . '&orderby=' . $this->orderby . '&order=' . $this->order .
			    	'&search=0">' . $this->html_prev . '</a>&nbsp; ';
		}
		else 
		{
			$html .= $this->html_prev . '&nbsp; ';
		}
		
		$viewing = '&nbsp;' . $this->offset . ' to ';
		$viewing .= ($this->max > ($this->offset + $this->count)) ? ($this->offset + $this->count) : $this->max;
		$viewing .= '&nbsp;';
        $viewing .= 'of ' . $this->max . ' ';
		
		$html .= $viewing;
		$this->viewing = 'View records ' . $viewing;
		
		if($this->offset + $this->count < $this->max)
		{
			$html .= '<a class="' . $this->link_class . '" href="' . $this->base_url . 'offset=' . ($this->offset + $this->count) . 
				    '&count=' . $this->count . '&orderby=' . $this->orderby . '&order=' . $this->order .
			    	'&search=0">' . $this->html_next . '</a>&nbsp; ';

			$html .= '<a class="' . $this->link_class . '" href="' . $this->base_url . 'offset=' . ($this->max - $this->count) . 
				    '&count=' . $this->count . '&orderby=' . $this->orderby . '&order=' . $this->order .
			    	'&search=0">' . $this->html_last . '</a>';
		}
		else 
		{
			$html .= $this->html_next . '&nbsp; ';
			$html .= $this->html_last . ' ';
		}

		return $html;    
	}

}

/* end class */

?>
