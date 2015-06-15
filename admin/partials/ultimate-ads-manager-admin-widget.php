<?php

/**
 * Provide a admin widget view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://codeneric.com
 * @since      1.0.0
 *
 * @package    Ultimate_Ads_Manager
 * @subpackage Ultimate_Ads_Manager/public/partials
 */

$title = __('Ultimate Ads Manager');

if(isset($instance['title']))
{
    $title = $instance['title'];

}
$ad_id = '-1';
if(isset($instance['ad_id']))
{
    $ad_id = $instance['ad_id'];

}
$display_title = 'on';
if(isset($instance['display_title']))
{
    $display_title = $instance['display_title'];

}


$ad_args = array(
    'posts_per_page'   => 999,
    'offset'           => 0,
    'orderby'          => 'date',
    'order'            => 'DESC',
    'post_type'        => 'codeneric_ad', //TODO: make this dynamic in case slug changes
    'post_status'      => 'publish',
    'suppress_filters' => true
);
$ad_array = get_posts( $ad_args );
$group_args = array(
    'posts_per_page'   => 999,
    'offset'           => 0,
    'orderby'          => 'date',
    'order'            => 'DESC',
    'post_type'        => 'codeneric_ad_group', //TODO: make this dynamic in case slug changes
    'post_status'      => 'publish',
    'suppress_filters' => true
);
$group_array = get_posts( $group_args );




    ?>


    <div class="widget-content">

        <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>

            <input  id="<?php echo $this->get_field_id( 'display_title' ); ?>" name="<?php echo $this->get_field_name( 'display_title' ); ?>" type="checkbox" <?php checked($display_title, 'on'); ?> />
            <label for="<?php echo $this->get_field_name( 'display_title' ); ?>"><?php _e( 'Display title' ); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_name( 'ad_id' ); ?>"><?php _e( 'Ad:' ); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name( 'ad_id' ); ?>" id="<?php echo $this->get_field_id( 'ad_id' ); ?>" >
                <option value="-1" disabled <?php echo $ad_id == -1 ? 'selected="selected"' : ''; ?>><?php echo __('- Select your ad -'); ?></option>
                <?php if(count($ad_array) > 0): ?>
                <optgroup label="Ads">
                    <?php foreach ($ad_array as $post): ?>
                        <option <?php echo $ad_id == $post->ID ? 'selected="selected"' : ''; ?>  value="<?php echo $post->ID; ?>"><?php echo $post->post_title != '' ? $post->post_title : __('(no title)'); ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <?php endif; ?>
                <?php if(count($ad_array) > 0): ?>
                    <optgroup label="Ad Groups">
                        <?php foreach ($group_array as $post): ?>
                            <option <?php echo $ad_id == $post->ID ? 'selected="selected"' : ''; ?>  value="<?php echo $post->ID; ?>"><?php echo $post->post_title != '' ? $post->post_title : __('(no title)'); ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>


            </select>
        </p>
    </div>


<?php
