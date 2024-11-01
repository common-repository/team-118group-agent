<?php
/*
Plugin Name: Team 118GROUP Agent
Description: Team 118GROUP Web Design Plugin to assist with monitoring your WordPress installation and assisting with system care
Version: 1.6.0
Author: Team118GROUP
Author URI: http://www.118group.com
Stable tag: 1.6.0
*/

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

 

// core initiation
if( !class_Exists('vooMainStart') ){
	class vooMainStart{
		var $locale;
		function __construct( $locale, $includes, $path ){
			$this->locale = $locale;
			
			// include files
			foreach( $includes as $single_path ){
				include( $path.$single_path );				
			}
			// calling localization
			add_action('plugins_loaded', array( $this, 'myplugin_init' ) );
		}
		function myplugin_init() {
		 $plugin_dir = basename(dirname(__FILE__));
		 load_plugin_textdomain( $this->locale , false, $plugin_dir );
		}
	}
	
	
}


// initiate main class
new vooMainStart('wtf', array(
	'modules/hooks.php',
	'modules/ajax.php',
	'modules/settings.php',
	'modules/scripts.php',
	'modules/shortcodes.php',
), dirname(__FILE__).'/' );

 
register_activation_hook( __FILE__, 'wtf_activate' );
function wtf_activate() {
require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); 
 global $wpdb;
 $table_name = 'wtf_404_log';
 $table_name =  $wpdb->prefix.$table_name;
 
 
 //$wpdb->query("DROP TABLE ".$table_name );

$sql = "CREATE TABLE IF NOT EXISTS $table_name (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `request_url` longtext NOT NULL ,
  `refferal_url` longtext NOT NULL,
  `timestamp` longtext NOT NULL,
  `redirect_url` longtext NOT NULL,
  `is_archive` mediumint(1) NOT NULL,
 
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

dbDelta($sql);

 


} 

 
?>