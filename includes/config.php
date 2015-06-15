<?php

/**
 * Configuration
 *
 * @link       http://codeneric.com
 * @since      1.0.0
 *
 * @package    Ultimate_Ads_Manager
 * @subpackage Ultimate_Ads_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ultimate_Ads_Manager
 * @subpackage Ultimate_Ads_Manager/includes
 * @author     Codeneric <contact@codeneric.com>
 */
class UAM_Config {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */

    public static $plugin_name;
    public static $plugin_display_name = 'Ultimate Ads Manager';
    public static $custom_post_slug = 'codeneric_ad';
    public static $wp_nonce_base = 'ultimate_ads_manager_meta';
    public static $plugin_root_path;
    public static $plugin_version;
    public static $table_name_events;
    public static $db_map;
    public static $iv;
    public static $key;
    public static $fake_prevention;
    public static $ENV;
    public static $plugin_adblock_url;
    public static $plugin_adblock_symlink_name;
    public static $plugin_adblock_symlink_path;


    public static $ad_map = array(
        'referral_url' =>  'referral_url',
        'traffic_percentage' => 'traffic_percentage'
    );


    public static function process_public($ID) {
        $post = get_post($ID, ARRAY_A);

        $post_type = $post['post_type'];

        $ad_id = $ID;
        $meta = null;
        if($post_type === UAM_Config::$custom_post_slug.'_group'){
            $group_meta = get_post_meta( $ID , 'ad_group' ,true);
            $r = mt_rand() / mt_getrandmax();
            $bond = 0;

            foreach($group_meta as $ad){
                if($r <= $ad->traffic + $bond && $meta === null){
                    $meta = get_post_meta( $ad->ID , 'ad' ,true);
                    $ad_id = $ad->ID;
                }
                $bond += $ad->traffic;
            }

        }
        else{
            $meta = get_post_meta( $ID , 'ad' ,true);
        }

        return array($ad_id,$meta);
    }



    //TODO deprecated?
    public static function regulate_traffic($percentage) {
        $rand = mt_rand(1, 100);

        // visitor should see the widget
        if ($rand <= $percentage)
            return true;

        return false;
    }

    public static function init() {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        require_once( ABSPATH . 'wp-includes/wp-db.php' );
        global $wpdb;
        $uploads_info = wp_upload_dir();

        UAM_Config::$plugin_root_path = plugin_dir_path(__FILE__).'../';
        $plugin_data = get_plugin_data( plugin_dir_url(__FILE__).'../ultimate-ads-manager.php',false, false );
        UAM_Config::$table_name_events = $wpdb->prefix . "codeneric_uam_events";
        UAM_Config::$plugin_version = $plugin_data['Version'];
        UAM_Config::$plugin_name = $plugin_data['Name'];
        UAM_Config::$db_map = array(
            'view' => 0,
            'click' => 1
        );
        UAM_Config::$plugin_adblock_symlink_name = 'uam-pipe';
        UAM_Config::$plugin_adblock_symlink_path = $uploads_info['basedir'].'/'.UAM_Config::$plugin_adblock_symlink_name;
        UAM_Config::$plugin_adblock_url = $uploads_info['baseurl'].'/'.UAM_Config::$plugin_adblock_symlink_name;


        $iv_enc = get_option( 'codeneric_uam_iv' );
        if($iv_enc !== false){
            UAM_Config::$iv = base64_decode( $iv_enc );
        }

        $random_str = get_option( 'codeneric_uam_key' );
        if($random_str !== false){
            UAM_Config::$key = pack('H*', $random_str);
        }

        UAM_Config::$fake_prevention = array(
            'max_users_behind_router' => 5,
            'click' => 1
        );

        UAM_Config::$ENV = 'development';

        //UAM_Config::$custom_post_slug =


    }

}
