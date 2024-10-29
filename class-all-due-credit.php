<?php
class class_all_due_credit
{
	const TYPE_AUTHOR = 'Written by';
	const TYPE_COAUTHOR = 'Co-written by';
	const TYPE_EDITOR = 'Edited by';
	const TYPE_REVIEWER = 'Reviewed by';
	const TYPE_THANKS = 'Thanks to';
	
	const INDEX_KEY = 0;
	const INDEX_VALUE = 1;
	const INDEX_VALUE_NAME = 0;
	const INDEX_VALUE_EMAIL = 1;
	const INDEX_VALUE_WEBSITE = 2;
	const ADC_PREFIX = '';
	const PATH_TO_CSS = '/all-due-credit/all-due-credit.css';
	
	//Style-related constants
	const GRAVATAR_SIZE = 48;//pixels
	const GRAVATAR_DEFAULT_IMAGE = 'mm';
	const MAXIMUM_COLS = 3;
	const SHOW_ON_HOME_PAGE = false;
	const SHOW_IN_FEED = true;
	
	private $option_columns_per_row;
	private $option_gravatar_size;
	private $option_gravatar_default_image;
	private $option_show_on_home_page;
	private $option_show_in_feed;
	private $width_row_percentage;
	
	//The default set of types.
	//New types are sometimes programmatically added.
	private $arr_types = array(0 => array(self::TYPE_AUTHOR, STRING_EMPTY),
					  1 => array(self::TYPE_COAUTHOR, STRING_EMPTY),
					  2 => array(self::TYPE_EDITOR, STRING_EMPTY),
					  3 => array(self::TYPE_REVIEWER, STRING_EMPTY),
					  4 => array(self::TYPE_THANKS, STRING_EMPTY));

	private $num_of_types = 5;
	private $num_of_values = 0;
	private $num_of_cells_written = 0;
	private $num_of_containers_written = 0;
	
	/**
	 * Generates an array of data which has the type, as well as the value for each type.
	 * The default array is global variable arr_types, but additional elements may be added by this method.
	 */
	function all_due_credit_generate_data_array($post, $arr_types, &$num_of_values, $num_of_types){
		$num_of_values = 0;
		
		if(!empty($arr_types)){
			
			//Go through the all types, and if we have something to display, modify the 
			//value in the array,as it is used later when showing ADC.
			for ($row = 0; $row < $num_of_types; $row++){
				$str_value = get_post_meta($post->ID, self::ADC_PREFIX . $arr_types[$row][self::INDEX_KEY], true);
				
				if($arr_types[$row][self::INDEX_KEY] != self::TYPE_THANKS){
				
					//Split the value simply so we can verify the name.
					$arr_split = explode(COMMA, $str_value);
					
					//Was a name provided?
					//	John Doe,jdoe@provider.com,jdoeswebsite.com
					//	^^^^^^^^
					if(!empty($arr_split[self::INDEX_VALUE_NAME])){
						
						$num_of_values += 1;
						
						//Update the value directly in the type array.
						//The value we are setting could look like this:
						//	John Doe,jdoe@provider.com,jdoeswebsite.com
						//This is not a problem, as the Show method handles splitting.
						$arr_types[$row][self::INDEX_VALUE] = $str_value;
					}
				}else{
					
					//Consider the special type: "Thanks". It can have multiple thanks in one meta field.
					//The format is the same as usual, but is semi-colon delimited to signal multiple thanks.
					//John Doe,jdoe@provider.com,jdoeswebsite.com;John Doe,jdoe@provider.com,jdoeswebsite.com
		
					$arr_thanks_data = explode(SEMI_COLON, $str_value);

					for($i = 0; $i < count($arr_thanks_data); $i++){
						
						//Split the value simply so we can verify the name.
						$arr_split = explode(COMMA, $arr_thanks_data[$i]);
						
						//Was a name provided?
						//	John Doe,jdoe@provider.com,jdoeswebsite.com
						//	^^^^^^^^
						if(!empty($arr_split[self::INDEX_VALUE_NAME])){
							
							//We're adding a new, valid data - so update the count.
							$num_of_values += 1;
						
							//Add a new "Thanks to" type. If multiple "Thanks to" values are provided in the meta,
							//we may end up adding multiple "Thanks to" types. This is acceptable because we later
							//iterate through the types using the array index id. Duplicates therefore don't cause any problems.
							array_push($arr_types, array(self::TYPE_THANKS, $arr_thanks_data[$i]));

						}

					}
				
				}
			}
		}
		
		return $arr_types;
	}
	
	/**
	 * Appends the necessary HTML code to open a row of credits.
	 */
	function all_due_credit_add_credit_row_open($str_credits){
		$this->num_of_containers_written += 1;
		$str_credits .= "<div id='adc_container_" . $this->num_of_containers_written . "' class='creditsContainer' style='height:" . $this->option_gravatar_size ."px'>" . CRLF;
		return $str_credits;
	}

	/**
	 * Appends the necessary HTML code to close a row of credits.
	 */	
	function all_due_credit_add_credit_row_close($str_credits){
		$str_credits .= '</div><br />' . CRLF;
		return $str_credits;
	}
	
	/**
	 * Given one particular arr_type entry, this method will add the required
	 * HTML elements to represent one cell in a row of credits.
	 */
	function all_due_credit_add_credit_cell($str_credits, $arr_type){
	
		$str_email = STRING_EMPTY;
		$str_website = STRING_EMPTY;
		$str_protocol = STRING_EMPTY;
		$str_link_open = STRING_EMPTY;
		$str_link_close = STRING_EMPTY;
		
		$bln_added_url = false;
		
		$this->num_of_cells_written += 1;
		
		//At this point, we expect something like this:
		//		John Doe,jdoe@provider.com,jdoeswebsite.com
		//Decided to use comma here because I felt it was the most intuitive.
		$arr_data = explode(COMMA, $arr_type[self::INDEX_VALUE]);

		//Make sure the array elements exist prior to using.
		if(array_key_exists(self::INDEX_VALUE_EMAIL, $arr_data))
			$str_email = $arr_data[self::INDEX_VALUE_EMAIL];
		
		if(array_key_exists(self::INDEX_VALUE_WEBSITE, $arr_data))
			$str_website = $arr_data[self::INDEX_VALUE_WEBSITE];

		if(!empty($str_website)){
			
			//Make sure a protocol has been specified.
			if(!strpos($str_website, PROTOCOL_IDENTIFIER))
				$str_protocol = PROTOCOL_PREFIX_DEFAULT;
			
			//Clean up the URL in case it is evil.
			$strUrl = esc_url($str_protocol . $str_website);

			$str_link_open = TAB . TAB . "<a href='" . $strUrl . "'>" . CRLF;
			$str_link_close = TAB . TAB . '</a>' . CRLF;
			$bln_added_url = true;
		}
		
		$str_credits .= TAB . "<div id='adc_cell_" . $this->num_of_cells_written . "' class='creditsCell' style='width: " . $this->width_row_percentage . "%;height:" . $this->option_gravatar_size . "px'>" . CRLF;
		
		$str_credits .= $str_link_open;
		
		if($bln_added_url){
			$str_credits .= TAB ;
		}
		
		//Don't bother checking to see if the e-mail is empty, 
		//as Gravatar will serve a default image if it doesn't recognize the input.
		$str_credits .= TAB . TAB . "<span id='adc_avatar_" . $this->num_of_cells_written . "' style='float: left;display: inline-block'><img id='adc_img_" . $this->num_of_cells_written . "' src='http://www.gravatar.com/avatar/" . md5(strtolower(trim($str_email))) . '?s=' . $this->option_gravatar_size . '&amp;d=' . $this->option_gravatar_default_image . "' alt='Gravatar Photo' class='creditsImage' style='width:" . $this->option_gravatar_size . "px;height:" . $this->option_gravatar_size . "px' /></span>" . CRLF;
		
		$str_credits .= $str_link_close;

		$str_credits .= TAB . TAB . "<span class='creditType' id='adc_type_" . $this->num_of_cells_written. "' style='line-height:" . ($this->option_gravatar_size/2) ."px'>" . $arr_type[self::INDEX_KEY] . '</span><br />' . CRLF;
		
		$str_credits .= $str_link_open;
		
		if($bln_added_url){
			$str_credits .= TAB ;
		}
		
		$str_credits .= TAB . TAB ."<span class='creditName' id='adc_name_" . $this->num_of_cells_written. "' style='line-height:" . ($this->option_gravatar_size/2) . "px'>" . $arr_data[self::INDEX_VALUE_NAME] . '</span>' . CRLF;

		$str_credits .= $str_link_close;

		$str_credits .= TAB . '</div>' . CRLF;
		
		return $str_credits;
	}

	/**
	 * Add the javascript call that will invoke resizing intelligence.
	 */
	function all_due_credit_add_resize_javascript($str_credits, $post_id){
	
		$str_credits .= "<script type='text/javascript'>var g_obj_all_due_credit_resizer = new all_due_credit_resizer(); if(g_obj_all_due_credit_resizer){g_obj_all_due_credit_resizer.start(" . $post_id . ',' . $this->option_columns_per_row . ',' . $this->option_gravatar_size . ");}</script>";
		
		return $str_credits;
	}
	
	function all_due_credit_load_options(){
		
		//Columns per row
		try{
			$this->option_columns_per_row = get_option('columns_per_row'); 
		}catch (Exception $e){
			$this->option_columns_per_row = self::MAXIMUM_COLS;
		}
			
		if(empty($this->option_columns_per_row) || !is_numeric($this->option_columns_per_row))
			$this->option_columns_per_row = self::MAXIMUM_COLS;
			
		$this->width_row_percentage = 100 / $this->option_columns_per_row;
		
		//Gravatar size
		try{
			$this->option_gravatar_size = get_option('gravatar_size'); 
		}catch (Exception $e){
			$this->option_gravatar_size = self::GRAVATAR_SIZE;
		}
		
		if(empty($this->option_gravatar_size) || !is_numeric($this->option_gravatar_size))
			$this->option_gravatar_size = self::GRAVATAR_SIZE;
			
		//Gravatar default image
		try{
			$this->option_gravatar_default_image = get_option('gravatar_default_image'); 
		}catch (Exception $e){
			$this->option_gravatar_default_image = self::GRAVATAR_DEFAULT_IMAGE;
		}
		
		if(empty($this->option_gravatar_default_image))
			$this->option_gravatar_default_image = self::GRAVATAR_DEFAULT_IMAGE;
		
		//Show on home page
		try{
			$this->option_show_on_home_page = (boolean) get_option('show_on_home_page'); 
		}catch (Exception $e){
			$this->option_show_on_home_page = self::SHOW_ON_HOME_PAGE;
		}
		
		//Show in RSS Feed
		try{
			$this->option_show_in_feed = (boolean) get_option('show_in_feed');
		}catch (Exception $e){
			$this->option_show_in_feed = self::SHOW_IN_FEED;
		}
	
	}
	
	/**
	 * Contains the master logic behind the generation of the All Due Credits HTML.
	 * Responsible for adding the resulting HTML to the post on-the-fly.
	 */
	function all_due_credit_show($content){
		global $post;
		$str_credits = STRING_EMPTY;
		$int_arr_count = 0;
		$this->num_of_values = 0;
		$abort = false;
		$this->all_due_credit_load_options();
		
		$this->arr_types = $this->all_due_credit_generate_data_array($post, $this->arr_types, $this->num_of_values, $this->num_of_types);

		//Consider aborting if we detect the homepage.
		if(is_home() && !$this->option_show_on_home_page){
			$abort = true;
		}
		
		//Consider aborting if we detect that we're in the feed.
		if(is_feed() && !$this->option_show_in_feed){
			$abort = true;
		}
		
		if(!$abort){
			$row_cells_used = 0;
			
			if($this->num_of_values > 0){
				
				$int_arr_count = count($this->arr_types);
				
				
				for ($row = 0; $row < $int_arr_count; $row++){

					$str_value = $this->arr_types[$row][self::INDEX_VALUE];

					if(!empty($str_value)){
						if($row_cells_used == 0)
							$str_credits = $this->all_due_credit_add_credit_row_open($str_credits);
						
						$str_credits = $this->all_due_credit_add_credit_cell($str_credits, $this->arr_types[$row]);
						
						$row_cells_used = $row_cells_used + 1;
	
						//Close the row when:
						//	- We've reached the column limit.
						//	- The cell we just wrote is the last in the loop.
						if($row_cells_used == $this->option_columns_per_row || ($this->num_of_values == 1)){
							$str_credits = $this->all_due_credit_add_credit_row_close($str_credits);
							$row_cells_used = 0;
						}
						
						$this->num_of_values = $this->num_of_values - 1;
					}
				}

				$str_credits = $this->all_due_credit_add_resize_javascript($str_credits, $post->ID);
			}
		}
		
		//check if the post meta has been set.
		return $content . $str_credits;
	}

	/**
	 * Register and enqueue the All Due Credit CSS file.
	 * Uses the suggested approach as per the API documentation:
	 * 		http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * The approach taken requires Wordpress 2.7 to function.
	 */
    function all_due_credit_add_styles() {
        $adc_style_url = WP_PLUGIN_URL . self::PATH_TO_CSS;
        $adc_style_file = WP_PLUGIN_DIR . self::PATH_TO_CSS;
		
        if ( file_exists($adc_style_file) ) {
            wp_register_style('all-due-credit', $adc_style_url);
            wp_enqueue_style( 'all-due-credit');
		}
    }
	
}

function all_due_credit_add_styles_wrapper(){
	global $obj_all_due_credit;
	
	if(isset($obj_all_due_credit)){
		$obj_all_due_credit->all_due_credit_add_styles();
	}
}

function all_due_credit_show_wrapper($content){
	global $obj_all_due_credit;
	
	if(isset($obj_all_due_credit)){
		$content = $obj_all_due_credit->all_due_credit_show($content);
	}
	
	return $content;
}
?>