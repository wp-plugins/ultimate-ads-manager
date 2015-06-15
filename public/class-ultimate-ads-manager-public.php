<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://codeneric.com
 * @since      1.0.0
 *
 * @package    Ultimate_Ads_Manager
 * @subpackage Ultimate_Ads_Manager/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ultimate_Ads_Manager
 * @subpackage Ultimate_Ads_Manager/public
 * @author     Codeneric <contact@codeneric.com>
 */
class Ultimate_Ads_Manager_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		require_once plugin_dir_path( __FILE__ ) . '../includes/config.php';
		UAM_Config::init();


	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ultimate_Ads_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ultimate_Ads_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, UAM_Config::$plugin_adblock_url . '/public/css/ultimate-ads-manager-public.css', array(), $this->version, 'all');

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{


		$gen_set = get_option( UAM_Config::$custom_post_slug.'_general_settings', array() );
		$url = !empty($gen_set['block_adblock']) ? UAM_Config::$plugin_adblock_url  : plugins_url( '..', __FILE__ );
		wp_enqueue_script($this->plugin_name, $url . '/public/js/ultimate-ads-manager-public.js', array('jquery'), $this->version, true);

		// inject ajax path as variable ajaxurl
		wp_localize_script( $this->plugin_name, 'ajaxurl', admin_url( 'admin-ajax.php' ));


	}

	public function handle_client_side_ad_event()
	{

		global $wpdb;
		$post = $_POST;
		if(!isset($post) || !isset($post['type']) || !isset($post['ad_id'])|| !isset($post['ad_slide_id'])
		|| !isset(UAM_Config::$db_map[$post['type']]) ){
			status_header( 400 );
			exit;
		}

		$uuid = 42; //magic
		$type = UAM_Config::$db_map[$post['type']];

		if(isset($post['uuid'])){
			$enc_id = base64_decode($post['uuid']);
			$temp_uuid = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, UAM_Config::$key,
				$enc_id, MCRYPT_MODE_CBC, UAM_Config::$iv);
			$temp_uuid = trim($temp_uuid); //because PHP is bullshit

			if(ctype_digit($temp_uuid)) //check if all characters are digits
				$uuid = intval ( $temp_uuid );
			else{
				status_header( 400 );
				exit;
			}
		}

		$table_name = UAM_Config::$table_name_events;
		$ip = $this->get_client_ip();



		$event = array(
			'time' => current_time( 'mysql' ),
			'type' => $type,
			'uuid' => $uuid,
			'ip'   => $ip, //TODO: save ip as Binary(4) http://stackoverflow.com/questions/1385552/datatype-for-storing-ip-address-in-sql-server
			'ad_id' => $post['ad_id'],
			'ad_slide_id' => $post['ad_slide_id']
		);

		$prop_of_fake = $this->prop_that_fake_event($event);


		$succ = true;//$prop_of_fake < 0.5 ? $wpdb->insert($table_name,$event ) : false; //will be escaped by wordpress
		$wpdb->insert($table_name,$event );


		status_header( $succ !== false ? 200 : 400 );
		if(!isset($post['uuid']) && $succ !== false){
			$wpdb->update( $table_name, array('uuid' => $wpdb->insert_id), array('id'=> $wpdb->insert_id) );
			header( "Content-Type: application/json" );
			$enc_id = mcrypt_encrypt ( MCRYPT_RIJNDAEL_128 , UAM_Config::$key , $wpdb->insert_id , MCRYPT_MODE_CBC, UAM_Config::$iv);
			$res = new stdClass();
			$res->uuid = base64_encode($enc_id);;
			exit(json_encode($res));
		}else{

			exit;
		}


	}

	private function get_client_ip() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}

	private function prop_that_fake_event($event){
		global $wpdb;
		$prop = 0; //we ar optimistic, no fake.

		$table_name = UAM_Config::$table_name_events;
		$db_map = UAM_Config::$db_map;

		//get most recent click of this ip
		$query = "SELECT * FROM $table_name WHERE ip = '%s' AND type = %d ORDER BY time DESC LIMIT 1";

		$old_event = $wpdb->get_results( $wpdb->prepare($query, $event['ip'], $db_map['click']) );
		if($wpdb->num_rows === 1){
			$old_time  = strtotime($old_event[0]->time);
			$curr_time = strtotime($event['time']);
			$diff_in_sec = $curr_time - $old_time;

			if($diff_in_sec <= 60 && $old_event[0]->uudi !== $event['uuid']){ //somebody wants to produce 2 total events
				$prop = 1;
			}
		}
		/*
		foreach($users_latest_events as $event){

		}*/

		return $prop;
	}
}
