<?php
/*
	Plugin Name: NS Google Sheets Connector
	Plugin URI: http://neversettle.it
	Description: This is a painless way to integrate and automatically send WordPress data to Google Sheets.
	Text Domain: ns-plugin-template
	Author: Never Settle
	Version: 1.0.1
	Author URI: http://neversettle.it
	License: GPLv2 or later
*/

/*
	Copyright 2014 Never Settle (email : dev@neversettle.it)
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
		
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**
 * This plugin uses code originally distributed under the MPL 1.1 and 
 * modified by Never Settle 
 * 
 * The original can be found here:
 * https://code.google.com/p/form-connector-php-submit-to-google-spreadsheets/ 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // exit if accessed directly!
}

require_once(plugin_dir_path(__FILE__).'ns-sidebar/ns-sidebar.php');

// TODO: rename this class
class ns_google_sheets_connector {
	
	var $path; 				// path to plugin dir
	var $wp_plugin_page; 	// url to plugin page on wp.org
	var $ns_plugin_page; 	// url to pro plugin page on ns.it
	var $ns_plugin_name; 	// friendly name of this plugin for re-use throughout
	var $ns_plugin_menu; 	// friendly menu title for re-use throughout
	var $ns_plugin_slug; 	// slug name of this plugin for re-use throughout
	var $ns_plugin_ref; 	// reference name of the plugin for re-use throughout
	
	function __construct(){		
		$this->path = plugin_dir_path( __FILE__ );
		// TODO: update to actual
		$this->wp_plugin_page = "http://wordpress.org/plugins/ns-google-sheets-connector";
		// TODO: update to link builder generated URL or other public page or redirect
		$this->ns_plugin_page = "http://neversettle.it/";
		// TODO: update this - used throughout plugin code and only have to update here
		$this->ns_plugin_name = "NS Google Sheets Connector";
		// TODO: update this - used throughout plugin code and only have to update here
		$this->ns_plugin_menu = "NS Sheets";
		// TODO: update this - used throughout plugin code and only have to update here
		$this->ns_plugin_slug = "ns-google-sheets-connector";
		// TODO: update this - used throughout plugin code and only have to update here
		$this->ns_plugin_ref = "ns_google_sheets_connector";
		
		add_action( 'plugins_loaded', array($this, 'setup_plugin') );
		add_action( 'admin_notices', array($this,'admin_notices'), 11 );
		add_action( 'network_admin_notices', array($this, 'admin_notices'), 11 );		
		add_action( 'admin_init', array($this,'register_settings_fields') );		
		add_action( 'admin_menu', array($this,'register_settings_page'), 20 );
		add_action( 'admin_enqueue_scripts', array($this, 'admin_assets') );
		add_action( 'wpcf7_mail_sent', array($this, 'wpcf7_send_to_sheets'), 1);
		
		// TODO: uncomment this if you want to add custom JS 
		//add_action( 'admin_print_footer_scripts', array($this, 'add_javascript'), 100 );
		
		// TODO: uncomment this if you want to add custom actions to run on deactivation
		//register_deactivation_hook( __FILE__, array($this, 'deactivate_plugin_actions') );
	}

	function deactivate_plugin_actions(){
		// TODO: add any deactivation actions here
	}
	
	/*********************************
	 * NOTICES & LOCALIZATION
	 */
	 
	 function setup_plugin(){
	 	load_plugin_textdomain( $this->ns_plugin_slug, false, $this->path."lang/" ); 
	 }
	
	function admin_notices(){
		$message = '';	
		if ( $message != '' ) {
			echo "<div class='updated'><p>$message</p></div>";
		}
		if ( !function_exists( 'wpcf7' ) ) {
			echo "<div class='update-nag'><p>NS Google Sheets Connector requires Contact Form 7 in order to work.</p></div>";
		}
	}

	function admin_assets($page){
	 	wp_register_style( $this->ns_plugin_slug, plugins_url("css/ns-custom.css",__FILE__), false, '1.0.0' );
	 	wp_register_script( $this->ns_plugin_slug, plugins_url("js/ns-custom.js",__FILE__), false, '1.0.0' );
		if( strpos($page, $this->ns_plugin_ref) !== false  ){
			wp_enqueue_style( $this->ns_plugin_slug );
			wp_enqueue_script( $this->ns_plugin_slug );
		}		
	}
	
	/**********************************
	 * SETTINGS PAGE
	 */
	
	function register_settings_fields() {
		// TODO: might want to update / add additional sections and their names, if so update 'default' in add_settings_field too
		add_settings_section( 
			$this->ns_plugin_ref.'_set_section', 	// ID used to identify this section and with which to register options
			$this->ns_plugin_name, 					// Title to be displayed on the administration page
			false, 									// Callback used to render the description of the section
			$this->ns_plugin_ref 					// Page on which to add this section of options
		);
		// google login user
		add_settings_field( 
			$this->ns_plugin_ref.'_user', 		// ID used to identify the field
			'Google User Login Email', 			// The label to the left of the option interface element
			array($this,'show_settings_field'), // The name of the function responsible for rendering the option interface
			$this->ns_plugin_ref, 				// The page on which this option will be displayed
			$this->ns_plugin_ref.'_set_section',// The name of the section to which this field belongs
			array( 								// args to pass to the callback function rendering the option interface
				'field_name' => $this->ns_plugin_ref.'_user'
			)
		);
		register_setting( $this->ns_plugin_ref, $this->ns_plugin_ref.'_user');
		// google login password
		add_settings_field( 
			$this->ns_plugin_ref.'_pass', 		// ID used to identify the field
			'Google User Login Password', 		// The label to the left of the option interface element
			array($this,'show_settings_field'), // The name of the function responsible for rendering the option interface
			$this->ns_plugin_ref, 				// The page on which this option will be displayed
			$this->ns_plugin_ref.'_set_section',// The name of the section to which this field belongs
			array( 								// args to pass to the callback function rendering the option interface
				'field_name' => $this->ns_plugin_ref.'_pass',
				'warning' => 'WARNING: In order for this to work, the google account password must be saved in your wordpress DB in plain text. This is a potential security concern. Use at your own risk!'
			)
		);
		register_setting( $this->ns_plugin_ref, $this->ns_plugin_ref.'_pass');
		// google sheets name
		add_settings_field( 
			$this->ns_plugin_ref.'_sheet', 		// ID used to identify the field
			'Google Sheet Name', 				// The label to the left of the option interface element
			array($this,'show_settings_field'), // The name of the function responsible for rendering the option interface
			$this->ns_plugin_ref, 				// The page on which this option will be displayed
			$this->ns_plugin_ref.'_set_section',// The name of the section to which this field belongs
			array( 								// args to pass to the callback function rendering the option interface
				'field_name' => $this->ns_plugin_ref.'_sheet'
			)
		);
		register_setting( $this->ns_plugin_ref, $this->ns_plugin_ref.'_sheet');
		// google sheets name
		add_settings_field( 
			$this->ns_plugin_ref.'_tab', 		// ID used to identify the field
			'Google Sheet Tab Name',			// The label to the left of the option interface element
			array($this,'show_settings_field'), // The name of the function responsible for rendering the option interface
			$this->ns_plugin_ref, 				// The page on which this option will be displayed
			$this->ns_plugin_ref.'_set_section',// The name of the section to which this field belongs
			array( 								// args to pass to the callback function rendering the option interface
				'field_name' => $this->ns_plugin_ref.'_tab'
			)
		);
		register_setting( $this->ns_plugin_ref, $this->ns_plugin_ref.'_tab');
		// google sheets name
		add_settings_field( 
			$this->ns_plugin_ref.'_form', 		// ID used to identify the field
			'Contact Form 7 ID to use',			// The label to the left of the option interface element
			array($this,'show_settings_field'), // The name of the function responsible for rendering the option interface
			$this->ns_plugin_ref, 				// The page on which this option will be displayed
			$this->ns_plugin_ref.'_set_section',// The name of the section to which this field belongs
			array( 								// args to pass to the callback function rendering the option interface
				'field_name' => $this->ns_plugin_ref.'_form'
			)
		);
		register_setting( $this->ns_plugin_ref, $this->ns_plugin_ref.'_form');
	}	

	function show_settings_field($args){
		$saved_value = get_option( $args['field_name'] );
		// initialize in case there are no existing options
		if ( empty($saved_value) ) {
			echo '<input type="text" name="' . $args['field_name'] . '" value="Setting Value" /><br/>';
		} else {
			echo '<input type="text" name="' . $args['field_name'] . '" value="'.$saved_value.'" /><br/>';
		}
		if ( !empty($args['warning']) ) {
			echo '<p style="color:red">'.$args['warning'].'</p>';
		} 
	}

	function register_settings_page(){
		add_submenu_page(
			'options-general.php',								// Parent menu item slug	
			__($this->ns_plugin_name, $this->ns_plugin_name),	// Page Title
			__($this->ns_plugin_menu, $this->ns_plugin_name),	// Menu Title
			'manage_options',									// Capability
			$this->ns_plugin_ref,								// Menu Slug
			array( $this, 'show_settings_page' )				// Callback function
		);
	}
	
	function show_settings_page(){
		?>
		<div class="wrap">
			
			<h2><?php $this->plugin_image( 'banner.png', __('ALT') ); ?></h2>
			
			<!-- BEGIN Left Column -->
			<div class="ns-col-left">
				<form method="POST" action="options.php" style="width: 100%;">
					<?php settings_fields($this->ns_plugin_ref); ?>
					<?php do_settings_sections($this->ns_plugin_ref); ?>
					<?php submit_button(); ?>
				</form>
			</div>
			<!-- END Left Column -->
						
			<!-- BEGIN Right Column -->			
			<div class="ns-col-right">
				<h3>Thanks for using <?php echo $this->ns_plugin_name; ?></h3>
				<?php ns_sidebar::widget( 'subscribe' ); ?>
				<?php ns_sidebar::widget( 'share', array('plugin_url'=>'http://neversettle.it/buy/wordpress-plugins/ns-fba-for-woocommerce/','plugin_desc'=>'Automate your WooCommerce fulfillment with FBA!','text'=>'Would anyone else you know enjoy NS FBA for WooCommerce?') ); ?>
				<?php ns_sidebar::widget( 'donate' ); ?>				
			</div>
			<!-- END Right Column -->
			<!-- BEGIN Right RIGHT Column -->			
			<div class="ns-col-right-right">
				<?php ns_sidebar::widget( 'links', array('ns-sheets') ); ?>
				<?php ns_sidebar::widget( 'random'); ?>
			</div>
			<!-- END Right RIGHT Column -->
		</div>
		<?php
	}
	
	
	/*************************************
	 * FUNCTIONALITY
	 */
	
	function wpcf7_send_to_sheets($cfdata) {

		/* Use WPCF7_Submission object's get_posted_data() method to get it. */
        $submission = WPCF7_Submission::get_instance();

        if ($submission) {
            $posted_data = $submission->get_posted_data();
			// make sure the form ID matches the setting otherwise don't do anything
			if ( $posted_data['_wpcf7'] == get_option($this->ns_plugin_ref.'_form') ) {
				include_once(plugin_dir_path(__FILE__) . "lib/google-sheets.php");
				$doc = new googlesheet();
				$doc->authenticate(get_option($this->ns_plugin_ref.'_user'), get_option($this->ns_plugin_ref.'_pass'));
				$doc->settitleSpreadsheet(get_option($this->ns_plugin_ref.'_sheet'));
				$doc->settitleWorksheet(get_option($this->ns_plugin_ref.'_tab'));
				$my_data["date"]=date('n/j/Y');
				foreach ( $posted_data as $key => $value ) {
					// exclude the default wpcf7 fields in object
					if ( strpos($key, '_wpcf7') !== false || strpos($key, '_wpnonce') !== false ) {
						// do nothing
					} else {
						// handle strings and array elements
						if (is_array($value)) {
							$my_data[$key] = implode(', ', $value);	
						} else {
							$my_data[$key] = $value;
						}					
					}
				}				
				$doc->add_row($my_data);
			}
        }
		// uncomment for debugging		
		//$test_file = fopen(plugin_dir_path(__FILE__) . 'logs/test.txt', 'a');
		//$test_result = fwrite($test_file, print_r($my_data, TRUE));
	}

	
	/*************************************
	 * UITILITY
	 */
	 
	 function plugin_image( $filename, $alt='', $class='' ){
	 	echo "<img src='".plugins_url("/images/$filename",__FILE__)."' alt='$alt' class='$class' />";
	 }
	
}

new ns_google_sheets_connector();
