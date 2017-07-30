<?php
/******************************************************************************************
 *
 * @link              https://github.com/danilor/
 * @since             1.0.0
 * @package           WP_BlockThemAll
 *
 * @wordpress-plugin
 * Plugin Name:       Wordpress Block Them All
 * Plugin URI:        https://github.com/danilor/block_them_all
 * Description:       This plugin will block users and hosts that were failed attempts
 * Version:           1.0.0
 * Author:            Danilo Josué Ramírez Mattey
 * Author URI:        https://github.com/danilor/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       block_them_all
 ******************************************************************************************/

/**
 * DEFINITIONS
 */

define( "REG_TABLE"     ,   "bta_registries"        );
define( "BLOCKED_TABLE" ,   "bta_blocked"           );
define( "MAX_REGISTRIES",   5                       );
define( "MAX_TIME"      ,   15                      ); // In minutes
define( "RECORD_TIME"   ,   3                       ); // In minutes
define( "CLEAN_OLD_REG_TIMES",  360                 ); // In minutes

/**********************************************
 * Lets require all necessary files
 **********************************************/
require_once plugin_dir_path( __FILE__ ) . 'classes/BlockThemAll.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/Utils.php';

/**********************************************
 * Lets call the main class
 **********************************************/

$WBTA = new BlockThemAll();

/**
 * Install the plugin function
 */
function bta_install_database(){
    BlockThemAll::installDatabase();
}

/**
 * Function that executes when the plugin is deactivated
 */
function bta_uninstall_database(){
    BlockThemAll::uninstallDatabase();
}

/**
 * This function will execute if there is a failed attempt
 */
function bta_register_fail( $username ){
    BlockThemAll::registerFail( $username );
    $ip         =       Utils::getRealIP();
    BlockThemAll::checkAndRegisterBlock($username,$ip);
}

/**
 * It will execute before the user authentication
 */
function bta_check_custom_authentication( $username , $password ){

    global $wpdb;
    $ip         =       Utils::getRealIP();
    $table_name = $wpdb->prefix . BLOCKED_TABLE;
    $sql = "SELECT * FROM $table_name WHERE (ip = '$ip' OR username = '$username') AND until > CURRENT_TIMESTAMP ;";

    $result = $wpdb->query( $sql );



    if( $result > 0 ){
        echo file_get_contents( plugin_dir_path( __FILE__ ) . 'pages/auth_error.php' );
        exit();
    }
    //var_dump( $result );
    // die(  );
}

/**
 * This method will add all the hooks
 */
function addHooks(){
    register_activation_hook(       __FILE__        ,   'bta_install_database'                                           );
    register_deactivation_hook(     __FILE__        ,   'bta_uninstall_database'                                         );
    add_action(                     'wp_login_failed'   ,           'bta_register_fail'                 ,       10      ,       2   );
    add_action(                     'wp_authenticate'   ,           'bta_check_custom_authentication'   ,  1            ,       2   );

}

addHooks();

