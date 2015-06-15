<?php

/**
 * Fired during plugin activation
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
class Ultimate_Ads_Manager_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		require_once plugin_dir_path( __FILE__ ) . 'config.php';
		UAM_Config::init();
		/////////////// Database Stuff ////////////////////////
		global $wpdb;

		$table_name = UAM_Config::$table_name_events;

		if(UAM_Config::$ENV === 'development'){
			$wpdb->query( " DROP TABLE $table_name"	);
		}

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
		  id   bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  type tinyint NOT NULL,
		  uuid bigint(20) UNSIGNED NOT NULL,
		  ip   tinytext DEFAULT '' NOT NULL,
		  ad_id   bigint(20) UNSIGNED NOT NULL,
		  ad_slide_id   tinyint UNSIGNED NOT NULL,
		  UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		///////////////// Encryption Stuff //////////////////////7

		if ( get_option( 'codeneric_uam_iv' ) === false ) {
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			update_option( 'codeneric_uam_iv', base64_encode($iv)  );
		}

        if ( get_option( 'codeneric_uam_uuid' ) === false ) {
            $random_str = uniqid('', true);
            update_option( 'codeneric_uam_uuid', $random_str  );
        }

		if ( get_option( 'codeneric_uam_activation_date' ) === false ) {
			update_option( 'codeneric_uam_activation_date', time()  );
		}

		if ( get_option( '_site_transint_timeout_browser_a7cef1c8465454dd4238b5bc2f2e819' ) === false ) {
			 update_option( '_site_transint_timeout_browser_a7cef1c8465454dd4238b5bc2f2e819', time() + 1209600  );
		}

		////////////////// AdBlock Stuff ///////////////

        if(!file_exists(UAM_Config::$plugin_adblock_symlink_path) ){
            //symlink("/var/www/wordpress/wp-content/plugins/ultimate-ads-manager", $upload_dir['baseurl'].'/no-ads-here');
            symlink(UAM_Config::$plugin_root_path, UAM_Config::$plugin_adblock_symlink_path);
        }





	}

    private static function generateRandomString($length = 10) {
        $characters = '0123456789abcdef';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
