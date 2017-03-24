<?php
 /*
    Plugin Name: Neyrinck Custom Forms
    Description: This plug-in loads all Neyrinck custom forms.
    Author: Bernice Ling
    Version: 1.0
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if (!defined('NCF_PLUGIN_DIR')){
  define('NCF_PLUGIN_DIR', dirname(__FILE__));
}
    

if (!defined('NCF_PLUGIN_DIR_BASE'))
    define('NCF_PLUGIN_DIR_BASE', plugin_basename(__FILE__));


function NCF_install() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'ncf_settings';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		db_user VARCHAR(10) NOT NULL,
      	db_password VARCHAR(100) NULL,
      	db_name VARCHAR(100) NULL,
      	db_server VARCHAR(100) NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );


}


register_activation_hook( __FILE__, 'NCF_install' );




/**
 * The core plugin class 
 */
require NCF_PLUGIN_DIR . '/includes/class-neyrinck-custom-forms.php';

/**
 * Begins execution of the plugin.
 */
function run_neyrinck_custom_forms() {

    $plugin = new Neyrinck_Custom_Forms();
    $plugin->run();
   
}

run_neyrinck_custom_forms();

?>