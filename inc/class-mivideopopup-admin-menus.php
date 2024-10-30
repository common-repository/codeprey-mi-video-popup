<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Mivideopopup_Admin_Menus')) :

    /**
     * Mivideopopup_Admin_Menus Class
     */
    class Mivideopopup_Admin_Menus {

        public function __construct() {

            add_action('admin_menu', array($this, 'myvideopopup_menus'));
       }

        /**
         * Creating Menus of backend
         */
        public function myvideopopup_menus() {
            add_menu_page('Video Popups', 'Video Popups', 'manage_options', 'my-popups', array($this, 'mivideopopup_my_video_listings'), 'dashicons-format-gallery');
            add_submenu_page('my-popups', 'List Video popups', 'List Video Popups', 'manage_options', 'my-popups');
            add_submenu_page('my-popups', 'Add Video popups', 'Add Video Popups', 'manage_options', 'video', array($this, 'mivideopopup_my_video_settings'));
        }

        /**
         * Rendring View for popup Creation in admin panel.
         * */
        public function mivideopopup_my_video_settings() {
            echo Mivideopopup::mivideopopup_create();
        }

        /**
         * Rendring List of all popups in admin panel.
         * */
        public function mivideopopup_my_video_listings() {
            echo Mivideopopup::mivideopopup_listVideos();
        }
        
        
    }

    endif;
return new Mivideopopup_Admin_Menus();