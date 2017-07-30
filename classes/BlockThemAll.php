<?php

/**
 * Class BlockThemAll
 * This is the main block them all class
 */
class BlockThemAll
{
    /**
     * @param $user
     */
    public static function registerFail(  $user ){
        global $wpdb;
        $ip         =       Utils::getRealIP();
        $table_name = $wpdb->prefix . REG_TABLE;

        $sql = " INSERT INTO $table_name (dated,ip,username) VALUES(CURRENT_TIMESTAMP , '$ip' , '$user') ";
        $wpdb->query( $sql );
        self::cleanOldRegistries();
    }

    /**
     * Cleans old registries from the log table
     */
    public static function cleanOldRegistries(){
        global $wpdb;
        $table_name = $wpdb->prefix . REG_TABLE;
        $timer = CLEAN_OLD_REG_TIMES;
        $sql = "DELETE FROM $table_name WHERE dated < date_sub(now(), INTERVAL $timer MINUTE)";
        $wpdb->query( $sql );
    }

    /**
     *
     */
    public static function installDatabase(){
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . REG_TABLE;

        $sql = "CREATE TABLE $table_name (
                        id bigint NOT NULL AUTO_INCREMENT,
                        dated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                        ip varchar(50) NULL,
                        username varchar(50) NULL,
                        UNIQUE KEY id (id)
	    ) $charset_collate;";
        dbDelta( $sql );
        $table_name = $wpdb->prefix . BLOCKED_TABLE;
        $sql = "CREATE TABLE $table_name (
                        id bigint NOT NULL AUTO_INCREMENT,
                        until datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                        ip varchar(50) NULL,
                        username varchar(50) NULL,
                        permanent int(4) DEFAULT 0 NULL,
                        UNIQUE KEY id (id)
	    ) $charset_collate;";
        dbDelta( $sql );
    }

    public static function uninstallDatabase(){
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $table_name = $wpdb->prefix . REG_TABLE;
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query( $sql );
        $table_name = $wpdb->prefix . BLOCKED_TABLE;
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query( $sql );
    }

    /**
     * @param $username
     * @param $ip
     */
    public static function checkAndRegisterBlock( $username , $ip ){
        global $wpdb;
        $table_name = $wpdb->prefix . REG_TABLE;
        $timer = RECORD_TIME;
        $sql = "SELECT * FROM $table_name WHERE (ip = '$ip' OR username = '$username') AND dated > date_sub(now(), INTERVAL $timer MINUTE) ;";
        $result = $wpdb->query( $sql );
        if( $result >= MAX_REGISTRIES ){
            self::insertBlockRegistry( $username , $ip );
        }

    }

    /**
     * @param $username
     * @param $ip
     */
    public static function insertBlockRegistry( $username , $ip ){
       global $wpdb;
       $table_name = $wpdb->prefix . BLOCKED_TABLE;
       $dated = new \DateTime();
       $sql = " INSERT INTO $table_name (until, ip, username, permanent) VALUES( DATE_ADD( NOW() , INTERVAL " . MAX_TIME . " MINUTE ) , '$ip' , '$username' , 0  ) ";
       $wpdb ->show_errors();
       $wpdb -> query( $sql );
    }


}