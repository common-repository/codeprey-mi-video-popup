<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Mivideopopup_Install Class
 */
class Mivideopopup_Install {

    /**
     * Install MI VIDEO POPUP
     */
    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'install' ));
        
    }

    public static function install() {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        $table_name = $wpdb->prefix . 'popsubscribe';
        
        
            
            $sql = "
			CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."videos` (
                    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `popup_title` varchar(255) NOT NULL,
                    `popup_type` varchar(255) NOT NULL,
                    `popup_link` varchar(255) DEFAULT NULL,
                    `popup_autoplay` int(11) NOT NULL DEFAULT '0',
                    `popup_background` varchar(255) DEFAULT NULL,
                    `color1` varchar(255) DEFAULT NULL,
                    `color2` varchar(255) DEFAULT NULL,
                    `picture_link` varchar(255) DEFAULT NULL,
                    `popup_size` varchar(255) DEFAULT NULL,
                    `creation_date` varchar(255) NOT NULL,
                    `update_date` varchar(255) DEFAULT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1; ";
           
            dbDelta($sql);
			add_option( 'videopopup_version', '1.0' );
        
    }
    

}
Mivideopopup_Install::init();