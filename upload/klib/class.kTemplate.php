<?php
/**
 * class.kTemplate.php - Kytoo Dynamic Template Component
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     3.1
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
 * 2009-05-20 (3.1) - Minor update
 * 2009-04-23 (3.0) - Updated for Kytoo 2.0  
 * (2.8.2)
 *
*/

class kTemplate extends kBase
{

    /**
     * @access  public
     * @var     array $paths            List of paths where template files are located
    */
    var $paths = array();
    /**
     * @access  public
     * @var     array $vars             List of global variables and values
    */
    var $vars = array();
    /**
     * @access  public
     * @var     array $conditions       List of conditions that can be met
    */
    var $conditions = array();
    /**
     * @access  public
     * @var     array $conditionals     List of found conditionals
    */
    var $conditionals = array();
    /**
     * @access  public
     * @var     string $content         The final output buffer for all rendered content
    */
    var $content;
    /**
     * @access  public
     * @var     array $nodes            List of all nodes in the entire node tree used for final rendering
    */
    var $nodes = array();
    /**
     * @access  public
     * @var     array $blocks           Array of all blocks defined from template files
    */    
    var $blocks = array();
    /**
     * @access  public
     * @var     string $layout          Name of the layout to use, false for default
    */    
    var $layout = false;
    /**
     * @access  public
     * @var     bool $auto_parse        Replace unassigned variables in template files with empty string?
    */
    var $auto_parse = 1;
    /**
     * @access  public
     * @var     bool $parse_widgets     Parse widgets
    */
    var $parse_widgets = true;
    /**
     * @access  public
     * @var     bool $recursive_paths   Search all subdirectories for templates?
    */
    var $recursive_paths = 0;   
    /**
     * @access  public
     * @var     bool $strip_comments    Replace all comments with empty strings to conserve bandwidth?
    */
    var $strip_comments = 0;
    /**
     * @access  public
     * @var     bool $strip_whitespace  Remove all whitespace and newlines to conserve bandwidth?
    */
    var $strip_whitespace = 0;
    /**
     * @access  public
     * @var     string $tag_block_begin     Start tag identifier for dynamic blocks
    */
    var $tag_block_begin = 'BEGIN BLOCK:';
    /**
     * @access  public
     * @var     string $tag_block_end       End tag identifier for dynamic blocks
    */
    var $tag_block_end = 'END BLOCK:';
    /**
     * @access  public
     * @var     string $tag_block_begin     Start tag identifier for dynamic blocks
    */
    var $tag_condition_begin = 'BEGIN CONDITION:';
    /**
     * @access  public
     * @var     string $tag_block_end       End tag identifier for dynamic blocks
    */
    var $tag_condition_end = 'END CONDITION:';

    var $pathnames = array();
    var $REGEX_EMBED = '/(<!--\s*BEGIN\s+DBLOCK:\s*([A-Za-z][-_A-Za-z0-9.]+)(\s*|\s+.*?)-->)/';    
    var $REGEX_DYNBEG = '/(<!--\s*BEGIN\s+DBLOCK:\s*([A-Za-z][-_A-Za-z0-9.]+)(\s*|\s+.*?)-->)/';
    var $REGEX_DYNEND = '/(<!--\s*END\s+DBLOCK:\s*([A-Za-z][-_A-Za-z0-9.]+)(\s*|\s+.*?)-->)/';
    var $REGEX_VAR = '/\{[A-Za-z][-_A-Za-z0-9]*\}/';
    
    function kTemplate()
    {
        $this->nodes = array();
        $this->nodes[0]['parent'] = -1;
    }

    /**
     * add_path() - Adds a template path
     *
     * Adds an absolute path to the list of locations where we are keeping our template files.
     *
     * @access      public
     * @param       string $path Absolute path
     * @return      bool
    */
    function add_path($path)
    {
        if(is_dir($path))
        {       
            $this->paths[] = $this->get_file_tree($path, 'tpl');
            return true;
        }
        else
        {
        	return false;	
        }
    } // end add_path()


    /**
     * assign() - Assigns a global variable
     *
     * Adds a key/value to the global template variables collection, which
     * will be parsed on all templates as the last step before rendering. 
     *
     * @access      public
     * @param       string $var_name    Variable name
     * @param       string $value       Variable value
     * @return      bool
    */
    function assign($var_name, $value)
    {    
        if(isset($value))
        {
        	$this->vars[$var_name] = $value;    
        }
    } // end assign()


    /**
     * assign_array() - Assigns an array of variables
     *
     * Assigns an array of key/value pairs as new global variables using assign(). Each key is a variable name.
     *
     * @access      public
     * @param       array $array       An associative array of variables to assign
     * @return      bool
    */
    function assign_array($array, $stripslashes = 0)
    {
        foreach($array as $key=>$value)
        {
            if(!is_array($value))
            {
                $this->assign($key, ($stripslashes == 1) ? stripslashes($value) : $value);
            }
        }
    } // end assign_array()

    /**
     * assign_array_d() - Assigns an array of variables
     *
     * Assigns an array of key/value pairs as new global variables using assign(). Each key is a variable name.
     *
     * @access      public
     * @param       array $array       An associative array of variables to assign
     * @return      bool
    */
    function assign_array_d($blockname, $array, $stripslashes = 0)
    {
        foreach($array as $key=>$value)
        {
            if(isset($value))
            {
            	$this->blocks[$blockname]['vars_c'][$key] = ($stripslashes == 1) ? stripslashes($value) : $value;
            }
        }
    } // end assign_array()
    
    /**
     * assign_d() - Assigns a variables to a dynamic block
     *
     * Adds a key/value to the current instance of the specified dynamic block row.
     *
     * @access      public
     * @param       array $array       An associative array of variables to assign
     * @return      bool
    */    
    function assign_d($blockname, $var_name, $value)
    {        
        if(isset($value))
        {
        	$this->blocks[$blockname]['vars_c'][$var_name] = $value;
        }
    } // end assign_d()

    
    
    /**
     * create_node() - Creates a new node on the node tree
     *
     * Creates a new node on the node tree with a block name, parent ID, and optional variable fields.
     *
     * @access      public
     * @param       int     $parent_id      ID of parent node
     * @param       string  $blockname      Block name
     * @param       array [$fields]       Variable field names
     * @return      bool
    */
    function create_node($parent_id, $blockname, $fields = '')
    {
        $node_max = sizeof($this->nodes) > 1 ? sizeof($this->nodes) : 1;

        $this->nodes[$node_max]['parent'] = $parent_id;
        $this->nodes[$node_max]['blockname'] = $blockname;
        $this->nodes[$node_max]['fields'] = $fields;

        return true;
    } // end create_node()

    
    /**
     * define() - Defines a new block
     *
     * Defines a new block from either a filename or a string. Set the optional $is_string parameter to 1
     * in order to have $input read as a string instead of a filename.
     *
     * @access      public
     * @param       string  $blockname      Block name
     * @param       string  $input          Contains either a filename (default) or a string (optional)
     * @param       int     [$is_string]    Set to 1 to read input as a string instead of a filename     
     * @return      bool
    */
    function define($blockname, $input, $is_string = 0)
    { 
        if($is_string)
        {
            if(!empty($input))
            {
                $this->blocks[$blockname]['content'] = $input;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $this->blocks[$blockname]['filename'] = $input;
            $this->blocks[$blockname]['content'] = $this->read_file($input);
        }

        if(isset($this->blocks[$blockname]['content']))
        {
            $this->blocks[$blockname]['active'] = true;
            $this->blocks[$blockname]['parent'] = "_TOP";

            return true;
        }
        else
        {
            $this->errors[] = 'Error loading template block: ' . $blockname;
            return false;
        }
    } // end define()
    
    
    /**
     * define_d() - Defines a dynamic block
     *
     * Defines part of an existing block contents as a new dynamic block.
     *
     * @access      public
     * @param       string  $blockname      Block name
     * @param       string  $parent         Parent block name
     * @return      bool
    */
    function define_d($blockname, $parent)
    {

        // Make sure the parent block exists
        if(isset($this->blocks[$parent]['active']))
        {
            $this->blocks[$blockname]['parent'] = $parent;

            // Set up the begin and end tag format for a dynamic block
            $str_begin_b = "<!-- " . $this->tag_block_begin . " " . $blockname . " -->";
            $str_end_b = "<!-- " . $this->tag_block_end . " " . $blockname . " -->";            

            // Extract the block contents from the parent block
            $str_content_b = $this->str_extract($this->blocks[$parent]['content'], $str_begin_b, $str_end_b);

            if(isset($str_content_b))
            {
                $this->blocks[$blockname]['content'] = $str_content_b;

                // Replace block's contents with a tag in the parent block
                $regex = '/(<!--\s*BEGIN\s+BLOCK:\s*' . $blockname . 
                         ' -->[^?]*<!--\s*END\s+BLOCK:\s*' . $blockname . ' -->)/';
                         
                $this->blocks[$parent]['content'] = 
                    preg_replace($regex, '<!-- BLOCK: ' . $blockname . ' -->', str_replace('?', '&#63;',$this->blocks[$parent]['content']));

                $this->blocks[$blockname]['active'] = true;
                
                return true;                
            }
            else
            {
                unset($this->blocks[$blockname]);
                $this->errors[] = "Error in define_d, couldn't fetch dynamic block contents";
                return false;
            }
            
            return true;
        }
        else
        {
            $this->errors[] = "Error in define_d, parent block not defined or inactive";
            return false;
        }
    } // end define_d()


    /**
     * array get_file_list()
     *     
     * Returns a list of files in a directory.
     *
     * @access      public
     * @param       string  $blockname      Block name
     * @param       string  $parent         Parent block name
     * @return      bool
    */
    function get_file_list($path)
    {
        
        $d = dir($path);
        while (false !== ($entry = $d->read())) {
            if(substr($entry,0,1) != '.') $templates[] = $entry;
        }
        $d->close();
        return $templates;
    } // end get_file_list()

       
    /**
     * get_file_tree() - Gets a recursive list of files
     *
     * Recursively collects a file listing from a pathname and creates an array.
     *
     * @access      public
     * @param       string  $path       Path name
     * @param       string  $ext        File extension to filter on
     * @return      array
    */
    function get_file_tree($path, $ext = 0)
    {
        if(empty($list)) $list = array();
    
        $handle = opendir($path);
        while($a = readdir($handle))
        {
             if(!preg_match('/^\./',$a))
             {
                 $full_path = $path . $a . '/';
                 if(($ext !== 0) && (preg_match('/\.' . $ext . '$/', $a)))
                 {
                     $list[$a] = $path;
                 }
                 if((is_dir($full_path)) &&(!preg_match('/(\/.+){20,}/',$full_path)) && ($this->recursive_paths == 1))
                 {
                     $recursive = $this->get_file_tree($full_path, $ext);
                     foreach($recursive as $key=>$value) {
                           $list[$key] = $value;
                     }
                 }
             }
         }
         closedir($handle);

         return $list;
    } // end get_file_tree()
    
    
    /**
     * get_last_block_node() - Get the last block on the node tree
     *
     * Gets the most recently defined block ID of the last block specified in the node tree.
     *
     * @access      public
     * @param       string  $blockname      Block name
     * @return      int
    */
    function get_last_block_node($blockname)
    {
        // Get the total number of nodes (starting with zero)
        $node_max = sizeof($this->nodes) == 0 ? 0 : sizeof($this->nodes)-1;
        
        // Cycle backwards through each node and find the last instance of the specified blockname
        for($i=$node_max; $i>=0; $i--)
        {
            // If we find a matching blockname we return the current node ID
            if(isset($this->nodes[$i]['blockname']) && $this->nodes[$i]['blockname'] == $blockname) return $i;
        }
        
        return false;
        
    } // end get_last_block_node()
    
    
    
    /**
     * inject() - Injects a block
     *
     * Inserts a node into the node tree after the last occurrance of the specified blockname.
     *
     * @access      public
     * @param       string  $blockname      Block name
     * @param       string  $target         Target block name
     * @return      bool
     */
    function inject($blockname, $target)
    {
        // Get the node ID for the last block specified by $blockname
        if($parent_id = $this->get_last_block_node($this->blocks[$target]['parent']))
        {
            // Create a new node after the ID we retrieved
            $this->create_node($parent_id, $blockname, $this->blocks[$blockname]['vars_c']);
            
            // Unset the specified blocks variables... unsure if this is required anymore. :/
            unset($this->blocks[$blockname]['vars_c']);
            
            return true;
        }
        else
        {
            $this->errors[] = 'No matching node found for "' . $blockname . '" in $template->get_last_block_node()';
            return false;
        }
        
    } // end inject()
    
    
    /**
     * is_set() - Check if a global variable has been defined
     *
     * Determines whether the specified variable name has been specified in the global variables array.
     *
     * @access      public
     * @param       string  $var        Variable name
     * @return      bool
     */
    function is_set($var)
    {
        // Check the global variable array for a non-empty value
        if((array_key_exists($var, $this->vars)) && (!empty($this->vars[$var])))
        {
            return true;
        }
        else
        {
            return false;
        }
    } // end is_set()
    
        
    /**
     * parse() - Adds a block to the node tree
     *
     * Parses (creates) the specified blockname in the node tree.
     *
     * @access      public
     * @param       string  $blockname      Block name
     * @return      bool
     */
    function parse($blockname)
    {
        // Try to create a new node on the node map for the specified block
        if($this->create_node(0, $blockname))
        {
            // Look for any conditional blocks in the contents
            if(strpos($this->blocks[$blockname]['content'],'<!-- ' . $this->tag_condition_begin))
            {
                // Try to get the title of the condition
                if($str_content_title = $this->str_extract($this->blocks[$blockname]['content'], '<!-- ' . $this->tag_condition_begin . ' ', ' -->'))
                {
                    // Set up the begin and end tag format for a conditional block
                    $str_begin_b = "<!-- " . $this->tag_condition_begin . " " . $str_content_title . " -->";
                    $str_end_b = "<!-- " . $this->tag_condition_end . " " . $str_content_title . " -->";
    
                    // Extract the conditional contents from the block
                    $str_content_b = $this->str_extract($this->blocks[$blockname]['content'], $str_begin_b, $str_end_b);
        
                    if(isset($str_content_b))
                    {
                        // If we got the contents, add this instance of the conditional to the conditionals list
                        // and obtain the next index # for like-named conditionals.
                        if(isset($this->conditionals[$str_content_title]))
                        {
                            $index_b = sizeof($this->conditionals[$str_content_title]);
                        }
                        else 
                        {
                            $index_b = 0;
                        }
                        
                        $str_block_title = $str_content_title . $index_b;
                        $this->conditionals[$str_content_title][$index_b] = $str_block_title;

                        // Replace block's contents with a tag in the parent block
                        $regex = '/(<!--\s*BEGIN\s+CONDITION:\s*' . $str_content_title . 
                                 ' -->[^?]*<!--\s*END\s+CONDITION:\s*' . $str_content_title . ' -->)/';
                        
                        // Replace the conditional with a BLOCK tag in the parent's contents
                        $this->blocks[$blockname]['content'] = 
                            preg_replace($regex, '<!-- BLOCK: ' . $str_block_title . ' -->', str_replace('?', '&#63;',$this->blocks[$blockname]['content']));
                        
                        // Create a new block using the name generated from the conditional ID
                        $this->blocks[$str_block_title]['active'] = true;                       
                        $this->blocks[$str_block_title]['content'] = $str_content_b;
                        $this->blocks[$str_block_title]['parent'] = $blockname;
                        
                        return true;                
                    }
                }
                else
                {
                    unset($this->blocks[$blockname]);
                    $this->errors[] = "Error in define_d, couldn't fetch dynamic block contents";
                    return false;
                }
            }
            return true;   
        }
        else 
        {
            return false;
        }
    } // end parse()  

    
    /**
     * bool parse_d(string $blockname)
     *
     * Parses (creates) a new node on the node tree for the specified dynamic block.
     *
     * @access      public
     * @param       string  $blockname      Block name
     * @return      bool
     */
    function parse_d($blockname) {

        // Get the node ID for the last block parsed in the specified block's parent
        if(!$this->blocks[$blockname]['parent']) die('$template::parse_d ( ' . $blockname . ' )');
        
        if($parent_id = $this->get_last_block_node($this->blocks[$blockname]['parent']))
        {

            // Create a new child node for the node we just got
            if(!isset($this->blocks[$blockname]['vars_c']))
            {
                $this->blocks[$blockname]['vars_c'] = '';
            }
            $this->create_node($parent_id, $blockname, $this->blocks[$blockname]['vars_c']);
    
            // Clear the current instance of this dynamic block once written
            unset($this->blocks[$blockname]['vars_c']);
            return true;
        }
        else 
        {
            die('Error seeking last block node in template::parse_d ( ' . $blockname . ' )');
        }
    } // end parse_d()

    
    /**
     * read_file() - Reads a file and returns the contents
     *
     * Retrieve and return the contents of the specified file from disk.
     *
     * @access      public
     * @param       string  $filename      Path to filename
     * @return      string
     */
    function read_file($filename) {

    	if(isset($this->paths))
    	{
	        reset($this->paths);
	        // Loop through all template directories
	        foreach($this->paths as $k=>$v)
	        {
		        if(!empty($this->paths[$k][$filename]))
		        {        
		            $filename = $this->paths[$k][$filename] . $filename;
		
		            if(file_exists($filename))
		            {
		                $fhandle = fopen($filename, "r");
		                if(filesize($filename) > 0)
		                    $fcontent = fread ($fhandle, filesize($filename));
		                fclose($fhandle);
		        
		                return $fcontent;    
		            }
		        }
	        }
        }
    } // end read_file()

    
    /**
     * render_all() - Renders and displays the node tree
     *
     * Renders and displays the final node tree. render_all() first renders the parent node, then parses the current
     * output buffer for all global variables and additional operations as specified by the class parameters.
     *
     * @access      public
     * @return      bool
    */            
    function render_all($return = 0)
    {

        // Loop through all of the conditionals found in the templates
        foreach($this->conditionals as $key=>$value)
        {
            if((!empty($this->conditions[$key])) && ($this->conditions[$key] === 1))
            {
                if((is_array($this->conditionals[$key])))
                {
                    foreach($this->conditionals[$key] as $k=>$v)
                    {
                        $this->parse_d($v);
                    }   
                }

            }
        }
        $this->contents = '';
        $this->render_node(0); // Recursively render the entire node tree from the root 

        // Extract all NOPARSE regions and replace with indexed tags.
        // Inject extracted content after parsing.        
        $c_len = strlen($this->contents);
        $tagstart = '<!-- NOPARSE -->';
        $tagend = '<!-- END NOPARSE -->';
        $index = $pos = 0;
        $startlen = strlen($tagstart);
        $endlen = strlen($tagend);
        while($startpos = strpos($this->contents, $tagstart, $pos))
        {
            if($endpos = strpos($this->contents, $tagend, $startpos))
            {
                $noparse[$index] = substr($this->contents, $startpos, ($endpos - $startpos + $endlen));
                $this->contents = substr($this->contents, 0, $startpos) .
                                  '<!-- PARSER' . $index . ' -->' .
                                  substr($this->contents, ($endpos + $endlen));
                $index++;
                if($index > 500) break; // Obviously outta control here
            }
            else
            {
                // Broken tag - get out while we can!
                break;
            }
        }

        foreach($this->vars as $varname=>$value)
        {
            // Replace all matching variables from global fields in output buffer contents
            $this->contents = preg_replace("'\{".$varname."\}'si", str_replace("$","\\$",$value), $this->contents);
        }

        // Parse template functions
        if($this->parse_widgets)
        {
            // Set up regexp's
            $widget_reg = '/\{([A-Za-z0-9].*?)\(([^\}].*?)?\)\}/';

            // Check for template functions and parse
            preg_match_all($widget_reg, $this->contents, $matches_r);

            if((!empty($matches_r[0][0])) && (!empty($matches_r[1][0])))
            {

                for($i=0;$i<count($matches_r[1]);$i++)
                {
                    $widget_name = $widget_paramstr = '';
                    $widget_params = array();
                    
                    if(!empty($matches_r[1][$i]))
                    {
                        $widget_name = $matches_r[1][$i];
                        if(!empty($matches_r[2][$i]))
                        {
                            $widget_paramstr = $matches_r[2][$i];
                            $widget_params = k_explode_paramstr(',', $widget_paramstr);

                            foreach($widget_params as $key => $value)
                            {
                                // Make boolean
                                if($value == 'false') $widget_params[$key] = false;
                                if($value == 'true') $widget_params[$key] = true;
                            }

                            //$widget_params = explode(',', $widget_paramstr);
                        }
                        $widget_html = (function_exists('widget_' . $widget_name)) ? widgetLoad($widget_name, $widget_params) : '';
                        $this->contents = str_replace('{' . $widget_name . '(' . $widget_paramstr . ')}', $widget_html, $this->contents);
                    }
                }
            }  
        }

        // Run again to catch var's from var's
        // ** Change to replace in check vars only first (faster) 
        foreach($this->vars as $varname=>$value)
        {
            // Replace all matching variables from global fields in output buffer contents
            $this->contents = preg_replace("'\{".$varname."\}'si", str_replace("$","\\$",$value), $this->contents);
        }        
        // Automatically parse all variables
        if($this->auto_parse === 1)
        {
        	$this->contents = preg_replace('/({)[A-Za-z0-9_]*(})/e', '', $this->contents);
        }
        if((!empty($noparse)) && (is_array($noparse)))
        {
            foreach($noparse as $key => $value)
            {
                $this->contents = str_replace('<!-- PARSER' . $key . ' -->', $value, $this->contents);
            }
        }        
        
        // Automatically replace all comments
        // Buggy - stil strips too much in some cases
        if($this->strip_comments === 1)
        {
        	$this->contents = preg_replace('/(<!--)(.*)(-->)/e', '', $this->contents);
        }
        // Automatically strip all whitespace
        if($this->strip_whitespace === 1)
        {
        	$this->contents = str_replace("\n", " ", $this->contents);
        	$this->contents = eregi_replace(" +", " ", $this->contents);
        }
        
        $this->contents = str_replace('&#63;', '?', $this->contents);
        
        // Print the final output to the client
        if($return)
        {
            return trim($this->contents);
        }
        else 
        {
            echo trim($this->contents);
        }         
    } // end render_all()
    
    
    /**
     * render_node() - Renders a node and all of its children
     *
     * Recursively parses through all children of the specified node ID and renders
     * them (adds them to the output "buffer" variable $this->contents).
     *
     * @access      public
     * @param       int  $node_id      Node ID
     * @return      bool
    */
    function render_node($node_id) {
    
        // Check for top-level block
        if($this->nodes[$node_id]['parent'] == 0) 
        {
            // Render current node content to output buffer
            if(!empty($this->blocks[$this->nodes[$node_id]['blockname']]['content']))
            {
                $content_r = $this->blocks[$this->nodes[$node_id]['blockname']]['content'];

            	$this->contents .= $content_r;
            }
            else 
            {
            	$this->contents = ' ';
            }
        }
        else
        // Current node is a dynamic block 
        {
            
            if(isset($this->nodes[$node_id]['blockname']))
            {
                // Obtain the row template
                $content_r = $this->blocks[$this->nodes[$node_id]['blockname']]['content'];


                // Check for fields in the current node
                if(is_array($this->nodes[$node_id]['fields']))
                {
                    // Loop through fields                    
                    foreach($this->nodes[$node_id]['fields'] as $index_c=>$value_c)
                    {
                        // Validate current value as usable
                        if(!is_array($value_c))
                        {
                            // Replace all matching fields in the template contents
                            $content_r = preg_replace('/\{'.$index_c.'\}/', str_replace("$","\\$",$value_c), $content_r);
                        }
                    }
                    // Flag this node as parsed
//                    $this->nodes[$node_id]['fields'][$index_r]['_is_parsed'] = "true";
                }
                
                // Define template block identifier for current node
                $str_block_tag = "<!-- BLOCK: " . $this->nodes[$node_id]['blockname'] . " -->";

                // Find the position of the last occurance of the block identifier            
                $lastpos = $this->strrpos_mb($this->contents, $str_block_tag);

                // Render the parsed block to the output buffer
                $this->contents = substr_replace($this->contents, $content_r.$str_block_tag, $lastpos, strlen($str_block_tag));
            }
        }

        $node_max = sizeof($this->nodes); // Get total number of nodes
        
        // Loop through every node
        for($index=1; $index < $node_max; $index++)
        {
            // Check for parent matching current node
            if($this->nodes[$index]['parent'] == $node_id)
            {                
                $this->render_node($index); // Render child node recursively
            }
        }
                
        return true;        
    } // end render_node()

   
    
    /**
     * replace_var() - Replaces a variable within a string
     *
     * Replaces a variable using the replacement format and returns the formatted string.
     *
     * @access      public
     * @param       string  $search     Substring to find
     * @param       string  $replace    Substring to replace with
     * @param       string  $string     String to search
     * @return      string
    */    
    function replace_var($search, $replace, $string)
    {
        return preg_replace("'\{".$search."\}'si", str_replace("$","\\$",$replace), $string);    
    } // end replace_var()


    /**
     * set_condition() - Sets a conditional region
     *
     * Adds or sets the value of a conditional region to the searchable list that is read when reading
     * in new templates.
     *
     * @access      public
     * @param       string  $name       Name of the conditional region
     * @param       bool    [$value]    Value of the conditional (true/false)
     * @return      bool
    */
    function set_condition($name, $value = 0)
    {
        $this->conditions[$name] = $value;
    } // end set_condition()

    
    /**
     * str_extract() - Returns a substring using start/end identifiers
     *
     * Extracts a string from inside a start and end tag specified.
     *
     * @access      public
     * @param       string  $string     String to search
     * @param       string  $sep1       Separator 1
     * @param       string  $sep2       Separator 2
     * @return      string    
    */
	function str_extract($string, $sep1, $sep2) {
	
	    $string = substr($string, 0, strpos($string,$sep2));
	    $string = substr(strstr($string, $sep1), 0);
	    $string = str_replace($sep1, "", $string);
	    
	    return $string;
	} // end str_extract()


    /**
     * strrpos_mb() - Returns the position of a string within a string
     *
     * This performs the same function as mb_strrpos without requiring the mbstring
     * module for PHP 4.x. Taken from php.net/strrpos posted on 07-Oct-2004. Function
     * lastIndexOf on  17-Nov-2004 tested faster but returned inaccurate results. 
     * Possibly due to strrev not counting certain special characters.
     *
     * @access      public
     * @param       string  $haystack     String to search
     * @param       string  $needle       Substring to find
     * @return      string    
    */
    function strrpos_mb($haystack, $needle)
    {
    $offset = 0;
       $pos_rule = ($offset<0)?strlen($haystack)+($offset-1):$offset;
       $last_pos = false; $first_run = true;
       do {
           $pos=strpos($haystack, $needle, (intval($last_pos)+(($first_run)?0:strlen($needle))));
           if ($pos!==false && (($offset<0 && $pos <= $pos_rule)||$offset >= 0)) {
               $last_pos = $pos;
           } else { break; }
           $first_run = false;
       } while ($pos !== false);
       if ($offset>0 && $last_pos<$pos_rule) { $last_pos = false; }
       return $last_pos;
    } // end strrpos_mb  



}
/* end class */
?>