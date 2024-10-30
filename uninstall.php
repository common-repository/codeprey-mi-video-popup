<?php 
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();



$status_options = get_option( 'mivideopop_status_options', array() );

if ( ! empty( $status_options['uninstall_data'] ) ) {
    //drop a custom db table
    global $wpdb;
    $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}videos" );
}