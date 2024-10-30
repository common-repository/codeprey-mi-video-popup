<?php

/*
  Plugin Name: Codeprey - Mi Video Popup
  Plugin URI: http://codeprey.com/
  Description: For create video popup on your page load.
  Version: 1.0
  Author: Codeprey team
  Author URI: http://codeprey.com/
  Text Domain: codeprey.com
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('plugins_loaded', array('mivideopop', 'init'));

/**
 * Main Class of plugin mivideopop
 * */
final class mivideopop {

	/**
	 * Mivideopopup version.
	 *
	 * @var string
	 */
	public $version = '1.0';

    public static function init() {
        $class = __CLASS__;
        new $class;
    }

    /**
     * Including Core files in this construct
     * */
    public function __construct() {
        //construct what you see fit here...
        $this->includes();
        $this->init_hooks();
        add_action('admin_enqueue_scripts', array('mivideopop', 'mivideopop_uploader_enqueue'));
		do_action('mivideopop_loaded');
    }
	
	private function define_constants() {
		$upload_dir = wp_upload_dir();

		$this->define( 'Mivideopopup_PLUGIN_FILE', __FILE__ );
		$this->define( 'Mivideopopup_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'Mivideopopup_VERSION', $this->version );
		$this->define( 'Mivideopopup_VERSION', $this->version );
	}

    /**
     * Hook into actions and filters
     * @since  2.3
     */
    private function init_hooks() {
        register_activation_hook(__FILE__, array('Video_Install', 'install'));
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        include_once("inc/class-mivideopopup-install.php");
        include_once("inc/class-mivideopopup-admin-menus.php");
        include_once("inc/class-mivideopopup.php");
    }

    /**
     * Register Media Scripts For Add Media  
     */
    function mivideopop_uploader_enqueue() {
        wp_enqueue_media();
        wp_register_script('media-lib-uploader-js', plugins_url('media-lib-uploader.js', __FILE__), array('jquery'));
        wp_enqueue_script('media-lib-uploader-js');
        wp_enqueue_style('videopopup_admin_styles', mivideopop::plugin_url() . '/assets/css/admin.css', array());
        
    }

    /*
     * Chekcing type of page is it frontend or admin 
     */

    public function is_request($type) {
        switch ($type) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined('DOING_AJAX');
            case 'cron' :
                return defined('DOING_CRON');
            case 'frontend' :
                return (!is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
        }
    }

    /**
     * Get the plugin url.
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit(plugins_url('/', __FILE__));
    }
	
}