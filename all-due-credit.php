<?php
/*
Plugin Name: All Due Credit
Plugin URI: http://www.mattrefghi.com/projects/allduecredit/
Description: Credit those who helped by including a stylish list of names after any post, potentially including a Gravatar and link for each.
Version: 0.4.2
Author: Matt Refghi
Author URI: http://www.mattrefghi.com
License: GPL2
*/
/*  Copyright 2010  Mathieu Refghi  (email : mrefghi@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Requirements:
//Wordpress 2.7 is required for the all_due_credit_add_styles method. 
//Wordpress 2.8 for esc_url 
//Wordpress 2.9 for WP_PLUGIN_URL.

include 'class-all-due-credit.php';

define('STRING_EMPTY', '');
define('COMMA', ',');
define('SEMI_COLON', ';');
define('PROTOCOL_IDENTIFIER', '://');
define('PROTOCOL_PREFIX_DEFAULT', 'http://');
define('CRLF', "\r\n");
define('TAB', "\t");
define('DROPDOWN_OPTION_SELECTED', 'selected="selected"');

//Default option constants
define('OPTION_DEFAULT_COLS_PER_ROW', 3);
define('OPTION_DEFAULT_GRAVATAR_SIZE', 48);	
define('OPTION_DEFAULT_GRAVATAR_DEF_IMAGE', 'mm');

$obj_all_due_credit = new class_all_due_credit();

add_action('wp_print_styles', 'all_due_credit_add_styles_wrapper');

wp_enqueue_script('all-due-credit', WP_PLUGIN_URL . '/all-due-credit/all-due-credit.js');

//We'll be applying ADC at the end of the post contents, 
//without ever altering the post in the database.
add_filter('the_content', 'all_due_credit_show_wrapper');

add_action('admin_menu', 'all_due_credit_menu');

//call register settings function
add_action( 'admin_init', 'all_due_credit_register_settings' );

function all_due_credit_menu() {

  add_options_page('All Due Credit Options', 'All Due Credit', 'manage_options', 'adc-main-options', 'all_due_credit_settings_page');

}

function all_due_credit_settings_page() {
?>
<div class="wrap">
<h2>All Due Credit</h2>

<form method="post" action="options.php">
<?php
	settings_fields('adc-settings-group');
	
	//Handle defaults
	
	$columns_per_row = get_option('columns_per_row'); 
	$gravatar_size = get_option('gravatar_size');
	$gravatar_default_image = get_option('gravatar_default_image');
	$show_on_home_page = get_option('show_on_home_page');
	$show_on_home_page_yes = STRING_EMPTY;
	$show_on_home_page_no = STRING_EMPTY;
	$show_in_feed = get_option('show_in_feed');
	$show_in_feed_yes = STRING_EMPTY;
	$show_in_feed_no = STRING_EMPTY;
	
	
	if(empty($columns_per_row))
		$columns_per_row = OPTION_DEFAULT_COLS_PER_ROW;
		
	if(empty($gravatar_size))
		$gravatar_size = OPTION_DEFAULT_GRAVATAR_SIZE;
	
	if(empty($gravatar_default_image))
		$gravatar_default_image = OPTION_DEFAULT_GRAVATAR_DEF_IMAGE;
		
	if($show_on_home_page == STRING_EMPTY){
		
		$show_on_home_page_no = DROPDOWN_OPTION_SELECTED;
	}else{
		if($show_on_home_page){
			$show_on_home_page_yes = DROPDOWN_OPTION_SELECTED;
		}else{
			$show_on_home_page_no = DROPDOWN_OPTION_SELECTED;
		}
	}
	
	if($show_in_feed == STRING_EMPTY){
		
		$show_in_feed_yes = DROPDOWN_OPTION_SELECTED;
	}else{
		if($show_in_feed){
			$show_in_feed_yes = DROPDOWN_OPTION_SELECTED;
		}else{
			$show_in_feed_no = DROPDOWN_OPTION_SELECTED;
		}
	}
	
	?>
   <table class="form-table">
        <tr valign="top">
			<th scope="row">
				Maximum names per row
			</th>
			<td>
				<input type="text" name="columns_per_row" value="<?php echo $columns_per_row ?>" style="width: 20px" maxlength="1" /> name(s)<br />
				<p style="font-size: 10px">Changing this value will influence how many names are listed per row. The default value is 3, allowing up to three names to appear in the same row. <br /><br />If you notice that scrollbars are frequently appearing, you may want to decrease this value.</p>
			</td>
        </tr>
        <tr valign="top">
			<th scope="row">
				Gravatar size
			</th>
			<td>
				<input type="text" name="gravatar_size" value="<?php echo $gravatar_size ?>" style="width: 45px" maxlength="3"  /> pixels
				<p style="font-size: 10px">By default, the Gravatar included along with each name is 48 by 48 pixels. The gravatar size is what determines how much vertical space each name will have. Be careful when changing this value, as extreme changes might break the layout of All Due Credit.</p>
			</td>
        </tr>
        <tr valign="top">
			<th scope="row">
				Gravatar default image
			</th>
			<td>
			<input type="text" name="gravatar_default_image" value="<?php echo $gravatar_default_image ?>" style="width: 100px"  />
			<p style="font-size: 10px">Whenever a particular e-mail address has no associated Gravatar, a default image must be used instead. A list of the default images can be found <a href="http://en.gravatar.com/site/implement/images/" target="new">on the Gravatar website</a>. The default used by All Due Credit is 'mm', also known as Mystery Man.</p>
			</td>
        </tr>
        <tr valign="top">
			<th scope="row">
				Show on home page?
			</th>
			<td>
				<select name="show_on_home_page" id="show_on_home_page">
					<option value="1" <?php echo $show_on_home_page_yes ?>>Yes</option>
					<option value="0" <?php echo $show_on_home_page_no ?>>No</option>
				</select>
				<p style="font-size: 10px">By default, All Due Credit will not be visible when you view the blog home page. It will, however, be visible when you are viewing an individual blog post. Select "Yes" if you want All Due Credit to appear on the home page as well.</p>
			</td>
        </tr>
        <tr valign="top">
			<th scope="row">
				Show in RSS feed?
			</th>
			<td>
				<select name="show_in_feed" id="show_in_feed">
					<option value="1" <?php echo $show_in_feed_yes ?>>Yes</option>
					<option value="0" <?php echo $show_in_feed_no ?>>No</option>
				</select>
				<p style="font-size: 10px">By default, All Due Credit will be included in the RSS Feed. Select "No" to disable this feature.</p>
			</td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
</div>
<?php 
}

function all_due_credit_register_settings() {
	//register our settings
	register_setting( 'adc-settings-group', 'columns_per_row' );
	register_setting( 'adc-settings-group', 'gravatar_size' );
	register_setting( 'adc-settings-group', 'gravatar_default_image' );
	register_setting( 'adc-settings-group', 'show_on_home_page' );
	register_setting( 'adc-settings-group', 'show_in_feed' );
}

?>