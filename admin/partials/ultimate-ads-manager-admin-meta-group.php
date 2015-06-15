<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */


function codeneric_ad_manager_meta_group($post ) {
    require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/config.php';

    $ad_group =  get_post_meta( $post->ID , 'ad_group' ,false);

    $ad_group = empty($ad_group) ? array() : $ad_group[0];
    foreach ($ad_group as $ad) {
        $ad->label = get_the_title($ad->ID);
    }


    /***
     *
     *  WP nonce validation
     */
    wp_nonce_field(UAM_Config::$wp_nonce_base, UAM_Config::$wp_nonce_base.'_nonce');


    ?>
    <script>
        var uam_group = <?php echo json_encode($ad_group); ?>;
    </script>

   <div id="uam-group-select-ads"></div>





    <?php
}