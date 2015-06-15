<?php

/***
 * if no ad was selected, do not display any widget
 * */
if(!isset($instance['ad_id']))
    return false;


//$meta = get_post_meta( $instance['ad_id'] , 'ad_group' ,true);
//$ad_ids = explode(',', $meta);
//print_r($meta);

/**
*   $this is the Widget Class given in ultimate-ads-manager/trunk/admin/class-ultimate-ads-manager-widget.php:Ultimate_Ads_Manager_Widget
 */
/*
 *
 *

*/

$res = UAM_Config::process_public($instance['ad_id']);

$ad_id  =   $res[0];
$meta   =   $res[1];

// TODO images could be unset
?>

<?php echo $args['before_widget']; ?>

    <?php
        /*
         * Display title if needed
         * */

        if(isset($instance['display_title']) && !empty($instance['title']))
            echo $args['before_title'].$instance['title'].$args['after_title'];

        include(plugin_dir_path( dirname( __FILE__ ) ) . '../public/partials/public-template.php');
    ?>



<?php echo $args['after_widget']; ?>