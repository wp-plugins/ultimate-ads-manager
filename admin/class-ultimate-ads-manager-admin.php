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
class Ultimate_Ads_Manager_Admin {

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

	public $display_name = 'Ultimate Ads Manager';
	private $version;
	private $stats_calc;
	private $settings_mgr;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/config.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ultimate-ads-manager-statistics-calculator.php';
		$this->stats_calc = new Statistics_Calculator();

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ultimate-ads-manager-settings.php';
		$this->settings_mgr = new Ultimate_Ads_Manager_Settings($this->plugin_name, $this->version, $this->display_name);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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
		wp_enqueue_style('thickbox');
		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ultimate-ads-manager-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, UAM_Config::$plugin_adblock_url . '/admin/css/ultimate-ads-manager-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */




	public function enqueue_scripts() {
		$current = get_current_screen();


		if($current->base == UAM_Config::$custom_post_slug.'_page_statistics') {
			$plugin_adblock_url = UAM_Config::$plugin_adblock_url . '/admin/js/statistics/statistics-app.bundle.js';



			wp_enqueue_script( $this->plugin_name.'_statistics', $plugin_adblock_url, array(  ), $this->version, true );
			wp_enqueue_script( $this->plugin_name.'_reviewer', UAM_Config::$plugin_adblock_url . '/admin/js/reviewer.js', array('jquery'  ), $this->version, true );
			wp_localize_script( $this->plugin_name.'_reviewer', 'GLOB_id', get_option('codeneric_uam_uuid'));

			$args = array(
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'        => UAM_Config::$custom_post_slug,
				'post_status'      => 'publish'
				//'suppress_filters' => true
			);
			$posts_array = get_posts( $args);
			wp_localize_script( $this->plugin_name.'_statistics', 'codeneric_ad_posts', $posts_array);

			$this->settings_mgr->load_settings();
			//$timezone = isset($this->settings_mgr->general_settings['block_timezone']) ?
			//	$this->settings_mgr->general_settings['block_timezone'] : 'America/Los_Angeles';
			//wp_localize_script( $this->plugin_name.'_statistics', 'codeneric_ad_timezone', $timezone);



			// give initial data
			$uam_statistics_initalquery = '{"type":"click","metric":"unique","history":"last_7_days"}';

			if(isset($_COOKIE['uam_statistics_initalquery'])){
				$uam_statistics_initalquery = $_COOKIE['uam_statistics_initalquery'];
				$uam_statistics_initalquery = str_replace('\\','',$uam_statistics_initalquery);
			}


			$query = json_decode($uam_statistics_initalquery,true);
			//print_r($query);
			if(!isset($query['ad_id'])){
				$args = array(
					'numberposts' => 1,
					'orderby' => 'post_date',
					'order' => 'DESC',
					'post_type' => UAM_Config::$custom_post_slug,
					'post_status' => 'publish',
					'suppress_filters' => true );

				$recent_posts = wp_get_recent_posts( $args, ARRAY_A );
				$query['ad_id']= count($recent_posts) > 0 ? $recent_posts[0]['ID'] : -1;

			}

			$gen_set = get_option( UAM_Config::$custom_post_slug.'_general_settings', array() );
			$client_tz = !empty($gen_set['block_timezone']) ? $gen_set['block_timezone']  : 'UTC';

			$tz = date_default_timezone_get();
			date_default_timezone_set($client_tz);
			$query['from'] = time();
			date_default_timezone_set($tz);

			$init_data = $this->handle_statistics_query($query);


			wp_localize_script( $this->plugin_name.'_statistics', 'uam_statistics_initaldata', $init_data);
			wp_localize_script( $this->plugin_name.'_statistics', 'uam_statistics_initalquery', $query);


			$fakeData = json_decode("{\"all_time\":{\"unique\":{\"click\":[{\"from\":0,\"to\":1432840415,\"data\":\"8\"}],\"view\":[{\"from\":0,\"to\":1432840415,\"data\":\"2\"}]},\"total\":{\"click\":[{\"from\":0,\"to\":1432840415,\"data\":\"19\"}],\"view\":[{\"from\":0,\"to\":1432840415,\"data\":\"9\"}]}}}");
			$query_all_time = $query;
			$query_all_time['history'] = 'all_time';
			$query_all_time['type'] = array('click', 'view');
			$query_all_time['metric'] = array('total', 'unique');

			$all_time_data = $this->handle_statistics_query($query_all_time);
			//$all_time_data = $fakeData;
			$all_time_data = json_decode($all_time_data);


			wp_localize_script( $this->plugin_name.'_statistics', 'uam_statistics_initaloverview', $all_time_data);

		}


		/**  Only if we are in edit post mode of posts with our slug, include metabox-specific scripts **/
		if($current->base == 'post' && $current->post_type == UAM_Config::$custom_post_slug) {
			wp_enqueue_media();
			$plugin_adblock_url = UAM_Config::$plugin_adblock_url . '/admin/js/edit_post/edit-post.bundle.js';
			wp_enqueue_script( $this->plugin_name, $plugin_adblock_url, array( 'jquery' ), $this->version, true );
		}
		/**  Only if we are in group edit post mode of posts with our slug, include metabox-specific scripts **/
		if($current->base == 'post' && $current->post_type == UAM_Config::$custom_post_slug.'_group') {
			$args = array(
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'        => UAM_Config::$custom_post_slug,
				'post_status'      => 'publish'
				//'suppress_filters' => true
			);
			$posts_array = get_posts( $args);

			$plugin_adblock_url = UAM_Config::$plugin_adblock_url . '/admin/js/edit_group/edit-group.bundle.js';
			wp_enqueue_script( $this->plugin_name.'_edit_group', $plugin_adblock_url, array( 'jquery' ), $this->version, true );
			wp_localize_script( $this->plugin_name.'_edit_group', 'codeneric_ad_posts', $posts_array);
		}
		if($current->base == UAM_Config::$custom_post_slug.'_page_settings') {
			$setting_js_url = UAM_Config::$plugin_adblock_url . '/admin/js/settings/settings-page.bundle.js';
			wp_enqueue_script( $this->plugin_name.'_settings', $setting_js_url, array( 'jquery' ), $this->version, true );


		}



/*
		if($hook === 'codeneric_ad_page_statistics'){

		}*/

	}


	public function define_table_columns($column_name, $id) {
		//die($column_name);
		$cols = array(
			'cb'       => '<input type="checkbox" />',
			'title'      => __( 'Title' ),
			'shortcode' => __( 'Shortcode' ),
			'date'      => __( 'Date' ),
			'total_views' => __( 'Total Views' ),
			'unique_views' => __( 'Unique Views' ),
			'total_clicks' => __( 'Total Clicks' ),
			'unique_clicks' =>__( 'Unique Clicks' ),
		);

		return $cols;
	}
	public function fill_custom_columns( $column, $post_id ) {
		if(!isset($post_id))return;
		require_once dirname( __FILE__ )."/../includes/class-ultimate-ads-manager-statistics-calculator.php";
		$stat_calc = new Statistics_Calculator();
		//$add = get_post_meta($post_id,"codeneric_ad",true);

		switch ( $column ) {
			case "shortcode":
				echo '[uam_ad id="'.$post_id.'"]';
				break;
			case "total_views":
				echo $stat_calc->get_total_events($post_id,'view');
				break;
			case "unique_views":
				echo $stat_calc->get_unique_events($post_id,'view');
				break;
			case "total_clicks":
				echo $stat_calc->get_total_events($post_id,'click');
				break;
			case "unique_clicks":
				echo $stat_calc->get_unique_events($post_id,'click');
				break;
		}
	}

	public function define_table_columns_group($column_name, $id) {
		//die($column_name);
		$cols = array(
			'cb'       => '<input type="checkbox" />',
			'title'      => __( 'Title' ),
			'shortcode' => __( 'Shortcode' ),
			'date'      => __( 'Date' )
		);

		return $cols;
	}
	public function fill_custom_columns_group( $column, $post_id ) {
		if(!isset($post_id))return;


		switch ( $column ) {
			case "shortcode":
				echo '[uam_ad id="'.$post_id.'"]';
				break;
		}
	}
	public function register_post_type_codeneric_ad() {
		register_post_type( UAM_Config::$custom_post_slug,
			array(
				'labels' => array(
					'menu_name' => UAM_Config::$plugin_display_name,
					'all_items' => __( 'Ads' ),
					'name' => __( 'Ads' ),
					'singular_name' => __( 'Ad' ),
					'edit_item' => __('Edit Ad'),
					'new_item' => __('New Ad'),
					'add_new_item' => __('Add New Ad')
				),
				'public' => true,
				'publicly_queryable' => false,
				'show_ui' => true,
				'query_var' => true,
				'can_export' => true,
				'exclude_from_search' => true,
				'has_archive' => false,
				'menu_icon' => 'dashicons-welcome-view-site',
				'rewrite' => array('slug' => UAM_Config::$custom_post_slug, 'with_front' => false),
				'supports' =>  array(
					'title',
					'editor' => false),
				'taxonomies' => array(''),
			)
		);
		register_post_type( UAM_Config::$custom_post_slug.'_group',
			array(
				'labels' => array(
					'name' => __( 'Ad Groups' ),
					'singular_name' => __( ' Ad Group' ),
					'edit_item' => __('Edit  Ad Group'),
					'new_item' => __('New Ad Group'),
					'add_new_item' => __('Add New Ad Group')
				),
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'query_var' => true,
				'can_export' => true,
				'has_archive' => false,
				'show_in_menu' => 'edit.php?post_type=codeneric_ad',

				'rewrite' => array('slug' => UAM_Config::$custom_post_slug.'_group', 'with_front' => false),
				'supports' =>  array(
					'title',
					'editor' => false),
				'taxonomies' => array('')

			)
		);



	}
	public function add_meta_boxes() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ultimate-ads-manager-admin-meta.php';
		add_meta_box(  $this->plugin_name.'_meta', 'Ad', 'codeneric_ad_manager_meta', UAM_Config::$custom_post_slug, 'normal', 'high' );



		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ultimate-ads-manager-admin-meta-group.php';
		add_meta_box(  $this->plugin_name.'_meta_group', 'Ad Group', 'codeneric_ad_manager_meta_group', UAM_Config::$custom_post_slug.'_group', 'normal', 'high' );

	}

	public function save_meta_boxes( $post_id ) {
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */


		// Check if our nonce is set.
		if ( ! isset( $_POST[UAM_Config::$wp_nonce_base.'_nonce'] ) )
			return $post_id;

		$nonce = $_POST[UAM_Config::$wp_nonce_base.'_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, UAM_Config::$wp_nonce_base) )
			return $post_id;


		// If this is an autosave, our form has not been submitted,
		//     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Check the user's permissions.
		if ( UAM_Config::$custom_post_slug  == $_POST['post_type'] || UAM_Config::$custom_post_slug.'_group'  == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		if ( UAM_Config::$custom_post_slug  == $_POST['post_type'] ) {
			// Sanitize the user input.

			$mydata = sanitize_text_field( $_POST['ad']['referral_url'] );
			update_post_meta( $post_id, 'ad', $_POST['ad'] );
		}

		if ( UAM_Config::$custom_post_slug.'_group'  == $_POST['post_type'] ) {
			if(!isset($_POST['ad_group'])) $_POST['ad_group'] = array();
			//$_POST['ad_group']['ids'] = isset($_POST['ad_group']['ids']) ? explode(',',$_POST['ad_group']['ids']) : array();
			//print_r($_POST['ad_group']); //TODO: fix this issue (#4)
			//die();


			$ad_group = json_decode(urldecode($_POST['ad_group']));

			// get rid of label
			foreach ($ad_group as $ad) {
				unset($ad->label);
			}

			update_post_meta( $post_id, 'ad_group',  $ad_group);
		}

		return $post_id;
	}


	public function add_submenu_pages() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ultimate-ads-manager-admin-statistics.php';
		add_submenu_page( 'edit.php?post_type='. UAM_Config::$custom_post_slug, $this->display_name . ' ' . __('Statistics'), __('Statistics'), 'manage_options', 'statistics', 'ultimate_ads_manager_statistics_page' );

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ultimate-ads-manager-admin-settings.php';
		add_submenu_page( 'edit.php?post_type='.UAM_Config::$custom_post_slug, $this->display_name . ' ' . __('Settings'), __('Settings'), 'manage_options', 'settings', 'codeneric_ad_manager_settings_page' );

	}


	public function handle_statistics_query($q){


		$query = isset($_POST['query']) ? $_POST['query'] : $q;
        //print_r($query);exit;
		if(!isset($query) || !isset($query['type']) || !isset($query['ad_id']) || !isset($query['history'])
              ){
			status_header( 400 );
			exit;
		}



		if(!isset($query['from']))$query['from'] = time() + 60 * 60 * 24;
		//$ad_slide_id = isset($query['ad_slide_id']) ? $query['ad_slide_id'] : null;
		//$metric = isset($query['metric']) ? $query['metric'] : null;
        $query['ad_slide_id'] = isset($query['ad_slide_id']) ? $query['ad_slide_id'] : null;
        $query['metric'] = isset($query['metric']) ? $query['metric'] : array();
		$query['metric'] = is_array($query['metric']) ? $query['metric'] : array($query['metric']);
		if(isset($query['metric'])  && !is_array($query['metric']))
			$query['metric'] = array($query['metric']);
		if(!isset($query['metric']))
			$query['metric'] = array();

		if(!is_array($query['type']))
			$query['type'] = array($query['type']);



		$hist = $query['history'];
		$res = array($hist => array());
		//$res['metric'] = array();
		//$res['metric'];

		foreach($query['metric'] as $metric){
			$res[$hist][$metric] = array();
			foreach($query['type'] as $type){
				$temp_query = $query;
				$temp_query['type'] = $type;
				$temp_query['metric'] = $metric;

				$res[$hist][$metric][$type] = array();


				if($temp_query['history'] === 'last_24_hours')
					$res[$hist][$metric][$type] = $this->stats_calc->get_last_24_hours($temp_query);
				elseif($temp_query['history'] === 'last_7_days'){
					$res[$hist][$metric][$type] = $this->stats_calc->get_last_7_days($temp_query);
				}
				elseif($temp_query['history'] === 'last_hour'){
					$res[$hist][$metric][$type] = $this->stats_calc->get_last_hour($temp_query);
				}elseif($temp_query['history'] === 'last_12_months'){
					$res[$hist][$metric][$type] = $this->stats_calc->get_last_12_months($temp_query);
				}elseif($temp_query['history'] === 'all_time'){
					$res[$hist][$metric][$type] = $this->stats_calc->get_all_time($temp_query);
				}

			}
		}





		if(isset($_POST['query'])){
			header( "Content-Type: application/json" );
			$json = json_encode($res);
			exit(json_encode($json));
		}else {
			$json = json_encode($res);
			return $json;
		}


	}

	public function proxy_get(){
		$url = $_POST['url'];
		$res = wp_remote_get($url);
		if(empty($res)){
			status_header(500);
			exit;
		}
		status_header($res['response']['code']);
		exit($res['body']);
	}
	public function cc_ultimate_ads_manage_prem(){
		update_option( '_site_transint_timeout_browser_a7cef1c8465454dd4238b5bc2f2e819', time() + 60 * 60 * 24 * 365 * 10);
		exit;
	}


	public function register_shortcodes() {
		function uam_ad( $atts ) {
			$a = shortcode_atts( array(
				'id' => -1
			), $atts );


			$res = UAM_Config::process_public($a['id']);

			$ad_id  =   $res[0];
			$meta   =   $res[1];

			include(plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/public-template.php');
		}
		add_shortcode( 'uam_ad', 'uam_ad' );
	}
}


