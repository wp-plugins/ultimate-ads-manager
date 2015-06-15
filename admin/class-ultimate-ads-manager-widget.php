<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://codeneric.com
 * @since      1.0.0
 *
 * @package    Ultimate_Ads_Manager
 * @subpackage Ultimate_Ads_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ultimate_Ads_Manager
 * @subpackage Ultimate_Ads_Manager/admin
 * @author     Codeneric <contact@codeneric.com>
 */

class Ultimate_Ads_Manager_Widget extends WP_Widget {
     /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
//        $this->plugin_name = $plugin_name;
//        $this->version = $version;
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/config.php';

        parent::__construct(
            UAM_Config::$custom_post_slug.'_widget', // Base ID
            __( 'Ultimate Ads Widget' ), // Name
            array( 'description' => __( 'The widget of Ultimate Ads Manager'))// Args
        );


    }

    public function enqueue_scripts()
    {

    }

    /**
     * Add the styles for the upload media box
     */
    public function enqueue_styles()
    {

    }



    public function regulate_traffic($percentage) {
        $rand = mt_rand(1, 100);

        // visitor should see the widget
        if ($rand <= $percentage)
            return true;

        return false;
    }
    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        // outputs the content of the widget
        include(plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/ultimate-ads-manager-public-widget.php');
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     * @return void
     */
    public function form( $instance ) {
        // outputs the options form on admin
        //require(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/config.php');
        include(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ultimate-ads-manager-admin-widget.php');
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     * @return array $new_instance The new options
     */
    public function update( $new_instance, $old_instance ) {
        // processes widget options to be saved
        //die(print_r($new_instance));
        return $new_instance;
    }
    /*
    public function fuckAdblock() {
        echo "<script>
            function block(node) {
                if (   (node.nodeName == 'LINK' && node.href == 'data:text/css,') // new style
                    || (node.nodeName == 'STYLE' && node.innerText.match(/^\/\*This block of style rules is inserted by AdBlock/)) // old style
                ) {
                    node.parentElement.removeChild(node);
                }

            }
            document.addEventListener('DOMContentLoaded', function() {
                document.addEventListener('DOMNodeInserted', function(e) {
                    console.log('DOM INSERTED', e);
                    // disable blocking styles inserted by AdBlock
                    block(e.target);
                }, false);

            }, false);
            </script>";
    }
    */
    public function init() {
        register_widget("Ultimate_Ads_Manager_Widget");
    }
}

